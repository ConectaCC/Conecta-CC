import { cn } from '@/lib/utils';
import { FILTERS, type MyFilter } from './hooks';

export function StatusFilters({ value, onChange }: { value: MyFilter; onChange: (next: MyFilter) => void }) {
  return (
    <div role="group" aria-label="Filtrar por situação" className="mt-4 flex flex-wrap gap-2">
      {FILTERS.map((filter) => (
        <button
          key={filter.value}
          type="button"
          aria-pressed={value === filter.value}
          onClick={() => onChange(filter.value)}
          className={cn(
            'h-10 rounded-full border px-4 text-[15px] font-medium',
            value === filter.value ? 'border-navy-900 bg-navy-900 text-white' : 'border-blue-200 bg-white text-foreground hover:bg-ice',
          )}
        >
          {filter.label}
        </button>
      ))}
    </div>
  );
}
