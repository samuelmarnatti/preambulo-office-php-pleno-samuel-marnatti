<template>
    <div class="page-container">
        <!-- Background -->
        <div class="background-overlay"></div>
        <div class="background-pattern"></div>

        <Navbar />

        <div class="content-wrapper">
            <!-- Loading -->
            <div v-if="loading" class="loading-container">
                <div class="loading-spinner-large"></div>
                <p class="loading-text">Carregando detalhes...</p>
            </div>

            <!-- Conteúdo -->
            <div v-else-if="filme" class="detalhes-container">
                <!-- Header com Botão Voltar -->
                <div class="header-actions">
                    <button @click="voltar" class="back-button">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar
                    </button>
                </div>

                <!-- Card Principal -->
                <div class="filme-card">
                    <div class="filme-grid">
                        <!-- Poster -->
                        <div class="poster-container">
                            <div class="poster-wrapper">
                                <div v-if="filme.imagem_url" class="poster-image">
                                    <img 
                                        :src="getImageUrl(filme.imagem_url)" 
                                        :alt="filme.titulo"
                                        @error="handleImageError"
                                    />
                                </div>
                                <div v-else class="poster-placeholder">
                                    <svg class="placeholder-icon" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M3.5 3a.5.5 0 00-.5.5v17a.5.5 0 00.5.5h17a.5.5 0 00.5-.5v-17a.5.5 0 00-.5-.5h-17zm4.5 3a2.5 2.5 0 110 5 2.5 2.5 0 010-5zm9.5 12h-11v-2l3-3 2.5 2.5L15 12l3.5 3.5v2.5z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Badges -->
                            <div class="badges-container">
                                <span class="category-badge">{{ filme.categoria }}</span>
                                <span 
                                    :class="[
                                        'status-badge',
                                        filme.quantidade_disponivel > 0 ? 'status-available' : 'status-unavailable'
                                    ]"
                                >
                                    {{ filme.quantidade_disponivel > 0 ? 'Disponível' : 'Indisponível' }}
                                </span>
                            </div>
                        </div>

                        <!-- Informações -->
                        <div class="info-container">
                            <h1 class="filme-title">{{ filme.titulo }}</h1>
                            
                            <div class="meta-info">
                                <div class="meta-item">
                                    <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ filme.ano }}</span>
                                </div>
                                <div class="meta-item">
                                    <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <span>{{ filme.quantidade_disponivel }} unidades em estoque</span>
                                </div>
                            </div>

                            <div class="sinopse-section">
                                <h2 class="section-title">Sinopse</h2>
                                <p class="sinopse-text">{{ filme.sinopse }}</p>
                            </div>

                            <div class="pricing-section">
                                <div class="price-info">
                                    <span class="price-label">Valor da Locação</span>
                                    <span class="price-value">R$ {{ formatarPreco(filme.valor_locacao) }}</span>
                                </div>
                                <div class="rental-info">
                                    <div class="rental-period">
                                        <svg class="period-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Período de 7 dias</span>
                                    </div>
                                    <div class="late-fee-info">
                                        <svg class="fee-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>A multa diária é de R$ 5,00 por filme, por dia de atraso</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Ações -->
                            <div class="actions-container">
                                <button
                                    v-if="filme.quantidade_disponivel > 0"
                                    @click="alugarFilme"
                                    :disabled="processando"
                                    class="btn-rent"
                                >
                                    <svg v-if="processando" class="btn-icon animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg v-else class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ processando ? 'Processando...' : 'Alugar Agora' }}
                                </button>
                                <button v-else class="btn-unavailable" disabled>
                                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    Indisponível no Momento
                                </button>
                            </div>

                            <!-- Mensagem -->
                            <transition name="slide-down">
                                <div v-if="mensagem.texto" :class="['message-banner', `message-banner-${mensagem.tipo}`]">
                                    <svg v-if="mensagem.tipo === 'success'" class="message-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg v-else class="message-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ mensagem.texto }}
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Erro -->
            <div v-else class="error-state">
                <svg class="error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="error-title">Filme não encontrado</h3>
                <button @click="voltar" class="btn-back">Voltar ao Catálogo</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Navbar from '@/components/Navbar.vue';
import api from '@/services/api';

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const processando = ref(false);
const filme = ref(null);
const mensagem = ref({ tipo: '', texto: '' });

onMounted(async () => {
    await carregarFilme();
});

const carregarFilme = async () => {
    try {
        loading.value = true;
        const response = await api.get(`/filmes/${route.params.id}`);
        filme.value = response.data;
    } catch (error) {
        console.error('Erro ao carregar filme:', error);
    } finally {
        loading.value = false;
    }
};

const alugarFilme = async () => {
    try {
        processando.value = true;
        mensagem.value = { tipo: '', texto: '' };

        await api.post('/locacoes', {
            filme_ids: [filme.value.id],
            dias_locacao: 7,
        });

        mensagem.value = {
            tipo: 'success',
            texto: 'Locação realizada com sucesso!'
        };

        setTimeout(() => {
            router.push({ name: 'MinhasLocacoes' });
        }, 2000);
    } catch (error) {
        console.error('Erro ao alugar filme:', error);
        mensagem.value = {
            tipo: 'danger',
            texto: error.response?.data?.message || 'Erro ao realizar locação'
        };
    } finally {
        processando.value = false;
    }
};

const voltar = () => {
    router.push({ name: 'Filmes' });
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
  // Se a imagem não carregar, mostra o placeholder
  const posterImage = event.target.closest('.poster-wrapper').querySelector('.poster-image');
  if (posterImage) {
    posterImage.style.display = 'none';
  }
};

