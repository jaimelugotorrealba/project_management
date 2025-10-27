<template>
  <div class="full-height flex-column">
    <section
      class="profile-container register-container mx-auto p-6 bg-white rounded shadow-md wider-container"
      style="max-height: 80vh; overflow-y: auto; border: 3px solid; border-image-slice: 1;
             border-width: 3px; border-image-source: linear-gradient(to right, #2e7dff, #4f46e5);"
    >
      <h1 class="text-2xl font-semibold mb-6 text-gray-700">Crear Proyecto</h1>
      <form @submit.prevent="createProject" class="space-y-4" novalidate>
        <div>
          <label for="name" class="block font-semibold mb-1">Nombre</label>
          <input
            id="name"
            v-model="form.name"
            required
            type="text"
            minlength="3"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label for="description" class="block font-semibold mb-1">Descripción</label>
          <textarea
            id="description"
            v-model="form.description"
            rows="4"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
          ></textarea>
        </div>

        <div class="flex justify-center">
          <button
            type="submit"
            :disabled="loading"
            class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-2 rounded-lg hover:from-blue-600 hover:to-indigo-700 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Creando...' : 'Crear Proyecto' }}
          </button>
        </div>

        <p v-if="error" class="text-red-600 mt-3 text-center">{{ error }}</p>
        <p v-if="success" class="text-green-600 mt-3 text-center">Proyecto creado exitosamente.</p>
      </form>
    </section>
  </div>
</template>

<script>
import api from '../../http.js';
import { ref } from 'vue';

export default {
  name: 'CreateProject',
  setup() {
    const form = ref({
      name: '',
      description: '',
    });
    const loading = ref(false);
    const error = ref('');
    const success = ref(false);

    const createProject = async () => {
      error.value = '';
      success.value = false;

      if (form.value.name.trim().length < 3) {
        error.value = 'El nombre del proyecto debe tener al menos 3 caracteres.';
        return;
      }

      loading.value = true;
      try {
        // Obtener user_id desde localStorage
        const authStr = localStorage.getItem('auth');
        let user_id = null;
        if (authStr) {
          const auth = JSON.parse(authStr);
          user_id = auth.user?.id || null;
        }

        if (!user_id) {
          error.value = 'Usuario no autenticado.';
          loading.value = false;
          return;
        }

        // Enviar datos junto con user_id
        const payload = {
          name: form.value.name,
          description: form.value.description,
          user_id: user_id,
        };

        await api.post('/api/projects', payload);
        success.value = true;
        form.value = {
          name: '',
          description: '',
        };
      } catch (err) {
        if (err.response?.data?.message) {
          error.value = err.response.data.message;
        } else {
          error.value = 'Error al crear el proyecto.';
        }
      } finally {
        loading.value = false;
      }
    };

    return {
      form,
      loading,
      error,
      success,
      createProject,
    };
  },
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

.wider-container {
  max-width: 900px;
  width: 100%;
}
</style>
