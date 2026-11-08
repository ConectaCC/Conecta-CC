import { expect, type Browser, type Page } from '@playwright/test';

export const ACCOUNTS = {
  aluno: 'aluno@academico.ifsul.edu.br',
  moderador: 'moderador@academico.ifsul.edu.br',
  // sem disciplinas no DatabaseSeeder: perfil incompleto
  moderador2: 'moderador2@academico.ifsul.edu.br',
  admin: 'admin@academico.ifsul.edu.br',
} as const;

// cada pessoa numa sessão própria, como em navegadores diferentes
export async function signIn(browser: Browser, who: keyof typeof ACCOUNTS, viewport = { width: 1440, height: 900 }): Promise<Page> {
  const page = await (await browser.newContext({ viewport })).newPage();
  await page.goto('/login');
  await page.getByLabel('E-mail institucional').fill(ACCOUNTS[who]);
  await page.getByLabel('Senha', { exact: true }).fill('password');
  await page.getByRole('button', { name: 'Entrar' }).click();
  await expect(page).toHaveURL(/\/home$/);
  return page;
}

// um título que não se repete entre execuções
export const unique = (prefix: string) => `${prefix} e2e ${Date.now()}`;

// aprova pela fila da Moderação o item com esse título
export async function approveInModeration(page: Page, title: string) {
  await page.goto('/moderacao');
  const item = page.getByRole('listitem').filter({ hasText: title });
  await item.getByRole('button', { name: 'Analisar' }).click();
  await page.getByRole('dialog').getByRole('button', { name: 'Aprovar', exact: true }).click();
  await page.getByRole('button', { name: 'Aprovar conteúdo' }).click();
  await expect(page.getByText('Conteúdo aprovado com sucesso.')).toBeVisible();
}