const formatarPreco = (valor) => {
  return parseFloat(valor).toFixed(2).replace('.', ',');
};
</script>

<style scoped>
/* Header Actions */
.header-actions {
    margin-bottom: 32px;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
}

.back-button:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    transform: translateX(-4px);
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

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-text {
    color: rgba(255, 255, 255, 0.6);
    font-size: 16px;
}

/* Filme Card */
.detalhes-container {
    max-width: 1200px;
    margin: 0 auto;
}

.filme-card {
    background: rgba(20, 20, 30, 0.6);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 24px;
    padding: 48px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

.filme-grid {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 48px;
}

/* Poster */
.poster-container {
    position: relative;
}

.poster-wrapper {
    aspect-ratio: 2/3;
    border-radius: 16px;
    overflow: hidden;
    border: 2px solid rgba(167, 139, 250, 0.3);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
    margin-bottom: 20px;
}

.poster-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.poster-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #1e1b4b, #312e81);
    display: flex;
    align-items: center;
    justify-content: center;
}

.placeholder-icon {
    width: 96px;
    height: 96px;
    color: rgba(255, 255, 255, 0.2);
}

.badges-container {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.category-badge {
    padding: 8px 16px;
    background: linear-gradient(135deg, #a78bfa, #8b5cf6);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-available {
    background: rgba(16, 185, 129, 0.2);
    color: #6ee7b7;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.status-unavailable {
    background: rgba(239, 68, 68, 0.2);
    color: #fca5a5;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

/* Info Container */
.info-container {
    display: flex;
    flex-direction: column;
    gap: 32px;
}

.filme-title {
    font-size: 48px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    background: linear-gradient(135deg, #ffffff, #a78bfa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.meta-info {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    color: rgba(255, 255, 255, 0.7);
}

.meta-icon {
    width: 20px;
    height: 20px;
    color: #a78bfa;
    stroke-width: 2;
}

.sinopse-section {
    padding: 24px;
    background: rgba(255, 255, 255, 0.03);
    border-left: 4px solid #a78bfa;
    border-radius: 8px;
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 12px;
}

.sinopse-text {
    font-size: 16px;
    line-height: 1.8;
    color: rgba(255, 255, 255, 0.8);
}

/* Pricing */
.pricing-section {
    padding: 24px;
    background: linear-gradient(135deg, rgba(167, 139, 250, 0.1), rgba(217, 70, 239, 0.1));
    border: 2px solid rgba(167, 139, 250, 0.3);
    border-radius: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.price-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.price-label {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.price-value {
    font-size: 36px;
    font-weight: 800;
    color: #34d399;
}

.rental-info {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.rental-period {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    color: rgba(255, 255, 255, 0.7);
}

.period-icon {
    width: 20px;
    height: 20px;
    color: #a78bfa;
}

.late-fee-info {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.6);
    padding: 8px 12px;
    background: rgba(239, 68, 68, 0.1);
    border-left: 3px solid #ef4444;
    border-radius: 6px;
}

.fee-icon {
    width: 18px;
    height: 18px;
    color: #fca5a5;
    flex-shrink: 0;
}

/* Actions */
.actions-container {
    display: flex;
    gap: 16px;
}

.btn-rent {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 18px 32px;
    font-size: 18px;
    font-weight: 700;
    background: linear-gradient(135deg, #a78bfa, #8b5cf6);
    border: none;
    border-radius: 12px;
    color: #ffffff;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(167, 139, 250, 0.4);
    transition: all 0.3s ease;
}

.btn-rent:hover:not(:disabled) {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    box-shadow: 0 12px 32px rgba(167, 139, 250, 0.6);
    transform: translateY(-2px);
}

.btn-rent:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.btn-unavailable {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 18px 32px;
    font-size: 18px;
    font-weight: 700;
    background: rgba(239, 68, 68, 0.2);
    border: 2px solid rgba(239, 68, 68, 0.3);
    border-radius: 12px;
    color: #fca5a5;
    cursor: not-allowed;
}

/* Error State */
.error-state {
    text-align: center;
    padding: 80px 20px;
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
    stroke-width: 2.5;
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

.error-state {
    text-align: center;
    padding: 80px 20px;
}

.error-icon {
    width: 80px;
    height: 80px;
    color: rgba(239, 68, 68, 0.5);
    stroke-width: 1.5;
    margin: 0 auto 24px;
}

.error-title {
    font-size: 24px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 24px;
}

.btn-back {
    padding: 14px 32px;
    font-size: 15px;
    font-weight: 600;
    background: linear-gradient(135deg, #a78bfa, #8b5cf6);
    border: none;
    border-radius: 12px;
    color: #ffffff;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-back:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(167, 139, 250, 0.4);
}

/* Transitions */
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.3s ease;
}

.slide-down-enter-from,
.slide-down-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}

/* Responsive */
@media (max-width: 1024px) {
    .filme-grid {
        grid-template-columns: 1fr;
        gap: 32px;
    }

    .poster-wrapper {
        max-width: 400px;
        margin: 0 auto 20px;
    }

    .filme-title {
        font-size: 36px;
    }
}

@media (max-width: 768px) {
    .filme-card {
        padding: 32px 24px;
    }

    .filme-title {
        font-size: 28px;
    }

    .price-value {
        font-size: 28px;
    }

    .pricing-section {
        flex-direction: column;
        align-items: flex-start;
    }

    .actions-container {
        flex-direction: column;
    }

    .btn-rent,
    .btn-unavailable {
        width: 100%;
    }
}
</style>
