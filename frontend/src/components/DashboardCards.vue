<template>
  <div class="card-container">
    <div class="dashboard-card">
      <h3>Usuarios</h3>
      <p>{{ totals.users }}</p>
    </div>
    <div class="dashboard-card">
      <h3>Proyectos</h3>
      <p>{{ totals.projects }}</p>
    </div>
    <div class="dashboard-card">
      <h3>Tareas</h3>
      <p>{{ totals.tasks }}</p>
    </div>
  </div>
</template>

<script>
import api from '../http.js'; // tu instancia Axios configurada

export default {
  name: 'Dashboard',
  data() {
    return {
      totals: {
        users: 0,
        projects: 0,
        tasks: 0,
      },
    };
  },
  mounted() {
    // Obtener el user_id almacenado en localStorage
    const storedUser = localStorage.getItem('auth');
    let userId = null;
    if (storedUser) {
      try {
        const userObj = JSON.parse(storedUser);
        userId = userObj.user.id;
        
      } catch {
        console.error('Error parsing user from localStorage');
      }
    }
    api.get('/api/get-totals', {
      params: { user_id: userId }
    })
      .then(response => {
        if (response.data.success) {
          this.totals = response.data.totals;
        }
      })
      .catch(error => {
        console.error('Error fetching totals:', error);
      });
  }

};
</script>

<style scoped>
.card-container {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.dashboard-card {
  background: #f3f4f6;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  flex: 1;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.dashboard-card h3 {
  margin: 0 0 0.5rem;
  font-weight: 600;
  font-size: 1.125rem;
}

.dashboard-card p {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
}
</style>
