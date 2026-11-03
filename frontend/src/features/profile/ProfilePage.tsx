import { PencilLine } from 'lucide-react';
import { useState } from 'react';
import { Link, useSearchParams } from 'react-router';
import { FormAlert } from '@/components/FormAlert';
import { Skeleton } from '@/components/Skeleton';
import { Button } from '@/components/ui/button';
import { useDisciplines, useProfile } from './hooks';
import { ProfileForm } from './ProfileForm';
import { ProfileView } from './ProfileView';

export function ProfilePage() {
  const [params, setParams] = useSearchParams();
  const editing = params.has('editar');
  const profile = useProfile();
  const disciplines = useDisciplines();
  const [saved, setSaved] = useState(false);

  const failed = profile.isError || disciplines.isError;
  const ready = profile.data && disciplines.data;

  return (
    <div>
      <header className="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
        <div>
          <h1 className="font-display text-4xl font-bold text-foreground">Perfil</h1>
          <p className="mt-2 text-lg text-muted-foreground">Atualize as informações que ajudam a personalizar sua experiência no ConectaCC.</p>
        </div>
        {editing ? (
          <span className="self-start rounded-full bg-[#d6e4f7] px-3 py-1 text-sm font-semibold text-navy-900 sm:mt-3">Editando</span>
        ) : (
          ready && (
            <div className="flex flex-wrap gap-3 self-start">
              <Button asChild variant="outline" className="h-12 border-action px-5 text-base font-semibold text-action">
                <Link to="/perfil/meus-conteudos">Meus conteúdos</Link>
              </Button>
              <Button asChild className="h-12 px-5 text-base font-semibold">
                <Link to="/perfil?editar" replace onClick={() => setSaved(false)}>
                  <PencilLine className="size-5" aria-hidden />
                  Editar perfil
                </Link>
              </Button>
            </div>
          )
        )}
      </header>

      <div className="mt-8 space-y-6">
        {saved && !editing && (
          <FormAlert variant="success" onDismiss={() => setSaved(false)}>
            Perfil atualizado com sucesso.
          </FormAlert>
        )}

        {failed && (
          <FormAlert
            variant="warning"
            action={
              <button
                type="button"
                onClick={() => {
                  profile.refetch();
                  disciplines.refetch();
                }}
                className="font-semibold underline underline-offset-2"
              >
                Tentar novamente
              </button>
            }
          >
            Não foi possível carregar algumas informações agora. Tente novamente.
          </FormAlert>
        )}

        {!ready && !failed && (
          <div className="space-y-6" aria-busy="true">
            <Skeleton className="h-36 rounded-2xl" />
            <Skeleton className="h-32 rounded-2xl" />
            <Skeleton className="h-32 rounded-2xl" />
            <Skeleton className="h-40 rounded-2xl" />
          </div>
        )}

        {ready &&
          (editing ? (
            <ProfileForm
              profile={profile.data}
              disciplines={disciplines.data}
              focusDisciplines={params.get('editar') === 'disciplinas'}
              onSaved={() => {
                setSaved(true);
                setParams({}, { replace: true, state: { leaveEditing: true } });
              }}
              onCancel={() => setParams({}, { replace: true, state: { leaveEditing: true } })}
            />
          ) : (
            <ProfileView profile={profile.data} />
          ))}
      </div>
    </div>
  );
}
