import { useInfiniteQuery } from '@tanstack/react-query';
import { api } from '@/lib/api/client';
import { call } from '@/lib/api/errors';
import type { components } from '@/lib/api/schema';

export type FeedItem = components['schemas']['CommunityFeedItemResource'];

export function useCommunityFeed() {
  return useInfiniteQuery({
    queryKey: ['community', 'feed'],
    queryFn: ({ pageParam }) => call(api.GET('/community/feed', { params: { query: pageParam ? { cursor: pageParam } : {} } })),
    initialPageParam: null as string | null,
    getNextPageParam: (last) => last.meta.next_cursor,
  });
}
