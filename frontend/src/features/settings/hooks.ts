import { useMutation } from '@tanstack/react-query';
import { api, ensureCsrfCookie } from '@/lib/api/client';
import { call } from '@/lib/api/errors';
import type { components } from '@/lib/api/schema';

export type ChangePasswordInput = components['schemas']['ChangePasswordRequest'];

export function useChangePassword() {
  return useMutation({
    mutationFn: async (body: ChangePasswordInput) => {
      await ensureCsrfCookie();
      return call(api.PUT('/me/password', { body }));
    },
  });
}
