import { CircleAlert, Loader2, Lock } from 'lucide-react';
import { useEffect, useId, useState } from 'react';
import { Controller, useForm, useWatch } from 'react-hook-form';
import type { Location } from 'react-router';
import { DisciplinePicker } from '@/components/DisciplinePicker';
import { FormAlert } from '@/components/FormAlert';
import { TagInput } from '@/components/TagInput';
import { TextField } from '@/components/TextField';
import { Button } from '@/components/ui/button';
import { useUnsavedChangesGuard } from '@/components/layout/useUnsavedChangesGuard';
import { applyFieldErrors } from '@/features/auth/applyFieldErrors';
import { ApiError } from '@/lib/api/errors';
import { cn } from '@/lib/utils';
import { DiscardChangesDialog } from './DiscardChangesDialog';
import { useUpdateProfile, type Discipline, type Profile } from './hooks';
import { ProfileSection, VisibilityNote } from './ProfileSection';
import { ProfileSummaryCard } from './ProfileSummaryCard';

type FormValues = {
  display_name: string;
  discipline_ids: number[];
  about: string;
  interests: string[];
  can_help_with: string[];
  needs_help_with: string[];
  github_url: string;
  linkedin_url: string;
};

const FIELDS = ['display_name', 'discipline_ids', 'about', 'interests', 'can_help_with', 'needs_help_with', 'github_url', 'linkedin_url'] as const;
const NAME_REQUIRED = 'Informe seu nome de exibição.';
const DISCIPLINE_REQUIRED = 'Selecione pelo menos uma disciplina atual para completar seu perfil.';
const TAG_HINT = 'Digite um assunto e pressione Enter para adicionar.';

const required = (
  <span aria-hidden className="text-danger">
    {' '}*
  </span>
);

function toForm(profile: Profile): FormValues {
  return {
    display_name: profile.display_name,
    discipline_ids: profile.disciplines.map((discipline) => discipline.id),
    about: profile.about ?? '',
    interests: profile.interests,
    can_help_with: profile.can_help_with,
    needs_help_with: profile.needs_help_with,
    github_url: profile.github_url ?? '',
    linkedin_url: profile.linkedin_url ?? '',
  };
}

const orNull = (text: string) => (text.trim() === '' ? null : text);

type Props = {
  profile: Profile;
  disciplines: Discipline[];
  focusDisciplines: boolean;
  onSaved: () => void;
  onCancel: () => void;
};

// continuar no modo de edição e a saída já decidida (salvar ou Cancelar confirmado, com leaveEditing no state) não perguntam
function isFreeExit(current: Location, next: Location) {
  const staysEditing = next.pathname === current.pathname && new URLSearchParams(next.search).has('editar');
  return staysEditing || (next.state as { leaveEditing?: boolean } | null)?.leaveEditing === true;
}

