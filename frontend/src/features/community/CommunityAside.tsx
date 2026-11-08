import { BarChart3, Briefcase, ChevronDown, FileText, Plus, Users } from 'lucide-react';
import { useState } from 'react';
import { useNavigate } from 'react-router';
import { Skeleton } from '@/components/Skeleton';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { AddOpportunityDialog } from '@/features/opportunities/AddOpportunityDialog';
import { useCommunityStats } from './hooks';

const number = new Intl.NumberFormat('pt-BR');

function Stat({ icon: Icon, value, label }: { icon: typeof FileText; value: number; label: string }) {
  return (
    <div className="flex min-w-0 flex-col items-center gap-1 rounded-xl border border-blue-200/70 px-1 py-4 text-center">
      <span aria-hidden className="grid size-10 place-items-center rounded-full bg-[#e3eefb] text-action">
        <Icon className="size-5" />
      </span>
      <span className="mt-1 font-display text-2xl font-bold text-foreground">{number.format(value)}</span>
      <span className="max-w-full text-xs [overflow-wrap:anywhere] text-muted-foreground sm:text-sm">{label}</span>
    </div>
  );
}

export function CommunityAside() {
  const stats = useCommunityStats();
  const navigate = useNavigate();
  const [addingOpportunity, setAddingOpportunity] = useState(false);

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
            <p className="mt-1 text-[15px] text-muted-foreground">Compartilhe materiais e oportunidades para ajudar outros estudantes.</p>
          </div>
        </div>
        {/* um botão para os dois tipos (decisoes.md §69.2); o material segue indo para Estudos, onde se escolhe a disciplina */}
        <DropdownMenu modal={false}>
          <DropdownMenuTrigger asChild>
            <Button type="button" className="mt-5 h-11 w-full gap-2 text-[15px] font-semibold">
              <Plus className="size-5" aria-hidden />
              Compartilhar conteúdo
              <ChevronDown className="size-4" aria-hidden />
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end" className="min-w-56">
            <DropdownMenuItem className="gap-3 py-2.5 text-[15px]" onSelect={() => navigate('/estudos')}>
              <FileText className="size-5" aria-hidden />
              Material
            </DropdownMenuItem>
            <DropdownMenuItem className="gap-3 py-2.5 text-[15px]" onSelect={() => setAddingOpportunity(true)}>
              <Briefcase className="size-5" aria-hidden />
              Oportunidade
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </section>

      <section aria-labelledby="numeros" className="rounded-2xl border border-blue-200/70 bg-white p-6">
        <h2 id="numeros" className="flex items-center gap-2 font-display text-lg font-bold text-foreground">
          <BarChart3 className="size-5 text-action" aria-hidden />A comunidade em números
        </h2>
        {stats.isPending && (
          <div className="mt-4 grid grid-cols-3 gap-2" aria-busy="true">
            <Skeleton className="h-32 rounded-xl" />
            <Skeleton className="h-32 rounded-xl" />
            <Skeleton className="h-32 rounded-xl" />
          </div>
        )}
        {stats.isError && <p className="mt-3 text-[15px] text-muted-foreground">Não foi possível carregar os números agora.</p>}
        {stats.data && (
          <div className="mt-4 grid grid-cols-3 gap-2">
            <Stat icon={FileText} value={stats.data.materials} label="Materiais" />
            <Stat icon={Briefcase} value={stats.data.opportunities} label="Oportunidades" />
            <Stat icon={Users} value={stats.data.students} label="Estudantes" />
          </div>
        )}
      </section>
      <AddOpportunityDialog open={addingOpportunity} onOpenChange={setAddingOpportunity} backLabel="Voltar para a Comunidade" />
    </aside>
  );
}
