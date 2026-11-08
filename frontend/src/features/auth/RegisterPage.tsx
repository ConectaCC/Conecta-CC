import { useQueryClient } from '@tanstack/react-query';
import { useRef, useState } from 'react';
import { useForm } from 'react-hook-form';
import { Link, useNavigate } from 'react-router';
import { FormAlert } from '@/components/FormAlert';
import { TextField } from '@/components/TextField';
import { Button } from '@/components/ui/button';
import { ApiError } from '@/lib/api/errors';
import { AuthLayout } from './AuthLayout';
import { applyFieldErrors } from './applyFieldErrors';
import { useRegister, type RegisterInput } from './hooks';
import { EMAIL_PATTERN } from './LoginPage';
import { TermsDialog } from './TermsDialog';

type FormValues = Omit<RegisterInput, 'terms_version'>;

const EMAIL_TAKEN = 'Não foi possível criar uma conta com este e-mail. Se você já possui cadastro, entre.';
const FIELDS = ['name', 'email', 'password', 'password_confirmation'] as const;

type Problem = { kind: 'message'; message: string } | { kind: 'network' };

export function RegisterPage() {
  const navigate = useNavigate();
  const registerAccount = useRegister();
  const queryClient = useQueryClient();
  // campo que recebe o foco quando o modal fecha por erro do servidor (senão o Radix devolve o foco ao botão)
  const focusAfterClose = useRef<(typeof FIELDS)[number] | null>(null);
  const submitRef = useRef<HTMLButtonElement>(null);
  const [termsOpen, setTermsOpen] = useState(false);
  const [problem, setProblem] = useState<Problem | null>(null);
  const {
    register,
    handleSubmit,
    getValues,
    setError,
    setFocus,
    formState: { errors },
  } = useForm<FormValues>({ defaultValues: { name: '', email: '', password: '', password_confirmation: '' } });

  const openTerms = handleSubmit(() => {
    setProblem(null);
    setTermsOpen(true);
  });

  const createAccount = async (termsVersion: string) => {
    const values = getValues();
    try {
      await registerAccount.mutateAsync({ ...values, terms_version: termsVersion });
      void navigate('/login', { state: { registeredEmail: values.email.trim().toLowerCase() } });
    } catch (error) {
      if (error instanceof ApiError && error.status === 422) {
        // versão dos Termos mudou: buscar o texto novo antes de a pessoa aceitar de novo
        if (error.fieldErrors.terms_version) void queryClient.invalidateQueries({ queryKey: ['terms'] });
        focusAfterClose.current = FIELDS.find((field) => error.fieldErrors[field]) ?? null;
        const rest = applyFieldErrors(setError, error.fieldErrors, FIELDS);
        const other = Object.values(rest)[0]?.[0];
        if (other) setProblem({ kind: 'message', message: other });
      } else if (error instanceof ApiError && error.status === 429) {
        setProblem({ kind: 'message', message: error.message });
      } else {
        setProblem({ kind: 'network' });
      }
      setTermsOpen(false);
    }
  };

  // o modal abre pelo envio do formulário, sem gatilho do Radix: o foco precisa ser devolvido à mão
  const restoreFocus = (event: Event) => {
    event.preventDefault();
    if (focusAfterClose.current) setFocus(focusAfterClose.current);
    else submitRef.current?.focus();
    focusAfterClose.current = null;
  };

  const emailError =
    errors.email?.message === EMAIL_TAKEN ? (
      <>
        Não foi possível criar uma conta com este e-mail. Se você já possui cadastro,{' '}
        <Link to="/login" className="font-semibold underline underline-offset-2">
          entre
        </Link>
        .
      </>
    ) : (
      errors.email?.message
    );

  return (
    <AuthLayout>
      <h1 className="font-display text-[28px] font-bold text-foreground sm:text-[44px]">Crie sua conta</h1>
      <p className="mt-3 text-lg text-muted-foreground">
        Use seu e-mail institucional para participar da versão de testes do ConectaCC.
      </p>

      <form noValidate onSubmit={openTerms} className="mt-10 space-y-6">
        {problem?.kind === 'message' && <FormAlert variant="danger">{problem.message}</FormAlert>}
        {problem?.kind === 'network' && (
          <FormAlert
            variant="danger"
            action={
              <button type="button" onClick={() => openTerms()} className="font-semibold underline underline-offset-2">
                Tentar novamente
              </button>
            }
          >
            Não foi possível conectar ao ConectaCC. Verifique sua conexão e tente novamente.
          </FormAlert>
        )}

        <TextField
          label="Nome completo"
          autoComplete="name"
          maxLength={120}
          hint="Também será seu nome de exibição inicial. Você pode alterá-lo no Perfil."
          error={errors.name?.message}
          {...register('name', { validate: (value) => value.trim() !== '' || 'Informe seu nome completo.' })}
        />
        <TextField
          label="E-mail institucional"
          type="email"
          autoComplete="email"
          placeholder="seuemail@..."
          error={emailError}
          {...register('email', {
            required: 'Informe um endereço de e-mail válido.',
            pattern: { value: EMAIL_PATTERN, message: 'Informe um endereço de e-mail válido.' },
          })}
        />
        <TextField
          label="Senha"
          type="password"
          autoComplete="new-password"
          hint={errors.password ? undefined : 'Use pelo menos 8 caracteres.'}
          error={errors.password?.message}
          {...register('password', {
            required: 'Use pelo menos 8 caracteres.',
            minLength: { value: 8, message: 'Use pelo menos 8 caracteres.' },
          })}
        />
        <TextField
          label="Confirmar senha"
          type="password"
          autoComplete="new-password"
          error={errors.password_confirmation?.message}
          {...register('password_confirmation', {
            validate: (value) => (value !== '' && value === getValues('password')) || 'As senhas informadas não coincidem.',
          })}
        />

        <Button ref={submitRef} type="submit" className="h-12 w-full text-base font-semibold">
          Criar minha conta
        </Button>
      </form>

      <hr className="my-10 border-slate-200" />
      <p className="text-center text-muted-foreground">
        Já tem uma conta?{' '}
        <Link to="/login" className="font-semibold text-action hover:underline">
          Entrar
        </Link>
      </p>

      <TermsDialog open={termsOpen} onOpenChange={setTermsOpen} onAccept={createAccount} submitting={registerAccount.isPending} onCloseAutoFocus={restoreFocus} />
    </AuthLayout>
  );
}
