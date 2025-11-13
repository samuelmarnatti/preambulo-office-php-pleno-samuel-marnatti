import { defineStore } from 'pinia';
import api from '@/services/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isCliente: (state) => state.user?.role === 'cliente',
    isAtendente: (state) => state.user?.role === 'atendente',
    isAdministrador: (state) => state.user?.role === 'administrador',
  },

  actions: {
    async login(credentials) {
      try {
        const response = await api.post('/login', credentials);
        this.token = response.data.token;
        this.user = response.data.user;
        
        localStorage.setItem('token', this.token);
        localStorage.setItem('user', JSON.stringify(this.user));
        
        return response.data;
      } catch (error) {
        throw error;
      }
    },

    async register(userData) {
      try {
        const response = await api.post('/register', userData);
        this.token = response.data.token;
        this.user = response.data.user;
        
        localStorage.setItem('token', this.token);
        localStorage.setItem('user', JSON.stringify(this.user));
        
        return response.data;
      } catch (error) {
        throw error;
      }
    },

    async logout() {
      try {
        await api.post('/logout');
      } catch (error) {
        console.error('Erro ao fazer logout:', error);
      } finally {
        this.user = null;
        this.token = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
      }
    },

    async fetchUser() {
      try {
        const response = await api.get('/me');
        this.user = response.data;
        localStorage.setItem('user', JSON.stringify(this.user));
      } catch (error) {
        this.logout();
      }
    },
  },
});