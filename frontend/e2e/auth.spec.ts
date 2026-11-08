import { expect, test } from '@playwright/test';
import { ACCOUNTS, signIn } from './helpers';

test('senha errada mostra a mensagem única e não entra', async ({ page }) => {
  await page.goto('/login');
  await page.getByLabel('E-mail institucional').fill(ACCOUNTS.aluno);
  await page.getByLabel('Senha', { exact: true }).fill('senha-errada');
  await page.getByRole('button', { name: 'Entrar' }).click();

  await expect(page.getByText('E-mail ou senha incorretos.')).toBeVisible();
  await expect(page).toHaveURL(/\/login$/);
});

test('entra, vê a saudação e sai sem poder voltar para a área logada', async ({ browser }) => {
  const page = await signIn(browser, 'aluno');
  await expect(page.getByRole('heading', { name: /^Olá/ })).toBeVisible();

  await page.getByRole('button', { name: 'Sair' }).click();
  await expect(page).toHaveURL(/\/login$/);
  await page.goto('/home');
  await expect(page).toHaveURL(/\/login$/);
});
