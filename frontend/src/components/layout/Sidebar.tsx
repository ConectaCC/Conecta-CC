import { BookOpen, House, LayoutDashboard, LogOut, Settings, ShieldCheck, User as UserIcon, X, type LucideIcon } from 'lucide-react';
import { NavLink, useNavigate } from 'react-router';
import { Brand } from '@/components/Brand';
import { useLogout, type User } from '@/features/auth/hooks';
import { canModerate, isAdmin } from '@/features/auth/roles';
import { cn } from '@/lib/utils';
import { useLeaveGuard } from './leaveGuard';

type Item = { to: string; label: string; icon: LucideIcon };

const mainItems: Item[] = [
  { to: '/home', label: 'Home', icon: House },
  { to: '/estudos', label: 'Estudos', icon: BookOpen },
  { to: '/perfil', label: 'Perfil', icon: UserIcon },
];

const itemClass =
  'relative flex h-14 items-center gap-4 rounded-lg px-4 text-[17px] text-glow transition-colors hover:bg-white/5 hover:text-white';
const activeClass =
  'bg-[#1d3c74] hover:bg-[#1d3c74] font-semibold text-white before:absolute before:inset-y-2 before:left-0 before:w-1 before:rounded-full before:bg-glow';

function NavItem({ item, onNavigate }: { item: Item; onNavigate?: () => void }) {
  const Icon = item.icon;
  return (
    <NavLink to={item.to} onClick={onNavigate} className={({ isActive }) => cn(itemClass, isActive && activeClass)}>
      <Icon className="size-5" aria-hidden />
      {item.label}
    </NavLink>
  );
}

export function Sidebar({ user, onClose }: { user?: User; onClose?: () => void }) {
  const logout = useLogout();
  const navigate = useNavigate();
  const { confirmLeave } = useLeaveGuard();

  const signOut = async () => {
    if (!(await confirmLeave())) return; // alterações não salvas: perguntar antes de encerrar a sessão
    await logout.mutateAsync().catch(() => undefined); // mesmo sem resposta, a sessão local é descartada
    navigate('/login', { replace: true });
  };

  return (
    <div className="flex h-full flex-col bg-navy-900 px-4 pb-6 pt-6">
      <div className="flex items-center justify-between px-3">
        <Brand tone="dark" className="text-[28px]" />
        {onClose && (
          <button type="button" onClick={onClose} aria-label="Fechar menu" className="grid size-11 place-items-center rounded-lg text-glow hover:text-white">
            <X className="size-6" aria-hidden />
          </button>
        )}
      </div>

      <nav aria-label="Principal" className="mt-8 flex flex-1 flex-col">
        <ul className="space-y-1">
          {mainItems.map((item) => (
            <li key={item.to}>
              <NavItem item={item} onNavigate={onClose} />
            </li>
          ))}
        </ul>

        <div className="mt-auto space-y-1">
          {user && canModerate(user.role) && (
            <div className="space-y-1 border-t border-white/10 pt-4">
              <NavItem item={{ to: '/moderacao', label: 'Moderação', icon: ShieldCheck }} onNavigate={onClose} />
              {isAdmin(user.role) && (
                // o painel é renderizado pelo Laravel, fora do React Router
                <a href="/admin" className={itemClass}>
                  <LayoutDashboard className="size-5" aria-hidden />
                  Painel administrativo
                </a>
              )}
            </div>
          )}
          <div className="space-y-1 border-t border-white/10 pt-4">
            <NavItem item={{ to: '/configuracoes', label: 'Configurações', icon: Settings }} onNavigate={onClose} />
            <button type="button" onClick={signOut} disabled={logout.isPending} className={cn(itemClass, 'w-full')}>
              <LogOut className="size-5" aria-hidden />
              Sair
            </button>
          </div>
        </div>
      </nav>
    </div>
  );
}
