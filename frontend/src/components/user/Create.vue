<template>
  <div class="full-height flex-column">
    <section class="profile-container register-container max-w-xl mx-auto p-6 bg-white rounded shadow-md"
      style="max-height: 80vh; overflow-y: auto; border: 3px solid; border-image-slice: 1; border-width: 3px; border-image-source: linear-gradient(to right, #2e7dff, #4f46e5);">
      <h1 class="text-2xl font-semibold mb-6 text-gray-700">Registro de Usuario</h1>
      <form @submit.prevent="registerUser" enctype="multipart/form-data" class="space-y-4">
        <div>
          <label for="email" class="block font-semibold mb-1">Correo Electrónico</label>
          <input id="email" v-model="form.email" required type="email"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
          <label for="first_name" class="block font-semibold mb-1">Nombre</label>
          <input id="first_name" v-model="form.first_name" required type="text"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
          <label for="last_name" class="block font-semibold mb-1">Apellido</label>
          <input id="last_name" v-model="form.last_name" required type="text"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
          <label for="rol" class="block font-semibold mb-1">Rol</label>
          <select id="rol" v-model="form.rol" required
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option disabled value="">Seleccione un rol</option>
            <option value="admin">Administrador</option>
            <option value="dev">Desarrollador</option>
          </select>
        </div>

        <div>
          <label for="profile_image" class="block font-semibold mb-1">Imagen de Perfil</label>
          <input ref="fileInput" id="profile_image" type="file" accept="image/*" @change="onFileChange"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          <img v-if="previewImage" :src="previewImage" alt="Profile Preview"
            class="mt-2 w-24 h-24 object-cover rounded-full" />
        </div>

        <div>
          <label for="password" class="block font-semibold mb-1">Contraseña</label>
          <input id="password" v-model="form.password" type="password" placeholder="Contraseña"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
          <label for="password_confirmation" class="block font-semibold mb-1">Confirmar Contraseña</label>
          <input id="password_confirmation" v-model="form.password_confirmation" type="password"
            placeholder="Confirma tu contraseña"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div class="flex justify-center">
          <button type="submit" :disabled="loading"
            class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-2 rounded-lg hover:from-blue-600 hover:to-indigo-700 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
            {{ loading ? 'Registrando...' : 'Registrar Usuario' }}
          </button>
        </div>

        <p v-if="error" class="text-red-600 mt-3 text-center">{{ error }}</p>
        <p v-if="success" class="text-green-600 mt-3 text-center">Usuario registrado exitosamente.</p>
      </form>
    </section>
  </div>

</template>

<script>
import api from '../../http.js';
import { ref } from 'vue';


export default {
  name: 'RegisterUser',
  setup() {
    const fileInput = ref(null);
    const form = ref({
      email: '',
      first_name: '',
      last_name: '',
      rol: '',
      password: '',
      password_confirmation: '',
      profile_image: null
    });
    const previewImage = ref(null);
    const loading = ref(false);
    const error = ref('');
    const success = ref(false);

    const onFileChange = (e) => {
      const file = e.target.files[0];
      form.value.profile_image = file || null;
      if (file) {
        previewImage.value = URL.createObjectURL(file);
      } else {
        previewImage.value = null;
      }
    };

    const resetForm = () => {
      form.value = {
        email: '',
        first_name: '',
        last_name: '',
        rol: '',
        password: '',
        password_confirmation: '',
        profile_image: null
      };
      previewImage.value = null;
      if (fileInput.value) {
        fileInput.value.value = '';
      }
    };

    const registerUser = async () => {
      error.value = '';
      success.value = false;
      loading.value = true;

      try {
        const formData = new FormData();
        formData.append('email', form.value.email);
        formData.append('first_name', form.value.first_name);
        formData.append('last_name', form.value.last_name);
        formData.append('rol', form.value.rol);
        formData.append('password', form.value.password);
        formData.append('password_confirmation', form.value.password_confirmation);
        if (form.value.profile_image) {
          formData.append('profile_image', form.value.profile_image);
        }

        await api.post('/api/users', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });

        success.value = true;
        resetForm();
      } catch (err) {
        if (err.response && err.response.data && err.response.data.message) {
          error.value = err.response.data.message;
        } else {
          error.value = 'Error al registrar usuario.';
        }
      } finally {
        loading.value = false;
      }
    };

    return {
      form,
      previewImage,
      loading,
      error,
      success,
      onFileChange,
      registerUser,
      fileInput
    };
  }
};
</script>

<style scoped>
.full-height {
  height: 100vh;
  display: flex;
  flex-direction: column;
}

.profile-container {
  background: white;
  padding: 30px 40px;
  flex-grow: 1;
  overflow-y: auto;
  border-radius: 10px;
  margin: 20px auto;
}
</style>
