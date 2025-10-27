<template>
  <main class="main-content" tabindex="0">
    <div class="header-row">
      <h1 class="header-title">{{ currentSectionTitle }}</h1>
      <button v-if="showCreateButton" @click="createAction" class="btn-create">Crear</button>
    </div>
    <component :is="currentComponent" />
    <router-view />
  </main>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import DashboardCards from '@/components/DashboardCards.vue';
import UserComponent from '@/components/UserComponent.vue';
import ProjectComponent from '@/components/ProjectComponent.vue';
import TaskComponent from '@/components/TaskComponent.vue';

const route = useRoute();
const router = useRouter();
const showCreateButton = computed(() => route.name === 'usuarios' || route.name === 'proyectos' || route.name === 'tareas');
const currentSectionTitle = computed(() => {
  const titles = {
    dashboard: 'Dashboard',
    usuarios: 'Usuarios',
    proyectos: 'Proyectos',
    tareas: 'Tareas',
  };
  return titles[route.name] || 'Dashboard';
});

const currentComponent = computed(() => {
  const map = { dashboard: DashboardCards, usuarios: UserComponent, proyectos: ProjectComponent, tareas: TaskComponent };
  return map[route.name] || null;
});
const createAction = () => {
  if (route.name === 'usuarios') {
    router.push({ name: 'usuarios-crear' });
  }
  if (route.name === 'proyectos') {
    router.push({ name: 'proyectos-crear' });
  }
  if (route.name === 'tareas') {
    router.push({ name: 'tareas-crear' });
  }
};
</script>

<style scoped>
.header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.btn-create {
  background-color: #2e7dff;
  color: white;
  border: none;
  padding: 6px 14px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
}

.main-content {
  background: white;
  padding: 30px 40px;
  overflow-y: auto;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border-radius: 10px;
  min-width: 0;
  height: 90vh;
  display: flex;
  flex-direction: column;
}

.header-title {
  margin-bottom: 20px;
  font-weight: 700;
  font-size: 1.8rem;
  color: #34495e;
}

@media (max-width: 768px) {
  .main-content {
    margin: 10px;
    padding: 20px;
    border-radius: 8px;
    box-shadow: none;
    min-height: calc(100vh - 60px - 40px);
    overflow-y: auto;
  }
}
</style>