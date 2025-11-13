<script setup>
import { onMounted, ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import Navbar from '@/components/Navbar.vue';
import api from '@/services/api';

const authStore = useAuthStore();
const stats = ref(null);
const loading = ref(true);

onMounted(async () => {
  try {
    const [filmesRes, locacoesRes] = await Promise.all([
      api.get('/filmes/disponiveis'),
      authStore.isCliente ? api.get('/locacoes/ativas') : Promise.resolve({ data: [] }),
    ]);

    stats.value = {
      filmesDisponiveis: filmesRes.data.length,
      locacoesAtivas: locacoesRes.data.length,
    };
  } catch (error) {
    console.error('Erro ao carregar dados:', error);
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="page-container">
    <!-- Background -->
    <div class="background-overlay"></div>
    <div class="background-pattern"></div>

    <Navbar />

    <div class="content-wrapper">
      <!-- Hero Section -->
      <div class="hero-section">
        <div class="welcome-badge">
          <svg class="badge-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
          </svg>
          <span>{{ authStore.user?.role?.toUpperCase() }}</span>
        </div>
        
        <h1 class="hero-title">
          Olá, <span class="gradient-text">{{ authStore.user?.name }}</span>
        </h1>
        
        <p class="hero-subtitle">
          O que você quer assistir hoje?
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-container">
        <div class="loading-spinner-large"></div>
        <p class="loading-text">Carregando seu conteúdo...</p>
      </div>

      <!-- Stats Cards -->
      <div v-else class="stats-grid">
        <div class="stat-card stat-card-primary">
          <div class="stat-icon-wrapper">
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <rect x="2" y="3" width="20" height="18" rx="2" stroke-width="2"/>
              <line x1="7" y1="3" x2="7" y2="21" stroke-width="2"/>
              <line x1="17" y1="3" x2="17" y2="21" stroke-width="2"/>
            </svg>
          </div>
          <div class="stat-content">
            <h3 class="stat-label">Filmes Disponíveis</h3>
            <p class="stat-value">{{ stats?.filmesDisponiveis || 0 }}</p>
            <div class="stat-bar">
              <div class="stat-bar-fill" :style="{ width: '75%' }"></div>
            </div>
          </div>
        </div>

        <div v-if="authStore.isCliente" class="stat-card stat-card-success">
          <div class="stat-icon-wrapper">
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <circle cx="12" cy="12" r="10" stroke-width="2"/>
              <path d="M10 8l6 4-6 4V8z" fill="currentColor" stroke="none"/>
            </svg>
          </div>
          <div class="stat-content">
            <h3 class="stat-label">Locações Ativas</h3>
            <p class="stat-value">{{ stats?.locacoesAtivas || 0 }}</p>
            <div class="stat-bar">
              <div class="stat-bar-fill" :style="{ width: '60%' }"></div>
            </div>
          </div>
        </div>

        <div class="stat-card stat-card-accent">
          <div class="stat-icon-wrapper">
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke-width="2" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="stat-content">
            <h3 class="stat-label">Seu Perfil</h3>
            <p class="stat-value stat-value-text">{{ authStore.user?.role }}</p>
            <p class="stat-description">Acesso completo</p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="actions-section">
        <h2 class="section-title">
          <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke-width="2" stroke-linejoin="round"/>
          </svg>
          Ações Rápidas
        </h2>

        <div class="actions-grid">
          <router-link to="/filmes" class="action-card action-card-catalog">
            <div class="action-card-glow"></div>
            <div class="action-icon-wrapper">
              <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/>
                <path d="M7 3v18M17 3v18M3 8h4M3 16h4M17 8h4M17 16h4" stroke-width="2"/>
              </svg>
            </div>
            <div class="action-content">
              <h3 class="action-title">Explorar Catálogo</h3>
              <p class="action-description">Descubra filmes incríveis para assistir</p>
            </div>
            <div class="action-arrow">
              <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
              </svg>
            </div>
          </router-link>

          <router-link
            v-if="authStore.isCliente"
            to="/minhas-locacoes"
            class="action-card action-card-rentals"
          >
            <div class="action-card-glow"></div>
            <div class="action-icon-wrapper">
              <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2"/>
                <path d="M9 12h6M9 16h6" stroke-width="2"/>
              </svg>
            </div>
            <div class="action-content">
              <h3 class="action-title">Minhas Locações</h3>
              <p class="action-description">Gerencie suas locações e histórico</p>
            </div>
            <div class="action-arrow">
              <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
              </svg>
            </div>
          </router-link>

          <router-link
            v-if="authStore.isAdmin || authStore.isAtendente"
            to="/admin"
            class="action-card action-card-admin"
          >
            <div class="action-card-glow"></div>
            <div class="action-icon-wrapper">
              <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M12 2a5 5 0 00-5 5v3H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V7a5 5 0 00-5-5z" stroke-width="2"/>
                <circle cx="12" cy="16" r="1" fill="currentColor"/>
              </svg>
            </div>
            <div class="action-content">
              <h3 class="action-title">Painel Admin</h3>
              <p class="action-description">Gerencie o sistema e usuários</p>
            </div>
            <div class="action-arrow">
              <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
              </svg>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Hero Section - Home specific */
.hero-section {
  text-align: center;
  padding: 48px 0 64px;
}

.welcome-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 20px;
  background: rgba(167, 139, 250, 0.1);
  border: 1px solid rgba(167, 139, 250, 0.3);
  border-radius: 50px;
  color: #c4b5fd;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 24px;
}

.badge-icon {
  width: 16px;
  height: 16px;
}

.hero-title {
  font-size: 56px;
  font-weight: 800;
  color: #ffffff;
  margin-bottom: 16px;
  line-height: 1.2;
}

.hero-subtitle {
  font-size: 20px;
  color: rgba(255, 255, 255, 0.7);
  font-weight: 300;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
  margin-bottom: 64px;
}

.stat-card {
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 32px;
  display: flex;
  gap: 24px;
  align-items: flex-start;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, transparent, var(--stat-color), transparent);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-4px);
  border-color: var(--stat-color);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
}

.stat-card:hover::before {
  opacity: 1;
}

.stat-card-primary {
  --stat-color: #a78bfa;
}

.stat-card-success {
  --stat-color: #34d399;
}

.stat-card-accent {
  --stat-color: #f472b6;
}

.stat-icon-wrapper {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--stat-color);
  background: linear-gradient(135deg, var(--stat-color), rgba(255, 255, 255, 0.2));
  flex-shrink: 0;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}

