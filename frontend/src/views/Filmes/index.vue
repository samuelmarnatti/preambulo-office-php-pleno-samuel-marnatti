<script setup>
import { onMounted, ref, computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import Navbar from '@/components/Navbar.vue';
import api from '@/services/api';

const authStore = useAuthStore();
const router = useRouter();
const filmes = ref([]);
const loading = ref(true);
const categoriaSelecionada = ref('Todas');
const busca = ref('');
const filmesSelecionados = ref([]);
const processandoLocacao = ref(false);
const mensagem = ref({ tipo: '', texto: '' });

const categorias = computed(() => {
  const cats = ['Todas', ...new Set(filmes.value.map(f => f.categoria))];
  return cats;
});

const filmesFiltrados = computed(() => {
  let resultado = filmes.value;

  if (categoriaSelecionada.value !== 'Todas') {
    resultado = resultado.filter(f => f.categoria === categoriaSelecionada.value);
  }

  if (busca.value) {
    resultado = resultado.filter(f =>
      f.titulo.toLowerCase().includes(busca.value.toLowerCase())
    );
  }

  return resultado;
});

const carregarFilmes = async () => {
  try {
    const response = await api.get('/filmes/disponiveis');
    filmes.value = response.data;
  } catch (error) {
    console.error('Erro ao carregar filmes:', error);
  } finally {
    loading.value = false;
  }
};

const toggleSelecao = (filmeId) => {
  const index = filmesSelecionados.value.indexOf(filmeId);
  if (index > -1) {
    filmesSelecionados.value.splice(index, 1);
  } else {
    filmesSelecionados.value.push(filmeId);
  }
};

const criarLocacao = async () => {
  if (filmesSelecionados.value.length === 0) {
    mensagem.value = { tipo: 'error', texto: 'Selecione pelo menos um filme!' };
    return;
  }

  processandoLocacao.value = true;
  mensagem.value = { tipo: '', texto: '' };

  try {
    await api.post('/locacoes', {
      filme_ids: filmesSelecionados.value,
      dias_locacao: 7,
    });

    mensagem.value = { tipo: 'success', texto: 'Locação realizada com sucesso!' };
    filmesSelecionados.value = [];
    await carregarFilmes(); // Recarregar para atualizar estoque
  } catch (error) {
    mensagem.value = {
      tipo: 'error',
      texto: error.response?.data?.message || 'Erro ao criar locação',
    };
  } finally {
    processandoLocacao.value = false;
  }
};

const valorTotal = computed(() => {
  return filmesSelecionados.value.reduce((total, id) => {
    const filme = filmes.value.find(f => f.id === id);
    return total + (filme?.valor_locacao || 0);
  }, 0);
});

const handleFilmeClick = (filme) => {
  // Se for atendente ou admin, vai para edição
  if (authStore.user?.role === 'atendente' || authStore.user?.role === 'administrador') {
    router.push({ name: 'FilmesEditar', params: { id: filme.id } });
  }
  // Se for cliente e filme disponível, vai para detalhes
  else if (authStore.isCliente) {
    router.push({ name: 'FilmesDetalhes', params: { id: filme.id } });
  }
};

const handleInfoClick = (filme) => {
  // Mesmo comportamento ao clicar nas informações
  handleFilmeClick(filme);
};

const getImageUrl = (imagemUrl) => {
  // Se a URL já é completa (http/https), retorna como está
  if (imagemUrl?.startsWith('http')) {
    return imagemUrl;
  }
  // Caso contrário, constrói a URL completa com o backend
  const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000';
  return `${baseUrl}/storage/${imagemUrl}`;
};

const handleImageError = (event) => {
  // Se a imagem não carregar, esconde o elemento
  event.target.parentElement.style.display = 'none';
};

const formatarPreco = (valor) => {
  return parseFloat(valor).toFixed(2).replace('.', ',');
};

onMounted(() => {
  carregarFilmes();
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
          <h1 class="page-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <rect x="2" y="3" width="20" height="18" rx="2" stroke-width="2"/>
              <line x1="7" y1="3" x2="7" y2="21" stroke-width="2"/>
              <line x1="17" y1="3" x2="17" y2="21" stroke-width="2"/>
            </svg>
            Explorar Catálogo
          </h1>
          <p class="page-subtitle">{{ filmesFiltrados.length }} filmes disponíveis</p>
        </div>
        
        <!-- Botão Adicionar Filme (apenas para atendente/admin) -->
        <router-link 
          v-if="authStore.user?.role === 'atendente' || authStore.user?.role === 'administrador'"
          :to="{ name: 'FilmesNovo' }"
          class="add-movie-button"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Adicionar Filme
        </router-link>
      </div>

      <!-- Filtros -->
      <div class="filters-container">
        <div class="filter-group">
          <div class="search-wrapper">
            <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
            </svg>
            <input
              v-model="busca"
              type="text"
              placeholder="Buscar filmes..."
              class="search-input"
            />
          </div>
        </div>

        <div class="categories-wrapper">
          <button
            v-for="cat in categorias"
            :key="cat"
            @click="categoriaSelecionada = cat"
            :class="[
              'category-chip',
              categoriaSelecionada === cat ? 'category-chip-active' : ''
            ]"
          >
            {{ cat }}
          </button>
        </div>
      </div>

      <!-- Mensagens -->
      <transition name="fade">
        <div v-if="mensagem.texto" :class="[
          'message-banner',
          mensagem.tipo === 'success' ? 'message-banner-success' : 'message-banner-error'
        ]">
          <svg v-if="mensagem.tipo === 'success'" class="message-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <svg v-else class="message-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          {{ mensagem.texto }}
        </div>
      </transition>

      <!-- Carrinho de Locação -->
      <transition name="slide-down">
        <div v-if="authStore.isCliente && filmesSelecionados.length > 0" class="cart-banner">
          <div class="cart-content">
            <div class="cart-info">
              <svg class="cart-icon" viewBox="0 0 20 20" fill="currentColor">
                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
              </svg>
              <div>
                <h3 class="cart-title">{{ filmesSelecionados.length }} filme(s) no carrinho</h3>
                <p class="cart-total">Total: R$ {{ formatarPreco(valorTotal) }}</p>
              </div>
            </div>
            <button
              @click="criarLocacao"
              :disabled="processandoLocacao"
              class="cart-button"
            >
              <span v-if="processandoLocacao" class="button-spinner"></span>
              {{ processandoLocacao ? 'Processando...' : 'Confirmar Locação' }}
            </button>
          </div>
        </div>
      </transition>

      <!-- Loading -->
      <div v-if="loading" class="loading-container">
        <div class="loading-spinner-large"></div>
        <p class="loading-text">Carregando catálogo...</p>
      </div>

      <!-- Grid de Filmes -->
      <div v-else-if="filmesFiltrados.length > 0" class="movies-grid">
        <div
          v-for="filme in filmesFiltrados"
          :key="filme.id"
          :class="[
            'movie-card',
            filmesSelecionados.includes(filme.id) ? 'movie-card-selected' : '',
            filme.quantidade_disponivel === 0 ? 'movie-card-unavailable' : ''
          ]"
        >
          <!-- Poster placeholder -->
          <div 
            class="movie-poster"
            @click="handleFilmeClick(filme)"
          >
            <div v-if="filme.imagem_url" class="poster-image">
              <img 
                :src="getImageUrl(filme.imagem_url)" 
                :alt="filme.titulo"
                @error="handleImageError"
              />
            </div>
            <div v-else class="poster-gradient">
              <svg class="poster-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3.5 3a.5.5 0 00-.5.5v17a.5.5 0 00.5.5h17a.5.5 0 00.5-.5v-17a.5.5 0 00-.5-.5h-17zm4.5 3a2.5 2.5 0 110 5 2.5 2.5 0 010-5zm9.5 12h-11v-2l3-3 2.5 2.5L15 12l3.5 3.5v2.5z"/>
              </svg>
            </div>
            
            <!-- Selection indicator -->
            <div v-if="filmesSelecionados.includes(filme.id)" class="selection-badge">
              <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
            </div>

            <!-- Category badge -->
            <div class="category-badge">{{ filme.categoria }}</div>

            <!-- Availability badge -->
            <div v-if="filme.quantidade_disponivel === 0" class="unavailable-overlay">
              <span>Indisponível</span>
            </div>
          </div>

          <!-- Movie info -->
          <div 
            class="movie-info"
            @click="handleInfoClick(filme)"
          >
            <h3 class="movie-title">{{ filme.titulo }}</h3>
            
            <p class="movie-synopsis">{{ filme.sinopse }}</p>

            <div class="movie-meta">
              <span class="meta-item">
                <svg viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                {{ filme.ano }}
              </span>
              <span class="meta-item">
                <svg viewBox="0 0 20 20" fill="currentColor">
                  <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 100 4v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2a2 2 0 100-4V6z"/>
                </svg>
                {{ filme.quantidade_disponivel }} disponível
              </span>
            </div>

            <div class="movie-footer">
              <span class="movie-price">R$ {{ formatarPreco(filme.valor_locacao) }}</span>
              <span 
                v-if="filme.quantidade_disponivel > 0" 
                class="status-badge status-available"
              >
                Disponível
              </span>
              <span 
                v-else 
                class="status-badge status-unavailable"
              >
                Esgotado
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else class="empty-state">
        <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path d="M9.172 16.172a4 4 0 015.656 0M12 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <h3 class="empty-title">Nenhum filme encontrado</h3>
        <p class="empty-description">Tente ajustar os filtros ou buscar por outro termo</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Add Movie Button */
