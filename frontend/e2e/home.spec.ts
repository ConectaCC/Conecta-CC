import { expect, test } from '@playwright/test';
import { signIn } from './helpers';

// a moderador2 do DatabaseSeeder não tem disciplinas: a Home mostra o card "Complete seu perfil" ao lado
test('com o perfil incompleto, a Home não espreme o texto em telas largas', async ({ browser }) => {
  const page = await signIn(browser, 'moderador2');
  await expect(page.getByText('Complete seu perfil')).toBeVisible();

  for (const width of [390, 1024, 1280, 1440, 1920, 2400]) {
    await page.setViewportSize({ width, height: 1000 });
    const title = await page.getByRole('heading', { name: /Conecte-se/ }).boundingBox();
    const hubs = await page.getByRole('navigation', { name: 'Áreas do ConectaCC' }).boundingBox();

    expect(title?.width, `largura do título em ${width}px`).toBeGreaterThan(250);
    // os atalhos ficam abaixo do texto, nunca por cima dele
    expect(hubs?.y, `atalhos abaixo do texto em ${width}px`).toBeGreaterThan((title?.y ?? 0) + (title?.height ?? 0));
  }
});

test('com o perfil completo, a Home põe os atalhos ao lado do texto nas telas largas', async ({ browser }) => {
  const page = await signIn(browser, 'moderador', { width: 1440, height: 1000 });
  await expect(page.getByText('Complete seu perfil')).toHaveCount(0);

  const title = await page.getByRole('heading', { name: /Conecte-se/ }).boundingBox();
  const hubs = await page.getByRole('navigation', { name: 'Áreas do ConectaCC' }).boundingBox();
  expect(title?.width).toBeGreaterThan(250);
  expect(hubs?.x).toBeGreaterThan((title?.x ?? 0) + (title?.width ?? 0));
});
