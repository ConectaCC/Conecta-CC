import { vi } from 'vitest';
import * as client from '@/lib/api/client';

type Method = 'GET' | 'POST' | 'PUT' | 'DELETE';

// o formato que o openapi-fetch devolve: data quando dá certo, error com o corpo quando não
export function reply(status: number, body?: unknown) {
  const ok = status >= 200 && status < 300;
  return { data: ok ? body : undefined, error: ok ? undefined : body, response: new Response(null, { status }) };
}

// troca o método do cliente da API por respostas na ordem dada; o cookie CSRF sempre responde
export function mockApi(method: Method, ...replies: ReturnType<typeof reply>[]) {
  vi.spyOn(globalThis, 'fetch').mockResolvedValue(new Response(null, { status: 204 }));
  const spy = vi.spyOn(client.api, method);
  for (const response of replies) spy.mockResolvedValueOnce(response);
  return spy;
}

export function failNetwork(method: Method) {
  vi.spyOn(globalThis, 'fetch').mockResolvedValue(new Response(null, { status: 204 }));
  return vi.spyOn(client.api, method).mockRejectedValue(new TypeError('Failed to fetch'));
}