.add-movie-button {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 14px 28px;
  font-size: 15px;
  font-weight: 600;
  background: linear-gradient(135deg, #a78bfa, #8b5cf6);
  border: none;
  border-radius: 12px;
  color: #ffffff;
  text-decoration: none;
  box-shadow: 0 8px 24px rgba(167, 139, 250, 0.4);
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.add-movie-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s ease;
}

.add-movie-button:hover {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
  box-shadow: 0 12px 32px rgba(167, 139, 250, 0.5);
  transform: translateY(-2px);
}

.add-movie-button:hover::before {
  left: 100%;
}

.add-movie-button:active {
  transform: translateY(0);
  box-shadow: 0 4px 16px rgba(167, 139, 250, 0.3);
}

/* Filters */
.filters-container {
  margin-bottom: 32px;
}

.filter-group {
  margin-bottom: 24px;
}

.search-wrapper {
  position: relative;
  max-width: 600px;
  margin: 0 auto;
}

.search-icon {
  position: absolute;
  left: 20px;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  color: rgba(255, 255, 255, 0.4);
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 16px 20px 16px 52px;
  font-size: 16px;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 50px;
  color: #ffffff;
  transition: all 0.3s ease;
  outline: none;
}

.search-input::placeholder {
  color: rgba(255, 255, 255, 0.4);
}

.search-input:focus {
  background: rgba(20, 20, 30, 0.8);
  border-color: #a78bfa;
  box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.1);
}

