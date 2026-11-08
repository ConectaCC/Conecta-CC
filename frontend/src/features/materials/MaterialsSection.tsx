import { Loader2 } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { useLocation } from 'react-router';
import { FormAlert } from '@/components/FormAlert';
import { Button } from '@/components/ui/button';
import type { Discipline } from '@/features/profile/hooks';
import { useDisciplineMaterials } from './hooks';
import { MaterialCard } from './MaterialCard';
import { ShareMaterialDialog } from './ShareMaterialDialog';

export function MaterialsSection({ discipline }: { discipline: Discipline }) {
  const materials = useDisciplineMaterials(discipline.id);
  const [sharing, setSharing] = useState(false);
  const [notice, setNotice] = useState<string | null>(null);
  const noticeRef = useRef<HTMLDivElement>(null);
  const items = materials.data?.pages.flatMap((page) => page.data) ?? [];

  // vindo do mural da Comunidade (#material-<id>): carrega até achar, rola e destaca, como em Meus conteúdos
  const { hash } = useLocation();
  const target = /^#material-\d+$/.test(hash) ? Number(hash.slice('#material-'.length)) : null;
  const found = target !== null && items.some((item) => item.id === target);
  const scrolled = useRef<number | null>(null);

  useEffect(() => {
    if (target === null || found || !materials.hasNextPage || materials.isFetchingNextPage || materials.isFetchNextPageError) return;
    materials.fetchNextPage();
  }, [target, found, materials]);

  useEffect(() => {
    if (!found || scrolled.current === target) return;
    scrolled.current = target;
    const card = document.getElementById(`material-${target}`);
    card?.scrollIntoView({ block: 'center' });
    // destaque passageiro: só visual, por isso direto no elemento e não em estado
    card?.classList.add('ring-4', 'ring-glow');
    const timer = setTimeout(() => card?.classList.remove('ring-4', 'ring-glow'), 2000);
    return () => clearTimeout(timer);
  }, [found, target]);

  useEffect(() => {
    if (notice) noticeRef.current?.focus();
  }, [notice]);

  const retry = (
    <button
      type="button"
      onClick={() => (materials.isFetchNextPageError ? materials.fetchNextPage() : materials.refetch())}
      className="font-semibold underline underline-offset-2"
    >
      Tentar novamente
    </button>
  );

  const shareButton = (
    <Button type="button" onClick={() => setSharing(true)} className="h-11 px-5 text-[15px] font-semibold">
      Compartilhar material
    </Button>
  );

  return (
    <section aria-labelledby="materiais" className="rounded-2xl border border-blue-200/70 bg-white p-7">
      <div className="flex flex-wrap items-start justify-between gap-4">
        <div>
          <h2 id="materiais" className="font-display text-2xl font-bold text-foreground">
            Materiais compartilhados
          </h2>
          <p className="mt-1 text-[15px] text-muted-foreground">Mais recentes primeiro.</p>
        </div>
        {items.length > 0 && shareButton}
      </div>

      {materials.isPending && (
        <p aria-busy="true" className="mt-5 text-base text-muted-foreground">
          Carregando materiais…
        </p>
      )}

      {notice && (
        <div ref={noticeRef} tabIndex={-1} className="mt-5 outline-none">
          <FormAlert variant="success" onDismiss={() => setNotice(null)}>
            {notice}
          </FormAlert>
        </div>
      )}

      {/* também com cards na tela (recarga ou "Mostrar mais" que falhou): o aviso aparece e os cards ficam */}
      {materials.isError && (
        <div className="mt-5">
          <FormAlert variant="warning" action={retry}>
            Não foi possível carregar os materiais agora.
          </FormAlert>
        </div>
      )}

      {materials.data && items.length === 0 && (
        <div className="mt-5">
          <h3 className="font-display text-lg font-bold text-foreground">Ainda não há materiais compartilhados</h3>
          <p className="mt-2 text-base text-muted-foreground">
            Se você conhece um material que pode ajudar outros estudantes, compartilhe com a comunidade.
          </p>
          <div className="mt-5">{shareButton}</div>
        </div>
      )}

      {items.length > 0 && (
        <div className="mt-5 space-y-4">
          {items.map((material) => (
            <MaterialCard key={material.id} material={material} discipline={discipline} onDeleted={() => setNotice('Material excluído.')} />
          ))}
        </div>
      )}

      {materials.hasNextPage && !materials.isFetchNextPageError && (
        <div className="mt-5 flex justify-center">
          <Button
            type="button"
            variant="outline"
            onClick={() => materials.fetchNextPage()}
            disabled={materials.isFetchingNextPage}
            className="h-11 border-action px-5 text-[15px] font-semibold text-action"
          >
            {materials.isFetchingNextPage && <Loader2 className="size-5 animate-spin" aria-hidden />}
            Mostrar mais
          </Button>
        </div>
      )}

      <ShareMaterialDialog discipline={discipline} open={sharing} onOpenChange={setSharing} />
    </section>
  );
}
