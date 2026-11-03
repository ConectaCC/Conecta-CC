import { useInfiniteQuery, useMutation, useQueryClient, type QueryClient } from '@tanstack/react-query';
import { api, ensureCsrfCookie } from '@/lib/api/client';
import { call } from '@/lib/api/errors';
import type { components } from '@/lib/api/schema';

export type MyMaterial = components['schemas']['MyMaterialResource'];
export type MyFilter = 'all' | 'pending' | 'approved' | 'rejected' | 'deleted';

export const FILTERS: { value: MyFilter; label: string }[] = [
  { value: 'all', label: 'Todos' },
  { value: 'pending', label: 'Em andamento' },
  { value: 'approved', label: 'Aprovados' },
  { value: 'rejected', label: 'Rejeitados' },
  { value: 'deleted', label: 'Excluídos' },
];

export function useMyMaterials(filter: MyFilter) {
  return useInfiniteQuery({
    queryKey: ['my-materials', filter],
    queryFn: ({ pageParam }) =>
      call(api.GET('/me/materials', { params: { query: { status: filter, ...(pageParam ? { cursor: pageParam } : {}) } } })),
    initialPageParam: null as string | null,
    getNextPageParam: (last) => last.meta.next_cursor,
  });
}

// o que o autor muda aparece em Meus conteúdos, na disciplina e na fila de Moderação
export function refreshAfterAuthorAction(client: QueryClient, disciplineId: number) {
  client.invalidateQueries({ queryKey: ['my-materials'] });
  client.invalidateQueries({ queryKey: ['materials', disciplineId] });
  client.invalidateQueries({ queryKey: ['moderation', 'materials'] });
}

export function useCancelMaterial() {
  const client = useQueryClient();
  return useMutation({
    mutationFn: async (material: { id: number; discipline: { id: number } }) => {
      await ensureCsrfCookie();
      return call(api.POST('/materials/{material}/cancel', { params: { path: { material: material.id } } }));
    },
    onSuccess: (_, material) => refreshAfterAuthorAction(client, material.discipline.id),
  });
}

export function useDeleteMaterial() {
  const client = useQueryClient();
  return useMutation({
    mutationFn: async (material: { id: number; discipline: { id: number } }) => {
      await ensureCsrfCookie();
      return call(api.DELETE('/materials/{material}', { params: { path: { material: material.id } } }));
    },
    onSuccess: (_, material) => refreshAfterAuthorAction(client, material.discipline.id),
  });
}
