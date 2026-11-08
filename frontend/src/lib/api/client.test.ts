import { afterEach, expect, it } from 'vitest';
import { readCookie } from './client';

afterEach(() => {
  document.cookie = 'XSRF-TOKEN=; max-age=0';
  document.cookie = 'outro=; max-age=0';
});

it('lê o cookie pelo nome exato e decodifica o valor', () => {
  document.cookie = 'outro=1';
  document.cookie = 'XSRF-TOKEN=abc%3D%3D';

  expect(readCookie('XSRF-TOKEN')).toBe('abc==');
  expect(readCookie('TOKEN')).toBeNull();
});

it('devolve null sem o cookie', () => {
  expect(readCookie('XSRF-TOKEN')).toBeNull();
});
