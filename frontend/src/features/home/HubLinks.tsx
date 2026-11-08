import { BookOpen, Briefcase, User as UserIcon, Users, type LucideIcon } from 'lucide-react';
import { Link } from 'react-router';
import { cn } from '@/lib/utils';

type Hub = { to: string; label: string; icon: LucideIcon };

const HUBS: Hub[] = [
  { to: '/estudos', label: 'Estudos', icon: BookOpen },
  { to: '/perfil', label: 'Perfil', icon: UserIcon },
  { to: '/comunidade', label: 'Comunidade', icon: Users },
  { to: '/oportunidade', label: 'Oportunidades', icon: Briefcase },
];

// as quatro áreas em volta da comunidade: no lugar do antigo "Acesso rápido", cada uma é um link
// compact: ao lado do card "Complete seu perfil" não há espaço para o círculo
export function HubLinks({ compact = false }: { compact?: boolean }) {
  return (
    <nav aria-label="Áreas do ConectaCC" className={cn('relative w-full shrink-0', !compact && 'xl:w-[25rem]')}>
      {/* círculo tracejado e centro: só decoração, a partir do computador */}
      <div aria-hidden className={cn('pointer-events-none absolute inset-0 hidden place-items-center', !compact && 'xl:grid')}>
        <svg viewBox="0 0 220 220" className="size-56 text-action/60">
          <circle cx="110" cy="110" r="96" fill="none" stroke="currentColor" strokeWidth="1.5" strokeDasharray="5 6" />
          <circle cx="110" cy="14" r="5" fill="currentColor" />
          <circle cx="14" cy="110" r="4" fill="white" stroke="currentColor" strokeWidth="2" />
          <circle cx="206" cy="110" r="4" fill="white" stroke="currentColor" strokeWidth="2" />
          <circle cx="110" cy="206" r="4" fill="white" stroke="currentColor" strokeWidth="2" />
        </svg>
        <span className="absolute grid size-28 place-items-center rounded-full bg-gradient-to-b from-white to-[#dbe7f8] text-action shadow-md">
          <Users className="size-12" />
        </span>
      </div>
      <ul className={cn('relative grid grid-cols-1 gap-3 min-[400px]:grid-cols-2', !compact && 'xl:grid-cols-[max-content_max-content] xl:justify-between xl:gap-y-24')}>
        {HUBS.map(({ to, label, icon: Icon }) => (
          <li key={to}>
            <Link
              to={to}
              className="flex h-14 items-center gap-3 rounded-2xl border border-blue-100 bg-white px-3 pr-4 font-semibold whitespace-nowrap text-foreground shadow-sm transition-shadow hover:shadow-md"
            >
              <span aria-hidden className="grid size-9 shrink-0 place-items-center rounded-xl bg-[#e3eefb] text-action">
                <Icon className="size-5" />
              </span>
              {label}
            </Link>
          </li>
        ))}
      </ul>
    </nav>
  );
}
