import { ExternalLink, MoreHorizontal } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { ConfirmActionDialog } from '@/features/my-contents/ConfirmActionDialog';
import { useDeleteMaterial } from '@/features/my-contents/hooks';
import { ApiError } from '@/lib/api/errors';
import { hostOf, timeAgo } from '@/lib/format';
import type { Material } from './hooks';
import { ShareMaterialDialog } from './ShareMaterialDialog';

type Props = { material: Material; discipline: { id: number; name: string } };

export function MaterialCard({ material, discipline }: Props) {
  const host = hostOf(material.url);
  const when = material.published_at ? ` · ${timeAgo(material.published_at)}` : '';
  const [editing, setEditing] = useState(false);
  const [deleting, setDeleting] = useState(false);
  const [deleteError, setDeleteError] = useState<string | null>(null);
  const remove = useDeleteMaterial();

  async function confirmDelete() {
    setDeleteError(null);
    try {
      await remove.mutateAsync({ id: material.id, discipline });
      setDeleting(false);
    } catch (error) {
      setDeleteError(error instanceof ApiError && error.status === 409 ? error.message : 'Não foi possível concluir a ação. Tente novamente.');
    }
  }

  return (
    <article className="rounded-2xl border border-blue-200/70 bg-white p-6">
      <div className="flex items-start justify-between gap-3">
        <h3 className="min-w-0 font-display text-lg font-bold leading-snug [overflow-wrap:anywhere] text-foreground">{material.title}</h3>
        {material.is_mine && (
          // modal={false}: o diálogo aberto por um item do menu não herda o bloqueio de clique do menu
          <DropdownMenu modal={false}>
            <DropdownMenuTrigger asChild>
              <Button type="button" variant="ghost" size="icon" aria-label={`Ações do material ${material.title}`} className="-mt-1 -mr-2 size-11 shrink-0">
                <MoreHorizontal className="size-5" aria-hidden />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="min-w-36">
              <DropdownMenuItem className="text-[15px]" onSelect={() => setEditing(true)}>
                Editar
              </DropdownMenuItem>
              <DropdownMenuItem variant="destructive" className="text-[15px]" onSelect={() => setDeleting(true)}>
                Excluir
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        )}
      </div>
      {material.description && <p className="mt-2 text-base [overflow-wrap:anywhere] text-muted-foreground">{material.description}</p>}
      <p className="mt-2 text-[15px] text-muted-foreground">
        {material.author_name ? (
          <>
            Compartilhado por <span className="font-semibold text-foreground">{material.author_name}</span>
            {when}
          </>
        ) : (
          `Compartilhado pela comunidade${when}`
        )}
      </p>
      <p className="mt-4 flex flex-wrap items-center gap-x-3 gap-y-1">
        <a
          href={material.url}
          target="_blank"
          rel="noopener noreferrer"
          className="inline-flex items-center gap-2 font-semibold text-action hover:underline"
        >
          Acessar material
          <ExternalLink className="size-4" aria-hidden />
          <span className="sr-only">(abre em nova guia, {host})</span>
        </a>
        <span aria-hidden className="text-sm break-all text-muted-foreground">
          {host}
        </span>
      </p>

      {editing && (
        <ShareMaterialDialog
          discipline={discipline}
          open
          onOpenChange={setEditing}
          editing={{ id: material.id, title: material.title, description: material.description, url: material.url }}
        />
      )}
      <ConfirmActionDialog
        open={deleting}
        title="Excluir material?"
        description="O material deixará de aparecer na disciplina. Você ainda poderá consultá-lo em Meus conteúdos."
        confirmLabel="Excluir material"
        busyLabel="Excluindo…"
        cancelLabel="Cancelar"
        busy={remove.isPending}
        error={deleteError}
        onConfirm={confirmDelete}
        onCancel={() => {
          setDeleting(false);
          setDeleteError(null);
        }}
      />
    </article>
  );
}
