import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';

type Props = { open: boolean; onContinue: () => void; onDiscard: () => void; description?: string; continueLabel?: string };

export function DiscardChangesDialog({
  open,
  onContinue,
  onDiscard,
  description = 'As alterações feitas no seu perfil ainda não foram salvas.',
  continueLabel = 'Continuar editando',
}: Props) {
  return (
    <Dialog open={open} onOpenChange={(next) => !next && onContinue()}>
      <DialogContent showCloseButton={false} className="w-[calc(100%-2rem)] max-w-md gap-0 p-6 sm:max-w-md">
        <DialogTitle className="font-display text-xl font-bold text-foreground">Descartar alterações?</DialogTitle>
        <DialogDescription className="mt-2 text-[15px] text-muted-foreground">
          {description}
        </DialogDescription>
        <div className="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
          <Button type="button" variant="outline" onClick={onContinue} className="h-11 border-action px-5 text-[15px] font-semibold text-action">
            {continueLabel}
          </Button>
          <Button type="button" onClick={onDiscard} className="h-11 bg-danger px-5 text-[15px] font-semibold text-white hover:bg-danger/90">
            Descartar alterações
          </Button>
        </div>
      </DialogContent>
    </Dialog>
  );
}
