<template>
  <nav class="sidebar bg-gradient-to-br from-blue-500 to-indigo-600" :class="{ open: isSidebarOpen }" id="sidebar"
    aria-label="Menú lateral">
    <h2>PANEL</h2>
    <button class="close-sidebar-btn" @click="$emit('toggle-sidebar')" aria-label="Cerrar menú lateral">
      ×
    </button>
    <RouterLink v-for="link in links" :key="link.section" :to="{ name: link.section }" class="nav-link"
      :class="{ active: $route.name === link.section }" @click.native="$emit('toggle-sidebar')"
      :aria-current="$route.name === link.section ? 'page' : null">
      {{ link.label }}
    </RouterLink>
  </nav>
</template>


<script>
import { RouterLink } from 'vue-router';

export default {
  name: 'Sidebar',
  props: {
    isSidebarOpen: Boolean,
  },
  components: { RouterLink },
  data() {
    return {
      links: [
        { section: 'dashboard', label: 'Dashboard' },
        { section: 'usuarios', label: 'Usuarios' },
        { section: 'proyectos', label: 'Proyectos' },
        { section: 'tareas', label: 'Tareas' },
      ],
    };
  },
};
</script>

<style scoped>
.sidebar {
  color: white;
  display: flex;
  flex-direction: column;
  padding: 20px 10px;
  overflow-y: auto;
  grid-column: 1 / 2;
  grid-row: 1 / 2;
  position: relative;
  z-index: 900;
}

.sidebar h2 {
  margin-bottom: 40px;
  font-weight: 700;
  font-size: 1.5rem;
  letter-spacing: 3px;
  text-align: center;
}

.close-sidebar-btn {
  display: none;
  position: absolute;
  top: 10px;
  right: 10px;
  background: none;
  border: 2px solid white;
  color: white;
  font-size: 18px;
  font-weight: bold;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  cursor: pointer;
  text-align: center;
  line-height: 26px;
  padding: 0;
  user-select: none;
  z-index: 1001;
}

.nav-link {
  color: #cfd8dc;
  text-decoration: none;
  padding: 12px 16px;
  margin-bottom: 10px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.nav-link:hover,
.nav-link.active {
  background-color: #2e7dff;
  color: white;
}

@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 220px;
    height: 100vh;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
    z-index: 2000;
  }

  .sidebar.open {
    transform: translateX(0);
  }

  .close-sidebar-btn {
    display: block;
  }
}
</style>