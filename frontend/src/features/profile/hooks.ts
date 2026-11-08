import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { api, ensureCsrfCookie } from '@/lib/api/client';
import { call } from '@/lib/api/errors';
import type { components } from '@/lib/api/schema';

export type Profile = components['schemas']['ProfileResource'];
export type Discipline = components['schemas']['DisciplineResource'];
export type ProfileInput = components['schemas']['UpdateProfileRequest'];

export function useProfile() {
  return useQuery({ queryKey: ['profile'], queryFn: () => call(api.GET('/profile')) });
}

export function useDisciplines() {
  return useQuery({ queryKey: ['disciplines'], queryFn: () => call(api.GET('/disciplines')), staleTime: Infinity });
}

export function useUpdateProfile() {
  const client = useQueryClient();
  return useMutation({
    mutationFn: async (body: ProfileInput) => {
      await ensureCsrfCookie();
      return call(api.PUT('/profile', { body }));
    },
    onSuccess: (profile) => {
      client.setQueryData(['profile'], profile);
      // header e card da Home dependem do /me (nome, iniciais, profile_complete)
      void client.invalidateQueries({ queryKey: ['me'] });
    },
  });
}
