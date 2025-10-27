import axios from 'axios';
import { useAuthStore } from './stores/auth';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL
});
api.interceptors.request.use((config) => {
  const auth = useAuthStore();
  if (auth.token) {
    config.headers.Authorization = `Bearer ${auth.token}`;
  }
  return config;
}, (error) => {
  return Promise.reject(error);
});
export default api;
