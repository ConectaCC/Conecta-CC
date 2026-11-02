import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { api, ensureCsrfCookie } from '@/lib/api/client';
import { call } from '@/lib/api/errors';
import type { components } from '@/lib/api/schema';

export type AppNotification = components['schemas']['NotificationResource'];

const KEY = ['notifications'];

// shortcut: polling de 60 s (decisoes.md 55.5); trocar por tempo real se a demora incomodar
export function useNotifications() {
  return useQuery({
    queryKey: KEY,
    queryFn: () => call(api.GET('/notifications')),
    refetchInterval: 60_000,
    refetchIntervalInBackground: false,
  });
}

export function useReadNotification() {
  const client = useQueryClient();
  return useMutation({
    mutationFn: async (id: string) => {
      await ensureCsrfCookie();
      return call(api.POST('/notifications/{notification}/read', { params: { path: { notification: id } } }));
    },
    onSuccess: () => client.invalidateQueries({ queryKey: KEY }),
  });
}

export function useReadAllNotifications() {
  const client = useQueryClient();
  return useMutation({
    mutationFn: async () => {
      await ensureCsrfCookie();
      return call(api.POST('/notifications/read-all'));
    },
    onSuccess: () => client.invalidateQueries({ queryKey: KEY }),
  });
}
