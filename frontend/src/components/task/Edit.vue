<template>
    <div class="full-height flex-column">
        <section class="profile-container register-container mx-auto p-6 bg-white rounded shadow-md wider-container"
            style="max-height: 80vh; overflow-y: auto; border: 3px solid; border-image-slice: 1;
            border-width: 3px; border-image-source: linear-gradient(to right, #2e7dff, #4f46e5);">
            <h1 class="text-2xl font-semibold mb-6 text-gray-700">Editar Tarea</h1>
            <form @submit.prevent="updateTask" class="space-y-4" novalidate>
                <div>
                    <label class="block font-semibold mb-1">Proyecto</label>
                    <p class="border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed">
                        {{projects.find(p => p.id === form.project_id)?.name || 'No disponible'}}
                    </p>
                    <!-- Campo oculto para enviar el id -->
                    <input type="hidden" v-model="form.project_id" />
                </div>

                <div>
                    <label for="assigned_to" class="block font-semibold mb-1">Asignado a</label>
                    <select id="assigned_to" v-model="form.assigned_to"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">No asignado</option>
                        <option v-for="user in users" :value="user.id" :key="user.id">
                            {{ user.full_name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label for="title" class="block font-semibold mb-1">Título</label>
                    <input id="title" v-model="form.title" required minlength="3" type="text"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label for="description" class="block font-semibold mb-1">Descripción</label>
                    <textarea id="description" v-model="form.description" rows="4"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>

                <div>
                    <label for="status" class="block font-semibold mb-1">Estado</label>
                    <select id="status" v-model="form.status" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled>Seleccione estado</option>
                        <option value="pending">Pendiente</option>
                        <option value="in_progress">En progreso</option>
                        <option value="completed">Completada</option>
                    </select>
                </div>

                <div class="flex justify-center">
                    <button type="submit" :disabled="loading"
                        class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-2 rounded-lg hover:from-blue-600 hover:to-indigo-700 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ loading ? 'Actualizando...' : 'Actualizar Tarea' }}
                    </button>
                </div>

                <p v-if="error" class="text-red-600 mt-3 text-center">{{ error }}</p>
                <p v-if="success" class="text-green-600 mt-3 text-center">Tarea actualizada exitosamente.</p>
            </form>
        </section>
    </div>
</template>

<script>
import api from '../../http.js';
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

export default {
    name: 'EditTask',
    setup() {
        const route = useRoute();
        const router = useRouter();

        const form = ref({
            project_id: '',
            assigned_to: '',
            title: '',
            description: '',
            status: '',
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

        const fetchTask = async (id) => {
            loading.value = true;
            error.value = '';
            try {
                const res = await api.get(`/api/tasks/${id}`);
                const task = res.data.data || res.data;

                form.value.project_id = task.project_id || '';
                form.value.assigned_to = task.assigned_to || '';
                form.value.title = task.title || '';
                form.value.description = task.description || '';
                form.value.status = task.status || '';
            } catch (err) {
                error.value = 'Error al cargar la tarea.';
            } finally {
                loading.value = false;
            }
        };

        const updateTask = async () => {
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
            if (!form.value.status) {
                error.value = 'Debe seleccionar un estado.';
                return;
            }

            loading.value = true;
            try {
                await api.put(`/api/tasks/${route.query.id}`, {
                    project_id: form.value.project_id,
                    assigned_to: form.value.assigned_to || null,
                    title: form.value.title,
                    description: form.value.description,
                    status: form.value.status,
                });
                success.value = true;
                // Opcional: redirigir después de actualizar, por ejemplo:
                // router.push({ name: 'task-list' });
            } catch (err) {
                if (err.response?.data?.message) {
                    error.value = err.response.data.message;
                } else {
                    error.value = 'Error al actualizar la tarea.';
                }
            } finally {
                loading.value = false;
            }
        };

        onMounted(() => {
            fetchProjects();
            fetchUsers();
            if (route.query.id) {
                fetchTask(route.query.id);
            } else {
                error.value = 'No se especificó ID de tarea.';
            }
        });

        return {
            form,
            loading,
            error,
            success,
            projects,
            users,
            updateTask,
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
