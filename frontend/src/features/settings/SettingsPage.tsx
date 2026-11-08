import { Loader2, Settings } from 'lucide-react';
import { useEffect, useRef, useState, type SubmitEvent } from 'react';
import { useForm } from 'react-hook-form';
import type { Location } from 'react-router';
import { FormAlert } from '@/components/FormAlert';
import { PageHeader } from '@/components/PageHeader';
import { useUnsavedChangesGuard } from '@/components/layout/useUnsavedChangesGuard';
import { Skeleton } from '@/components/Skeleton';
import { TextField } from '@/components/TextField';
import { Button } from '@/components/ui/button';
import { applyFieldErrors } from '@/features/auth/applyFieldErrors';
import { useMe } from '@/features/auth/hooks';
import { DiscardChangesDialog } from '@/features/profile/DiscardChangesDialog';
import { ApiError } from '@/lib/api/errors';
import { useChangePassword, type ChangePasswordInput } from './hooks';

const EMPTY: ChangePasswordInput = { current_password: '', password: '', password_confirmation: '' };
const FIELDS = ['current_password', 'password', 'password_confirmation'] as const;
const SAVE_FAILED = 'Não foi possível alterar sua senha agora. Tente novamente.';
const MIN_LENGTH = 'Use pelo menos 8 caracteres.';
const SECTION = 'rounded-2xl border border-blue-200/70 bg-white p-6 sm:p-7';

const required = (
  <span aria-hidden className="text-danger">
    {' '}*
  </span>
);

// o link da sidebar para esta página não sai dela, então não há o que descartar
function isSamePage(current: Location, next: Location) {
  return current.pathname === next.pathname;
}

