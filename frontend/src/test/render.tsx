import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { render } from '@testing-library/react';
import type { ReactElement } from 'react';
import { createMemoryRouter, MemoryRouter, RouterProvider } from 'react-router';
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
export function renderWithDataRouter(ui: ReactElement, { path = '/', query = {} }: { path?: string; query?: Record<string, unknown> } = {}) {
  const client = newClient();
  for (const [key, value] of Object.entries(query)) client.setQueryData([key], value);
  const router = createMemoryRouter([{ path, element: <LeaveGuardProvider>{ui}</LeaveGuardProvider> }], { initialEntries: [path] });

  return render(
    <QueryClientProvider client={client}>
      <RouterProvider router={router} />
    </QueryClientProvider>,
  );
}
