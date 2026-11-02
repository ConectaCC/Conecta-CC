import { useMutation, useQuery, useQueryClient, type QueryClient } from '@tanstack/react-query';
import { api, ensureCsrfCookie } from '@/lib/api/client';
import { call } from '@/lib/api/errors';
import type { components } from '@/lib/api/schema';

export type ModerationMaterial = components['schemas']['ModerationMaterialResource'];

const QUEUE_KEY = ['moderation', 'materials'];

export function useModerationQueue() {
  return useQuery({ queryKey: QUEUE_KEY, queryFn: () => call(api.GET('/moderation/materials')) });
}

function refresh(client: QueryClient, material: ModerationMaterial) {
  client.invalidateQueries({ queryKey: QUEUE_KEY });
  client.invalidateQueries({ queryKey: ['materials', material.discipline.id] });
}

export function useApproveMaterial() {
  const client = useQueryClient();
  return useMutation({
    mutationFn: async (material: ModerationMaterial) => {
      await ensureCsrfCookie();
      return call(api.POST('/moderation/materials/{material}/approve', { params: { path: { material: material.id } } }));
    },
    onSuccess: (_, material) => refresh(client, material),
  });
}

export function useRejectMaterial() {
  const client = useQueryClient();
  return useMutation({
    mutationFn: async ({ material, reason }: { material: ModerationMaterial; reason: string }) => {
      await ensureCsrfCookie();
      return call(api.POST('/moderation/materials/{material}/reject', { params: { path: { material: material.id } }, body: { reason } }));
    },
    onSuccess: (_, { material }) => refresh(client, material),
  });
}
