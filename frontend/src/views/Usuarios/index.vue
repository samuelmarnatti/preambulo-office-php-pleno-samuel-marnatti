<script setup>
import { onMounted, ref, computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import Navbar from '@/components/Navbar.vue';
import api from '@/services/api';

const authStore = useAuthStore();
const router = useRouter();
const usuarios = ref([]);
const loading = ref(true);
const filtroRole = ref('Todos');
const busca = ref('');

// Verificar permissão
if (!authStore.isAdministrador && !authStore.isAtendente) {
  router.push('/');
}

const roles = computed(() => {
  return ['Todos', 'administrador', 'atendente', 'cliente'];
});

const usuariosFiltrados = computed(() => {
  let resultado = usuarios.value;

  if (filtroRole.value !== 'Todos') {
    resultado = resultado.filter(u => u.role === filtroRole.value);
  }

  if (busca.value) {
    resultado = resultado.filter(u =>
      u.name.toLowerCase().includes(busca.value.toLowerCase()) ||
      u.email.toLowerCase().includes(busca.value.toLowerCase())
    );
  }

  return resultado;
});

const carregarUsuarios = async () => {
  try {
    const response = await api.get('/users');
    usuarios.value = response.data;
  } catch (error) {
    console.error('Erro ao carregar usuários:', error);
  } finally {
    loading.value = false;
  }
};

const excluirUsuario = async (id) => {
  if (!confirm('Tem certeza que deseja excluir este usuário?')) return;

  try {
    await api.delete(`/users/${id}`);
    await carregarUsuarios();
  } catch (error) {
    alert(error.response?.data?.message || 'Erro ao excluir usuário');
  }
};

const editarUsuario = (id) => {
  router.push({ name: 'UsuariosEditar', params: { id } });
};

const getRoleLabel = (role) => {
  const labels = {
    administrador: 'Administrador',
    atendente: 'Atendente',
    cliente: 'Cliente',
  };
  return labels[role] || role;
};

const getRoleClass = (role) => {
  const classes = {
    administrador: 'role-admin',
    atendente: 'role-atendente',
    cliente: 'role-cliente',
  };
  return classes[role] || '';
};

onMounted(() => {
  carregarUsuarios();
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
          <h1 class="page-title">Gerenciar Usuários</h1>
          <p class="page-subtitle">{{ usuariosFiltrados.length }} usuários encontrados</p>
        </div>
        
        <!-- Botão Adicionar Usuário -->
        <router-link 
          :to="{ name: 'UsuariosNovo' }"
          class="add-user-button"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Adicionar Usuário
        </router-link>
      </div>

      <!-- Filtros -->
      <div class="filters-container">
        <!-- Search -->
        <div class="search-wrapper">
          <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input
            v-model="busca"
            type="text"
            placeholder="Buscar por nome ou email..."
            class="search-input"
          />
        </div>

        <!-- Role Filters -->
        <div class="categories-wrapper">
          <button
            v-for="role in roles"
            :key="role"
            @click="filtroRole = role"
            :class="['category-chip', filtroRole === role ? 'category-chip-active' : '']"
          >
            {{ role === 'Todos' ? role : getRoleLabel(role) }}
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="loading-container">
        <div class="loading-spinner-large"></div>
        <p class="loading-text">Carregando usuários...</p>
      </div>

      <!-- Users Grid -->
      <div v-else-if="usuariosFiltrados.length > 0" class="users-grid">
        <div
          v-for="usuario in usuariosFiltrados"
          :key="usuario.id"
          class="user-card"
          @click="editarUsuario(usuario.id)"
        >
          <!-- Avatar -->
          <div class="user-avatar-large">
            {{ usuario.name.charAt(0).toUpperCase() }}
          </div>

          <!-- User Info -->
          <div class="user-info">
            <h3 class="user-name">{{ usuario.name }}</h3>
            <p class="user-email">{{ usuario.email }}</p>
            
            <!-- Role Badge -->
            <div class="user-meta">
              <span :class="['role-badge', getRoleClass(usuario.role)]">
                {{ getRoleLabel(usuario.role) }}
              </span>
            </div>

            <!-- Additional Info -->
            <div class="user-details">
              <div v-if="usuario.cpf" class="detail-item">
                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                </svg>
                <span>{{ usuario.cpf }}</span>
              </div>
              <div v-if="usuario.phone" class="detail-item">
                <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span>{{ usuario.phone }}</span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="user-actions" @click.stop>
            <button @click="editarUsuario(usuario.id)" class="action-btn action-edit" title="Editar">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </button>
            <button 
              v-if="usuario.id !== authStore.user?.id"
              @click="excluirUsuario(usuario.id)" 
              class="action-btn action-delete" 
              title="Excluir"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else class="empty-state">
        <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <h3 class="empty-title">Nenhum usuário encontrado</h3>
        <p class="empty-description">Tente ajustar os filtros ou buscar por outro termo</p>
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
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40px;
  gap: 24px;
  flex-wrap: wrap;
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

/* Add User Button */
.add-user-button {
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

.add-user-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s ease;
}

.add-user-button:hover {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
  box-shadow: 0 12px 32px rgba(167, 139, 250, 0.5);
  transform: translateY(-2px);
}

.add-user-button:hover::before {
  left: 100%;
}

.add-user-button:active {
  transform: translateY(0);
  box-shadow: 0 4px 16px rgba(167, 139, 250, 0.3);
}

.w-5 {
  width: 20px;
  height: 20px;
}

.h-5 {
  height: 20px;
}

/* Filters */
.filters-container {
  margin-bottom: 32px;
}

.search-wrapper {
  position: relative;
  max-width: 600px;
  margin: 0 auto 24px;
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
  background: rgba(255, 255, 255, 0.08);
  border-color: #a78bfa;
  color: #ffffff;
  transform: translateY(-2px);
}

.category-chip-active {
  background: linear-gradient(135deg, #a78bfa, #8b5cf6);
  border-color: #a78bfa;
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(167, 139, 250, 0.3);
}

/* Loading */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
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

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Users Grid */
.users-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 24px;
}

.user-card {
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  padding: 24px;
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.user-card:hover {
  transform: translateY(-4px);
  border-color: #a78bfa;
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.5);
}

.user-avatar-large {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #a78bfa, #8b5cf6);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 32px;
  color: #ffffff;
  box-shadow: 0 8px 24px rgba(167, 139, 250, 0.4);
  margin: 0 auto;
}

.user-info {
  text-align: center;
}

.user-name {
  font-size: 20px;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 4px;
}

.user-email {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 12px;
}

.user-meta {
  display: flex;
  justify-content: center;
  margin-bottom: 16px;
}

.role-badge {
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.role-admin {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.role-atendente {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.role-cliente {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.user-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 16px;
  background: rgba(0, 0, 0, 0.2);
  border-radius: 8px;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: rgba(255, 255, 255, 0.7);
}

.detail-icon {
  width: 16px;
  height: 16px;
  color: #a78bfa;
  flex-shrink: 0;
}

/* User Actions */
.user-actions {
  display: flex;
  gap: 8px;
  justify-content: center;
  padding-top: 12px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.action-btn {
  padding: 10px 16px;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  background: rgba(255, 255, 255, 0.05);
  color: #ffffff;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 6px;
}

.action-edit:hover {
  background: rgba(59, 130, 246, 0.2);
  border-color: rgba(59, 130, 246, 0.5);
  transform: translateY(-2px);
}

.action-delete:hover {
  background: rgba(239, 68, 68, 0.2);
  border-color: rgba(239, 68, 68, 0.5);
  transform: translateY(-2px);
}

.w-4 {
  width: 16px;
}

.h-4 {
  height: 16px;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 80px 20px;
}

.empty-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 24px;
  color: rgba(255, 255, 255, 0.2);
  stroke-width: 1.5;
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
  .page-title {
    font-size: 32px;
  }

  .users-grid {
    grid-template-columns: 1fr;
  }

  .page-header {
    flex-direction: column;
    align-items: stretch;
  }

  .add-user-button {
    width: 100%;
    justify-content: center;
  }
}
</style>
