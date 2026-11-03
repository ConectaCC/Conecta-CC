import { CircleAlert, Loader2 } from 'lucide-react';
import { useId, useRef, useState } from 'react';
import { Controller, useForm } from 'react-hook-form';
import { Link } from 'react-router';
import { FormAlert } from '@/components/FormAlert';
import { TextField } from '@/components/TextField';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { applyFieldErrors } from '@/features/auth/applyFieldErrors';
import { DiscardChangesDialog } from '@/features/profile/DiscardChangesDialog';
import { ApiError } from '@/lib/api/errors';
import { cn } from '@/lib/utils';
import { useReviseMaterial, useShareMaterial } from './hooks';

type Values = { title: string; description: string; url: string; anonymous: boolean };

const EMPTY: Values = { title: '', description: '', url: '', anonymous: false };
const FIELDS = ['title', 'description', 'url', 'anonymous'] as const;
const GENERIC_ERROR = 'Não foi possível enviar o material. Tente novamente.';

export type EditingMaterial = { id: number; title: string; description: string | null; url: string };

type Props = {
  discipline: { id: number; name: string };
  open: boolean;
  onOpenChange: (open: boolean) => void;
  // com editing, o diálogo envia uma alteração do material publicado (a autoria não muda)
  editing?: EditingMaterial;
};

export function ShareMaterialDialog({ discipline, open, onOpenChange, editing }: Props) {
  const share = useShareMaterial(discipline.id);
  const revise = useReviseMaterial();
  const busy = share.isPending || revise.isPending;
  const submitting = useRef(false);
  const [sent, setSent] = useState(false);
  const [problem, setProblem] = useState<string | null>(null);
  const [confirmDiscard, setConfirmDiscard] = useState(false);
  const id = useId();
  const {
    register,
    control,
    handleSubmit,
    setError,
    reset,
    formState: { errors, isDirty },
  } = useForm<Values>({
    defaultValues: editing ? { title: editing.title, description: editing.description ?? '', url: editing.url, anonymous: false } : EMPTY,
  });

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
      const body = { ...values, description: values.description.trim() || null };
      if (editing) await revise.mutateAsync({ id: editing.id, body });
      else await share.mutateAsync(body);
      setSent(true);
    } catch (error) {
      if (error instanceof ApiError && error.status === 422) {
        const rest = applyFieldErrors(setError, error.fieldErrors, FIELDS);
        if (Object.keys(rest).length > 0) setProblem(GENERIC_ERROR);
      } else if (error instanceof ApiError && (error.status === 429 || error.status === 409)) {
        setProblem(error.message);
      } else {
        setProblem(GENERIC_ERROR);
      }
    } finally {
      submitting.current = false;
    }
  }

  const descriptionId = `${id}-description`;

  return (
    <>
      <Dialog open={open} onOpenChange={(next) => !next && requestClose()}>
        <DialogContent showCloseButton={false} className="max-h-[calc(100dvh-2rem)] w-[calc(100%-2rem)] max-w-xl gap-0 overflow-y-auto p-6 sm:max-w-xl">
          {sent ? (
            <div>
              <DialogTitle className="font-display text-xl font-bold text-foreground">
                {editing ? 'Alteração enviada para análise' : 'Material enviado para análise'}
              </DialogTitle>
              <DialogDescription className="mt-2 text-[15px] text-muted-foreground">
                {editing ? (
                  'A versão atual continua visível até a alteração ser aprovada.'
                ) : (
                  <>
                    Você pode acompanhar a análise em{' '}
                    <Link to="/perfil/meus-conteudos" className="font-semibold text-action hover:underline">
                      Meus conteúdos
                    </Link>
                    .
                  </>
                )}
              </DialogDescription>
              <div className="mt-6 flex justify-end">
                <Button type="button" onClick={close} className="h-11 px-5 text-[15px] font-semibold">
                  {editing ? 'Voltar' : 'Voltar para a disciplina'}
                </Button>
              </div>
            </div>
          ) : (
            <form noValidate onSubmit={(event) => handleSubmit(onValid)(event)}>
              <DialogTitle className="font-display text-xl font-bold text-foreground">{editing ? 'Editar material' : 'Compartilhar material'}</DialogTitle>
              <DialogDescription className="mt-2 text-[15px] text-muted-foreground">
                Campos marcados com <span aria-hidden>*</span>
                <span className="sr-only">asterisco</span> são obrigatórios.
              </DialogDescription>

              <div className="mt-5 space-y-5">
                {problem && <FormAlert variant="danger">{problem}</FormAlert>}

                <div>
                  <p className="text-[15px] font-medium text-foreground">Disciplina</p>
                  <p className="mt-1 text-base text-muted-foreground">{discipline.name}</p>
                </div>

                <TextField
                  label="Título *"
                  aria-required
                  disabled={busy}
                  error={errors.title?.message}
                  {...register('title', { validate: (value) => value.trim() !== '' || 'Informe um título para o material.' })}
                />

                <div className="space-y-2">
                  <Label htmlFor={descriptionId} className="text-[15px] font-medium text-foreground">
                    Descrição
                  </Label>
                  <textarea
                    id={descriptionId}
                    rows={3}
                    disabled={busy}
                    aria-invalid={errors.description ? true : undefined}
                    aria-describedby={`${descriptionId}-hint${errors.description ? ` ${descriptionId}-error` : ''}`}
                    className={cn(
                      'w-full rounded-lg border border-input bg-white px-4 py-3 text-base outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50',
                      errors.description && 'border-danger',
                    )}
                    {...register('description')}
                  />
                  {errors.description && (
                    <p id={`${descriptionId}-error`} className="flex items-start gap-2 text-sm text-danger">
                      <CircleAlert className="mt-0.5 size-4 shrink-0" aria-hidden />
                      <span>{errors.description.message}</span>
                    </p>
                  )}
                  <p id={`${descriptionId}-hint`} className="text-sm text-muted-foreground">
                    Explique brevemente o que o material contém.
                  </p>
                </div>

                <TextField
                  label="Link *"
                  aria-required
                  inputMode="url"
                  disabled={busy}
                  error={errors.url?.message}
                  hint="Use um link que outros estudantes consigam acessar."
                  {...register('url', { validate: (value) => value.trim() !== '' || 'Informe o link do material.' })}
                />

                {!editing && (
                  <Controller
                    control={control}
                    name="anonymous"
                    render={({ field }) => (
                      <div className="flex items-start gap-3">
                        <Checkbox
                          id={`${id}-anonymous`}
                          checked={field.value}
                          onCheckedChange={(value) => field.onChange(value === true)}
                          disabled={busy}
                          aria-describedby={`${id}-anonymous-hint`}
                          className="mt-1 size-5"
                        />
                        <div>
                          <Label htmlFor={`${id}-anonymous`} className="text-base font-semibold text-foreground">
                            Compartilhar anonimamente
                          </Label>
                          <p id={`${id}-anonymous-hint`} className="mt-1 text-sm text-muted-foreground">
                            Outros estudantes não verão seu nome. A administração do ConectaCC continua sabendo quem enviou.
                          </p>
                        </div>
                      </div>
                    )}
                  />
                )}

                <p className="text-[15px] text-muted-foreground">
                  {editing
                    ? 'A alteração será analisada pela moderação. Até lá, a versão atual continua visível.'
                    : 'O material será analisado pela moderação antes de aparecer na disciplina.'}
                </p>
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
        description="As informações deste material ainda não foram enviadas."
        continueLabel="Continuar preenchendo"
        onContinue={() => setConfirmDiscard(false)}
        onDiscard={close}
      />
    </>
  );
}
