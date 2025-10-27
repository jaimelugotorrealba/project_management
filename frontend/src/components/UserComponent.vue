<template>
  <div>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Rol</th>
          <th>Opciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.full_name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.role === 'dev' ? 'Desarrollador' : 'Administrador' }}</td>
          <td class="flex justify-evenly">
            <button
              @click="editUser(user)"
              class="bg-gray-300 hover:bg-gray-400 text-gray-800 rounded p-1 cursor-pointer"
              aria-label="Editar usuario"
            >
              <!-- SVG editar -->
              <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
              </svg>
            </button>

            <button
              @click="deleteUser(user.id)"
              :disabled="user.id === currentUserId"
              class="bg-red-500 hover:bg-red-600 text-white rounded p-1 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
              aria-label="Eliminar usuario"
              :title="user.id === currentUserId ? 'No puedes eliminarte a ti mismo' : 'Eliminar usuario'"
            >
              <!-- SVG eliminar -->
              <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z" clip-rule="evenodd" />
              </svg>
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="pagination">
      <button class="cursor-pointer" @click="fetchUsers(currentPage - 1)" :disabled="currentPage === 1">Anterior</button>
      <span>Página {{ currentPage }} de {{ totalPages }}</span>
      <button class="cursor-pointer" @click="fetchUsers(currentPage + 1)" :disabled="currentPage === totalPages">Siguiente</button>
    </div>
  </div>
</template>

<script>
import api from '../http.js';
import Swal from 'sweetalert2';

export default {
  name: 'UserComponent',
  data() {
    return {
      users: [],
      currentPage: 1,
      totalPages: 1,
      pageSize: 10,
      currentUserId: null,
    };
  },
  mounted() {
    // Obtener id del usuario autenticado desde localStorage 
    const authUserStr = localStorage.getItem('auth');
    if (authUserStr) {
      try {
        const authUser = JSON.parse(authUserStr);
        this.currentUserId = authUser.user.id;
      } catch (error) {
        console.error('Error parsing user from localStorage', error);
      }
    }
    this.fetchUsers();
  },
  methods: {
    fetchUsers(page = 1) {
      if (page < 1 || (this.totalPages && page > this.totalPages)) return;

      api.get('/api/get-all-users', {
        params: { page: page, per_pag: this.pageSize },
      })
        .then(res => {
          const data = res.data.data;
          this.users = data.data || data;
          this.currentPage = data.current_page || page;
          this.totalPages = data.last_page || 1;
        })
        .catch(err => {
          console.error('Error fetching users:', err);
          Swal.fire({
            icon: 'error',
            title: 'Error al obtener usuarios',
            text: err.response?.data?.message || 'Error desconocido'
          });
        });
    },

    editUser(user) {
      this.$router.push({ name: 'usuarios-editar', query: { id: user.id } });
    },

    deleteUser(userId) {
      if (userId === this.currentUserId) {
        Swal.fire({
          icon: 'warning',
          title: 'No puedes eliminarte a ti mismo',
          confirmButtonText: 'OK'
        });
        return;
      }

      Swal.fire({
        title: '¿Seguro que quieres eliminar este usuario?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          api.delete(`/api/users/${userId}`)
            .then(() => {
              this.fetchUsers(this.currentPage);
              Swal.fire({
                icon: 'success',
                title: 'Usuario eliminado',
                timer: 1500,
                showConfirmButton: false
              });
            })
            .catch(err => {
              console.error('Error eliminando usuario:', err);
              Swal.fire({
                icon: 'error',
                title: 'Error al eliminar usuario',
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
th, td {
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
</style>
