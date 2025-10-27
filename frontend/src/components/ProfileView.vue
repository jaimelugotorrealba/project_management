<template>
  <section class="profile-container">
    <h1 class="text-2xl font-semibold mb-6 text-gray-700">Perfil de Usuario</h1>
    <div class="flex justify-center">
      <div class="w-full max-w-4xl md:grid md:grid-cols-12 md:gap-6">
        <div class="col-span-0 md:col-span-2"></div>
        <form @submit.prevent="updateProfile"
          class="col-span-12 md:col-span-8 border border-blue-500 p-6 rounded-lg shadow-md bg-white">
          <div class="mb-4">
            <label for="email" class="block font-semibold mb-1">Correo Electrónico</label>
            <input id="email" type="email" v-model="user.email" disabled
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
          <div class="mb-4">
            <label for="first_name" class="block font-semibold mb-1">Nombre</label>
            <input id="first_name" v-model="user.first_name" required
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
          <div class="mb-4">
            <label for="last_name" class="block font-semibold mb-1">Apellido</label>
            <input id="last_name" v-model="user.last_name" required
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>

          <div class="mb-4">
            <label class="block font-semibold mb-1">Rol</label>
            <span>{{ user.role === 'dev' ? 'Desarrollador' : 'Administrador' }}</span>
          </div>

          <div class="mb-4">
            <label for="profile_image" class="block font-semibold mb-1">Imagen de Perfil</label>
            <input id="profile_image" ref="fileInput" type="file" accept="image/*" @change="onFileChange"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <img v-if="previewImage && previewImage.length > 0" :src="previewImage" alt="Perfil"
              class="mt-2 w-24 h-24 object-cover rounded-full" />
          </div>

          <div class="mb-4">
            <label for="password" class="block font-semibold mb-1">Nueva Contraseña</label>
            <input id="password" type="password" v-model="password" placeholder="Deja vacío para no cambiar"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
          <div class="mb-4">
            <label for="password_confirmation" class="block font-semibold mb-1">Confirmar Contraseña</label>
            <input id="password_confirmation" type="password" v-model="passwordConfirmation"
              placeholder="Confirmar contraseña"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>

          <div class="flex justify-center">
            <button type="submit" :disabled="loading"
              class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-2 rounded-lg hover:from-blue-600 hover:to-indigo-700 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
              {{ loading ? 'Guardando...' : 'Guardar Cambios' }}
            </button>
          </div>

          <p v-if="error" class="text-red-600 mt-3">{{ error }}</p>
          <p v-if="success" class="text-green-600 mt-3">Perfil actualizado correctamente.</p>
        </form>
        <div class="col-span-0 md:col-span-2"></div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../http.js';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const user = ref({
  first_name: '',
  last_name: '',
  email: '',
  role: ''
});
const password = ref('');
const passwordConfirmation = ref('');
const profileImage = ref(null);
const previewImage = ref(null);
const loading = ref(false);
const error = ref(null);
const success = ref(false);
const fileInput = ref(null);

onMounted(() => {
  const authRaw = localStorage.getItem('auth');
  if (authRaw) {
    try {
      const authData = JSON.parse(authRaw);
      const userData = authData.user || {};

      user.value.first_name = userData.first_name || '';
      user.value.last_name = userData.last_name || '';
      user.value.email = userData.email || '';
      user.value.role = userData.role || '';

    } catch (e) {
      console.error('Error parsing auth data from localStorage', e);
    }
  }
});

function onFileChange(event) {
  const file = event.target.files[0];
  if (file) {
    profileImage.value = file;
    previewImage.value = URL.createObjectURL(file);
  } else {
    profileImage.value = null;
    previewImage.value = null;
  }
}

async function updateProfile() {
  loading.value = true;
  error.value = null;
  success.value = false;

  try {
    const formData = new FormData();
    formData.append('first_name', user.value.first_name);
    formData.append('last_name', user.value.last_name);
    if (password.value) formData.append('password', password.value);
    if (passwordConfirmation.value) formData.append('password_confirmation', passwordConfirmation.value);
    if (profileImage.value) formData.append('profile_image', profileImage.value);

    // Nota: no poner headers Content-Type al hacer esta llamada
    const res = await api.post('/api/update-profile', formData);

    success.value = true;
    password.value = '';
    passwordConfirmation.value = '';
    profileImage.value = null;
    previewImage.value = null;

    // Actualiza localStorage
    const storedAuth = JSON.parse(localStorage.getItem('auth') || '{}');
    storedAuth.user.first_name = user.value.first_name;
    storedAuth.user.last_name = user.value.last_name;
    if (res.data.profile_image) {
      storedAuth.user.profile_image = res.data.profile_image;
    }
    storedAuth.user.url_profile_image = res.data.url_profile_image;
    localStorage.setItem('auth', JSON.stringify(storedAuth));
    if (fileInput.value) {
      fileInput.value.value = null;
    }
    if (res.data.url_profile_image) {
      authStore.user.url_profile_image = res.data.url_profile_image;
    }
  } catch (err) {
    console.error('Error completo:', err.response);
    if (err.response && err.response.data) {
      const data = err.response.data;
      if (data.errors) {
        error.value = Object.values(data.errors).flat().join(' ');
      } else if (data.message) {
        error.value = data.message;
      } else {
        error.value = 'Error al actualizar perfil';
      }
    } else {
      error.value = err.message || 'Error al actualizar perfil';
    }
  } finally {
    loading.value = false;
  }
}

</script>

<style scoped>
.profile-container {
  background: white;
  padding: 30px 40px;
  max-height: calc(100vh - 100px);
  overflow-y: auto;
  border-radius: 10px;
  margin: 20px auto;
}
</style>
