import { createBrowserRouter } from 'react-router';
import { AppLayout } from '@/components/layout/AppLayout';
import { CommunityPage } from '@/features/community/CommunityPage';
import { OpportunitiesPage } from '@/features/opportunities/OpportunitiesPage';
import { GuestOnly, RequireAuth, RequireModeration, RootRedirect } from '@/features/auth/guards';
import { LoginPage } from '@/features/auth/LoginPage';
import { RegisterPage } from '@/features/auth/RegisterPage';
import { HomePage } from '@/features/home/HomePage';
import { ModerationPage } from '@/features/moderation/ModerationPage';
import { MyContentsPage } from '@/features/my-contents/MyContentsPage';
import { ProfilePage } from '@/features/profile/ProfilePage';
import { DisciplinePage } from '@/features/studies/DisciplinePage';
import { SettingsPage } from '@/features/settings/SettingsPage';
import { StudentProfilePage } from '@/features/students/StudentProfilePage';
import { StudiesPage } from '@/features/studies/StudiesPage';

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
          { path: '/perfil/meus-conteudos', element: <MyContentsPage /> },
          { path: '/estudos', element: <StudiesPage /> },
          { path: '/estudos/:slug', element: <DisciplinePage /> },
          { path: '/oportunidade', element: <OpportunitiesPage /> },
          { path: '/comunidade', element: <CommunityPage /> },
          { path: '/estudantes/:code', element: <StudentProfilePage /> },
          { path: '/configuracoes', element: <SettingsPage /> },
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
