import { expect, it } from 'vitest';
import { initialsOf } from './initials';

it.each([
  ['Ana Silva', 'AS'],
  ['  ana   maria   silva ', 'AS'],
  ['Ana', 'A'],
  ['élio souza', 'ÉS'],
  ['   ', ''],
  ['', ''],
])('"%s" vira "%s"', (name, expected) => {
  expect(initialsOf(name)).toBe(expected);
});
