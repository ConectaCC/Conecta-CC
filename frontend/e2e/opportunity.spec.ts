import { expect, test } from '@playwright/test';
import { approveInModeration, signIn, unique } from './helpers';

test('oportunidade indicada aparece depois da aprovação, sem mostrar quem indicou', async ({ browser }) => {
  const title = unique('Estágio');
  const student = await signIn(browser, 'aluno');

  await student.goto('/oportunidade');
  await student.getByRole('button', { name: 'Adicionar oportunidade' }).click();
  const dialog = student.getByRole('dialog');
  await dialog.getByLabel('Tipo *').selectOption('ESTAGIO');
  await dialog.getByLabel('Título *').fill(title);
  await dialog.getByLabel('Descrição curta *').fill('Vaga para estudantes de CC.');
  await dialog.getByLabel('Link *').fill(`example.com/vaga-${Date.now()}`);
  await dialog.getByLabel('Remoto').check();
  await dialog.getByRole('button', { name: 'Enviar para análise' }).click();
  await expect(student.getByText(/Você receberá uma notificação quando sua indicação for analisada/)).toBeVisible();

  await approveInModeration(await signIn(browser, 'moderador'), title);

  await student.goto('/oportunidade');
  const card = student.getByRole('article').filter({ hasText: title });
  await expect(card).toContainText('Estágio');
  await expect(card).toContainText('Remoto');
  await expect(card).not.toContainText('Ana Maria Aluna');

  await student.goto('/comunidade');
  await expect(student.getByRole('article').filter({ hasText: title })).toContainText('Comunidade adicionou uma nova oportunidade em Estágio');
});
