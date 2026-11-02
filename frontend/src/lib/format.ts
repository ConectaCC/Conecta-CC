const relative = new Intl.RelativeTimeFormat('pt-BR', { numeric: 'auto' });

const UNITS: [Intl.RelativeTimeFormatUnit, number][] = [
  ['year', 31_536_000],
  ['month', 2_592_000],
  ['week', 604_800],
  ['day', 86_400],
  ['hour', 3_600],
  ['minute', 60],
];

// "há 2 dias", "ontem", "agora": a maior unidade cheia
export function timeAgo(iso: string, now = new Date()): string {
  const seconds = (new Date(iso).getTime() - now.getTime()) / 1000;
  for (const [unit, size] of UNITS) {
    if (Math.abs(seconds) >= size) return relative.format(Math.trunc(seconds / size), unit);
  }
  return relative.format(0, 'second');
}

// "25/10/2026"
export function formatDate(iso: string): string {
  return new Date(iso).toLocaleDateString('pt-BR');
}

// "25/10/2026 às 14:10"
export function formatDateTime(iso: string): string {
  const date = new Date(iso);
  return `${date.toLocaleDateString('pt-BR')} às ${date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}`;
}

// dado antigo ou inválido não pode derrubar a página: devolve o texto como veio
export function hostOf(url: string): string {
  try {
    return new URL(url).hostname.replace(/^www\./, '');
  } catch {
    return url;
  }
}

export function pathOf(url: string): string {
  try {
    const parsed = new URL(url);
    return parsed.pathname + parsed.search + parsed.hash;
  } catch {
    return '';
  }
}
