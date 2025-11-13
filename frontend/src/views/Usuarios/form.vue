<script setup>
import { onMounted, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import Navbar from '@/components/Navbar.vue';
import api from '@/services/api';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const loading = ref(false);
const isEdit = ref(false);

// Verificar permissão
if (!authStore.isAdministrador && !authStore.isAtendente) {
  router.push('/');
}

const form = ref({
  name: '',
  email: '',
  cpf: '',
  phone: '',
  password: '',
  password_confirmation: '',
  role: 'cliente',
});

const error = ref('');

// Funções de máscara
const formatarCPF = (value) => {
  const cpf = value.replace(/\D/g, '');
  return cpf
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
};

const formatarTelefone = (value) => {
  const tel = value.replace(/\D/g, '');
  if (tel.length <= 10) {
    return tel.replace(/(\d{2})(\d{4})(\d)/, '($1) $2-$3');
  }
  return tel.replace(/(\d{2})(\d{5})(\d)/, '($1) $2-$3');
};

const handleCPFInput = (event) => {
  form.value.cpf = formatarCPF(event.target.value);
};

const handlePhoneInput = (event) => {
  form.value.phone = formatarTelefone(event.target.value);
};

const carregarUsuario = async () => {
  if (!route.params.id) return;

  isEdit.value = true;
  loading.value = true;

  try {
    const response = await api.get(`/users/${route.params.id}`);
    form.value = {
      name: response.data.name,
      email: response.data.email,
      cpf: response.data.cpf ? formatarCPF(response.data.cpf) : '',
      phone: response.data.phone ? formatarTelefone(response.data.phone) : '',
      role: response.data.role,
      password: '',
      password_confirmation: '',
    };
  } catch (err) {
    error.value = 'Erro ao carregar usuário';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const salvarUsuario = async () => {
  error.value = '';
  loading.value = true;

  try {
    const data = { ...form.value };
    
    // Remover formatação do CPF (manter apenas números)
    if (data.cpf) {
      data.cpf = data.cpf.replace(/\D/g, '');
    }
    
    // Remover formatação do telefone (manter apenas números)
    if (data.phone) {
      data.phone = data.phone.replace(/\D/g, '');
    }
    
    // Se for edição e senha vazia, remove campos de senha
    if (isEdit.value && !data.password) {
      delete data.password;
      delete data.password_confirmation;
    }

    if (isEdit.value) {
      await api.put(`/users/${route.params.id}`, data);
    } else {
      await api.post('/users', data);
    }

    router.push({ name: 'Usuarios' });
  } catch (err) {
    // Melhor tratamento de erros de validação
    if (err.response?.data?.errors) {
      const errors = err.response.data.errors;
      const firstError = Object.values(errors)[0][0];
      error.value = firstError;
    } else {
      error.value = err.response?.data?.message || 'Erro ao salvar usuário';
    }
    console.error(err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  carregarUsuario();
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
        <div class="header-content">
          <button @click="router.back()" class="back-button">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar
          </button>
          <h1 class="page-title">{{ isEdit ? 'Editar Usuário' : 'Novo Usuário' }}</h1>
        </div>
      </div>

      <!-- Form Card -->
      <div class="form-card">
        <form @submit.prevent="salvarUsuario" class="user-form">
          <!-- Basic Info -->
          <div class="form-section">
            <h2 class="section-title">Informações Básicas</h2>
            
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Nome Completo *</label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  class="form-input"
                  placeholder="Digite o nome completo"
                />
              </div>

              <div class="form-group">
                <label class="form-label">E-mail *</label>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  class="form-input"
                  placeholder="exemplo@email.com"
                />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">CPF *</label>
                <input
                  v-model="form.cpf"
                  @input="handleCPFInput"
                  type="text"
                  required
                  maxlength="14"
                  class="form-input"
                  placeholder="000.000.000-00"
                />
              </div>

              <div class="form-group">
                <label class="form-label">Telefone</label>
                <input
                  v-model="form.phone"
                  @input="handlePhoneInput"
                  type="text"
                  maxlength="15"
                  class="form-input"
                  placeholder="(00) 00000-0000"
                />
              </div>
            </div>
          </div>

          <!-- Role -->
          <div class="form-section">
            <h2 class="section-title">Perfil de Acesso</h2>
            
            <div class="form-group">
              <label class="form-label">Tipo de Usuário *</label>
              <select v-model="form.role" required class="form-select">
                <option value="cliente">Cliente</option>
                <option value="atendente">Atendente</option>
                <option value="administrador">Administrador</option>
              </select>
            </div>
          </div>

          <!-- Password -->
          <div class="form-section">
            <h2 class="section-title">
              {{ isEdit ? 'Alterar Senha (deixe em branco para manter)' : 'Senha de Acesso' }}
            </h2>
            
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Senha {{ isEdit ? '' : '*' }}</label>
                <input
                  v-model="form.password"
                  type="password"
                  :required="!isEdit"
                  class="form-input"
                  placeholder="Digite a senha"
                />
              </div>

              <div class="form-group">
                <label class="form-label">Confirmar Senha {{ isEdit ? '' : '*' }}</label>
                <input
                  v-model="form.password_confirmation"
                  type="password"
                  :required="!isEdit && form.password"
                  class="form-input"
                  placeholder="Confirme a senha"
                />
              </div>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="error-message">
            <svg class="error-icon" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ error }}
          </div>

          <!-- Actions -->
          <div class="form-actions">
            <button type="button" @click="router.back()" class="btn-secondary">
              Cancelar
            </button>
            <button type="submit" :disabled="loading" class="btn-primary">
              <span v-if="loading" class="loading-spinner"></span>
              {{ loading ? 'Salvando...' : (isEdit ? 'Atualizar' : 'Cadastrar') }}
            </button>
          </div>
        </form>
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
  max-width: 900px;
  margin: 0 auto;
  padding: 100px 24px 48px;
}

/* Page Header */
.page-header {
  margin-bottom: 32px;
}

.header-content {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.back-button {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  font-size: 14px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.7);
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  width: fit-content;
}

.back-button:hover {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.08);
  border-color: #a78bfa;
  transform: translateX(-4px);
}

.page-title {
  font-size: 36px;
  font-weight: 700;
  background: linear-gradient(135deg, #a78bfa, #8b5cf6);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: -0.5px;
}

.w-5 {
  width: 20px;
  height: 20px;
}

/* Form Card */
.form-card {
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 40px;
}

.user-form {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.form-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #a78bfa;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(167, 139, 250, 0.2);
}

.form-row {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-label {
  font-size: 14px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.8);
}

.form-input,
.form-select {
  width: 100%;
  padding: 14px 18px;
  font-size: 15px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  color: #ffffff;
  transition: all 0.3s ease;
  outline: none;
}

.form-input::placeholder {
  color: rgba(255, 255, 255, 0.4);
}

.form-input:focus,
.form-select:focus {
  background: rgba(255, 255, 255, 0.08);
  border-color: #a78bfa;
  box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.1);
}

.form-select {
  cursor: pointer;
}

.form-select option {
  background: #1a1a2e;
  color: #ffffff;
}

/* Error Message */
.error-message {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 18px;
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.3);
  border-radius: 8px;
  color: #fca5a5;
  font-size: 14px;
  font-weight: 500;
}

.error-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: 16px;
  justify-content: flex-end;
  padding-top: 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.btn-secondary {
  padding: 14px 32px;
  font-size: 15px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.7);
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-secondary:hover {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(255, 255, 255, 0.2);
}

.btn-primary {
  padding: 14px 32px;
  font-size: 15px;
  font-weight: 600;
  color: #ffffff;
  background: linear-gradient(135deg, #a78bfa, #8b5cf6);
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  position: relative;
  overflow: hidden;
}

.btn-primary::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s ease;
}

.btn-primary:hover:not(:disabled) {
  box-shadow: 0 8px 20px rgba(167, 139, 250, 0.4);
  transform: translateY(-2px);
}

.btn-primary:hover:not(:disabled)::before {
  left: 100%;
}

.btn-primary:active:not(:disabled) {
  transform: translateY(0);
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #ffffff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Responsive */
@media (max-width: 768px) {
  .form-card {
    padding: 24px;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .btn-secondary,
  .btn-primary {
    width: 100%;
    justify-content: center;
  }

  .page-title {
    font-size: 28px;
  }
}
</style>
