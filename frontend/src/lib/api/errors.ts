export type FieldErrors = Record<string, string[]>;

export class ApiError extends Error {
  readonly status: number;
  readonly fieldErrors: FieldErrors;
  // código estável dos 409 de regra (ex.: "link_taken"); o front decide por ele, não pelo texto
  readonly code: string | undefined;

  constructor(status: number, message: string, fieldErrors: FieldErrors = {}, code?: string) {
    super(message);
    this.status = status;
    this.fieldErrors = fieldErrors;
    this.code = code;
  }
}

export class NetworkError extends Error {}

type FetchResult<T> = { data?: T; error?: unknown; response: Response };

export async function call<T>(request: Promise<FetchResult<T>>): Promise<T> {
  let result: FetchResult<T>;
  try {
    result = await request;
  } catch {
    throw new NetworkError('network');
  }
  if (result.response.ok) return result.data as T;
  const body = (result.error ?? {}) as { message?: string; errors?: FieldErrors; code?: string };
  throw new ApiError(result.response.status, body.message ?? '', body.errors ?? {}, body.code);
}
