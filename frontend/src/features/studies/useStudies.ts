import { useDisciplines, useProfile } from '@/features/profile/hooks';

export function useStudies() {
  const profile = useProfile();
  const disciplines = useDisciplines();

  return {
    disciplines: disciplines.data,
    mineIds: new Set(profile.data?.disciplines.map((discipline) => discipline.id)),
    ready: Boolean(profile.data && disciplines.data),
    failed: profile.isError || disciplines.isError,
    retry: () => {
      if (profile.isError) void profile.refetch();
      if (disciplines.isError) void disciplines.refetch();
    },
  };
}
