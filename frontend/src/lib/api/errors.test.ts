import { expect, it } from 'vitest';
import { ApiError, call, NetworkError } from './errors';

const response = (status: number) => new Response(null, { status });

it('devolve os dados quando a resposta é ok', async () => {
  await expect(call(Promise.resolve({ data: { id: 1 }, response: response(200) }))).resolves.toEqual({ id: 1 });
});

it('transforma o erro da API em ApiError, com mensagem, campos e código', async () => {
  const error = await call(
    Promise.resolve({ error: { message: 'Já existe.', errors: { url: ['Repetido.'] }, code: 'link_taken' }, response: response(409) }),
  ).catch((e: unknown) => e);

  expect(error).toBeInstanceOf(ApiError);
  expect(error).toMatchObject({ status: 409, message: 'Já existe.', fieldErrors: { url: ['Repetido.'] }, code: 'link_taken' });
});

it('aceita erro sem corpo', async () => {
  const error = await call(Promise.resolve({ response: response(500) })).catch((e: unknown) => e);

  expect(error).toMatchObject({ status: 500, message: '', fieldErrors: {}, code: undefined });
});

it('falha de rede vira NetworkError', async () => {
  await expect(call(Promise.reject(new TypeError('Failed to fetch')))).rejects.toBeInstanceOf(NetworkError);
});
