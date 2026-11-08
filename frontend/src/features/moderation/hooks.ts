import { useMutation, useQuery, useQueryClient, type QueryClient } from '@tanstack/react-query';
import { api, ensureCsrfCookie } from '@/lib/api/client';
import { call } from '@/lib/api/errors';
import type { components } from '@/lib/api/schema';

export type ModerationMaterial = components['schemas']['ModerationMaterialResource'];
export type ModerationOpportunity = components['schemas']['ModerationOpportunityResource'];
// a fila mistura os dois tipos; cada item diz o seu (decisoes.md §70)
export type QueueItem = { kind: 'material'; item: ModerationMaterial } | { kind: 'opportunity'; item: ModerationOpportunity };

const MATERIALS_KEY = ['moderation', 'materials'];
const OPPORTUNITIES_KEY = ['moderation', 'opportunities'];

export function useModerationQueue() {
  const materials = useQuery({ queryKey: MATERIALS_KEY, queryFn: () => call(api.GET('/moderation/materials')) });
  const opportunities = useQuery({ queryKey: OPPORTUNITIES_KEY, queryFn: () => call(api.GET('/moderation/opportunities')) });

  const data: QueueItem[] | undefined =
    materials.data && opportunities.data
      ? [
          ...materials.data.map((item) => ({ kind: 'material' as const, item })),
          ...opportunities.data.map((item) => ({ kind: 'opportunity' as const, item })),
          // mais antigos primeiro, como a fila de materiais já fazia
        ].sort((a, b) => a.item.submitted_at.localeCompare(b.item.submitted_at))
      : undefined;

  return {
    data,
    isPending: materials.isPending || opportunities.isPending,
    isError: materials.isError || opportunities.isError,
    refetch: () => Promise.all([materials.refetch(), opportunities.refetch()]),
  };
}

function refresh(client: QueryClient, queued: QueueItem) {
  if (queued.kind === 'material') {
    void client.invalidateQueries({ queryKey: MATERIALS_KEY });
    void client.invalidateQueries({ queryKey: ['materials', queued.item.discipline.id] });
  } else {
    void client.invalidateQueries({ queryKey: OPPORTUNITIES_KEY });
    void client.invalidateQueries({ queryKey: ['opportunities'] });
  }
  void client.invalidateQueries({ queryKey: ['community'] });
}

export function useApprove() {
  const client = useQueryClient();
  return useMutation({
    mutationFn: async (queued: QueueItem) => {
      await ensureCsrfCookie();
      return queued.kind === 'material'
        ? call(api.POST('/moderation/materials/{material}/approve', { params: { path: { material: queued.item.id } } }))
        : call(api.POST('/moderation/opportunities/{opportunity}/approve', { params: { path: { opportunity: queued.item.id } } }));
    },
    onSuccess: (_, queued) => refresh(client, queued),
  });
}

export function useReject() {
  const client = useQueryClient();
  return useMutation({
    mutationFn: async ({ queued, reason }: { queued: QueueItem; reason: string }) => {
      await ensureCsrfCookie();
      return queued.kind === 'material'
        ? call(api.POST('/moderation/materials/{material}/reject', { params: { path: { material: queued.item.id } }, body: { reason } }))
        : call(api.POST('/moderation/opportunities/{opportunity}/reject', { params: { path: { opportunity: queued.item.id } }, body: { reason } }));
    },
    onSuccess: (_, { queued }) => refresh(client, queued),
  });
}
