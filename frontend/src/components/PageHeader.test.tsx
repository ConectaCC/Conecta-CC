import { render, screen } from '@testing-library/react';
import { Briefcase } from 'lucide-react';
import { createRef } from 'react';
import { expect, it } from 'vitest';
import { PageHeader } from './PageHeader';

it('com headingRef, o título recebe foco por código (tabIndex -1)', () => {
  const ref = createRef<HTMLHeadingElement>();
  render(<PageHeader icon={Briefcase} title="Oportunidades" description="Descrição" headingRef={ref} />);

  expect(screen.getByRole('heading', { level: 1, name: 'Oportunidades' })).toHaveAttribute('tabindex', '-1');
  expect(ref.current).toBe(screen.getByRole('heading', { level: 1 }));
});

it('sem headingRef, o título fica fora da ordem de foco', () => {
  render(<PageHeader icon={Briefcase} title="Oportunidades" description="Descrição" />);

  expect(screen.getByRole('heading', { level: 1, name: 'Oportunidades' })).not.toHaveAttribute('tabindex');
});
