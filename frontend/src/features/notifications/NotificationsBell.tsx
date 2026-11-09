import { Bell, Check, X } from 'lucide-react';
import { useEffect, useId, useRef, useState } from 'react';
import { useLocation, useNavigate } from 'react-router';
import { FormAlert } from '@/components/FormAlert';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { timeAgo } from '@/lib/format';
import { cn } from '@/lib/utils';
import { type AppNotification, useNotifications, useReadAllNotifications, useReadNotification } from './hooks';

export function NotificationsBell() {
  const notifications = useNotifications();
  const read = useReadNotification();
  const readAll = useReadAllNotifications();
  const [open, setOpen] = useState(false);
  const navigate = useNavigate();
  const { pathname } = useLocation();
  const titleId = useId();
  const titleRef = useRef<HTMLHeadingElement>(null);
  const { refetch } = notifications;

  // além do polling, cada troca de página traz o contador atualizado
  useEffect(() => {
    void refetch();
  }, [pathname, refetch]);

  const items = notifications.data?.data ?? [];
  const unread = notifications.data?.unread_count ?? 0;
  const label = unread === 0 ? 'Notificações' : `Notificações, ${unread} ${unread === 1 ? 'não lida' : 'não lidas'}`;

  function openItem(item: AppNotification) {
    setOpen(false);
    if (!item.read) read.mutate(item.id);
    void navigate(item.link);
  }

  return (
    <Popover open={open} onOpenChange={setOpen}>
      <PopoverTrigger asChild>
        <button
          type="button"
          aria-label={label}
          className={cn('relative grid size-11 place-items-center rounded-lg text-navy-900 hover:bg-ice', open && 'bg-ice')}
        >
          <Bell className="size-6" aria-hidden />
          {unread > 0 && (
            <span aria-hidden className="absolute top-1 right-1 grid min-w-5 place-items-center rounded-full bg-badge px-1 text-xs font-bold text-white">
              {unread > 9 ? '9+' : unread}
            </span>
          )}
        </button>
      </PopoverTrigger>
      <PopoverContent align="end" aria-labelledby={titleId} className="w-[min(380px,calc(100vw-1rem))] gap-0 p-0">
        <div className="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
          <h2 id={titleId} ref={titleRef} tabIndex={-1} className="text-base font-semibold text-foreground outline-none">
            Notificações
          </h2>
          {unread > 0 && (
            <button
              type="button"
              // o botão some quando não há mais não lidas: o foco vai para o título do painel
              onClick={() => readAll.mutate(undefined, { onSuccess: () => titleRef.current?.focus() })}
              className="relative text-sm font-semibold text-action after:absolute after:-inset-x-2 after:-inset-y-3 hover:underline">
              Marcar todas como lidas
            </button>
          )}
        </div>

        <div className="max-h-[min(28rem,70dvh)] overflow-y-auto">
          {notifications.isPending && <p className="px-4 py-6 text-[15px] text-muted-foreground">Carregando notificações…</p>}

          {notifications.isError && (
            <div className="p-3">
              <FormAlert
                variant="warning"
                action={
                  <button type="button" onClick={() => refetch()} className="font-semibold underline underline-offset-2">
                    Tentar novamente
                  </button>
                }
              >
                Não foi possível carregar as notificações agora.
              </FormAlert>
            </div>
          )}

          {notifications.data && items.length === 0 && (
            <div className="px-4 py-6">
              <p className="text-sm font-semibold text-foreground">Você ainda não tem notificações.</p>
              <p className="mt-1 text-sm text-muted-foreground">Quando algo que você enviar for analisado, o resultado aparece aqui.</p>
            </div>
          )}

          {items.length > 0 && (
            <ul>
              {items.map((item) => (
                <li key={item.id} className="border-b border-slate-100 last:border-b-0">
                  <button
                    type="button"
                    onClick={() => openItem(item)}
                    className={cn('flex w-full items-start gap-3 px-4 py-3 text-left', item.read ? 'hover:bg-ice/70' : 'bg-unread hover:bg-unread/80')}
                  >
                    <span aria-hidden className="mt-0.5 grid size-7 shrink-0 place-items-center rounded-full bg-ice text-navy-900">
                      {item.ok ? <Check className="size-4" /> : <X className="size-4" />}
                    </span>
                    <span className="min-w-0 flex-1">
                      <span className={cn('block text-sm text-foreground', !item.read && 'font-semibold')}>{item.title}</span>
                      <span className="mt-0.5 block text-sm [overflow-wrap:anywhere] text-muted-foreground">{item.detail}</span>
                      <span className="mt-1 flex items-center gap-2 text-xs text-muted-foreground">
                        {timeAgo(item.created_at)}
                        {!item.read && <span className="rounded-full bg-action px-2 py-0.5 font-semibold text-white">Nova</span>}
                      </span>
                    </span>
                  </button>
                </li>
              ))}
            </ul>
          )}
        </div>
      </PopoverContent>
    </Popover>
  );
}
