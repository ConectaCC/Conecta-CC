import { CircleAlert, Loader2 } from 'lucide-react';
import { useId, useRef, useState } from 'react';
import { useForm, useWatch } from 'react-hook-form';
import { Link } from 'react-router';
import { FormAlert } from '@/components/FormAlert';
import { TextField } from '@/components/TextField';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { applyFieldErrors } from '@/features/auth/applyFieldErrors';
import { DiscardChangesDialog } from '@/features/profile/DiscardChangesDialog';
import { ApiError } from '@/lib/api/errors';
import { cn } from '@/lib/utils';
import { useAddOpportunity, type OpportunityInput } from './hooks';
import { MODALITY_LABELS, todayInput, TYPE_LABELS } from './labels';

type Values = { type: string; title: string; description: string; url: string; modality: string; deadline: string };

const EMPTY: Values = { type: '', title: '', description: '', url: '', modality: '', deadline: '' };
const FIELDS = ['type', 'title', 'description', 'url', 'modality', 'deadline'] as const;
const GENERIC_ERROR = 'Não foi possível enviar a oportunidade. Tente novamente.';
const MAX_DESCRIPTION = 300;

const fieldClass =
  'w-full rounded-lg border border-input bg-white px-4 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 disabled:opacity-60';

function FieldError({ id, message }: { id: string; message?: string }) {
  if (!message) return null;
  return (
    <p id={id} className="flex items-start gap-2 text-sm text-danger">
      <CircleAlert className="mt-0.5 size-4 shrink-0" aria-hidden />
      <span>{message}</span>
    </p>
  );
}

type Props = { open: boolean; onOpenChange: (open: boolean) => void; backLabel: string };

