import { useInfiniteQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { api, ensureCsrfCookie } from '@/lib/api/client';
import { call } from '@/lib/api/errors';
import type { components } from '@/lib/api/schema';

export type Material = components['schemas']['MaterialResource'];
export type MaterialInput = components['schemas']['StoreMaterialRequest'];

export function useDisciplineMaterials(disciplineId: number) {
  return useInfiniteQuery({
    queryKey: ['materials', disciplineId],
    queryFn: ({ pageParam }) =>
      call(
        api.GET('/disciplines/{discipline}/materials', {
          params: { path: { discipline: disciplineId }, query: pageParam ? { cursor: pageParam } : {} },
        }),
      ),
    initialPageParam: null as string | null,
    getNextPageParam: (last) => last.meta.next_cursor,
  });
}

export function useShareMaterial(disciplineId: number) {
  return useMutation({
    mutationFn: async (body: MaterialInput) => {
      await ensureCsrfCookie();
      return call(api.POST('/disciplines/{discipline}/materials', { params: { path: { discipline: disciplineId } }, body }));
    },
  });
}

export function useReviseMaterial() {
  const client = useQueryClient();
  return useMutation({
    mutationFn: async ({ id, body }: { id: number; body: MaterialInput }) => {
      await ensureCsrfCookie();
      return call(api.PUT('/materials/{material}', { params: { path: { material: id } }, body }));
    },
    onSuccess: () => {
      void client.invalidateQueries({ queryKey: ['my-materials'] });
      void client.invalidateQueries({ queryKey: ['moderation', 'materials'] });
    },
  });
}
