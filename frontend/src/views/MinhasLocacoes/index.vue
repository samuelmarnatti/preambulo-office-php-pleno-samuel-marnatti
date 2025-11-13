<script setup>
import { onMounted, ref, computed } from 'vue';
import Navbar from '@/components/Navbar.vue';
import api from '@/services/api';

const locacoes = ref([]);
const loading = ref(true);
const filtroStatus = ref('todas');

const locacoesFiltradas = computed(() => {
  if (filtroStatus.value === 'todas') return locacoes.value;
  return locacoes.value.filter(l => l.status === filtroStatus.value);
});

const carregarLocacoes = async () => {
  try {
    const response = await api.get('/locacoes');
    locacoes.value = response.data;
  } catch (error) {
    console.error('Erro ao carregar locações:', error);
  } finally {
    loading.value = false;
  }
};

const devolver = async (locacaoId) => {
  if (!confirm('Deseja realmente devolver esta locação?')) return;

  try {
    await api.post(`/locacoes/${locacaoId}/devolver`);
    await carregarLocacoes();
    alert('Devolução realizada com sucesso!');
  } catch (error) {
    alert(error.response?.data?.message || 'Erro ao devolver locação');
  }
};

const formatarData = (data) => {
  return new Date(data).toLocaleDateString('pt-BR');
};

const calcularDiasRestantes = (dataPrevista) => {
  const hoje = new Date();
  const prevista = new Date(dataPrevista);
  const diff = Math.ceil((prevista - hoje) / (1000 * 60 * 60 * 24));
  return diff;
};

const getStatusClass = (status) => {
  const classes = {
    ativo: 'badge-primary',
    atrasado: 'badge-danger',
    devolvido: 'badge-success',
  };
  return classes[status] || 'badge-primary';
};

const getStatusLabel = (status) => {
  const labels = {
    ativo: 'Ativo',
    atrasado: 'Atrasado',
    devolvido: 'Devolvido',
  };
  return labels[status] || status;
};

const formatarPreco = (valor) => {
  return parseFloat(valor).toFixed(2).replace('.', ',');
};

