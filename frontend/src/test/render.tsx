import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { render, renderHook } from '@testing-library/react';
import type { ReactElement, ReactNode } from 'react';
import { createMemoryRouter, MemoryRouter, RouterProvider, type RouteObject } from 'react-router';
import { vi } from 'vitest';
import { LeaveGuardProvider } from '@/components/layout/LeaveGuardProvider';

const newClient = () => new QueryClient({ defaultOptions: { queries: { retry: false }, mutations: { retry: false } } });

// a tela com Router e React Query, sem nova tentativa: um erro de API aparece na hora
// route aceita o estado da navegação, como { pathname: '/login', state: { registeredEmail } }
export function renderWithProviders(ui: ReactElement, { route = '/' }: { route?: string | { pathname: string; state: unknown } } = {}) {
  return render(
    <QueryClientProvider client={newClient()}>
      <MemoryRouter initialEntries={[route]}>{ui}</MemoryRouter>
    </QueryClientProvider>,
  );
}

// para telas com aviso de alterações não salvas: o useBlocker só funciona com um data router, e o guard vem do AppLayout
// routes são as outras páginas para onde a tela pode navegar
export function renderWithDataRouter(
  ui: ReactElement,
  { path = '/', query = {}, routes = [] }: { path?: string; query?: Record<string, unknown>; routes?: RouteObject[] } = {},
) {
  const client = newClient();
  for (const [key, value] of Object.entries(query)) client.setQueryData([key], value);
  const router = createMemoryRouter([{ path, element: <LeaveGuardProvider>{ui}</LeaveGuardProvider> }, ...routes], { initialEntries: [path] });

  const view = render(
    <QueryClientProvider client={client}>
      <RouterProvider router={router} />
    </QueryClientProvider>,
  );
  return { ...view, router };
}

// um hook do React Query, com as chaves que ele invalida
export function renderHookWithClient<T>(hook: () => T) {
  const client = newClient();
  const invalidate = vi.spyOn(client, 'invalidateQueries');
  const view = renderHook(hook, { wrapper: ({ children }: { children: ReactNode }) => <QueryClientProvider client={client}>{children}</QueryClientProvider> });

  return { ...view, client, invalidatedKeys: () => invalidate.mock.calls.map(([filters]) => filters?.queryKey) };
}
