import { Hand } from 'lucide-react';
import { Skeleton } from '@/components/Skeleton';
import { useMe } from '@/features/auth/hooks';
import { cn } from '@/lib/utils';
import { CompleteProfileCard } from './CompleteProfileCard';
import { HubLinks } from './HubLinks';
import { InstitutionalLinks } from './InstitutionalLinks';

export function HomePage() {
  const me = useMe();
  const firstName = me.data?.display_name.trim().split(/\s+/)[0];
  // só depois do /me carregar, para o card não piscar (decisoes.md §8)
  const showCompleteProfile = me.data ? !me.data.profile_complete : false;

  return (
    <>
      {me.isPending ? (
        <div className="space-y-4" aria-busy="true">
          <Skeleton className="h-10 w-72" />
          <Skeleton className="h-4 w-1/2 rounded-full" />
        </div>
      ) : (
        <header>
          <h1 className="font-display text-4xl font-bold text-foreground lg:text-5xl">
            {firstName ? `Olá, ${firstName}!` : 'Olá!'}
            <Hand className="ml-3 inline size-10 -rotate-12 align-[-0.15em] text-action" aria-hidden />
          </h1>
          <p className="mt-3 text-lg text-muted-foreground">Boas-vindas ao ConectaCC.</p>
        </header>
      )}

      <div className={cn('mt-10 grid gap-6', showCompleteProfile && 'xl:grid-cols-[minmax(0,1fr)_340px]')}>
        {/* com o card "Complete seu perfil" ao lado não sobra largura para o texto e os atalhos lado a lado: ficam em coluna */}
        <section
          className={cn(
            'flex flex-col justify-between gap-8 rounded-2xl border border-blue-200/70 bg-gradient-to-r from-white to-[#d9e7f8] p-6 sm:p-8 lg:p-11',
            !showCompleteProfile && 'xl:flex-row xl:items-center',
          )}
        >
          <div className="max-w-2xl min-w-0 flex-1">
            <h2 className="font-display text-2xl font-bold leading-snug text-foreground lg:text-3xl">
              Conecte-se, aprenda e cresça junto com a comunidade.
            </h2>
            <p className="mt-4 text-lg leading-relaxed text-muted-foreground">
              Um espaço para compartilhar conhecimento, descobrir oportunidades e criar conexões entre estudantes de Ciência da
              Computação do IFSul – Câmpus Passo Fundo.
            </p>
          </div>
          <HubLinks compact={showCompleteProfile} />
        </section>
        {showCompleteProfile && <CompleteProfileCard />}
      </div>

      <InstitutionalLinks />
    </>
  );
}