.stat-icon {
  width: 32px;
  height: 32px;
  color: #ffffff;
}

.stat-content {
  flex: 1;
}

.stat-label {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.6);
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 48px;
  font-weight: 800;
  color: #ffffff;
  line-height: 1;
  margin-bottom: 12px;
}

.stat-value-text {
  font-size: 24px;
  text-transform: capitalize;
}

.stat-description {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.5);
  margin-top: 4px;
}

.stat-bar {
  height: 4px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 2px;
  overflow: hidden;
}

.stat-bar-fill {
  height: 100%;
  background: var(--stat-color);
  border-radius: 2px;
  transition: width 1s ease;
}

/* Actions Section */
.actions-section {
  margin-top: 48px;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 28px;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 32px;
}

.section-icon {
  width: 32px;
  height: 32px;
  color: #a78bfa;
  stroke-width: 2;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 24px;
}

.action-card {
  position: relative;
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 28px;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  text-decoration: none;
  transition: all 0.3s ease;
  overflow: hidden;
  cursor: pointer;
}

.action-card-glow {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, var(--action-color), transparent);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.action-card:hover .action-card-glow {
  opacity: 0.1;
}

.action-card:hover {
  transform: translateX(8px);
  border-color: var(--action-color);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.action-card-catalog {
  --action-color: #a78bfa;
}

.action-card-rentals {
  --action-color: #34d399;
}

.action-card-admin {
  --action-color: #f59e0b;
}

.action-icon-wrapper {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.05);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  border: 1px solid rgba(255, 255, 255, 0.1);
  transition: all 0.3s ease;
}

.action-card:hover .action-icon-wrapper {
  background: var(--action-color);
  border-color: var(--action-color);
  transform: scale(1.1);
}

.action-icon {
  width: 28px;
  height: 28px;
  color: var(--action-color);
  stroke-width: 2;
  transition: color 0.3s ease;
}

.action-card:hover .action-icon {
  color: #ffffff;
}

.action-content {
  flex: 1;
  position: relative;
  z-index: 1;
}

.action-title {
  font-size: 18px;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 6px;
}

.action-description {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.6);
  line-height: 1.4;
}

.action-arrow {
  width: 24px;
  height: 24px;
  color: rgba(255, 255, 255, 0.4);
  flex-shrink: 0;
  transition: all 0.3s ease;
  position: relative;
  z-index: 1;
}

.action-card:hover .action-arrow {
  color: var(--action-color);
  transform: translateX(4px);
}

/* Responsive */
@media (max-width: 768px) {
  .hero-title {
    font-size: 36px;
  }

  .hero-subtitle {
    font-size: 16px;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .stat-card {
    padding: 24px;
  }

  .stat-value {
    font-size: 36px;
  }

  .actions-grid {
    grid-template-columns: 1fr;
  }

  .action-card {
    padding: 20px;
  }
}
</style>