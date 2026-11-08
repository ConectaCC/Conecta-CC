import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { api, ensureCsrfCookie } from '@/lib/api/client';
import { ApiError, call } from '@/lib/api/errors';
import type { components } from '@/lib/api/schema';

export type User = components['schemas']['UserResource'];
export type Role = components['schemas']['Role'];
export type LoginInput = components['schemas']['LoginRequest'];
export type RegisterInput = components['schemas']['RegisterRequest'];
export type Terms = {
  version: string;
  title: string;
  sections: { title: string; body: string }[];
  acceptance_label: string;
};

export function useMe() {
  return useQuery({
    queryKey: ['me'],
    queryFn: async (): Promise<User | null> => {
      try {
        return await call(api.GET('/me'));
      } catch (error) {
        if (error instanceof ApiError && error.status === 401) return null;
        throw error;
      }
    },
  });
}

export function useLogin() {
  const client = useQueryClient();
  return useMutation({
    mutationFn: async (body: LoginInput) => {
      await ensureCsrfCookie();
      return call(api.POST('/login', { body }));
    },
    onSuccess: (user) => client.setQueryData(['me'], user),
  });
}

export function useRegister() {
  return useMutation({
    mutationFn: async (body: RegisterInput) => {
      await ensureCsrfCookie();
      await call(api.POST('/register', { body }));
    },
  });
}

export function useLogout() {
  const client = useQueryClient();
  return useMutation({
    mutationFn: () => call(api.POST('/logout')),
    onSettled: () => {
      client.clear();
      client.setQueryData(['me'], null);
    },
  });
}

export function useTerms() {
  return useQuery({
    queryKey: ['terms'],
    // o Scramble não infere o corpo deste endpoint (JSON lido de arquivo), por isso o tipo é declarado aqui
    queryFn: async () => (await call(api.GET('/terms/current'))) as Terms,
    staleTime: Infinity,
  });
}