.categories-wrapper {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  justify-content: center;
}

.category-chip {
  padding: 10px 24px;
  font-size: 14px;
  font-weight: 600;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 50px;
  color: rgba(255, 255, 255, 0.7);
  cursor: pointer;
  transition: all 0.3s ease;
  outline: none;
}

.category-chip:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(167, 139, 250, 0.5);
  color: #ffffff;
}

.category-chip-active {
  background: linear-gradient(135deg, #a78bfa, #8b5cf6);
  border-color: #a78bfa;
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(167, 139, 250, 0.3);
}

/* Cart Banner */
.cart-banner {
  background: rgba(167, 139, 250, 0.15);
  backdrop-filter: blur(20px);
  border: 2px solid #a78bfa;
  border-radius: 16px;
  padding: 20px 28px;
  margin-bottom: 32px;
  box-shadow: 0 8px 32px rgba(167, 139, 250, 0.2);
}

.cart-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 24px;
  flex-wrap: wrap;
}

.cart-info {
  display: flex;
  align-items: center;
  gap: 16px;
}

.cart-icon {
  width: 32px;
  height: 32px;
  color: #a78bfa;
}

.cart-title {
  font-size: 18px;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 4px;
}

.cart-total {
  font-size: 14px;
  color: #c4b5fd;
}

.cart-button {
  padding: 12px 32px;
  font-size: 15px;
  font-weight: 700;
  background: linear-gradient(135deg, #a78bfa, #8b5cf6);
  border: none;
  border-radius: 8px;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.cart-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(167, 139, 250, 0.4);
}

.cart-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.button-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #ffffff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Loading */
.loading-container {
  text-align: center;
  padding: 80px 20px;
}

.loading-spinner-large {
  width: 48px;
  height: 48px;
  border: 4px solid rgba(167, 139, 250, 0.2);
  border-top-color: #a78bfa;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

.loading-text {
  color: rgba(255, 255, 255, 0.6);
  font-size: 16px;
}

/* Movies Grid */
.movies-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 24px;
}

.movie-card {
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
}

.movie-card:hover {
  transform: translateY(-8px) scale(1.02);
  border-color: #a78bfa;
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.5);
}

