import { Loader2 } from 'lucide-react';
import { FormAlert } from '@/components/FormAlert';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';

type Props = {
  open: boolean;
  title: string;
  description: string;
  confirmLabel: string;
  busyLabel: string;
  cancelLabel: string;
  busy: boolean;
  error: string | null;
  onConfirm: () => void;
  onCancel: () => void;
  onCloseAutoFocus?: (event: Event) => void;
};

export function ConfirmActionDialog({ open, title, description, confirmLabel, busyLabel, cancelLabel, busy, error, onConfirm, onCancel, onCloseAutoFocus }: Props) {
  return (
    <Dialog open={open} onOpenChange={(next) => !next && !busy && onCancel()}>
      <DialogContent showCloseButton={false} onCloseAutoFocus={onCloseAutoFocus} className="w-[calc(100%-2rem)] max-w-md gap-0 p-6 sm:max-w-md">
        <DialogTitle className="font-display text-xl font-bold text-foreground">{title}</DialogTitle>
        <DialogDescription className="mt-2 text-[15px] text-muted-foreground">{description}</DialogDescription>
        {error && (
          <div className="mt-4">
            <FormAlert variant="danger">{error}</FormAlert>
          </div>
        )}
        <div className="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
          <Button type="button" variant="outline" onClick={onCancel} disabled={busy} className="h-11 border-action px-5 text-[15px] font-semibold text-action">
            {cancelLabel}
          </Button>
          <Button type="button" onClick={onConfirm} disabled={busy} className="h-11 bg-danger px-5 text-[15px] font-semibold text-white hover:bg-danger/90">
            {busy && <Loader2 className="size-5 animate-spin" aria-hidden />}
            {busy ? busyLabel : confirmLabel}
          </Button>
        </div>
      </DialogContent>
    </Dialog>
  );
}
