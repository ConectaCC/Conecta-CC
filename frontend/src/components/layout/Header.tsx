import { Menu } from 'lucide-react';
import { Brand } from '@/components/Brand';
import { Skeleton } from '@/components/Skeleton';
import type { User } from '@/features/auth/hooks';
import { NotificationsBell } from '@/features/notifications/NotificationsBell';
import { UserAvatar } from './UserAvatar';

export function Header({ user, loading, onOpenMenu }: { user?: User | null; loading: boolean; onOpenMenu: () => void }) {
  return (
    <header className="sticky top-0 z-10 flex h-[72px] items-center gap-3 border-b border-slate-200 bg-white px-4 sm:px-8 lg:h-[88px] lg:justify-end lg:px-11">
      <button type="button" onClick={onOpenMenu} aria-label="Abrir menu" className="grid size-11 place-items-center rounded-lg text-navy-900 lg:hidden">
        <Menu className="size-7" aria-hidden />
      </button>
      <Brand tone="light" className="text-2xl lg:hidden" />

      <div className="ml-auto flex items-center gap-5 lg:ml-0">
        <NotificationsBell />
        {loading && (
          <>
            <Skeleton className="size-11 rounded-full" />
            <Skeleton className="hidden h-4 w-48 rounded-full lg:block" />
          </>
        )}
        {user && (
          <div className="flex items-center gap-3">
            <UserAvatar initials={user.initials} />
            <span className="hidden text-[17px] font-medium text-foreground lg:inline">{user.display_name}</span>
            <span className="sr-only lg:hidden">{user.display_name}</span>
          </div>
        )}
      </div>
    </header>
  );
}
