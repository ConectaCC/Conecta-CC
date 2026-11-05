import { CircleAlert, Eye, EyeOff } from 'lucide-react';
import { useId, useState, type InputHTMLAttributes, type ReactNode, type Ref } from 'react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

type Props = InputHTMLAttributes<HTMLInputElement> & {
  label: ReactNode;
  error?: ReactNode;
  hint?: string;
  visibilityLabel?: string;
  ref?: Ref<HTMLInputElement>;
};

export function TextField({ label, error, hint, visibilityLabel = 'senha', className, type = 'text', ref, ...props }: Props) {
  const id = useId();
  const [visible, setVisible] = useState(false);
  const isPassword = type === 'password';
  const describedBy = [error ? `${id}-error` : '', hint ? `${id}-hint` : ''].filter(Boolean).join(' ') || undefined;

  return (
    <div className="space-y-2">
      <Label htmlFor={id} className="text-[15px] font-medium text-foreground">
        {label}
      </Label>
      <div className="relative">
        <Input
          ref={ref}
          id={id}
          type={isPassword && visible ? 'text' : type}
          aria-invalid={error ? true : undefined}
          aria-describedby={describedBy}
          className={cn('h-12 bg-white px-4 text-base md:text-base', isPassword && 'pr-12', error && 'border-danger', className)}
          {...props}
        />
        {isPassword && (
          <button
            type="button"
            onClick={() => setVisible((current) => !current)}
            aria-label={`${visible ? 'Ocultar' : 'Mostrar'} ${visibilityLabel}`}
            aria-pressed={visible}
            className="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-md text-muted-foreground hover:text-foreground"
          >
            {visible ? <EyeOff className="size-5" aria-hidden /> : <Eye className="size-5" aria-hidden />}
          </button>
        )}
      </div>
      {error && (
        <p id={`${id}-error`} className="flex items-start gap-2 text-sm text-danger">
          <CircleAlert className="mt-0.5 size-4 shrink-0" aria-hidden />
          <span>{error}</span>
        </p>
      )}
      {hint && (
        <p id={`${id}-hint`} className="text-sm text-muted-foreground">
          {hint}
        </p>
      )}
    </div>
  );
}
