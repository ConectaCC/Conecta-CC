import { useCallback, useEffect, useState } from 'react';
import { useBlocker, type Location } from 'react-router';
import { useLeaveGuard } from './leaveGuard';

// pergunta "Descartar alterações?" em qualquer saída: outra página, voltar do navegador, Sair e fechar a aba.
// /login fica livre: o Sair já confirmou antes do logout e a sessão expirada não pergunta.
export function useUnsavedChangesGuard(isDirty: boolean, isFreeExit?: (current: Location, next: Location) => boolean) {
  const { setLeaveCheck } = useLeaveGuard();
  const [confirm, setConfirm] = useState<((discard: boolean) => void) | null>(null);

  const askDiscard = useCallback(
    () =>
      new Promise<boolean>((resolve) => {
        setConfirm(() => (discard: boolean) => {
          setConfirm(null);
          resolve(discard);
        });
      }),
    [],
  );

  const blocker = useBlocker(
    ({ currentLocation, nextLocation }) => isDirty && nextLocation.pathname !== '/login' && !isFreeExit?.(currentLocation, nextLocation),
  );

  useEffect(() => {
    if (blocker.state !== 'blocked') return;
    void askDiscard().then((discard) => (discard ? blocker.proceed() : blocker.reset()));
  }, [blocker, askDiscard]);

  useEffect(() => {
    setLeaveCheck(isDirty ? askDiscard : null);
    return () => setLeaveCheck(null);
  }, [isDirty, askDiscard, setLeaveCheck]);

  useEffect(() => {
    if (!isDirty) return;
    const warn = (event: BeforeUnloadEvent) => event.preventDefault();
    window.addEventListener('beforeunload', warn);
    return () => window.removeEventListener('beforeunload', warn);
  }, [isDirty]);

  return {
    askDiscard,
    dialogProps: { open: confirm !== null, onContinue: () => confirm?.(false), onDiscard: () => confirm?.(true) },
  };
}
