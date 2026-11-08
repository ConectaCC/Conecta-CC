import * as Sentry from '@sentry/react';
import { useEffect, useState } from 'react';
import { Outlet } from 'react-router';
import { FormAlert } from '@/components/FormAlert';
import { Sheet, SheetContent, SheetDescription, SheetTitle } from '@/components/ui/sheet';
import { useMe } from '@/features/auth/hooks';
import { Header } from './Header';
import { LeaveGuardProvider } from './LeaveGuardProvider';
import { Sidebar } from './Sidebar';

export function AppLayout() {
  const me = useMe();
  const user = me.data ?? undefined;
  const [menuOpen, setMenuOpen] = useState(false);
  useEffect(() => {
    // decisão 8: só o ID interno vai para o Sentry
    Sentry.setUser(user ? { id: String(user.id) } : null);
  }, [user]);

  return (
    <LeaveGuardProvider>
      <div className="min-h-dvh bg-ice lg:pl-[275px]">
        <aside className="fixed inset-y-0 left-0 hidden w-[275px] lg:block">
          <Sidebar user={user} />
        </aside>

        <Sheet open={menuOpen} onOpenChange={setMenuOpen}>
          <SheetContent side="left" showCloseButton={false} className="w-[300px] max-w-[85vw] border-none bg-navy-900 p-0">
            <SheetTitle className="sr-only">Menu</SheetTitle>
            <SheetDescription className="sr-only">Navegação principal do ConectaCC</SheetDescription>
            <Sidebar user={user} onClose={() => setMenuOpen(false)} />
          </SheetContent>
        </Sheet>

        <Header user={user} loading={me.isPending} onOpenMenu={() => setMenuOpen(true)} />

        {/* do branco do cabeçalho ao azul-claro da página, sem corte: só nos primeiros 140 px */}
        <main className="bg-[linear-gradient(to_bottom,var(--color-white),var(--color-ice)_140px)] px-4 py-8 sm:px-8 lg:px-11 lg:py-12">
          {me.isError && (
            <div className="mb-8">
              <FormAlert
                variant="warning"
                action={
                  <button type="button" onClick={() => me.refetch()} className="font-semibold underline underline-offset-2">
                    Tentar novamente
                  </button>
                }
              >
                Não foi possível carregar algumas informações agora. Tente novamente.
              </FormAlert>
            </div>
          )}
          <Outlet />
        </main>
      </div>
    </LeaveGuardProvider>
  );
}