onMounted(() => {
  carregarLocacoes();
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
        <div>
          <h1 class="page-title">Minhas Locações</h1>
          <p class="page-subtitle">{{ locacoesFiltradas.length }} locações encontradas</p>
        </div>
      </div>

      <!-- Filtros -->
      <div class="categories-wrapper">
        <button
          @click="filtroStatus = 'todas'"
          :class="['category-chip', filtroStatus === 'todas' ? 'category-chip-active' : '']"
        >
          Todas
        </button>
        <button
          @click="filtroStatus = 'ativo'"
          :class="['category-chip', filtroStatus === 'ativo' ? 'category-chip-active' : '']"
        >
          Ativas
        </button>
        <button
          @click="filtroStatus = 'atrasado'"
          :class="['category-chip', filtroStatus === 'atrasado' ? 'category-chip-active' : '']"
        >
          Atrasadas
        </button>
        <button
          @click="filtroStatus = 'devolvido'"
          :class="['category-chip', filtroStatus === 'devolvido' ? 'category-chip-active' : '']"
        >
          Devolvidas
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="loading-container">
        <div class="loading-spinner-large"></div>
        <p class="loading-text">Carregando suas locações...</p>
      </div>

      <!-- Lista de Locações -->
      <div v-else-if="locacoesFiltradas.length > 0" class="locacoes-grid">
        <div
          v-for="locacao in locacoesFiltradas"
          :key="locacao.id"
          class="locacao-card"
        >
          <!-- Header do Card -->
          <div class="locacao-header">
            <div class="header-info">
              <h3 class="locacao-id">Locação #{{ locacao.id }}</h3>
              <p class="locacao-data">
                <svg class="date-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ formatarData(locacao.data_inicio) }}
              </p>
            </div>
            <span :class="['status-badge', `status-${locacao.status}`]">
              {{ getStatusLabel(locacao.status) }}
            </span>
          </div>

          <!-- Filmes -->
          <div class="filmes-section">
            <h4 class="section-title">
              <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3.5 3a.5.5 0 00-.5.5v17a.5.5 0 00.5.5h17a.5.5 0 00.5-.5v-17a.5.5 0 00-.5-.5h-17z"/>
              </svg>
              Filmes Alugados
            </h4>
            <div class="filmes-grid">
              <div v-for="filme in locacao.filmes" :key="filme.id" class="filme-chip">
                <svg class="filme-chip-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                </svg>
                <span>{{ filme.titulo }}</span>
                <span class="filme-ano">({{ filme.ano }})</span>
              </div>
            </div>
          </div>

          <!-- Informações da Locação -->
          <div class="info-section">
            <div class="info-row">
              <div class="info-item">
                <span class="info-label">Devolução Prevista</span>
                <span class="info-value">{{ formatarData(locacao.data_prevista_devolucao) }}</span>
              </div>

              <div v-if="locacao.status !== 'devolvido'" class="info-item">
                <span class="info-label">Dias Restantes</span>
                <span :class="['info-value', calcularDiasRestantes(locacao.data_prevista_devolucao) < 0 ? 'text-red-400' : 'text-green-400']">
                  {{ calcularDiasRestantes(locacao.data_prevista_devolucao) }} dia(s)
                </span>
              </div>

              <div v-if="locacao.data_devolucao" class="info-item">
                <span class="info-label">Devolvido em</span>
                <span class="info-value">{{ formatarData(locacao.data_devolucao) }}</span>
              </div>
            </div>

            <div class="valor-section">
              <div class="valor-item">
                <span class="valor-label">Valor Total</span>
                <span class="valor-total">R$ {{ formatarPreco(locacao.valor_total) }}</span>
              </div>

              <div v-if="locacao.multa > 0" class="valor-item">
                <span class="valor-label">Multa</span>
                <span class="valor-multa">R$ {{ formatarPreco(locacao.multa) }}</span>
              </div>
            </div>
          </div>

          <!-- Alerta de Atraso -->
          <div v-if="locacao.status === 'atrasado'" class="alert-box">
            <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="alert-content">
              <p class="alert-title">Locação Atrasada!</p>
              <p class="alert-text">Multa acumulada: R$ {{ formatarPreco(locacao.multa) }}</p>
            </div>
          </div>

          <!-- Botão Devolver -->
          <button
            v-if="locacao.status !== 'devolvido'"
            @click="devolver(locacao.id)"
            class="btn-devolver"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Devolver Filmes
          </button>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="empty-title">Nenhuma locação encontrada</h3>
        <p class="empty-description">Você ainda não possui locações. Explore nosso catálogo!</p>
        <router-link to="/filmes" class="btn btn-primary mt-6">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
          </svg>
          Ver Catálogo de Filmes
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Container & Layout */
.page-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #0f0f1e 0%, #1a1a2e 50%, #16213e 100%);
  position: relative;
  overflow-x: hidden;
}

.background-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 50% 50%, rgba(167, 139, 250, 0.1) 0%, transparent 50%);
  pointer-events: none;
  z-index: 0;
}

.background-pattern {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: radial-gradient(circle at 25px 25px, rgba(255, 255, 255, 0.02) 2%, transparent 0%),
                    radial-gradient(circle at 75px 75px, rgba(255, 255, 255, 0.02) 2%, transparent 0%);
  background-size: 100px 100px;
  pointer-events: none;
  z-index: 0;
}

.content-wrapper {
  position: relative;
  z-index: 1;
  max-width: 1400px;
  margin: 0 auto;
  padding: 100px 24px 48px;
}

/* Page Header */
.page-header {
  text-align: center;
  margin-bottom: 48px;
}

.page-title {
  font-size: 48px;
  font-weight: 700;
  background: linear-gradient(135deg, #a78bfa, #8b5cf6);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 12px;
  letter-spacing: -0.5px;
}

.page-subtitle {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.6);
  font-weight: 400;
}

/* Category Filters */
.categories-wrapper {
  display: flex;
  gap: 12px;
  justify-content: center;
  flex-wrap: wrap;
  margin-bottom: 48px;
}

.category-chip {
  padding: 12px 28px;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 24px;
  color: rgba(255, 255, 255, 0.7);
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  outline: none;
}

.category-chip:hover {
  background: rgba(30, 30, 40, 0.8);
  border-color: rgba(167, 139, 250, 0.3);
  color: #fff;
  transform: translateY(-2px);
}

.category-chip-active {
  background: linear-gradient(135deg, #a78bfa, #8b5cf6);
  border-color: #a78bfa;
  color: #fff;
  box-shadow: 0 8px 20px rgba(167, 139, 250, 0.4);
}

/* Loading State */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 100px 20px;
  gap: 24px;
}

.loading-spinner-large {
  width: 60px;
  height: 60px;
  border: 4px solid rgba(167, 139, 250, 0.2);
  border-top-color: #a78bfa;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-text {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.6);
}

/* Locacoes Grid */
.locacoes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
  gap: 24px;
}

.locacao-card {
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 28px;
  transition: all 0.3s ease;
}

.locacao-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.5);
  border-color: rgba(167, 139, 250, 0.3);
}

