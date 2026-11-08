import createClient, { type Middleware } from 'openapi-fetch';
import { queryClient } from '@/lib/queryClient';
import { NetworkError } from './errors';
import type { paths } from './schema';

function readCookie(name: string): string | null {
  const value = document.cookie.match(new RegExp(`(?:^|; )${name}=([^;]*)`))?.[1];
  return value === undefined ? null : decodeURIComponent(value);
}

const session: Middleware = {
  onRequest({ request }) {
    const token = readCookie('XSRF-TOKEN');
    if (token) request.headers.set('X-XSRF-TOKEN', token);
    return request;
  },
  onResponse({ response }) {
    // sessão expirada ou encerrada: o guard de rota leva ao Login
    if (response.status === 401) queryClient.setQueryData(['me'], null);
    return response;
  },
};

export const api = createClient<paths>({
  baseUrl: '/api/v1',
  credentials: 'same-origin',
  headers: { Accept: 'application/json' },
});
api.use(session);

export async function ensureCsrfCookie(): Promise<void> {
  try {
    await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
  } catch {
    throw new NetworkError('network');
  }
}
