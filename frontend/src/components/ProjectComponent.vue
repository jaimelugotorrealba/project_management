<template>
    <div>
        <div v-if="projects.length === 0" class="text-center mt-4 text-gray-700">
            No hay proyectos registrados.
        </div>
        <table v-else>
            <thead>
                <tr>
                    <th @click="setSort('id')" class="cursor-pointer">
                        ID
                        <span v-if="sortBy === 'id'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th @click="setSort('name')" class="cursor-pointer">
                        Nombre
                        <span v-if="sortBy === 'name'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th>Descripción</th>
                    <th @click="setSort('created_at')" class="cursor-pointer">
                        Fecha de creación
                        <span v-if="sortBy === 'created_at'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th @click="setSort('user')" class="cursor-pointer">
                        Creador
                        <span v-if="sortBy === 'user'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th>Progreso</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="project in projects" :key="project.id">
                    <td>{{ project.id }}</td>
                    <td>{{ project.name }}</td>
                    <td>{{ project.description || '-' }}</td>
                    <td>{{ new Date(project.created_at).toLocaleDateString() }}</td>
                    <td>{{ project.user ? project.user.full_name : 'Desconocido' }}</td>
                    <td>{{ project.progress !== undefined ? project.progress : '-' }}%</td>
                    <td class="flex justify-evenly">
                        <button @click="editProject(project)"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 rounded p-1 cursor-pointer"
                            aria-label="Editar proyecto">
                            <!-- SVG editar -->
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                            </svg>
                        </button>

                        <button @click="deleteProject(project.id)"
                            class="bg-red-500 hover:bg-red-600 text-white rounded p-1 cursor-pointer"
                            aria-label="Eliminar proyecto" title="Eliminar proyecto">
                            <!-- SVG eliminar -->
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="pagination" v-if="projects.length > 0">
            <button class="cursor-pointer" @click="fetchProjects(currentPage - 1)"
                :disabled="currentPage === 1">Anterior</button>
            <span>Página {{ currentPage }} de {{ totalPages }}</span>
            <button class="cursor-pointer" @click="fetchProjects(currentPage + 1)"
                :disabled="currentPage === totalPages">Siguiente</button>
        </div>
    </div>
</template>

<script>
import api from '../http.js';
import Swal from 'sweetalert2';

export default {
    name: 'ProjectComponent',
    data() {
        return {
            projects: [],
            currentPage: 1,
            totalPages: 1,
            pageSize: 10,
            currentUserId: null,
            sortBy: null,
            sortDirection: 'asc',
        };
    },
    mounted() {
        const authUserStr = localStorage.getItem('auth');
        if (authUserStr) {
            try {
                const authUser = JSON.parse(authUserStr);
                this.currentUserId = authUser.user.id;
            } catch (error) {
                console.error('Error parsing user from localStorage', error);
            }
        }
        this.fetchProjects();
    },
    methods: {
        setSort(column) {
            if (this.sortBy === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortBy = column;
                this.sortDirection = 'asc';
            }
            this.fetchProjects(1);
        },
        fetchProjects(page = 1) {
            if (page < 1 || (this.totalPages && page > this.totalPages)) return;

            const params = {
                page: page,
                per_pag: this.pageSize,
            };
            const authUserStr = localStorage.getItem('auth');
            if (authUserStr) {
                try {
                    const authUser = JSON.parse(authUserStr);
                    params.user_id = authUser.user.id;
                } catch (error) {
                    console.error('Error parsing user from localStorage', error);
                }
            }
            if (this.sortBy) {
                params.sort_by = this.sortBy;
                params.sort_dir = this.sortDirection;
            }

            api.get('/api/projects', { params })
                .then(res => {
                    const data = res.data.data;

                    if (data && Array.isArray(data.data)) {
                        this.projects = data.data;
                        this.currentPage = data.current_page || page;
                        this.totalPages = data.last_page || 1;
                    } else if (Array.isArray(data)) {
                        this.projects = data;
                        this.currentPage = 1;
                        this.totalPages = 1;
                    } else {
                        this.projects = [];
                        this.currentPage = 1;
                        this.totalPages = 1;
                    }
                })
                .catch(err => {
                    console.error('Error fetching projects:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al obtener proyectos',
                        text: err.response?.data?.message || 'Error desconocido'
                    });
                });
        },
        editProject(project) {
            this.$router.push({ name: 'proyectos-editar', query: { id: project.id } });
        },
        deleteProject(projectId) {
            Swal.fire({
                title: '¿Seguro que quieres eliminar este proyecto?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    api.delete(`/api/projects/${projectId}`)
                        .then(() => {
                            this.fetchProjects(this.currentPage);
                            Swal.fire({
                                icon: 'success',
                                title: 'Proyecto eliminado',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        })
                        .catch(err => {
                            console.error('Error eliminando proyecto:', err);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al eliminar proyecto',
                                text: err.response?.data?.message || 'Error desconocido'
                            });
                        });
                }
            });
        }
    }
};
</script>

<style scoped>
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th,
td {
    padding: 14px 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #2a3f54;
    color: white;
    font-weight: 600;
}

tr:hover {
    background-color: #f1f1f1;
}

.pagination {
    margin-top: 12px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 12px;
}

button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

th.cursor-pointer {
    cursor: pointer;
}
</style>
