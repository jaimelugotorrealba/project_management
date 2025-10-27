<template>
  <div class="app">
    <Sidebar :isSidebarOpen="isSidebarOpen" @toggle-sidebar="toggleSidebar" />
    <div class="right-container">
      <Header :isSidebarOpen="isSidebarOpen" @toggle-sidebar="toggleSidebar" />
       <router-view />
    </div>
  </div>
</template>

<script>
import Sidebar from '@/components/Sidebar.vue';
import Header from '@/components/Header.vue';
import MainContent from '@/components/MainContent.vue';

export default {
  name: 'App',
  components: {
    Sidebar,
    Header,
    MainContent,
  },
  data() {
    return {
      isSidebarOpen: false,
    };
  },
  methods: {
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen;
      document.body.classList.toggle('menu-open', this.isSidebarOpen);
    },
  },
};
</script>

<style scoped>
.app {
  display: grid;
  grid-template-columns: 220px 1fr;
  grid-template-rows: 100vh;
  height: 100vh;
  overflow: hidden;
}

@media (max-width: 768px) {
  .app {
    display: block;
    padding-top: 60px;
    height: auto;
    overflow-x: hidden;
  }
}
</style>