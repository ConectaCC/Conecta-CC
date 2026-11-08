import { ArrowRight, BookOpen } from 'lucide-react';
import { Link } from 'react-router';
import { Chip } from '@/components/Chip';
import type { Discipline } from '@/features/profile/hooks';

export function DisciplineCard({ discipline, mine }: { discipline: Discipline; mine: boolean }) {
  return (
    <Link
      to={`/estudos/${discipline.slug}`}
      className="flex h-full flex-wrap items-center gap-x-4 gap-y-3 rounded-2xl border border-blue-200/70 bg-white p-5 transition-colors hover:border-blue-300 hover:bg-[#f5f9fe]"
    >
      <span aria-hidden className="grid size-14 shrink-0 place-items-center rounded-2xl bg-[#e3eefb] text-action">
        <BookOpen className="size-7" />
      </span>
      <span className="min-w-0 flex-[1_1_10rem]">
        <span className="block font-display text-lg leading-snug font-bold break-words text-foreground">{discipline.name}</span>
        {mine && (
          <span className="mt-1.5 inline-block whitespace-nowrap">
            <Chip>Minha disciplina</Chip>
          </span>
        )}
      </span>
      <span className="ml-auto inline-flex items-center gap-2 font-semibold text-action">
        Ver disciplina
        <ArrowRight className="size-4" aria-hidden />
      </span>
    </Link>
  );
}
