import { createBrowserRouter } from 'react-router';
import { AppLayout } from '@/components/layout/AppLayout';
import { GuestOnly, RequireAuth, RequireModeration, RootRedirect } from '@/features/auth/guards';
import { LoginPage } from '@/features/auth/LoginPage';
import { RegisterPage } from '@/features/auth/RegisterPage';
import { HomePage } from '@/features/home/HomePage';
import { ModerationPage } from '@/features/moderation/ModerationPage';
import { ProfilePage } from '@/features/profile/ProfilePage';
import { DisciplinePage } from '@/features/studies/DisciplinePage';
import { StudiesPage } from '@/features/studies/StudiesPage';
import { UnderConstructionPage } from '@/pages/UnderConstructionPage';

const placeholders = [
  ['/oportunidade', 'Oportunidade'],
  ['/comunidade', 'Comunidade'],
  ['/configuracoes', 'Configurações'],
] as const;

export const router = createBrowserRouter([
  {
    element: <GuestOnly />,
    children: [
      { path: '/login', element: <LoginPage /> },
      { path: '/cadastro', element: <RegisterPage /> },
    ],
  },
  {
    element: <RequireAuth />,
    children: [
      {
        element: <AppLayout />,
        children: [
          { path: '/home', element: <HomePage /> },
          { path: '/perfil', element: <ProfilePage /> },
          { path: '/estudos', element: <StudiesPage /> },
          { path: '/estudos/:slug', element: <DisciplinePage /> },
          ...placeholders.map(([path, title]) => ({ path, element: <UnderConstructionPage title={title} /> })),
          {
            element: <RequireModeration />,
            children: [{ path: '/moderacao', element: <ModerationPage /> }],
          },
        ],
      },
    ],
  },
  { path: '*', element: <RootRedirect /> },
]);
