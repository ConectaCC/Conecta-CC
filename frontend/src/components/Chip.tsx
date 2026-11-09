import { X } from 'lucide-react';
import type { ReactNode } from 'react';

export function Chip({ children, onRemove, removeLabel }: { children: ReactNode; onRemove?: () => void; removeLabel?: string }) {
  return (
    <span className="inline-flex items-center gap-1.5 rounded-full bg-[#d6e4f7] px-3 py-1 text-[15px] text-navy-900">
      {children}
      {onRemove && (
        <button
          type="button"
          onClick={onRemove}
          aria-label={removeLabel}
          className="relative -mr-1 grid size-6 shrink-0 place-items-center rounded-full after:absolute after:-inset-2.5 hover:bg-white/60"
        >
          <X className="size-3.5" aria-hidden />
        </button>
      )}
    </span>
  );
}
