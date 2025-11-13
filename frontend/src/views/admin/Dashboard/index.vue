<script setup>
import { onMounted, ref } from 'vue';
import Navbar from '@/components/Navbar.vue';
import api from '@/services/api';

const devolucoesPrevistas = ref([]);
const loading = ref(true);

const carregarDevolucoes = async () => {
  try {
    const response = await api.get('/admin/devolucoes-hoje');
    devolucoesPrevistas.value = response.data;
  } catch (error) {
    console.error('Erro ao carregar devoluções:', error);
  } finally {
    loading.value = false;
  }
};

const formatarData = (data) => {
  return new Date(data).toLocaleDateString('pt-BR');
};

const getStatusBadgeClass = (status) => {
  return status === 'ativo' ? 'badge-success' : 'badge-danger';
};

const getStatusLabel = (status) => {
  return status === 'ativo' ? 'No Prazo' : 'Atrasado';
};

const formatarPreco = (valor) => {
  return parseFloat(valor).toFixed(2).replace('.', ',');
};

onMounted(() => {
  carregarDevolucoes();
});
</script>

<template>
  <div class="page-container">
    <!-- Background -->
    <div class="background-overlay"></div>
    <div class="background-pattern"></div>

    <Navbar />

    <div class="content-wrapper">
      <!-- Header -->
      <div class="page-header">
        <h1 class="page-title">
          <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          Dashboard Administrativo
        </h1>
        <p class="page-subtitle">Gerencie devoluções e acompanhe estatísticas</p>
      </div>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card stat-primary">
          <div class="stat-icon-wrapper">
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
          </div>
          <div class="stat-content">
            <p class="stat-label">Total de Devoluções Hoje</p>
            <p class="stat-value">{{ devolucoesPrevistas.length }}</p>
          </div>
        </div>

        <div class="stat-card stat-success">
          <div class="stat-icon-wrapper">
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="stat-content">
            <p class="stat-label">No Prazo</p>
            <p class="stat-value">{{ devolucoesPrevistas.filter(l => l.status === 'ativo').length }}</p>
          </div>
        </div>

        <div class="stat-card stat-danger">
          <div class="stat-icon-wrapper">
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="stat-content">
            <p class="stat-label">Atrasadas</p>
            <p class="stat-value">{{ devolucoesPrevistas.filter(l => l.status === 'atrasado').length }}</p>
          </div>
        </div>
      </div>

      <!-- Devoluções Section -->
      <div class="devolucoes-section">
        <div class="section-header">
          <h2 class="section-title">
            <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Devoluções Previstas para Hoje
          </h2>
          <div class="date-badge">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ new Date().toLocaleDateString('pt-BR') }}
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="loading-container">
          <div class="loading-spinner-large"></div>
          <p class="loading-text">Carregando devoluções...</p>
        </div>

        <!-- Lista de Devoluções -->
        <div v-else-if="devolucoesPrevistas.length > 0" class="devolucoes-grid">
          <div
            v-for="locacao in devolucoesPrevistas"
            :key="locacao.id"
            class="devolucao-card glass-card"
          >
            <!-- Header -->
            <div class="devolucao-header">
              <div>
                <h3 class="devolucao-id">Locação #{{ locacao.id }}</h3>
                <p class="cliente-info">
                  <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  {{ locacao.cliente?.name }}
                </p>
                <p class="cliente-email">{{ locacao.cliente?.email }}</p>
              </div>
              <span :class="['status-badge', getStatusBadgeClass(locacao.status)]">
                {{ getStatusLabel(locacao.status) }}
              </span>
            </div>

            <!-- Filmes -->
            <div class="filmes-section">
              <h4 class="filmes-title">
                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                </svg>
                Filmes
              </h4>
              <ul class="filmes-list">
                <li v-for="filme in locacao.filmes" :key="filme.id" class="filme-item">
                  <span class="filme-dot">•</span>
                  {{ filme.titulo }}
                </li>
              </ul>
            </div>

            <!-- Info Grid -->
            <div class="info-grid">
              <div class="info-item">
                <p class="info-label">Data Início</p>
                <p class="info-value">{{ formatarData(locacao.data_inicio) }}</p>
              </div>
              <div class="info-item">
                <p class="info-label">Devolução Prevista</p>
                <p class="info-value">{{ formatarData(locacao.data_prevista_devolucao) }}</p>
              </div>
              <div class="info-item">
                <p class="info-label">Valor Total:</p>
                <p class="info-value text-green-400">R$ {{ formatarPreco(locacao.valor_total) }}</p>
              </div>
            </div>

            <!-- Multa Alert -->
            <div v-if="locacao.multa > 0" class="multa-alert">
              <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
              <div>
                <p class="alert-title">Multa Aplicada</p>
                <p class="alert-value">R$ {{ parseFloat(locacao.multa).toFixed(2) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="empty-state">
          <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <h3 class="empty-title">Nenhuma Devolução Prevista</h3>
          <p class="empty-description">Não há devoluções programadas para hoje.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
  margin-bottom: 48px;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 28px;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
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
  background: var(--stat-color);
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

.stat-primary {
  --stat-color: #a78bfa;
}

.stat-success {
  --stat-color: #34d399;
}

.stat-danger {
  --stat-color: #ef4444;
}

.stat-icon-wrapper {
  width: 64px;
  height: 64px;
  border-radius: 16px;
  background: linear-gradient(135deg, var(--stat-color), rgba(255, 255, 255, 0.1));
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}

.stat-icon {
  width: 32px;
  height: 32px;
  color: #ffffff;
  stroke-width: 2;
}

.stat-content {
  flex: 1;
}

.stat-label {
  font-size: 13px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.6);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 42px;
  font-weight: 800;
  color: #ffffff;
  line-height: 1;
}

/* Devoluções Section */
.devolucoes-section {
  margin-top: 32px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
  flex-wrap: wrap;
  gap: 16px;
}

.date-badge {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: rgba(167, 139, 250, 0.1);
  border: 1px solid rgba(167, 139, 250, 0.3);
  border-radius: 50px;
  color: #c4b5fd;
  font-size: 14px;
  font-weight: 600;
}

/* Devoluções Grid */
.devolucoes-grid {
  display: grid;
  gap: 24px;
}

.devolucao-card {
  padding: 28px;
  transition: all 0.3s ease;
}

.devolucao-card:hover {
  transform: translateY(-4px);
}

.devolucao-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 20px;
  padding-bottom: 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.devolucao-id {
  font-size: 20px;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 8px;
}

.cliente-info {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.8);
  margin-bottom: 4px;
  display: flex;
  align-items: center;
}