.movie-card-selected {
  border-color: #a78bfa;
  box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.3);
}

.movie-card-unavailable {
  opacity: 0.6;
  cursor: not-allowed;
}

.movie-card-unavailable:hover {
  transform: none;
}

/* Poster */
.movie-poster {
  position: relative;
  aspect-ratio: 2/3;
  overflow: hidden;
  background: linear-gradient(135deg, #1e1b4b, #312e81);
  cursor: pointer;
  transition: all 0.3s ease;
}

.movie-poster:hover {
  transform: scale(1.05);
}

.poster-image {
  width: 100%;
  height: 100%;
  position: relative;
  overflow: hidden;
}

.poster-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.3s ease;
}

.movie-poster:hover .poster-image img {
  transform: scale(1.1);
}

.poster-gradient {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, rgba(167, 139, 250, 0.2), rgba(139, 92, 246, 0.2));
}

.poster-icon {
  width: 64px;
  height: 64px;
  color: rgba(255, 255, 255, 0.3);
}

.selection-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  width: 32px;
  height: 32px;
  background: #a78bfa;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(167, 139, 250, 0.5);
  animation: scaleIn 0.3s ease;
}

@keyframes scaleIn {
  from { transform: scale(0); }
  to { transform: scale(1); }
}

.selection-badge svg {
  width: 20px;
  height: 20px;
  color: #ffffff;
}

.category-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  padding: 6px 12px;
  background: rgba(167, 139, 250, 0.9);
  backdrop-filter: blur(10px);
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  color: #ffffff;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.unavailable-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
}

.unavailable-overlay span {
  padding: 8px 16px;
  background: rgba(239, 68, 68, 0.9);
  border-radius: 6px;
  font-size: 13px;
  font-weight: 700;
  color: #ffffff;
  text-transform: uppercase;
}

/* Movie Info */
.movie-info {
  padding: 16px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.movie-info:hover {
  background: rgba(255, 255, 255, 0.03);
}

.movie-title {
  font-size: 16px;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 8px;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.movie-synopsis {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.6);
  line-height: 1.5;
  margin-bottom: 12px;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.movie-meta {
  display: flex;
  gap: 12px;
  margin-bottom: 12px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  color: rgba(255, 255, 255, 0.5);
}

.meta-item svg {
  width: 14px;
  height: 14px;
}

.movie-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 12px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.movie-price {
  font-size: 20px;
  font-weight: 800;
  color: #34d399;
}

.status-badge {
  padding: 4px 12px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-available {
  background: rgba(16, 185, 129, 0.2);
  color: #6ee7b7;
}

.status-unavailable {
  background: rgba(239, 68, 68, 0.2);
  color: #fca5a5;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 80px 20px;
}

.empty-icon {
  width: 80px;
  height: 80px;
  color: rgba(255, 255, 255, 0.2);
  stroke-width: 1.5;
  margin: 0 auto 24px;
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

/* Messages */
.message-banner {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 20px;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 600;
  backdrop-filter: blur(10px);
  border: 1px solid;
  animation: slideIn 0.3s ease-out;
  margin-bottom: 24px;
}

.message-banner-success {
  background: rgba(16, 185, 129, 0.15);
  border-color: rgba(16, 185, 129, 0.4);
  color: #6ee7b7;
  box-shadow: 0 8px 24px rgba(16, 185, 129, 0.2);
}

.message-banner-error {
  background: rgba(239, 68, 68, 0.15);
  border-color: rgba(239, 68, 68, 0.4);
  color: #fca5a5;
  box-shadow: 0 8px 24px rgba(239, 68, 68, 0.2);
}

.message-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from, .slide-down-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}

/* Responsive */
@media (max-width: 768px) {
  .page-title {
    font-size: 32px;
  }

  .title-icon {
    width: 32px;
    height: 32px;
  }

  .movies-grid {
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 16px;
  }

  .cart-content {
    flex-direction: column;
    align-items: stretch;
  }

  .cart-button {
    width: 100%;
    justify-content: center;
  }
}
</style>