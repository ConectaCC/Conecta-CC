import { ExternalLink } from 'lucide-react';
import { Link, useParams } from 'react-router';
import { Chip } from '@/components/Chip';
import { MaterialsSection } from '@/features/materials/MaterialsSection';
import { hostOf } from '@/lib/format';
import { StudiesError, StudiesLoading } from './StudiesStatus';
import { useStudies } from './useStudies';

const CARD = 'rounded-2xl border border-blue-200/70 bg-white p-7';

export function DisciplinePage() {
  const { slug } = useParams();
  const { disciplines, mineIds, ready, failed, retry } = useStudies();

  const discipline = disciplines?.find((item) => item.slug === slug?.toLowerCase());
  const host = discipline?.syllabus_url ? hostOf(discipline.syllabus_url) : '';

  return (
    <div>
      <nav aria-label="Trilha" className="text-[15px] text-muted-foreground">
        <Link to="/estudos" className="font-semibold text-action hover:underline">
          Estudos
        </Link>
        {discipline && (
          <>
            <span aria-hidden className="mx-2">
              /
            </span>
            <span aria-current="page">{discipline.name}</span>
          </>
        )}
      </nav>

      {failed && (
        <div className="mt-4">
          <StudiesError onRetry={retry} />
        </div>
      )}
      {!ready && !failed && (
        <div className="mt-4">
          <StudiesLoading />
        </div>
      )}

      {ready && !discipline && (
        <div className="mt-4">
          <h1 className="font-display text-4xl font-bold text-foreground">Disciplina não encontrada</h1>
          <Link to="/estudos" className="mt-4 inline-block font-semibold text-action hover:underline">
            Voltar para Estudos
          </Link>
        </div>
      )}

      {ready && discipline && (
        <>
          <header className="mt-4 flex flex-wrap items-center gap-3">
            {/* overflow-wrap:anywhere evita rolagem lateral com "Empreendedorismo" em telas de 320px */}
            <h1 className="min-w-0 font-display text-3xl font-bold [overflow-wrap:anywhere] text-foreground sm:text-4xl">
              {discipline.name}
            </h1>
            {mineIds.has(discipline.id) && <Chip>Minha disciplina</Chip>}
          </header>

          <div className="mt-8 space-y-6">
            <section aria-labelledby="info-disciplina" className={CARD}>
              <h2 id="info-disciplina" className="font-display text-2xl font-bold text-foreground">
                Informações da disciplina
              </h2>
              <p className="mt-3 text-base font-semibold text-foreground">
                {discipline.semester}º semestre · {discipline.workload_hours} h
              </p>
              <p className="mt-2 text-base text-muted-foreground">Consulte a ementa e as informações oficiais na fonte institucional.</p>
              {discipline.syllabus_url ? (
                <a
                  href={discipline.syllabus_url}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="mt-4 inline-flex items-center gap-2 font-semibold text-action hover:underline"
                >
                  Ver ementa oficial
                  <ExternalLink className="size-4" aria-hidden />
                  <span className="sr-only">(abre em nova guia, {host})</span>
                </a>
              ) : (
                <p className="mt-4 text-base text-muted-foreground">Link oficial a confirmar</p>
              )}
            </section>

            <MaterialsSection discipline={discipline} />
          </div>
        </>
      )}
    </div>
  );
}
