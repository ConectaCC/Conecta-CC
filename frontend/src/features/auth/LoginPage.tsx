import { Loader2 } from 'lucide-react';
import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { Link, useLocation, useNavigate } from 'react-router';
import { FormAlert } from '@/components/FormAlert';
import { TextField } from '@/components/TextField';
import { Button } from '@/components/ui/button';
import { ApiError } from '@/lib/api/errors';
import { AuthLayout } from './AuthLayout';
import { applyFieldErrors } from './applyFieldErrors';
import { useLogin, type LoginInput } from './hooks';

export const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

type Problem = { kind: 'credentials' } | { kind: 'throttle'; message: string } | { kind: 'network' };

export function LoginPage() {
  const location = useLocation();
  const registeredEmail = (location.state as { registeredEmail?: string } | null)?.registeredEmail;
  const navigate = useNavigate();
  const login = useLogin();
  const [problem, setProblem] = useState<Problem | null>(null);
  const {
    register,
    handleSubmit,
    setError,
    setFocus,
    resetField,
    formState: { errors },
  } = useForm<LoginInput>({ defaultValues: { email: registeredEmail ?? '', password: '' } });

  useEffect(() => {
    if (registeredEmail) setFocus('password');
  }, [registeredEmail, setFocus]);

  const onSubmit = handleSubmit(async (values) => {
    setProblem(null);
    try {
      await login.mutateAsync(values);
      void navigate('/home', { replace: true });
    } catch (error) {
      if (error instanceof ApiError && error.status === 422 && error.fieldErrors.credentials) {
        setProblem({ kind: 'credentials' });
        resetField('password');
        setFocus('password');
      } else if (error instanceof ApiError && error.status === 422) {
        applyFieldErrors(setError, error.fieldErrors, ['email', 'password']);
      } else if (error instanceof ApiError && error.status === 429) {
        setProblem({ kind: 'throttle', message: error.message });
      } else {
        setProblem({ kind: 'network' });
      }
    }
  });

  return (
    <AuthLayout>
      <h1 className="font-display text-[28px] font-bold text-foreground sm:text-[44px]">Bem-vindo(a) de volta</h1>
      <p className="mt-3 text-lg text-muted-foreground">Entre para continuar no ConectaCC.</p>

      <form noValidate onSubmit={onSubmit} className="mt-10 space-y-6">
        {problem?.kind === 'credentials' && <FormAlert variant="danger">E-mail ou senha incorretos.</FormAlert>}
        {problem?.kind === 'throttle' && <FormAlert variant="danger">{problem.message}</FormAlert>}
        {problem?.kind === 'network' && (
          <FormAlert
            variant="danger"
            action={
              <button type="button" onClick={() => onSubmit()} className="font-semibold underline underline-offset-2">
                Tentar novamente
              </button>
            }
          >
            Não foi possível conectar ao ConectaCC. Verifique sua conexão e tente novamente.
          </FormAlert>
        )}
        {registeredEmail && !problem && (
          <FormAlert variant="success">Conta criada. Entre com seu e-mail e senha.</FormAlert>
        )}

        <TextField
          label="E-mail institucional"
          type="email"
          autoComplete="email"
          placeholder="seuemail@..."
          error={errors.email?.message}
          {...register('email', {
            required: 'Informe um endereço de e-mail válido.',
            pattern: { value: EMAIL_PATTERN, message: 'Informe um endereço de e-mail válido.' },
          })}
        />
        <TextField
          label="Senha"
          type="password"
          autoComplete="current-password"
          error={errors.password?.message}
          {...register('password', { required: 'Informe sua senha.' })}
        />

        <Button type="submit" disabled={login.isPending} className="h-12 w-full text-base font-semibold">
          {login.isPending ? (
            <>
              <Loader2 className="size-5 animate-spin" aria-hidden />
              Entrando...
            </>
          ) : (
            'Entrar'
          )}
        </Button>
      </form>

      <hr className="my-10 border-slate-200" />
      <p className="text-center text-muted-foreground">
        Ainda não tem uma conta?{' '}
        <Link to="/cadastro" className="font-semibold text-action hover:underline">
          Cadastre-se
        </Link>
      </p>
    </AuthLayout>
  );
}
