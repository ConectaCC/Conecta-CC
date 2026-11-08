import { defineConfig, devices } from '@playwright/test';

// ponta a ponta contra o ambiente local de pé (make up), com as contas do DatabaseSeeder
export default defineConfig({
  testDir: './e2e',
  // os testes criam conteúdo e dependem da Moderação: um de cada vez
  workers: 1,
  timeout: 60_000,
  reporter: 'list',
  // fora da pasta do projeto: o container roda como root e não deixa arquivos no repositório
  outputDir: '/tmp/conectacc-e2e',
  use: {
    baseURL: process.env.E2E_BASE_URL ?? 'http://localhost:5173',
    locale: 'pt-BR',
    timezoneId: 'America/Sao_Paulo',
    trace: 'retain-on-failure',
  },
  projects: [{ name: 'chromium', use: { ...devices['Desktop Chrome'] } }],
});
