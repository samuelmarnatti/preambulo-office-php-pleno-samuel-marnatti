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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        {{ isEditMode ? 'Editar Filme' : 'Novo Filme' }}
                    </h1>
                    <p class="page-subtitle">
                        {{ isEditMode ? 'Atualize as informações do filme' : 'Adicione um novo filme ao catálogo' }}
                    </p>
                </div>
                <button
                    @click="voltar"
                    class="back-button"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </button>
            </div>

            <!-- Mensagens -->
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

            <!-- Formulário -->
            <div class="form-container">
                <form @submit.prevent="salvarFilme" class="form-content">
                    <!-- Título -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                            </svg>
                            Título do Filme
                            <span class="text-red-400">*</span>
                        </label>
                        <input
                            v-model="form.titulo"
                            type="text"
                            required
                            placeholder="Ex: Matrix Resurrections"
                            class="form-input"
                        />
                    </div>

                    <!-- Imagem do Filme -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Imagem do Filme
                        </label>
                        
                        <div class="image-upload-container">
                            <!-- Preview da Imagem -->
                            <div v-if="imagemPreview" class="image-preview">
                                <img :src="imagemPreview" alt="Preview" class="preview-img" />
                                <button
                                    type="button"
                                    @click="removerImagem"
                                    class="remove-image-btn"
                                    title="Remover imagem"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Upload Area -->
                            <div v-else class="upload-area" @click="$refs.fileInput.click()">
                                <input
                                    ref="fileInput"
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageUpload"
                                    class="hidden"
                                />
                                <div class="upload-content">
                                    <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="upload-text">Clique para selecionar uma imagem</p>
                                    <p class="upload-hint">PNG, JPG ou WEBP (máx. 5MB)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sinopse -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Sinopse
                            <span class="text-red-400">*</span>
                        </label>
                        <textarea
                            v-model="form.sinopse"
                            required
                            rows="4"
                            placeholder="Descreva a história do filme..."
                            class="form-input form-textarea"
                        ></textarea>
                    </div>

                    <!-- Ano e Categoria -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Ano
                                <span class="text-red-400">*</span>
                            </label>
                            <input
                                v-model.number="form.ano"
                                type="number"
                                required
                                min="1888"
                                :max="new Date().getFullYear() + 5"
                                placeholder="2024"
                                class="form-input"
                            />
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Categoria
                                <span class="text-red-400">*</span>
                            </label>
                            <select
                                v-model="form.categoria"
                                required
                                class="form-input form-select"
                            >
                                <option value="" disabled class="bg-gray-900">Selecione uma categoria</option>
                                <option value="Ação" class="bg-gray-900">Ação</option>
                                <option value="Aventura" class="bg-gray-900">Aventura</option>
                                <option value="Comédia" class="bg-gray-900">Comédia</option>
                                <option value="Drama" class="bg-gray-900">Drama</option>
                                <option value="Ficção Científica" class="bg-gray-900">Ficção Científica</option>
                                <option value="Terror" class="bg-gray-900">Terror</option>
                                <option value="Suspense" class="bg-gray-900">Suspense</option>
                                <option value="Romance" class="bg-gray-900">Romance</option>
                                <option value="Animação" class="bg-gray-900">Animação</option>
                                <option value="Documentário" class="bg-gray-900">Documentário</option>
                            </select>
                        </div>
                    </div>

                    <!-- Valor e Quantidade -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Valor da Locação (R$)
                                <span class="text-red-400">*</span>
                            </label>
                            <input
                                v-model.number="form.valor_locacao"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                placeholder="15.90"
                                class="form-input"
                            />
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Quantidade Disponível
                                <span class="text-red-400">*</span>
                            </label>
                            <input
                                v-model.number="form.quantidade_disponivel"
                                type="number"
                                min="0"
                                required
                                placeholder="10"
                                class="form-input"
                            />
                        </div>
                    </div>

                    <!-- Preview -->
                    <transition name="fade">
                        <div v-if="form.titulo" class="preview-card">
                            <div class="preview-header">
                                <svg class="preview-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <h3 class="preview-title">Pré-visualização</h3>
                            </div>
                            <div class="preview-content-grid">
                                <!-- Imagem Preview -->
                                <div v-if="imagemPreview" class="preview-image-container">
                                    <img :src="imagemPreview" alt="Preview do filme" class="preview-movie-img" />
                                </div>
                                
                                <!-- Informações -->
                                <div class="preview-info">
                                    <div class="preview-item">
                                        <span class="preview-label">Título:</span>
                                        <span class="preview-value">{{ form.titulo }}</span>
                                    </div>
                                    <div v-if="form.categoria" class="preview-item">
                                        <span class="preview-label">Categoria:</span>
                                        <span class="preview-value">{{ form.categoria }}</span>
                                    </div>
                                    <div v-if="form.ano" class="preview-item">
                                        <span class="preview-label">Ano:</span>
                                        <span class="preview-value">{{ form.ano }}</span>
                                    </div>
                                    <div v-if="form.valor_locacao" class="preview-item">
                                        <span class="preview-label">Valor:</span>
                                        <span class="preview-value gradient-text">R$ {{ formatarPreco(form.valor_locacao) }}</span>
                                    </div>
                                    <div v-if="form.quantidade_disponivel" class="preview-item">
                                        <span class="preview-label">Estoque:</span>
                                        <span class="preview-value">{{ form.quantidade_disponivel }} unidades</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>

                    <!-- Botões -->
                    <div class="form-actions">
                        <button
                            type="button"
                            @click="voltar"
                            class="btn-secondary"
                            :disabled="loading"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="btn-primary"
                            :disabled="loading"
                        >
                            <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ loading ? 'Salvando...' : isEditMode ? 'Atualizar Filme' : 'Cadastrar Filme' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import Navbar from '@/components/Navbar.vue';
