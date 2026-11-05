import { ArrowLeft, ArrowUpRight } from 'lucide-react';
import { Link, useLocation, useNavigate, useParams } from 'react-router';
import { Chip } from '@/components/Chip';
import { FormAlert } from '@/components/FormAlert';
import { Skeleton } from '@/components/Skeleton';
import { ProfileSection, VisibilityNote } from '@/features/profile/ProfileSection';
import { ApiError } from '@/lib/api/errors';
import { useStudentProfile } from './hooks';

function Tags({ items }: { items: string[] }) {
  return (
    <div className="flex flex-wrap gap-2 [&>span]:max-w-full [&>span]:[overflow-wrap:anywhere]">
      {items.map((item) => (
        <Chip key={item}>{item}</Chip>
      ))}
    </div>
  );
}

function ProfileLink({ label, url }: { label: string; url: string }) {
  return (
    <a href={url} target="_blank" rel="noopener noreferrer" className="inline-flex items-center gap-1 font-semibold text-action hover:underline">
      {label}
      <ArrowUpRight className="size-4" aria-hidden />
      <span className="sr-only">(abre em nova guia)</span>
    </a>
  );
}

export function StudentProfilePage() {
  const { code = '' } = useParams();
  const student = useStudentProfile(code);
  const profile = student.data;
  const navigate = useNavigate();
  const location = useLocation();
  const notFound = student.error instanceof ApiError && student.error.status === 404;

  return (
    <div>
      {notFound ? (
        <section className="rounded-2xl border border-blue-200/70 bg-white p-7">
          <h1 className="font-display text-2xl font-bold text-foreground">Este perfil não está disponível.</h1>
          <Link to="/estudos" className="mt-4 inline-block font-semibold text-action hover:underline">
            Voltar para Estudos
          </Link>
        </section>
      ) : (
        <>
          <button
            type="button"
            // "default" é a primeira página da sessão: o perfil foi aberto direto pelo endereço
            onClick={() => (location.key === 'default' ? navigate('/estudos') : navigate(-1))}
            className="inline-flex min-h-11 items-center gap-2 font-semibold text-action hover:underline"
          >
            <ArrowLeft className="size-4" aria-hidden />
            Voltar
          </button>

          <div className="mt-4 space-y-6">
            {student.isPending && (
              <div className="space-y-6" aria-busy="true">
                <p className="text-base text-muted-foreground">Carregando perfil…</p>
                <Skeleton className="h-36 rounded-2xl" />
                <Skeleton className="h-32 rounded-2xl" />
              </div>
            )}

            {student.isError && (
              <FormAlert
                variant="warning"
                action={
                  <button type="button" onClick={() => student.refetch()} className="font-semibold underline underline-offset-2">
                    Tentar novamente
                  </button>
                }
              >
                Não foi possível carregar este perfil agora.
              </FormAlert>
            )}

            {profile && (
              <>
                <header className="flex flex-col gap-5 rounded-2xl border border-blue-200/70 bg-white p-6 sm:flex-row sm:items-center sm:p-7">
                  <span aria-hidden className="grid size-20 shrink-0 place-items-center rounded-full bg-[#d6e4f7] font-display text-3xl font-bold text-navy-900">
                    {profile.initials || '?'}
                  </span>
                  <div className="min-w-0">
                    <h1 className="font-display text-3xl font-bold [overflow-wrap:anywhere] text-foreground">{profile.display_name}</h1>
                    <p className="mt-1 text-muted-foreground">Ciência da Computação</p>
                    {(profile.github_url || profile.linkedin_url) && (
                      <div className="mt-3 flex flex-wrap gap-4">
                        {profile.github_url && <ProfileLink label="GitHub" url={profile.github_url} />}
                        {profile.linkedin_url && <ProfileLink label="LinkedIn" url={profile.linkedin_url} />}
                      </div>
                    )}
                  </div>
                </header>

                {profile.about && (
                  <ProfileSection title="Sobre">
                    <p className="text-[17px] [overflow-wrap:anywhere] whitespace-pre-line text-foreground">{profile.about}</p>
                  </ProfileSection>
                )}

                {profile.disciplines.length > 0 && (
                  <ProfileSection title="Disciplinas atuais">
                    <Tags items={profile.disciplines.map((discipline) => discipline.name)} />
                  </ProfileSection>
                )}

                {profile.interests.length > 0 && (
                  <ProfileSection title="Interesses">
                    <Tags items={profile.interests} />
                  </ProfileSection>
                )}

                {(profile.can_help_with.length > 0 || profile.needs_help_with.length > 0) && (
                  <ProfileSection title="Apoio entre estudantes">
                    <div className="space-y-5">
                      {profile.can_help_with.length > 0 && (
                        <div>
                          <h3 className="mb-2 text-sm text-muted-foreground">Pode ajudar com</h3>
                          <Tags items={profile.can_help_with} />
                        </div>
                      )}
                      {profile.needs_help_with.length > 0 && (
                        <div>
                          <h3 className="mb-2 text-sm text-muted-foreground">Procura ajuda com</h3>
                          <Tags items={profile.needs_help_with} />
                        </div>
                      )}
                    </div>
                  </ProfileSection>
                )}

                <VisibilityNote>Perfil visível apenas para estudantes autenticados no ConectaCC.</VisibilityNote>
              </>
            )}
          </div>
        </>
      )}
    </div>
  );
}