.cliente-email {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.5);
}

/* Status Badge */
.status-badge {
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
}

/* Filmes Section */
.filmes-section {
  margin-bottom: 20px;
  padding: 16px;
  background: rgba(0, 0, 0, 0.2);
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.05);
}

.filmes-title {
  font-size: 14px;
  font-weight: 600;
  color: #a78bfa;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
}

.filmes-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.filme-item {
  color: rgba(255, 255, 255, 0.9);
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.filme-dot {
  color: #a78bfa;
  font-weight: 900;
  font-size: 18px;
}

/* Info Grid */
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 16px;
  margin-bottom: 20px;
}

.info-item {
  background: rgba(0, 0, 0, 0.2);
  padding: 14px;
  border-radius: 10px;
  border: 1px solid rgba(255, 255, 255, 0.05);
}

.info-label {
  font-size: 11px;
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 6px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
}

.info-value {
  font-size: 16px;
  font-weight: 700;
  color: #ffffff;
}

/* Multa Alert */
.multa-alert {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 16px;
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.3);
  border-radius: 12px;
  margin-top: 16px;
}

.alert-icon {
  width: 24px;
  height: 24px;
  color: #fca5a5;
  flex-shrink: 0;
}

.alert-title {
  font-size: 13px;
  font-weight: 700;
  color: #fca5a5;
  margin-bottom: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.alert-value {
  font-size: 18px;
  font-weight: 800;
  color: #ffffff;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 80px 20px;
  background: rgba(20, 20, 30, 0.4);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
}

.empty-icon {
  width: 80px;
  height: 80px;
  color: #34d399;
  stroke-width: 1.5;
  margin: 0 auto 24px;
  opacity: 0.5;
}

.empty-title {
  font-size: 24px;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 8px;
}

.empty-description {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.5);
}

/* Responsive */
@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }

  .stat-card {
    padding: 20px;
  }

  .stat-value {
    font-size: 32px;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .devolucao-header {
    flex-direction: column;
    gap: 12px;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }
}
</style>
