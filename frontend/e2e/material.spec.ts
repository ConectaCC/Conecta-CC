import { expect, test } from '@playwright/test';
import { approveInModeration, signIn, unique } from './helpers';

test('material anônimo: some o nome na área pública, e só o ADMINISTRADOR vê quem enviou', async ({ browser }) => {
  const title = unique('Lista anônima');
  const student = await signIn(browser, 'aluno');

  await student.goto('/estudos/algoritmos-i');
  await student.getByRole('button', { name: 'Compartilhar material' }).click();
  const dialog = student.getByRole('dialog');
  await dialog.getByLabel('Título *').fill(title);
  await dialog.getByLabel('Link *').fill(`example.com/${Date.now()}`);
  await dialog.getByLabel('Compartilhar anonimamente').check();
  await dialog.getByRole('button', { name: 'Enviar para análise' }).click();
  await expect(student.getByText('Material enviado para análise')).toBeVisible();

  await approveInModeration(await signIn(browser, 'moderador'), title);

  await student.goto('/estudos/algoritmos-i');
  const card = student.getByRole('article').filter({ hasText: title });
  await expect(card.getByText(/Compartilhado pela comunidade/)).toBeVisible();
  await expect(card.locator('a[href^="/estudantes"]')).toHaveCount(0);

  await student.goto('/comunidade');
  const entry = student.getByRole('article').filter({ hasText: title });
  await expect(entry).toContainText(`Comunidade compartilhou “${title}”`);
  await expect(entry.locator('a[href^="/estudantes"], a[href="/perfil"]')).toHaveCount(0);

  const admin = await signIn(browser, 'admin');
  await admin.goto('/admin/auditoria');
  const row = admin.getByRole('row').filter({ hasText: 'Material enviado' }).filter({ hasText: title });
  await expect(row).toContainText('Ana Maria Aluna');
  await expect(row).toContainText('Enviado anonimamente');
});
