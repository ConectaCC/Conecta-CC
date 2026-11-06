import type { Role } from './hooks';

// só decide o que aparece na tela; quem autoriza de verdade é a API
export function canModerate(role: Role): boolean {
  return role === 'MODERADOR' || role === 'ADMINISTRADOR';
}

export function isAdmin(role: Role): boolean {
  return role === 'ADMINISTRADOR';
}
