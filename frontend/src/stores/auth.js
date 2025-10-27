import { defineStore } from 'pinia';
import axios from 'axios';
import api from '../http.js';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null
  }),
  actions: {
    setUser(user) {
      this.user = user;
    },
    setToken(token) {
      this.token = token;
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    },
    async logout() {
      try {
        await api.post('/api/logout'); 
      } catch (error) {
        console.log(error);
      }
      this.user = null;
      this.token = null;
      delete axios.defaults.headers.common['Authorization'];
    }
  },
  persist: true,
});
