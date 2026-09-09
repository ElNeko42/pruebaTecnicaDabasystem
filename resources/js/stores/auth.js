import { defineStore } from 'pinia';
import axios from 'axios';
export const useAuthStore = defineStore('auth', {
  state: () => ({ loading: false, error: '' }),
  actions: {
    async login(credentials) { this.loading = true; this.error = ''; try { const { data } = await axios.post('/login', credentials); localStorage.setItem('admin_token', data.token); axios.defaults.headers.common.Authorization = `Bearer ${data.token}`; } catch (e) { this.error = e.response?.data?.message || 'No se pudo iniciar sesión.'; throw e; } finally { this.loading = false; } },
  },
});
