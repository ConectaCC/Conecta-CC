import { describe, expect, it, vi } from 'vitest';
import { formatDay, MODALITY_LABELS, todayInput, TYPE_LABELS } from './labels';

describe('formatDay', () => {
  it('troca a ordem sem passar por Date, para a data não mudar de dia', () => {
    expect(formatDay('2026-11-20')).toBe('20/11/2026');
    expect(formatDay('2027-01-01')).toBe('01/01/2027');
  });
});

describe('todayInput', () => {
  it('usa o dia do relógio do aparelho, com zero à esquerda', () => {
    vi.useFakeTimers();
    vi.setSystemTime(new Date(2026, 0, 5, 23, 30));
    expect(todayInput()).toBe('2026-01-05');
  });

  it('não pula para o dia seguinte em UTC depois das 21h no câmpus', () => {
    vi.useFakeTimers();
    vi.setSystemTime(new Date('2026-11-09T01:00:00Z')); // 22h do dia 08 em Brasília
    expect(todayInput()).toBe('2026-11-08');
  });
});

describe('rótulos', () => {
  it('tem os cinco tipos e as três modalidades da API', () => {
    expect(TYPE_LABELS).toEqual({ ESTAGIO: 'Estágio', EMPREGO: 'Emprego', EVENTO: 'Evento', PROJETO: 'Projeto', OUTRO: 'Outro' });
    expect(MODALITY_LABELS).toEqual({ REMOTO: 'Remoto', HIBRIDO: 'Híbrido', PRESENCIAL: 'Presencial' });
  });
});
