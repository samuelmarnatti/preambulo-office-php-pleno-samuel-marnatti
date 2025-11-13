<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter, useRoute } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();
const isLoggingOut = ref(false);

const handleLogout = async () => {
  isLoggingOut.value = true;
  try {
    await authStore.logout();
    router.push('/login');
  } finally {
    isLoggingOut.value = false;
  }
};
</script>

<template>
  <nav class="navbar">
    <div class="navbar-container">
      <!-- Logo -->
      <router-link to="/" class="navbar-logo">
        <div class="logo-icon">🎬</div>
        <div class="logo-text">
          <span class="logo-title">Locadora</span>
          <span class="logo-subtitle">Streaming</span>
        </div>
      </router-link>

      <!-- Navigation Links -->
      <div class="nav-links">
        <router-link 
          to="/" 
          class="nav-link"
          :class="{ 'nav-link-active': route.path === '/' }"
        >
          Início
        </router-link>
        <router-link 
          to="/filmes" 
          class="nav-link"
          :class="{ 'nav-link-active': route.path === '/filmes' }"
        >
          Filmes
        </router-link>
        <router-link
          v-if="authStore.isCliente"
          to="/minhas-locacoes"
          class="nav-link"
          :class="{ 'nav-link-active': route.path === '/minhas-locacoes' }"
        >
          Minhas Locações
        </router-link>
        <router-link
          v-if="authStore.isAdministrador || authStore.isAtendente"
          to="/usuarios"
          class="nav-link"
          :class="{ 'nav-link-active': route.path.startsWith('/usuarios') }"
        >
          Usuários
        </router-link>
        <router-link
          v-if="authStore.isAdministrador"
          to="/admin/dashboard"
          class="nav-link"
          :class="{ 'nav-link-active': route.path === '/admin/dashboard' }"
        >
          Dashboard
        </router-link>
      </div>

      <!-- User Menu -->
      <div class="nav-actions">
        <div class="user-info">
          <div class="user-avatar">
            {{ authStore.user?.name?.charAt(0).toUpperCase() }}
          </div>
          <div class="user-details">
            <span class="user-name">{{ authStore.user?.name }}</span>
            <span class="user-role">{{ authStore.user?.role }}</span>
          </div>
        </div>
        
        <button @click="handleLogout" class="logout-btn" :disabled="isLoggingOut">
          <svg v-if="!isLoggingOut" class="logout-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          <svg v-else class="logout-icon spinner" viewBox="0 0 24 24" fill="none">
            <circle class="spinner-circle" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
          </svg>
          <span>{{ isLoggingOut ? 'Saindo...' : 'Sair' }}</span>
        </button>
      </div>
    </div>
  </nav>
</template>

<style scoped>
.navbar {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(15, 12, 41, 0.95);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(167, 139, 250, 0.1);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
}

.navbar-container {
  max-width: 1600px;
  margin: 0 auto;
  padding: 0 24px;
  height: 72px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 48px;
}

/* Logo */
.navbar-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.navbar-logo:hover {
  transform: scale(1.05);
}

.logo-icon {
  font-size: 36px;
  filter: drop-shadow(0 0 10px rgba(167, 139, 250, 0.5));
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-5px); }
}

.logo-text {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.logo-title {
  font-size: 24px;
  font-weight: 800;
  background: linear-gradient(135deg, #a78bfa 0%, #ec4899 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: -0.5px;
}

.logo-subtitle {
  font-size: 10px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.5);
  text-transform: uppercase;
  letter-spacing: 1.5px;
}

/* Navigation Links */
.nav-links {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
}

.nav-link {
  position: relative;
  padding: 10px 20px;
  font-size: 15px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.nav-link::before {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 2px;
  background: linear-gradient(90deg, #a78bfa, #ec4899);
  transition: width 0.3s ease;
}

.nav-link:hover {
  color: #ffffff;
  background: rgba(167, 139, 250, 0.1);
}

.nav-link:hover::before {
  width: 80%;
}

.nav-link-active {
  color: #ffffff;
  background: rgba(167, 139, 250, 0.15);
}

.nav-link-active::before {
  width: 80%;
}

/* User Actions */
.nav-actions {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-shrink: 0;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 16px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 50px;
  transition: all 0.3s ease;
}

.user-info:hover {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(167, 139, 250, 0.3);
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg, #a78bfa, #8b5cf6);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
  color: #ffffff;
  box-shadow: 0 0 20px rgba(167, 139, 250, 0.4);
}

.user-details {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.user-name {
  font-size: 14px;
  font-weight: 600;
  color: #ffffff;
  line-height: 1;
}

.user-role {
  font-size: 11px;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.5);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  line-height: 1;
}

/* Logout Button */
.logout-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  font-size: 14px;
  font-weight: 600;
  color: #ffffff;
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.3);
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.logout-btn:hover:not(:disabled) {
  background: rgba(239, 68, 68, 0.2);
  border-color: rgba(239, 68, 68, 0.5);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.logout-btn:active:not(:disabled) {
  transform: translateY(0);
}

.logout-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.logout-icon {
  width: 18px;
  height: 18px;
}

.spinner {
  animation: spin 1s linear infinite;
}

.spinner-circle {
  stroke-dasharray: 60;
  stroke-dashoffset: 45;
  animation: dash 1.5s ease-in-out infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@keyframes dash {
  0% {
    stroke-dashoffset: 60;
  }
  50% {
    stroke-dashoffset: 15;
  }
  100% {
    stroke-dashoffset: 60;
  }
}

/* Responsive */
@media (max-width: 1024px) {
  .nav-links {
    gap: 4px;
  }

  .nav-link {
    padding: 8px 12px;
    font-size: 14px;
  }
}

@media (max-width: 768px) {
  .navbar-container {
    padding: 0 16px;
    height: 64px;
    gap: 16px;
  }

  .logo-text {
    display: none;
  }

  .nav-links {
    display: none;
  }

  .user-details {
    display: none;
  }

  .user-info {
    padding: 8px;
  }

  .logout-btn {
    padding: 10px;
  }

  .logout-btn span {
    display: none;
  }
}

@media (min-width: 769px) and (max-width: 1024px) {
  .user-name {
    max-width: 100px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
}
</style>