import api from '@/services/api';

const router = useRouter();
const route = useRoute();

const loading = ref(false);
const mensagem = ref({ texto: '', tipo: '' });
const imagemPreview = ref(null);
const imagemArquivo = ref(null);

const form = ref({
    titulo: '',
    sinopse: '',
    ano: new Date().getFullYear(),
    categoria: '',
    valor_locacao: 0,
    quantidade_disponivel: 0
});

const isEditMode = computed(() => !!route.params.id);

onMounted(async () => {
    if (isEditMode.value) {
        await carregarFilme();
    }
});

const carregarFilme = async () => {
    try {
        loading.value = true;
        const response = await api.get(`/filmes/${route.params.id}`);
        
        form.value = {
            titulo: response.data.titulo,
            sinopse: response.data.sinopse,
            ano: response.data.ano,
            categoria: response.data.categoria,
            valor_locacao: parseFloat(response.data.valor_locacao),
            quantidade_disponivel: response.data.quantidade_disponivel
        };

        // Se houver URL de imagem, carregar preview
        if (response.data.imagem_url) {
            imagemPreview.value = getImageUrl(response.data.imagem_url);
        }
    } catch (error) {
        console.error('Erro ao carregar filme:', error);
        
        if (error.response?.status === 404) {
            mensagem.value = {
                texto: 'Filme não encontrado',
                tipo: 'danger'
            };
            setTimeout(() => {
                router.push({ name: 'Filmes' });
            }, 2000);
        } else {
            mensagem.value = {
                texto: 'Erro ao carregar dados do filme',
                tipo: 'danger'
            };
        }
    } finally {
        loading.value = false;
    }
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

const formatarPreco = (valor) => {
    return parseFloat(valor).toFixed(2).replace('.', ',');
};

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    // Validar tamanho (5MB)
    if (file.size > 5 * 1024 * 1024) {
        mensagem.value = {
            texto: 'A imagem deve ter no máximo 5MB',
            tipo: 'danger'
        };
        return;
    }

    // Validar tipo
    if (!file.type.startsWith('image/')) {
        mensagem.value = {
            texto: 'Por favor, selecione uma imagem válida',
            tipo: 'danger'
        };
        return;
    }

    imagemArquivo.value = file;

    // Criar preview
    const reader = new FileReader();
    reader.onload = (e) => {
        imagemPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const removerImagem = () => {
    imagemPreview.value = null;
    imagemArquivo.value = null;
};

const salvarFilme = async () => {
    try {
        loading.value = true;
        mensagem.value = { texto: '', tipo: '' };

        // Criar FormData para enviar imagem
        const formData = new FormData();
        formData.append('titulo', form.value.titulo);
        formData.append('sinopse', form.value.sinopse);
        formData.append('ano', form.value.ano);
        formData.append('categoria', form.value.categoria);
        formData.append('valor_locacao', form.value.valor_locacao);
        formData.append('quantidade_disponivel', form.value.quantidade_disponivel);
        
        // Adicionar imagem se foi selecionada
        if (imagemArquivo.value) {
            formData.append('imagem', imagemArquivo.value);
        }

        if (isEditMode.value) {
            await api.post(`/filmes/${route.params.id}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                params: {
                    _method: 'PUT' // Laravel method spoofing
                }
            });
            mensagem.value = {
                texto: 'Filme atualizado com sucesso!',
                tipo: 'success'
            };
        } else {
            await api.post('/filmes', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                }
            });
            mensagem.value = {
                texto: 'Filme cadastrado com sucesso!',
                tipo: 'success'
            };
        }

        setTimeout(() => {
            router.push({ name: 'Filmes' });
        }, 1500);
    } catch (error) {
        console.error('Erro ao salvar filme:', error);
        mensagem.value = {
            texto: error.response?.data?.message || 'Erro ao salvar filme',
            tipo: 'danger'
        };
    } finally {
        loading.value = false;
    }
};

const voltar = () => {
    router.push({ name: 'Filmes' });
};
</script>

<style scoped>
/* Back Button */
.back-button {
    display: flex;
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

/* Form Container */
.form-container {
    max-width: 900px;
    margin: 0 auto;
    background: rgba(20, 20, 30, 0.6);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 24px;
    padding: 48px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

.form-content {
    display: flex;
    flex-direction: column;
    gap: 32px;
}

/* Form Group */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.label-icon {
    width: 18px;
    height: 18px;
    color: #a78bfa;
    stroke-width: 2;
}

/* Form Input */
.form-input {
    width: 100%;
    padding: 16px 20px;
    font-size: 16px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    color: #ffffff;
    transition: all 0.3s ease;
    outline: none;
}

.form-input::placeholder {
    color: rgba(255, 255, 255, 0.3);
}

.form-input:focus {
    background: rgba(255, 255, 255, 0.08);
    border-color: #a78bfa;
    box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.15);
    transform: translateY(-2px);
}

.form-textarea {
    resize: vertical;
    min-height: 120px;
    font-family: inherit;
    line-height: 1.6;
}

.form-select {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='rgba(255,255,255,0.5)'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 20px;
    padding-right: 48px;
}

.form-select option {
    background-color: #1a1a2e;
    color: white;
    padding: 12px;
}

/* Form Row */
.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
}

/* Image Upload */
.image-upload-container {
    position: relative;
    width: 100%;
}

.image-preview {
    position: relative;
    width: 100%;
    max-width: 400px;
    aspect-ratio: 2/3;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid rgba(167, 139, 250, 0.3);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
}

.preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.remove-image-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(239, 68, 68, 0.9);
    backdrop-filter: blur(10px);
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.remove-image-btn:hover {
    background: rgba(220, 38, 38, 1);
    transform: scale(1.1);
}

.upload-area {
    width: 100%;
    max-width: 400px;
    aspect-ratio: 2/3;
    border: 2px dashed rgba(167, 139, 250, 0.3);
    border-radius: 12px;
    background: rgba(167, 139, 250, 0.05);
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-area:hover {
    border-color: rgba(167, 139, 250, 0.6);
    background: rgba(167, 139, 250, 0.1);
}

.upload-content {
    text-align: center;
    padding: 32px;
}

.upload-icon {
    width: 64px;
    height: 64px;
    color: #a78bfa;
    margin: 0 auto 16px;
    stroke-width: 1.5;
}

.upload-text {
    font-size: 16px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 8px;
}

.upload-hint {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.5);
}

.hidden {
    display: none;
}

/* Preview Card */
.preview-card {
    margin-top: 16px;
    padding: 32px;
    background: linear-gradient(135deg, rgba(167, 139, 250, 0.1), rgba(217, 70, 239, 0.1));
    border: 2px solid rgba(167, 139, 250, 0.3);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(167, 139, 250, 0.2);
}

.preview-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.preview-icon {
    width: 24px;
    height: 24px;
    color: #a78bfa;
    stroke-width: 2;
}

.preview-title {
    font-size: 18px;
    font-weight: 700;
    color: #ffffff;
}

.preview-content-grid {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 24px;
    align-items: start;
}

.preview-image-container {
    width: 150px;
    aspect-ratio: 2/3;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid rgba(167, 139, 250, 0.3);
}

.preview-movie-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.preview-info {
    display: grid;
    gap: 16px;
}

.preview-content {
    display: grid;
    gap: 16px;
}

.preview-item {
    display: flex;
    align-items: baseline;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.preview-item:last-child {
    border-bottom: none;
}

.preview-label {
    font-size: 13px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.5);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    min-width: 120px;
}

.preview-value {
    font-size: 16px;
    font-weight: 600;
    color: #ffffff;
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 16px;
    padding-top: 32px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

/* Buttons */
.btn-secondary {
    padding: 14px 32px;
    font-size: 15px;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-secondary:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    transform: translateY(-2px);
}

.btn-secondary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-primary {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    font-size: 15px;
    font-weight: 600;
    background: linear-gradient(135deg, #a78bfa, #8b5cf6);
    border: none;
    border-radius: 12px;
    color: #ffffff;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(167, 139, 250, 0.4);
    transition: all 0.3s ease;
}

.btn-primary:hover:not(:disabled) {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    box-shadow: 0 12px 32px rgba(167, 139, 250, 0.5);
    transform: translateY(-2px);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
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

.fade-enter-active,
.fade-leave-active {
    transition: all 0.4s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

/* Responsive */
@media (max-width: 768px) {
    .form-container {
        padding: 32px 24px;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .btn-secondary,
    .btn-primary {
        width: 100%;
        justify-content: center;
    }

    .preview-card {
        padding: 24px;
    }

    .preview-content-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .preview-image-container {
        width: 100%;
        max-width: 200px;
        margin: 0 auto;
    }

    .preview-label {
        min-width: 100px;
        font-size: 12px;
    }

    .preview-value {
        font-size: 14px;
    }

    .upload-area {
        max-width: 100%;
    }

    .image-preview {
        max-width: 100%;
    }
}

/* Number input - remove spinner */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
    appearance: textfield;
}
</style>
