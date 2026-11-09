import { CircleAlert } from 'lucide-react';
import { useId, useRef, useState, type KeyboardEvent, type Ref } from 'react';
import { assignRef } from '@/lib/assignRef';
import { Chip } from '@/components/Chip';
import { cn } from '@/lib/utils';

type Props = {
  label: string;
  hint: string;
  placeholder: string;
  value: string[];
  onChange: (next: string[]) => void;
  error?: string;
  inputRef?: Ref<HTMLInputElement>;
};

export function TagInput({ label, hint, placeholder, value, onChange, error, inputRef }: Props) {
  const id = useId();
  const [text, setText] = useState('');
  const input = useRef<HTMLInputElement | null>(null);
  const describedBy = [`${id}-hint`, error ? `${id}-error` : ''].filter(Boolean).join(' ');

  const add = () => {
    const tag = text.trim();
    setText('');
    if (tag === '' || value.some((existing) => existing.toLowerCase() === tag.toLowerCase())) return;
    onChange([...value, tag]);
  };

  const onKeyDown = (event: KeyboardEvent<HTMLInputElement>) => {
    if (event.key !== 'Enter') return;
    event.preventDefault(); // Enter adiciona a tag e não envia o formulário
    add();
  };

  return (
    <div className="space-y-2">
      <div className="space-y-1">
        <label htmlFor={id} className="text-[15px] font-medium text-foreground">
          {label}
        </label>
        <p id={`${id}-hint`} className="text-sm text-muted-foreground">
          {hint}
        </p>
      </div>
      <div
        className={cn(
          'flex min-h-12 flex-wrap items-center gap-2 rounded-lg border border-input bg-white px-2 py-1.5 focus-within:border-ring focus-within:ring-3 focus-within:ring-ring/50',
          error && 'border-danger',
        )}
      >
        {value.map((tag) => (
          <Chip
            key={tag}
            onRemove={() => {
              onChange(value.filter((item) => item !== tag));
              input.current?.focus(); // o botão some; o foco volta ao campo
            }}
            removeLabel={`Remover ${tag}`}
          >
            {tag}
          </Chip>
        ))}
        <input
          ref={(element) => {
            input.current = element;
            assignRef(inputRef, element);
          }}
          id={id}
          value={text}
          onChange={(event) => setText(event.target.value)}
          onKeyDown={onKeyDown}
          onBlur={add} // texto digitado sem Enter não se perde ao salvar ou sair do campo
          placeholder={placeholder}
          aria-invalid={error ? true : undefined}
          aria-describedby={describedBy}
          className="h-9 w-0 min-w-40 flex-1 bg-transparent px-2 text-base outline-none placeholder:text-muted-foreground"
        />
      </div>
      {error && (
        <p id={`${id}-error`} className="flex items-start gap-2 text-sm text-danger">
          <CircleAlert className="mt-0.5 size-4 shrink-0" aria-hidden />
          <span>{error}</span>
        </p>
      )}
    </div>
  );
}
