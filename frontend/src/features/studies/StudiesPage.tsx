import { BookOpen, CalendarDays, ChevronDown, Search } from 'lucide-react';
import { useId, useState } from 'react';
import { Link } from 'react-router';
import { Button } from '@/components/ui/button';
import type { Discipline } from '@/features/profile/hooks';
import { AcademicResources } from './AcademicResources';
import { DisciplineCard } from './DisciplineCard';
import { normalizeSearch } from './normalize';
import { StudiesError, StudiesLoading } from './StudiesStatus';
import { useStudies } from './useStudies';

const SEMESTERS = [1, 2, 3, 4, 5, 6, 7, 8];
const GRID = 'grid gap-4 md:grid-cols-2';

export function StudiesPage() {
  const { disciplines, mineIds, ready, failed, retry } = useStudies();
  const [query, setQuery] = useState('');

  const term = normalizeSearch(query);
  const found = (disciplines ?? []).filter((discipline) => normalizeSearch(discipline.name).includes(term));
  const mine = (disciplines ?? []).filter((discipline) => mineIds.has(discipline.id));

  return (
    <div>
      <header className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#e3eefb] via-[#edf4fc] to-[#f6f9fe] px-6 py-7 sm:px-8">
        {/* onda decorativa, como na referência visual */}
        <svg aria-hidden viewBox="0 0 1200 120" preserveAspectRatio="none" className="pointer-events-none absolute inset-x-0 bottom-0 h-16 w-full text-white/60">
          <path d="M0 70 C 200 20, 380 120, 600 70 S 1000 20, 1200 60 L 1200 120 L 0 120 Z" fill="currentColor" />
        </svg>
        <div className="relative grid items-center gap-6 xl:grid-cols-[minmax(0,1fr)_auto_auto]">
          <div className="flex items-center gap-5">
            <span aria-hidden className="hidden size-20 shrink-0 place-items-center rounded-2xl bg-[#d6e4f7] text-action sm:grid">
              <BookOpen className="size-10" />
            </span>
            <div className="min-w-0">
              <h1 className="font-display text-4xl font-bold text-foreground">Estudos</h1>
              <p className="mt-2 text-lg text-muted-foreground">Acesse suas disciplinas e materiais compartilhados pela comunidade.</p>
            </div>
          </div>
          <span aria-hidden className="hidden h-20 w-px bg-blue-200/70 xl:block" />
          <AcademicResources />
        </div>
      </header>

      <div className="mt-8 space-y-10">
        {failed && <StudiesError onRetry={retry} />}
        {!ready && !failed && <StudiesLoading />}

        {ready && (
          <>
            <section aria-labelledby="minhas-disciplinas">
              <div className="flex flex-wrap items-baseline justify-between gap-3">
                <h2 id="minhas-disciplinas" className="font-display text-2xl font-bold text-foreground">
                  Minhas disciplinas
                </h2>
                <Link to="/perfil?editar=disciplinas" className="font-semibold text-action hover:underline">
                  Alterar no Perfil
                </Link>
              </div>
              {mine.length > 0 ? (
                <ul className={`mt-5 ${GRID}`}>
                  {mine.map((discipline) => (
                    <li key={discipline.id}>
                      <DisciplineCard discipline={discipline} mine />
                    </li>
                  ))}
                </ul>
              ) : (
                <div className="mt-5 rounded-2xl border border-blue-200/70 bg-white p-7">
                  <h3 className="font-display text-xl font-bold text-foreground">Você ainda não selecionou suas disciplinas</h3>
                  <p className="mt-2 text-base text-muted-foreground">
                    Adicione as disciplinas que está cursando para encontrá-las rapidamente aqui.
                  </p>
                  <Button asChild className="mt-5 h-12 px-5 text-base font-semibold">
                    <Link to="/perfil?editar=disciplinas">Completar perfil</Link>
                  </Button>
                </div>
              )}
            </section>

            <section aria-labelledby="todas-disciplinas">
              <h2 id="todas-disciplinas" className="font-display text-2xl font-bold text-foreground">
                Todas as disciplinas
              </h2>
              <div className="mt-5 flex flex-wrap items-center gap-3">
                <div className="relative min-w-0 flex-[1_1_16rem]">
                  <label htmlFor="busca-disciplina" className="sr-only">
                    Buscar disciplina
                  </label>
                  <Search className="pointer-events-none absolute top-1/2 left-3.5 size-5 -translate-y-1/2 text-muted-foreground" aria-hidden />
                  <input
                    id="busca-disciplina"
                    type="search"
                    value={query}
                    onChange={(event) => setQuery(event.target.value)}
                    placeholder="Buscar disciplina…"
                    className="h-12 w-full rounded-lg border border-input bg-white pr-3 pl-11 text-base text-foreground placeholder:text-muted-foreground"
                  />
                </div>
                <p aria-live="polite" className="rounded-full bg-[#dbe7f8] px-4 py-2 text-[15px] font-semibold text-navy-900">
                  {found.length === 1 ? '1 disciplina' : `${found.length} disciplinas`}
                </p>
              </div>

              {found.length === 0 ? (
                <p className="mt-4 text-base text-foreground">Nenhuma disciplina encontrada para “{query.trim()}”.</p>
              ) : (
                <div className="mt-6 space-y-3">
                  {SEMESTERS.map((semester) => {
                    const group = found.filter((discipline) => discipline.semester === semester);
                    if (group.length === 0) return null;
                    // a key muda ao entrar e sair da busca: os grupos reabrem com o resultado e fecham ao limpar
                    return (
                      <SemesterGroup
                        key={`${semester}-${term ? 'busca' : 'lista'}`}
                        semester={semester}
                        disciplines={group}
                        mineIds={mineIds}
                        defaultOpen={term !== ''}
                      />
                    );
                  })}
                </div>
              )}
            </section>
          </>
        )}
      </div>
    </div>
  );
}

type SemesterGroupProps = { semester: number; disciplines: Discipline[]; mineIds: Set<number>; defaultOpen: boolean };

function SemesterGroup({ semester, disciplines, mineIds, defaultOpen }: SemesterGroupProps) {
  const [open, setOpen] = useState(defaultOpen);
  const panelId = useId();

  return (
    <div className="rounded-2xl border border-blue-200/70 bg-white">
      <h3 className="font-display text-xl font-bold text-foreground">
        <button
          type="button"
          aria-expanded={open}
          aria-controls={panelId}
          onClick={() => setOpen((current) => !current)}
          className="flex min-h-14 w-full items-center justify-between gap-3 rounded-2xl px-6 py-4 text-left hover:bg-[#f5f9fe]"
        >
          <span className="flex items-center gap-4">
            <CalendarDays className="size-5 shrink-0 text-action" aria-hidden />
            {semester}º semestre
          </span>
          <ChevronDown className={`size-5 shrink-0 transition-transform ${open ? 'rotate-180' : ''}`} aria-hidden />
        </button>
      </h3>
      {open && (
        <ul id={panelId} className={`px-6 pb-6 ${GRID}`}>
          {disciplines.map((discipline) => (
            <li key={discipline.id}>
              <DisciplineCard discipline={discipline} mine={mineIds.has(discipline.id)} />
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}
