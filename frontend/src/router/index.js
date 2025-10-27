import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';
import LoginView from '../views/LoginView.vue';
import DashboardView from '../views/DashboardView.vue';
import ProfileView from '@/components/ProfileView.vue';
import MainContent from '@/components/MainContent.vue';
import Create from '@/components/user/Create.vue';
import Edit from '@/components/user/Edit.vue';
import CreateProject from '@/components/project/Create.vue';
import EditProject from '@/components/project/Edit.vue';
import CreateTask from '@/components/task/Create.vue';
import EditTask from '@/components/task/Edit.vue';

import { useAuthStore } from '../stores/auth';

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/login', component: LoginView },
  {
    path: '/dashboard',
    component: DashboardView,
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'dashboard', component: MainContent },
      { path: 'profile', name: 'profile', component: ProfileView },
      { path: 'usuarios', name: 'usuarios', component: MainContent },
      { path: 'usuarios/crear', name: 'usuarios-crear', component: Create },
      { path: 'usuarios/editar', name: 'usuarios-editar', component: Edit },
      { path: 'proyectos', name: 'proyectos', component: MainContent },
      { path: 'proyectos/crear', name: 'proyectos-crear', component: CreateProject },
      { path: 'proyectos/editar', name: 'proyectos-editar', component: EditProject },
      { path: 'tareas', name: 'tareas', component: MainContent },
      { path: 'tareas/crear', name: 'tareas-crear', component: CreateTask },
      { path: 'tareas/editar', name: 'tareas-editar', component: EditTask },
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();

  if (to.meta.requiresAuth) {
    if (auth.token) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${auth.token}`;
      try {
        // Validar sesión
        await axios.get('/api/profile');

        // Leer usuario desde localStorage
        let user = null;
        const authStr = localStorage.getItem('auth');
        if (authStr) {
          try {
            user = JSON.parse(authStr).user;
          } catch (e) {
            console.error('Error parsing auth from localStorage', e);
          }
        }

        // Restringir acceso a rutas que contengan 'usuarios' solo para admin
        if (to.name && to.name.startsWith('usuarios')) {
          if (!user || user.role !== 'admin') {
            return next({ name: 'dashboard' }); // Redirigir si no es admin
          }
        }

        next();
      } catch (error) {
        auth.logout();
        next('/login');
      }
    } else {
      next('/login');
    }
  } else {
    next();
  }
});

export default router;
