import { ArrowUpRight, Info } from 'lucide-react';
import type { ReactNode } from 'react';
import { Link } from 'react-router';
import { Chip } from '@/components/Chip';
import { Button } from '@/components/ui/button';
import type { Profile } from './hooks';
import { ProfileSection, VisibilityNote } from './ProfileSection';
import { ProfileSummaryCard } from './ProfileSummaryCard';

function Field({ label, children }: { label: string; children: ReactNode }) {
  return (
    <div>
      <p className="text-sm text-muted-foreground">{label}</p>
      <div className="mt-1.5 text-[17px] text-foreground">{children}</div>
    </div>
  );
}

const empty = <span className="text-muted-foreground">Não informado.</span>;

function Tags({ items }: { items: string[] }) {
  if (items.length === 0) return empty;
  return (
    <div className="flex flex-wrap gap-2">
      {items.map((item) => (
        <Chip key={item}>{item}</Chip>
      ))}
    </div>
  );
}

function ExternalLink({ url }: { url: string | null }) {
  if (!url) return <span className="text-muted-foreground">Não informado</span>;
  return (
    <a href={url} target="_blank" rel="noopener noreferrer" className="inline-flex items-center gap-1 font-semibold text-action hover:underline">
      {url.replace(/^https:\/\/(www\.)?/, '')}
      <ArrowUpRight className="size-4" aria-hidden />
      <span className="sr-only">(abre em nova guia)</span>
    </a>
  );
}

export function ProfileView({ profile }: { profile: Profile }) {
  return (
    <div className="space-y-6">
      {!profile.complete && (
        <section className="flex flex-col gap-4 rounded-2xl border border-blue-300 bg-white p-5 sm:flex-row sm:items-center sm:p-6">
          <span className="grid size-11 shrink-0 place-items-center rounded-full bg-[#d6e4f7] text-navy-900">
            <Info className="size-5" aria-hidden />
          </span>
          <div className="flex-1">
            <h2 className="font-semibold text-navy-900">Perfil incompleto</h2>
            <p className="mt-0.5 text-muted-foreground">Selecione pelo menos uma disciplina atual para completar seu perfil.</p>
          </div>
          <Button asChild variant="outline" className="h-11 self-start border-action px-5 text-[15px] font-semibold text-action sm:self-center">
            <Link to="/perfil?editar=disciplinas" replace>Selecionar disciplinas</Link>
          </Button>
        </section>
      )}

      <ProfileSummaryCard displayName={profile.display_name} />

      <ProfileSection title="Identidade">
        <div className="grid gap-5 sm:grid-cols-3">
          <Field label="Nome de exibição">{profile.display_name}</Field>
          <Field label="Curso">Ciência da Computação</Field>
        </div>
      </ProfileSection>

      <ProfileSection title="Minha jornada acadêmica">
        <p className="text-sm text-muted-foreground">Disciplinas atuais</p>
        <VisibilityNote className="mt-1.5">Visíveis para outros estudantes autenticados no ConectaCC.</VisibilityNote>
        <div className="mt-3">
          {profile.disciplines.length === 0 ? (
            <p className="text-muted-foreground">Nenhuma disciplina atual selecionada.</p>
          ) : (
            <Tags items={profile.disciplines.map((discipline) => discipline.name)} />
          )}
        </div>
      </ProfileSection>

      <ProfileSection title="Sobre mim">
        <div className="space-y-5">
          <p className="text-[17px] whitespace-pre-line text-foreground">{profile.about ?? empty}</p>
          <Field label="Interesses">
            <Tags items={profile.interests} />
          </Field>
        </div>
      </ProfileSection>

      <ProfileSection title="Apoio entre estudantes" description="Ajuda outros estudantes a encontrar apoio.">
        <div className="grid gap-5 sm:grid-cols-2">
          <Field label="Posso ajudar com">
            <Tags items={profile.can_help_with} />
          </Field>
          <Field label="Procuro ajuda com">
            <Tags items={profile.needs_help_with} />
          </Field>
        </div>
      </ProfileSection>

      <ProfileSection title="Links">
        <div className="grid gap-5 sm:grid-cols-2">
          <Field label="GitHub">
            <ExternalLink url={profile.github_url} />
          </Field>
          <Field label="LinkedIn">
            <ExternalLink url={profile.linkedin_url} />
          </Field>
        </div>
      </ProfileSection>
    </div>
  );
}
