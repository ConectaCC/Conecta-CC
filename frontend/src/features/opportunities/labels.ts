// os tipos do RF-43 e da decisão §22; os valores vêm da API
export const TYPE_LABELS: Record<string, string> = {
  ESTAGIO: 'Estágio',
  EMPREGO: 'Emprego',
  EVENTO: 'Evento',
  PROJETO: 'Projeto',
  OUTRO: 'Outro',
};

export const MODALITY_LABELS: Record<string, string> = {
  REMOTO: 'Remoto',
  HIBRIDO: 'Híbrido',
  PRESENCIAL: 'Presencial',
};

// "2026-11-20" → "20/11/2026", sem passar por Date: a data não tem fuso e não pode mudar de dia
export function formatDay(day: string): string {
  const [year, month, date] = day.split('-');
  return `${date}/${month}/${year}`;
}

// hoje no formato do campo de data, no relógio do aparelho
export function todayInput(): string {
  const now = new Date();
  return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
}