/* Card Header */
.locacao-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
  padding-bottom: 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.header-info {
  flex: 1;
}

.locacao-id {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.5);
  margin-bottom: 6px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.locacao-data {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 18px;
  color: #fff;
  font-weight: 600;
}

.date-icon {
  width: 18px;
  height: 18px;
  stroke-width: 2;
}

/* Status Badge */
.status-badge {
  padding: 8px 18px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
}

.status-ativo {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.status-atrasado {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: #fff;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.status-devolvido {
  background: linear-gradient(135deg, #6b7280, #4b5563);
  color: #fff;
  box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
}

/* Filmes Section */
.filmes-section {
  margin-bottom: 24px;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: rgba(255, 255, 255, 0.5);
  margin-bottom: 12px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.section-icon {
  width: 16px;
  height: 16px;
}

.filmes-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.filme-chip {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: rgba(167, 139, 250, 0.1);
  border: 1px solid rgba(167, 139, 250, 0.3);
  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
  color: #a78bfa;
  transition: all 0.2s ease;
}

.filme-chip:hover {
  background: rgba(167, 139, 250, 0.2);
  border-color: rgba(167, 139, 250, 0.5);
  transform: translateY(-1px);
}

.filme-chip-icon {
  width: 16px;
  height: 16px;
  stroke-width: 2;
}

.filme-ano {
  color: rgba(167, 139, 250, 0.6);
  font-size: 12px;
}

/* Info Section */
.info-section {
  margin-bottom: 20px;
}

.info-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
  margin-bottom: 16px;
  padding: 18px;
  background: rgba(0, 0, 0, 0.2);
  border-radius: 12px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.info-label {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.5);
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.info-value {
  font-size: 16px;
  color: #fff;
  font-weight: 600;
}

.text-red-400 {
  color: #f87171 !important;
}

.text-green-400 {
  color: #4ade80 !important;
}

/* Valor Section */
.valor-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
  padding: 18px;
  background: linear-gradient(135deg, rgba(167, 139, 250, 0.1), rgba(139, 92, 246, 0.1));
  border: 1px solid rgba(167, 139, 250, 0.2);
  border-radius: 12px;
}

.valor-item {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.valor-label {
  font-size: 12px;
  color: rgba(167, 139, 250, 0.7);
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.valor-total {
  font-size: 24px;
  color: #a78bfa;
  font-weight: 700;
}

.valor-multa {
  font-size: 20px;
  color: #ef4444;
  font-weight: 700;
}

/* Alert Box */
.alert-box {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.3);
  border-radius: 12px;
  margin-bottom: 20px;
}

.alert-icon {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
  stroke: #ef4444;
  stroke-width: 2;
}

.alert-content {
  flex: 1;
}

.alert-title {
  font-size: 14px;
  color: #ef4444;
  font-weight: 600;
  margin-bottom: 4px;
}

.alert-text {
  font-size: 13px;
  color: rgba(239, 68, 68, 0.8);
}

/* Button Devolver */
.btn-devolver {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 16px 24px;
  background: linear-gradient(135deg, #a78bfa, #8b5cf6);
  border: none;
  border-radius: 12px;
  color: #fff;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.btn-devolver::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s ease;
}

.btn-devolver:hover::before {
  left: 100%;
}

.btn-devolver:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(167, 139, 250, 0.4);
}

.btn-devolver:active {
  transform: translateY(0);
}

.w-5 {
  width: 20px;
}

.h-5 {
  height: 20px;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 100px 20px;
}

.empty-icon {
  width: 120px;
  height: 120px;
  margin: 0 auto 28px;
  color: rgba(255, 255, 255, 0.1);
  stroke-width: 1.5;
}

.empty-title {
  font-size: 28px;
  color: rgba(255, 255, 255, 0.8);
  margin-bottom: 12px;
  font-weight: 600;
}

.empty-description {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.5);
  margin-bottom: 36px;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 16px 32px;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  border: none;
  outline: none;
}

.btn-primary {
  background: linear-gradient(135deg, #a78bfa, #8b5cf6);
  color: #fff;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(167, 139, 250, 0.4);
}

.mt-6 {
  margin-top: 24px;
}

/* Responsive */
@media (max-width: 768px) {
  .page-title {
    font-size: 32px;
  }

  .content-wrapper {
    padding: 80px 16px 32px;
  }

  .locacoes-grid {
    grid-template-columns: 1fr;
  }

  .info-row {
    grid-template-columns: 1fr;
  }

  .valor-section {
    grid-template-columns: 1fr;
  }
}
</style>
