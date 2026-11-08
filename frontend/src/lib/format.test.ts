import { describe, expect, it } from 'vitest';
import { formatDate, formatDateTime, hostOf, pathOf, timeAgo } from './format';

const NOW = new Date('2026-11-08T15:00:00Z');

describe('timeAgo', () => {
  it.each([
    ['2026-11-08T14:59:30Z', 'agora'],
    ['2026-11-08T14:58:00Z', 'há 2 minutos'],
    ['2026-11-08T13:00:00Z', 'há 2 horas'],
    ['2026-11-07T15:00:00Z', 'ontem'],
    ['2026-10-25T15:00:00Z', 'há 2 semanas'],
    ['2026-09-01T15:00:00Z', 'há 2 meses'],
    ['2024-11-01T15:00:00Z', 'há 2 anos'],
  ])('%s vira "%s"', (iso, expected) => {
    expect(timeAgo(iso, NOW)).toBe(expected);
  });

  it('usa a unidade cheia no limite exato', () => {
    expect(timeAgo('2026-11-08T14:59:00Z', NOW)).toBe('há 1 minuto');
    expect(timeAgo('2026-11-08T14:00:00Z', NOW)).toBe('há 1 hora');
  });

  it('não mostra "em 2 minutos" quando o relógio do aparelho está atrasado', () => {
    expect(timeAgo('2026-11-08T15:02:00Z', NOW)).toBe('agora');
  });
});

describe('datas', () => {
  it('mostra no fuso do câmpus', () => {
    expect(formatDate('2026-11-08T02:30:00Z')).toBe('07/11/2026');
    expect(formatDateTime('2026-11-08T17:10:00Z')).toBe('08/11/2026 às 14:10');
  });
});

describe('links', () => {
  it('mostra o host sem www, e o texto como veio quando não é link', () => {
    expect(hostOf('https://www.site.com/a')).toBe('site.com');
    expect(hostOf('https://sub.site.com/a')).toBe('sub.site.com');
    expect(hostOf('não é link')).toBe('não é link');
  });

  it('mostra caminho, busca e âncora, ou nada quando não é link', () => {
    expect(pathOf('https://site.com/a/b?c=1#d')).toBe('/a/b?c=1#d');
    expect(pathOf('não é link')).toBe('');
  });
});