export function AddOpportunityDialog({ open, onOpenChange, backLabel }: Props) {
  const add = useAddOpportunity();
  const busy = add.isPending;
  const submitting = useRef(false);
  const [sent, setSent] = useState(false);
  const [problem, setProblem] = useState<string | null>(null);
  const [confirmDiscard, setConfirmDiscard] = useState(false);
  const id = useId();
  const {
    register,
    handleSubmit,
    setError,
    reset,
    control,
    formState: { errors, isDirty },
  } = useForm<Values>({ defaultValues: EMPTY });
  const description = useWatch({ control, name: 'description' });

  function close() {
    reset();
    setSent(false);
    setProblem(null);
    setConfirmDiscard(false);
    onOpenChange(false);
  }

  function requestClose() {
    if (busy) return;
    if (!sent && isDirty) setConfirmDiscard(true);
    else close();
  }

  async function onValid(values: Values) {
    // duplo clique: o segundo envio é ignorado enquanto o primeiro está em andamento
    if (submitting.current) return;
    submitting.current = true;
    setProblem(null);
    try {
      await add.mutateAsync({
        type: values.type as OpportunityInput['type'],
        title: values.title,
        description: values.description,
        url: values.url,
        modality: values.modality as OpportunityInput['modality'],
        deadline: values.deadline || null,
      });
      setSent(true);
    } catch (error) {
      if (error instanceof ApiError && error.status === 422) {
        const rest = applyFieldErrors(setError, error.fieldErrors, FIELDS);
        if (Object.keys(rest).length > 0) setProblem(GENERIC_ERROR);
      } else if (error instanceof ApiError && error.status === 429) {
        setProblem(error.message);
      } else {
        setProblem(GENERIC_ERROR);
      }
    } finally {
      submitting.current = false;
    }
  }

  const ids = { type: `${id}-type`, description: `${id}-description`, modality: `${id}-modality`, deadline: `${id}-deadline` };

  return (
    <>
      <Dialog open={open} onOpenChange={(next) => !next && requestClose()}>
        <DialogContent showCloseButton={false} className="max-h-[calc(100dvh-2rem)] w-[calc(100%-2rem)] max-w-xl gap-0 overflow-y-auto p-6 sm:max-w-xl">
          {sent ? (
            <div>
              <DialogTitle className="font-display text-xl font-bold text-foreground">Oportunidade enviada para análise</DialogTitle>
              <DialogDescription className="mt-2 text-[15px] text-muted-foreground">
                Você receberá uma notificação quando sua indicação for analisada. Você pode acompanhar a análise em{' '}
                <Link to="/perfil/meus-conteudos?tipo=oportunidades" className="font-semibold text-action hover:underline">
                  Meus conteúdos
                </Link>
                .
              </DialogDescription>
              <div className="mt-6 flex justify-end">
                <Button type="button" onClick={close} className="h-11 px-5 text-[15px] font-semibold">
                  {backLabel}
                </Button>
              </div>
            </div>
          ) : (
            <form noValidate onSubmit={(event) => handleSubmit(onValid)(event)}>
              <DialogTitle className="font-display text-xl font-bold text-foreground">Adicionar oportunidade</DialogTitle>
              <DialogDescription className="mt-2 text-[15px] text-muted-foreground">
                Campos marcados com <span aria-hidden>*</span>
                <span className="sr-only">asterisco</span> são obrigatórios.
              </DialogDescription>

              <div className="mt-5 space-y-5">
                {problem && <FormAlert variant="danger">{problem}</FormAlert>}

                <div className="space-y-2">
                  <Label htmlFor={ids.type} className="text-[15px] font-medium text-foreground">
                    Tipo *
                  </Label>
                  <select
                    id={ids.type}
                    aria-required
                    disabled={busy}
                    aria-invalid={errors.type ? true : undefined}
                    aria-describedby={errors.type ? `${ids.type}-error` : undefined}
                    className={cn(fieldClass, 'h-12', errors.type && 'border-danger')}
                    {...register('type', { required: 'Escolha o tipo da oportunidade.' })}
                  >
                    <option value="">Selecione o tipo</option>
                    {Object.entries(TYPE_LABELS).map(([value, label]) => (
                      <option key={value} value={value}>
                        {label}
                      </option>
                    ))}
                  </select>
                  <FieldError id={`${ids.type}-error`} message={errors.type?.message} />
                </div>

                <TextField
                  label="Título *"
                  aria-required
                  maxLength={200}
                  disabled={busy}
                  error={errors.title?.message}
                  {...register('title', { validate: (value) => value.trim() !== '' || 'Informe um título para a oportunidade.' })}
                />

                <div className="space-y-2">
                  <Label htmlFor={ids.description} className="text-[15px] font-medium text-foreground">
                    Descrição curta *
                  </Label>
                  <textarea
                    id={ids.description}
                    rows={3}
                    maxLength={MAX_DESCRIPTION}
                    aria-required
                    disabled={busy}
                    aria-invalid={errors.description ? true : undefined}
                    aria-describedby={`${ids.description}-hint${errors.description ? ` ${ids.description}-error` : ''}`}
                    className={cn(fieldClass, 'py-3', errors.description && 'border-danger')}
                    {...register('description', { validate: (value) => value.trim() !== '' || 'Descreva a oportunidade em poucas palavras.' })}
                  />
                  <FieldError id={`${ids.description}-error`} message={errors.description?.message} />
                  <p id={`${ids.description}-hint`} className="flex justify-between gap-3 text-sm text-muted-foreground">
                    <span>Conte brevemente por que essa oportunidade pode interessar outros estudantes.</span>
                    <span aria-live="polite">
                      {description.length}/{MAX_DESCRIPTION}
                    </span>
                  </p>
                </div>

                <TextField
                  label="Link *"
                  aria-required
                  inputMode="url"
                  disabled={busy}
                  error={errors.url?.message}
                  hint="Endereço original da oportunidade."
                  {...register('url', { validate: (value) => value.trim() !== '' || 'Informe o link da oportunidade.' })}
                />

                <fieldset aria-describedby={errors.modality ? `${ids.modality}-error` : undefined}>
                  <legend className="text-[15px] font-medium text-foreground">Modalidade *</legend>
                  <div className="mt-2 flex flex-wrap gap-x-6 gap-y-2">
                    {Object.entries(MODALITY_LABELS).map(([value, label]) => (
                      <label key={value} className="inline-flex min-h-11 items-center gap-2 text-base text-foreground">
                        <input type="radio" value={value} disabled={busy} className="size-5 accent-action" {...register('modality', { required: 'Escolha a modalidade.' })} />
                        {label}
                      </label>
                    ))}
                  </div>
                  <FieldError id={`${ids.modality}-error`} message={errors.modality?.message} />
                </fieldset>

                <div className="space-y-2">
                  <Label htmlFor={ids.deadline} className="text-[15px] font-medium text-foreground">
                    Inscrições até
                  </Label>
                  <input
                    id={ids.deadline}
                    type="date"
                    min={todayInput()}
                    disabled={busy}
                    aria-invalid={errors.deadline ? true : undefined}
                    aria-describedby={`${ids.deadline}-hint${errors.deadline ? ` ${ids.deadline}-error` : ''}`}
                    className={cn(fieldClass, 'h-12 sm:max-w-60', errors.deadline && 'border-danger')}
                    {...register('deadline')}
                  />
                  <FieldError id={`${ids.deadline}-error`} message={errors.deadline?.message} />
                  <p id={`${ids.deadline}-hint`} className="text-sm text-muted-foreground">
                    Opcional. Sem data, a oportunidade fica visível por 15 dias após a aprovação.
                  </p>
                </div>

                <p className="text-[15px] text-muted-foreground">A oportunidade será analisada pela moderação antes de aparecer na página.</p>
              </div>

              <div className="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <Button
                  type="button"
                  variant="outline"
                  onClick={requestClose}
                  disabled={busy}
                  className="h-11 border-action px-5 text-[15px] font-semibold text-action"
                >
                  Cancelar
                </Button>
                <Button type="submit" disabled={busy} className="h-11 px-5 text-[15px] font-semibold">
                  {busy ? (
                    <>
                      <Loader2 className="size-5 animate-spin" aria-hidden />
                      Enviando…
                    </>
                  ) : (
                    'Enviar para análise'
                  )}
                </Button>
              </div>
            </form>
          )}
        </DialogContent>
      </Dialog>

      <DiscardChangesDialog
        open={confirmDiscard}
        description="As informações desta oportunidade ainda não foram enviadas."
        continueLabel="Continuar preenchendo"
        onContinue={() => setConfirmDiscard(false)}
        onDiscard={close}
      />
    </>
  );
}
