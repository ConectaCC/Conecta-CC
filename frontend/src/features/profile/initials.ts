// mesma regra de User::initials() no backend, para a prévia ao vivo durante a edição
export function initialsOf(displayName: string): string {
  const parts = displayName.trim().split(/\s+/).filter(Boolean);
  const [first = '', ...rest] = parts;
  const last = rest.at(-1) ?? '';
  return (first.slice(0, 1) + last.slice(0, 1)).toUpperCase();
}
