import type { LucideIcon } from 'lucide-react';
import type { ReactNode, Ref } from 'react';

type Props = {
  icon: LucideIcon;
  title: string;
  description: ReactNode;
  // conteúdo à direita: recursos, card ou ações da página
  children?: ReactNode;
  headingRef?: Ref<HTMLHeadingElement>;
};

// cabeçalho das páginas internas: faixa azul com onda, ícone, título e subtítulo (decisoes.md §73)
export function PageHeader({ icon: Icon, title, description, children, headingRef }: Props) {
  return (
    <header className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#e3eefb] via-[#edf4fc] to-[#f6f9fe] px-6 py-7 sm:px-8">
      <svg aria-hidden viewBox="0 0 1200 120" preserveAspectRatio="none" className="pointer-events-none absolute inset-x-0 bottom-0 h-16 w-full text-white/60">
        <path d="M0 70 C 200 20, 380 120, 600 70 S 1000 20, 1200 60 L 1200 120 L 0 120 Z" fill="currentColor" />
      </svg>
      <div className="relative flex flex-col gap-6 xl:flex-row xl:items-center">
        <div className="flex min-w-0 flex-1 items-center gap-5">
          <span aria-hidden className="hidden size-20 shrink-0 place-items-center rounded-2xl bg-[#d6e4f7] text-action sm:grid">
            <Icon className="size-10" />
          </span>
          <div className="min-w-0">
            <h1 ref={headingRef} tabIndex={headingRef ? -1 : undefined} className="font-display text-4xl font-bold text-foreground outline-none">
              {title}
            </h1>
            <p className="mt-2 text-lg text-muted-foreground">{description}</p>
          </div>
        </div>
        {children && (
          <>
            <span aria-hidden className="hidden w-px self-stretch bg-blue-200/70 xl:block" />
            <div className="min-w-0 shrink-0">{children}</div>
          </>
        )}
      </div>
    </header>
  );
}
