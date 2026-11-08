import { keepPreviousData, useInfiniteQuery, useQuery } from '@tanstack/react-query';
import { api } from '@/lib/api/client';
import { call } from '@/lib/api/errors';
import type { components } from '@/lib/api/schema';

export type FeedItem = components['schemas']['CommunityFeedItemResource'];
export type FeedOrder = 'recent' | 'oldest';
export type FeedType = 'material' | 'opportunity' | null;

export function useCommunityFeed(q: string, order: FeedOrder, type: FeedType) {
  return useInfiniteQuery({
    queryKey: ['community', 'feed', q, order, type],
    queryFn: ({ pageParam }) =>
      call(
        api.GET('/community/feed', {
          params: {
            query: { ...(q ? { q } : {}), ...(order === 'oldest' ? { order } : {}), ...(type ? { type } : {}), ...(pageParam ? { cursor: pageParam } : {}) },
          },
        }),
      ),
    initialPageParam: null as string | null,
    getNextPageParam: (last) => last.meta.next_cursor,
    // ao digitar, a lista anterior fica na tela até a nova chegar
    placeholderData: keepPreviousData,
  });
}

export function useCommunityStats() {
  return useQuery({ queryKey: ['community', 'stats'], queryFn: () => call(api.GET('/community/stats')) });
}
