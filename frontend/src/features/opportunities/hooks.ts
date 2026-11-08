import { keepPreviousData, useInfiniteQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { api, ensureCsrfCookie } from '@/lib/api/client';
import { call } from '@/lib/api/errors';
import type { components } from '@/lib/api/schema';

export type Opportunity = components['schemas']['OpportunityResource'];
export type OpportunityInput = components['schemas']['StoreOpportunityRequest'];

export function useOpportunities(q: string, type: string | null) {
  return useInfiniteQuery({
    queryKey: ['opportunities', q, type],
    queryFn: ({ pageParam }) =>
      call(
        api.GET('/opportunities', {
          params: {
            query: {
              ...(q ? { q } : {}),
              ...(type ? { type: type as OpportunityInput['type'] } : {}),
              ...(pageParam ? { cursor: pageParam } : {}),
            },
          },
        }),
      ),
    initialPageParam: null as string | null,
    getNextPageParam: (last) => last.meta.next_cursor,
    placeholderData: keepPreviousData,
  });
}

export function useAddOpportunity() {
  const client = useQueryClient();
  return useMutation({
    mutationFn: async (body: OpportunityInput) => {
      await ensureCsrfCookie();
      return call(api.POST('/opportunities', { body }));
    },
    onSuccess: () => {
      client.invalidateQueries({ queryKey: ['my-opportunities'] });
      client.invalidateQueries({ queryKey: ['moderation'] });
    },
  });
}