export function SettingsPage() {
  const me = useMe();
  const email = me.data?.email ?? '';
  const change = useChangePassword();
  const noticeRef = useRef<HTMLDivElement>(null);
  const [saved, setSaved] = useState(false);
  const [problem, setProblem] = useState<string | null>(null);
  // trocar a key recria os campos, e as senhas voltam a ficar ocultas
  const [fieldsKey, setFieldsKey] = useState(0);
  const {
    register,
    handleSubmit,
    setError,
    resetField,
    reset,
    setFocus,
    getValues,
    formState: { errors, isDirty, isSubmitting },
  } = useForm<ChangePasswordInput>({ defaultValues: EMPTY });
  const { dialogProps } = useUnsavedChangesGuard(isDirty, isSamePage);

  // o envio não é cancelado ao sair: o diálogo espera o resultado e, se a senha mudou, a pessoa fica e vê o aviso
  useEffect(() => {
    if (saved && dialogProps.open) dialogProps.onContinue();
  }, [saved, dialogProps]);

  // os campos são recriados no sucesso e o campo com foco some: o foco vai para o aviso
  useEffect(() => {
    if (saved) noticeRef.current?.focus();
  }, [saved]);

  const clearNotices = () => {
    setSaved(false);
    setProblem(null);
  };

  const onSubmit = handleSubmit(async (values) => {
    clearNotices();
    try {
      await change.mutateAsync(values);
      reset(EMPTY);
      setFieldsKey((key) => key + 1);
      setSaved(true);
    } catch (error) {
      if (error instanceof ApiError && error.status === 422) {
        if (error.fieldErrors.current_password) resetField('current_password');
        applyFieldErrors(setError, error.fieldErrors, FIELDS);
      } else if (error instanceof ApiError && error.status === 429) {
        setProblem(error.message);
      } else if (!(error instanceof ApiError && error.status === 401)) {
        // o 401 já leva ao Login pelo cliente da API
        setProblem(SAVE_FAILED);
      }
    }
  });

  const submitOnce = (event: SubmitEvent<HTMLFormElement>) => {
    event.preventDefault();
    // um segundo envio falharia: a senha atual já teria mudado
    if (!isSubmitting) void onSubmit(event);
  };

  return (
    <div>
      <PageHeader icon={Settings} title="Configurações" description="Gerencie informações da sua conta e opções de segurança." />

      <div className="mt-8 space-y-6">
        <section aria-labelledby="configuracoes-conta" className={SECTION}>
          <h2 id="configuracoes-conta" className="font-display text-2xl font-bold text-foreground">
            Conta
          </h2>
          <dl className="mt-4 max-w-[440px]">
            <dt className="text-[15px] font-medium text-foreground">E-mail institucional</dt>
            <dd className="mt-2" aria-busy={me.isPending || undefined}>
              {/* o guard não espera o /me: a página abre enquanto ele carrega, e o AppLayout avisa se ele falhar */}
              {me.isPending ? (
                <Skeleton className="h-12 rounded-lg" />
              ) : (
                <div className="rounded-lg border border-slate-200 bg-ice px-4 py-3 text-base [overflow-wrap:anywhere] text-foreground">
                  {email || 'E-mail indisponível no momento.'}
                </div>
              )}
              <p className="mt-2 text-sm text-muted-foreground">Dado da conta. Não pode ser alterado nesta versão.</p>
            </dd>
          </dl>
        </section>

        <section aria-labelledby="configuracoes-seguranca" className={SECTION}>
          <h2 id="configuracoes-seguranca" className="font-display text-2xl font-bold text-foreground">
            Segurança
          </h2>
          <h3 id="configuracoes-senha" className="mt-4 font-display text-lg font-bold text-foreground">
            Alterar senha
          </h3>
          <p className="mt-1 text-[15px] text-muted-foreground">Informe sua senha atual e escolha uma nova senha.</p>

          <form noValidate onSubmit={submitOnce} aria-labelledby="configuracoes-senha" className="mt-5 max-w-[440px] space-y-5">
            {saved && (
              <div ref={noticeRef} tabIndex={-1} className="outline-none">
                <FormAlert
                  variant="success"
                  onDismiss={() => {
                    setSaved(false);
                    setFocus('current_password');
                  }}
                >
                  Senha alterada com sucesso.
                </FormAlert>
              </div>
            )}
            {problem && <FormAlert variant="danger">{problem}</FormAlert>}

            {/* ajuda o gerenciador de senhas a associar a nova senha à conta */}
            {email && <input type="email" autoComplete="username" value={email} readOnly tabIndex={-1} aria-hidden className="sr-only" />}

            <TextField
              key={`current-${fieldsKey}`}
              label={<>Senha atual{required}</>}
              type="password"
              autoComplete="current-password"
              visibilityLabel="senha atual"
              aria-required
              readOnly={isSubmitting}
              error={errors.current_password?.message}
              {...register('current_password', { required: 'Informe sua senha atual.', onChange: clearNotices })}
            />
            <TextField
              key={`new-${fieldsKey}`}
              label={<>Nova senha{required}</>}
              type="password"
              autoComplete="new-password"
              visibilityLabel="nova senha"
              aria-required
              readOnly={isSubmitting}
              hint={errors.password ? undefined : MIN_LENGTH}
              error={errors.password?.message}
              {...register('password', {
                required: 'Informe sua nova senha.',
                minLength: { value: 8, message: MIN_LENGTH },
                onChange: clearNotices,
              })}
            />
            <TextField
              key={`confirmation-${fieldsKey}`}
              label={<>Confirmar nova senha{required}</>}
              type="password"
              autoComplete="new-password"
              visibilityLabel="confirmação da nova senha"
              aria-required
              readOnly={isSubmitting}
              error={errors.password_confirmation?.message}
              {...register('password_confirmation', {
                required: 'Confirme sua nova senha.',
                validate: (value) => value === getValues('password') || 'As senhas informadas não coincidem.',
                onChange: clearNotices,
              })}
            />

            <Button type="submit" disabled={isSubmitting} aria-busy={isSubmitting} className="h-12 w-full px-6 text-base font-semibold sm:w-auto">
              {isSubmitting ? (
                <>
                  <Loader2 className="size-5 animate-spin" aria-hidden />
                  Salvando…
                </>
              ) : (
                'Salvar nova senha'
              )}
            </Button>
          </form>
        </section>
      </div>

      <DiscardChangesDialog {...dialogProps} open={dialogProps.open && !isSubmitting} description="As alterações feitas ainda não foram salvas." />
    </div>
  );
}
