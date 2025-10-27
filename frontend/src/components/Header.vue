<template>
  <header class="bg-gradient-to-br from-blue-500 to-indigo-600">
    <div class="hamburger" :class="{ open: isSidebarOpen }" id="hamburger" @click="$emit('toggle-sidebar')"
      @keydown.enter.prevent="$emit('toggle-sidebar')" @keydown.space.prevent="$emit('toggle-sidebar')"
      aria-label="Abrir menú lateral" role="button" tabindex="0" :aria-expanded="isSidebarOpen" aria-controls="sidebar">
      <span></span>
      <span></span>
      <span></span>
    </div>
    <div class="user-menu-wrapper p-3" :class="{ open: isUserMenuOpen }">
      <button class="user-menu-button" id="userMenuButton" @click="toggleUserMenu" :aria-expanded="isUserMenuOpen"
        aria-haspopup="true" aria-label="Menú de usuario">
        <img :src="userImageSrc" alt="Foto del usuario" />
      </button>
      <div class="user-menu-dropdown " id="userMenuDropdown" role="menu" aria-label="Menú usuario"
        v-show="isUserMenuOpen">
        <router-link to="/dashboard/profile" role="menuitem" @click="isUserMenuOpen = false">Perfil</router-link>
        <a href="#" role="menuitem" @click.prevent="handleLogout">Cerrar sesión</a>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onUnmounted, computed,onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import api from '../http.js';

defineProps({
  isSidebarOpen: Boolean,
});
defineEmits(['toggle-sidebar']);
const profileImageUrl = ref(null);
const isUserMenuOpen = ref(false);
const auth = useAuthStore();
const router = useRouter();

function toggleUserMenu() {
  isUserMenuOpen.value = !isUserMenuOpen.value;
}

async function handleLogout() {
  try {
    await api.post('/api/logout');
    localStorage.removeItem('token');
    await auth.logout();
    await router.push('/login');
  } catch (error) {
    console.error('Logout failed:', error);
  } finally {
    isUserMenuOpen.value = false;
  }
}

function handleOutsideClick(event) {
  const el = document.querySelector('.user-menu-wrapper');
  if (el && !el.contains(event.target)) {
    isUserMenuOpen.value = false;
  }
}

document.addEventListener('click', handleOutsideClick);
const profile_image = ref(null);

onMounted(() => {
  const authRaw = localStorage.getItem('auth');
  if (authRaw) {
    try {
      const authData = JSON.parse(authRaw);
      profileImageUrl.value = authData.user.url_profile_image || null;
    } catch (e) {
      profileImageUrl.value = null;
    }
  }
});
const authStore = useAuthStore();
const userImageSrc = computed(() => {
  return authStore.user.url_profile_image || 'https://randomuser.me/api/portraits/men/32.jpg';
});
onUnmounted(() => {
  document.removeEventListener('click', handleOutsideClick);
});


</script>

<style scoped>
header {
  color: white;
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: 0 20px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  position: relative;
  z-index: 1000;
}

.hamburger {
  display: none;
  flex-direction: column;
  justify-content: center;
  cursor: pointer;
  width: 35px;
  height: 30px;
  margin-right: auto;
}

.hamburger span {
  height: 4px;
  width: 100%;
  background: white;
  margin-bottom: 5px;
  border-radius: 2px;
  transition: all 0.3s ease;
}

.hamburger.open span:nth-child(1) {
  transform: rotate(45deg) translate(7px, 7px);
}

.hamburger.open span:nth-child(2) {
  opacity: 0;
}

.hamburger.open span:nth-child(3) {
  transform: rotate(-45deg) translate(7px, -7px);
}

.user-menu-wrapper {
  position: relative;
  display: inline-block;
}

.user-menu-button {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
}

.user-menu-button img {
  border-radius: 50%;
  width: 42px;
  height: 42px;
  object-fit: cover;
  border: 2px solid #2e7dff;
}

.user-menu-dropdown {
  position: absolute;
  right: 0;
  top: 50px;
  background: white;
  min-width: 160px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  border-radius: 6px;
  z-index: 1101;
}

.user-menu-dropdown a {
  display: block;
  padding: 12px 16px;
  color: #34495e;
  text-decoration: none;
  border-bottom: 1px solid #ddd;
  transition: background-color 0.2s;
}

.user-menu-dropdown a:last-child {
  border-bottom: none;
}

.user-menu-dropdown a:hover {
  background-color: #2e7dff;
  color: white;
}

@media (max-width: 768px) {
  .hamburger {
    display: flex;
    position: fixed;
    left: 15px;
    top: 14px;
    z-index: 2100;
  }

  header {
    justify-content: flex-end;
    padding-right: 60px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 60px;
    z-index: 1000;
  }
}
</style>