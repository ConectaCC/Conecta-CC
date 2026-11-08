import { CircleAlert, Search } from 'lucide-react';
import { useEffect, useId, useRef, useState, type KeyboardEvent, type Ref } from 'react';
import { Chip } from '@/components/Chip';
import { assignRef } from '@/lib/assignRef';
import { cn } from '@/lib/utils';

type Option = { id: number; name: string };

type Props = {
  id: string;
  options: Option[];
  value: number[];
  onChange: (ids: number[]) => void;
  error?: string;
  describedBy?: string;
  inputRef?: Ref<HTMLInputElement>;
};

// busca sem diferenciar acentos nem maiúsculas ("calculo" encontra "Cálculo")
const normalize = (text: string) => text.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase();

export function DisciplinePicker({ id, options, value, onChange, error, describedBy, inputRef }: Props) {
  const listId = useId();
  const [query, setQuery] = useState('');
  const [open, setOpen] = useState(false);
  const [active, setActive] = useState(0);
  const listRef = useRef<HTMLUListElement>(null);
  const input = useRef<HTMLInputElement | null>(null);

  const selected = value.map((selectedId) => options.find((option) => option.id === selectedId)).filter((option) => option !== undefined);
  const results = options.filter((option) => !value.includes(option.id) && normalize(option.name).includes(normalize(query.trim())));
  const activeOption = open ? results[active] : undefined;

  useEffect(() => {
    listRef.current?.querySelector('[aria-selected="true"]')?.scrollIntoView({ block: 'nearest' });
  }, [active, open]);

  const select = (option: Option) => {
    onChange([...value, option.id]);
    setQuery('');
    setActive(0);
    setOpen(false);
  };

  const onKeyDown = (event: KeyboardEvent<HTMLInputElement>) => {
    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
      event.preventDefault();
      if (!open) {
        setOpen(true);
        return;
      }
      const step = event.key === 'ArrowDown' ? 1 : -1;
      setActive((current) => (results.length === 0 ? 0 : (current + step + results.length) % results.length));
    } else if (event.key === 'Enter') {
      event.preventDefault(); // Enter seleciona e não envia o formulário
      if (activeOption) select(activeOption);
    } else if (event.key === 'Escape' && open) {
      event.preventDefault();
      setOpen(false);
    }
  };

  return (
    <div className="space-y-3">
      <div className="relative">
        <Search className="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-muted-foreground" aria-hidden />
        <input
          ref={(element) => {
            input.current = element;
            assignRef(inputRef, element);
          }}
          id={id}
          role="combobox"
          aria-expanded={open}
          aria-controls={listId}
          aria-autocomplete="list"
          aria-activedescendant={activeOption ? `${listId}-${activeOption.id}` : undefined}
          aria-invalid={error ? true : undefined}
          aria-describedby={[describedBy, error ? `${id}-error` : ''].filter(Boolean).join(' ') || undefined}
          value={query}
          placeholder="Buscar disciplina..."
          autoComplete="off"
          onChange={(event) => {
            setQuery(event.target.value);
            setActive(0);
            setOpen(true);
          }}
          onFocus={() => setOpen(true)}
          onBlur={() => setOpen(false)}
          onKeyDown={onKeyDown}
          className={cn(
            'h-12 w-full rounded-lg border border-input bg-white pr-4 pl-12 text-base outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50',
            error && 'border-danger',
          )}
        />
        {open && (
          <div className="absolute inset-x-0 top-full z-20 mt-1 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg">
            <ul ref={listRef} id={listId} role="listbox" aria-label="Disciplinas" className="max-h-72 overflow-y-auto py-1">
              {results.map((option, index) => (
                // padrão combobox do WAI-ARIA: o teclado fica no input (setas/Enter), as opções não recebem foco
                // eslint-disable-next-line jsx-a11y/click-events-have-key-events
                <li
                  key={option.id}
                  id={`${listId}-${option.id}`}
                  role="option"
                  aria-selected={index === active}
                  onMouseDown={(event) => event.preventDefault()} // mantém o foco no campo
                  onClick={() => select(option)}
                  onMouseEnter={() => setActive(index)}
                  className={cn('cursor-pointer px-4 py-3 text-base text-foreground', index === active && 'bg-[#e3eefb]')}
                >
                  {option.name}
                </li>
              ))}
            </ul>
            {results.length === 0 && <p className="px-4 py-3 text-base text-muted-foreground">Nenhuma disciplina encontrada.</p>}
          </div>
        )}
      </div>

      {error && (
        <p id={`${id}-error`} className="flex items-start gap-2 text-sm text-danger">
          <CircleAlert className="mt-0.5 size-4 shrink-0" aria-hidden />
          <span>{error}</span>
        </p>
      )}

      {selected.length > 0 ? (
        <div className="space-y-2">
          <p className="text-sm text-muted-foreground">Selecionadas ({selected.length})</p>
          <div className="flex flex-wrap gap-2">
            {selected.map((option) => (
              <Chip
                key={option.id}
                onRemove={() => {
                  onChange(value.filter((item) => item !== option.id));
                  input.current?.focus(); // o botão some; o foco volta à busca
                }}
                removeLabel={`Remover ${option.name}`}
              >
                {option.name}
              </Chip>
            ))}
          </div>
        </div>
      ) : (
        <p className="text-sm text-muted-foreground">Nenhuma disciplina selecionada.</p>
      )}
    </div>
  );
}
