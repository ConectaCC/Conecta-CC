import { BarChart3, FileText, Plus, Users } from 'lucide-react';
import { Link } from 'react-router';
import { Skeleton } from '@/components/Skeleton';
import { buttonVariants } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { useCommunityStats } from './hooks';

const number = new Intl.NumberFormat('pt-BR');

function Stat({ icon: Icon, value, label }: { icon: typeof FileText; value: number; label: string }) {
  return (
    <div className="flex flex-col items-center gap-1 rounded-xl border border-blue-200/70 px-3 py-4 text-center">
      <span aria-hidden className="grid size-10 place-items-center rounded-full bg-[#e3eefb] text-action">
        <Icon className="size-5" />
      </span>
      <span className="mt-1 font-display text-2xl font-bold text-foreground">{number.format(value)}</span>
      <span className="text-sm text-muted-foreground">{label}</span>
    </div>
  );
}

export function CommunityAside() {
  const stats = useCommunityStats();

  return (
    <aside className="space-y-6">
      <section aria-labelledby="contribua" className="rounded-2xl border border-blue-200/70 bg-white p-6">
        <div className="flex gap-4">
          <span aria-hidden className="grid size-12 shrink-0 place-items-center rounded-xl bg-[#e3eefb] text-action">
            <Users className="size-6" />
          </span>
          <div>
            <h2 id="contribua" className="font-display text-lg font-bold text-foreground">
              Contribua com a comunidade
            </h2>
            <p className="mt-1 text-[15px] text-muted-foreground">Compartilhe materiais para ajudar outros estudantes.</p>
          </div>
        </div>
        {/* o material é enviado dentro da disciplina: o botão leva a Estudos para escolher qual */}
        <Link to="/estudos" className={cn(buttonVariants(), 'mt-5 h-11 w-full gap-2 text-[15px] font-semibold')}>
          <Plus className="size-5" aria-hidden />
          Compartilhar material
        </Link>
      </section>

      <section aria-labelledby="numeros" className="rounded-2xl border border-blue-200/70 bg-white p-6">
        <h2 id="numeros" className="flex items-center gap-2 font-display text-lg font-bold text-foreground">
          <BarChart3 className="size-5 text-action" aria-hidden />A comunidade em números
        </h2>
        {stats.isPending && (
          <div className="mt-4 grid grid-cols-2 gap-3" aria-busy="true">
            <Skeleton className="h-32 rounded-xl" />
            <Skeleton className="h-32 rounded-xl" />
          </div>
        )}
        {stats.isError && <p className="mt-3 text-[15px] text-muted-foreground">Não foi possível carregar os números agora.</p>}
        {stats.data && (
          <div className="mt-4 grid grid-cols-2 gap-3">
            <Stat icon={FileText} value={stats.data.materials} label="Materiais" />
            <Stat icon={Users} value={stats.data.students} label="Estudantes" />
          </div>
        )}
      </section>
    </aside>
  );
}
