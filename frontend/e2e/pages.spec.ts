import { expect, test } from '@playwright/test';
import { signIn } from './helpers';

const PAGES = ['/home', '/estudos', '/estudos/algoritmos-i', '/oportunidade', '/comunidade', '/perfil', '/perfil/meus-conteudos', '/moderacao', '/configuracoes'];

for (const viewport of [
  { width: 390, height: 844 },
  { width: 1440, height: 900 },
]) {
  test(`as páginas abrem sem erro, sem pedido externo e sem rolagem lateral em ${viewport.width}px`, async ({ browser }) => {
    const page = await signIn(browser, 'moderador', viewport);
    const problems: string[] = [];
    page.on('pageerror', (error) => problems.push(`erro: ${error.message}`));
    page.on('response', (response) => {
      if (response.status() >= 400) problems.push(`${response.status()} ${response.url()}`);
    });
    page.on('request', (request) => {
      if (!/^(http:\/\/localhost|data:|blob:)/.test(request.url())) problems.push(`externo: ${request.url()}`);
    });

    for (const path of PAGES) {
      await page.goto(path);
      await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
      const overflow = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
      expect(overflow, `rolagem lateral em ${path}`).toBeLessThanOrEqual(0);
    }

    expect(problems).toEqual([]);
  });
}