export function ProfileForm({ profile, disciplines, focusDisciplines, onSaved, onCancel }: Props) {
  const id = useId();
  const update = useUpdateProfile();
  const [problem, setProblem] = useState<'invalid' | 'failed' | null>(null);
  const {
    register,
    control,
    handleSubmit,
    setError,
    setFocus,
    formState: { errors, isDirty },
  } = useForm<FormValues>({ defaultValues: toForm(profile) });
  const displayName = useWatch({ control, name: 'display_name' });

  const { askDiscard, dialogProps } = useUnsavedChangesGuard(isDirty, isFreeExit);

  useEffect(() => {
    if (focusDisciplines) setFocus('discipline_ids');
  }, [focusDisciplines, setFocus]);

  const onValid = async (values: FormValues) => {
    setProblem(null);
    try {
      await update.mutateAsync({
        ...values,
        about: orNull(values.about),
        github_url: orNull(values.github_url),
        linkedin_url: orNull(values.linkedin_url),
      });
      onSaved();
    } catch (error) {
      if (error instanceof ApiError && error.status === 422) {
        // erros de itens (interests.0, discipline_ids.2) vão para o campo da lista
        const byField = Object.fromEntries(Object.entries(error.fieldErrors).map(([key, messages]) => [key.split('.')[0], messages]));
        applyFieldErrors(setError, byField, FIELDS);
        setProblem('invalid');
      } else {
        setProblem('failed');
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    }
  };

  const cancel = async () => {
    if (isDirty && !(await askDiscard())) return;
    onCancel();
  };

  const disciplinesHintId = `${id}-disciplines-hint`;
  const disciplinesNoteId = `${id}-disciplines-note`;
  const aboutHintId = `${id}-about-hint`;

  return (
    <form noValidate onSubmit={handleSubmit(onValid, () => setProblem('invalid'))} className="space-y-6">
      {problem === 'invalid' && <FormAlert variant="danger">Revise os campos indicados antes de salvar.</FormAlert>}
      {problem === 'failed' && <FormAlert variant="danger">Não foi possível salvar suas alterações. Tente novamente.</FormAlert>}

      <ProfileSummaryCard displayName={displayName} />

      <ProfileSection title="Identidade">
        <div className="grid gap-5 sm:grid-cols-2">
          <TextField
            label={<>Nome de exibição{required}</>}
            hint="É assim que seu nome aparecerá para outros estudantes no ConectaCC."
            aria-required
            autoComplete="nickname"
            error={errors.display_name?.message}
            {...register('display_name', { validate: (value) => value.trim() !== '' || NAME_REQUIRED })}
          />
          <div className="space-y-2 sm:col-start-1">
            <label htmlFor={`${id}-course`} className="text-[15px] font-medium text-foreground">
              Curso
            </label>
            <div className="relative">
              <Lock className="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-muted-foreground" aria-hidden />
              <input
                id={`${id}-course`}
                value="Ciência da Computação"
                disabled
                className="h-12 w-full rounded-lg border border-slate-200 bg-ice pl-11 text-base text-foreground"
              />
            </div>
          </div>
        </div>
      </ProfileSection>

      <ProfileSection title="Minha jornada acadêmica">
        <div className="space-y-1">
          <label htmlFor={`${id}-disciplines`} className="text-[15px] font-medium text-foreground">
            Disciplinas atuais{required}
          </label>
          <p id={disciplinesHintId} className="text-sm text-muted-foreground">
            Selecione as disciplinas que você está cursando neste semestre.
          </p>
          <span id={disciplinesNoteId}>
            <VisibilityNote>Visíveis para outros estudantes autenticados no ConectaCC.</VisibilityNote>
          </span>
        </div>
        <div className="mt-3">
          <Controller
            control={control}
            name="discipline_ids"
            rules={{ validate: (value) => value.length > 0 || DISCIPLINE_REQUIRED }}
            render={({ field }) => (
              <DisciplinePicker
                id={`${id}-disciplines`}
                options={disciplines}
                value={field.value}
                onChange={field.onChange}
                inputRef={field.ref}
                describedBy={`${disciplinesHintId} ${disciplinesNoteId}`}
                error={errors.discipline_ids?.message}
              />
            )}
          />
        </div>
      </ProfileSection>

      <ProfileSection title="Sobre mim">
        <div className="space-y-6">
          <div className="space-y-2">
            <div className="space-y-1">
              <label htmlFor={`${id}-about`} className="text-[15px] font-medium text-foreground">
                Sobre mim
              </label>
              <p id={aboutHintId} className="text-sm text-muted-foreground">
                Conte um pouco sobre seus interesses acadêmicos ou profissionais.
              </p>
            </div>
            <textarea
              id={`${id}-about`}
              rows={4}
              aria-invalid={errors.about ? true : undefined}
              aria-describedby={[aboutHintId, errors.about ? `${id}-about-error` : ''].filter(Boolean).join(' ')}
              className={cn(
                'w-full rounded-lg border border-input bg-white px-4 py-3 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50',
                errors.about && 'border-danger',
              )}
              {...register('about')}
            />
            {errors.about && (
              <p id={`${id}-about-error`} className="flex items-start gap-2 text-sm text-danger">
                <CircleAlert className="mt-0.5 size-4 shrink-0" aria-hidden />
                <span>{errors.about.message}</span>
              </p>
            )}
          </div>
          <Controller
            control={control}
            name="interests"
            render={({ field }) => (
              <TagInput
                label="Interesses"
                hint={TAG_HINT}
                placeholder="Adicionar interesse"
                value={field.value}
                onChange={field.onChange}
                inputRef={field.ref}
                error={errors.interests?.message}
              />
            )}
          />
        </div>
      </ProfileSection>

      <ProfileSection title="Apoio entre estudantes" description="Ajuda outros estudantes a encontrar apoio.">
        <div className="grid gap-6 sm:grid-cols-2">
          {(
            [
              ['can_help_with', 'Posso ajudar com'],
              ['needs_help_with', 'Procuro ajuda com'],
            ] as const
          ).map(([name, label]) => (
            <Controller
              key={name}
              control={control}
              name={name}
              render={({ field }) => (
                <TagInput
                  label={label}
                  hint={TAG_HINT}
                  placeholder="Adicionar assunto"
                  value={field.value}
                  onChange={field.onChange}
                  inputRef={field.ref}
                  error={errors[name]?.message}
                />
              )}
            />
          ))}
        </div>
      </ProfileSection>

      <ProfileSection title="Links">
        <div className="grid gap-5 sm:grid-cols-2">
          <TextField
            label="GitHub"
            inputMode="url"
            autoComplete="url"
            placeholder="https://github.com/usuario"
            error={errors.github_url?.message}
            {...register('github_url')}
          />
          <TextField
            label="LinkedIn"
            inputMode="url"
            autoComplete="url"
            placeholder="https://www.linkedin.com/in/usuario"
            error={errors.linkedin_url?.message}
            {...register('linkedin_url')}
          />
        </div>
      </ProfileSection>

      <div className="sticky bottom-0 z-10 flex flex-col-reverse gap-3 rounded-2xl border border-blue-200/70 bg-white/95 p-4 shadow-[0_-4px_16px_rgba(7,31,67,0.06)] backdrop-blur sm:flex-row sm:justify-end">
        <Button type="button" variant="outline" onClick={cancel} className="h-12 border-action px-6 text-base font-semibold text-action">
          Cancelar
        </Button>
        <Button type="submit" disabled={update.isPending} className="h-12 px-6 text-base font-semibold">
          {update.isPending ? (
            <>
              <Loader2 className="size-5 animate-spin" aria-hidden />
              Salvando...
            </>
          ) : (
            'Salvar alterações'
          )}
        </Button>
      </div>

      <DiscardChangesDialog {...dialogProps} />
    </form>
  );
}
