import { useQuery } from '@tanstack/react-query';
import { api } from '@/lib/api/client';
import { call } from '@/lib/api/errors';

export function useStudentProfile(code: string) {
  return useQuery({
    queryKey: ['students', code],
    queryFn: () => call(api.GET('/students/{student}', { params: { path: { student: code } } })),
  });
}
