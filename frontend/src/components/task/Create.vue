<template>
  <div class="full-height flex-column">
    <section
      class="profile-container register-container mx-auto p-6 bg-white rounded shadow-md wider-container"
      style="max-height: 80vh; overflow-y: auto; border: 3px solid; border-image-slice: 1;
            border-width: 3px; border-image-source: linear-gradient(to right, #2e7dff, #4f46e5);"
    >
      <h1 class="text-2xl font-semibold mb-6 text-gray-700">Crear Tarea</h1>
      <form @submit.prevent="createTask" class="space-y-4" novalidate>
        <div>
          <label for="project" class="block font-semibold mb-1">Proyecto</label>
          <select
            id="project"
            v-model="form.project_id"
            required
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="" disabled>Seleccione un proyecto</option>
            <option v-for="project in projects" :value="project.id" :key="project.id">
              {{ project.name }}
            </option>
          </select>
        </div>

        <div>
          <label for="assigned_to" class="block font-semibold mb-1">Asignado a</label>
          <select
            id="assigned_to"
            v-model="form.assigned_to"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">No asignado</option>
            <option v-for="user in users" :value="user.id" :key="user.id">
              {{ user.full_name }}
            </option>
          </select>
        </div>

        <div>
          <label for="title" class="block font-semibold mb-1">Título</label>
          <input
            id="title"
            v-model="form.title"
            required
            minlength="3"
            type="text"
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
            {{ loading ? 'Creando...' : 'Crear Tarea' }}
          </button>
        </div>

        <p v-if="error" class="text-red-600 mt-3 text-center">{{ error }}</p>
        <p v-if="success" class="text-green-600 mt-3 text-center">Tarea creada exitosamente.</p>
      </form>
    </section>
  </div>
</template>

<script>
import api from '../../http.js';
import { ref, onMounted } from 'vue';

export default {
  name: 'CreateTask',
  setup() {
    const form = ref({
      project_id: '',
      assigned_to: '',
      title: '',
      description: '',
    });
    const loading = ref(false);
    const error = ref('');
    const success = ref(false);

    const projects = ref([]);
    const users = ref([]);

    const fetchProjects = async () => {
      try {
        const res = await api.get('/api/get-all-projects');
        projects.value = res.data.data || res.data || [];
      } catch (err) {
        console.error('Error fetching projects:', err);
      }
    };

    const fetchUsers = async () => {
      try {
        const res = await api.get('/api/get-users');
        users.value = res.data.data || res.data || [];
      } catch (err) {
        console.error('Error fetching users:', err);
      }
    };

    const createTask = async () => {
      error.value = '';
      success.value = false;

      if (!form.value.project_id) {
        error.value = 'Debe seleccionar un proyecto.';
        return;
      }
      if (form.value.title.trim().length < 3) {
        error.value = 'El título debe tener al menos 3 caracteres.';
        return;
      }

      loading.value = true;
      try {
        await api.post('/api/tasks', {
          project_id: form.value.project_id,
          assigned_to: form.value.assigned_to || null,
          title: form.value.title,
          description: form.value.description,
        });
        success.value = true;
        form.value = {
          project_id: '',
          assigned_to: '',
          title: '',
          description: '',
        };
      } catch (err) {
        if (err.response?.data?.message) {
          error.value = err.response.data.message;
        } else {
          error.value = 'Error al crear la tarea.';
        }
      } finally {
        loading.value = false;
      }
    };

    onMounted(() => {
      fetchProjects();
      fetchUsers();
    });

    return {
      form,
      loading,
      error,
      success,
      createTask,
      projects,
      users,
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
