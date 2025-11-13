<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
  email: '',
  password: '',
});

const error = ref('');
const loading = ref(false);

const handleLogin = async () => {
  error.value = '';
  loading.value = true;

  try {
    await authStore.login(form.value);
    router.push('/');
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao fazer login';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="login-container">
    <!-- Background gradient overlay -->
    <div class="background-overlay"></div>
    
    <!-- Background pattern -->
    <div class="background-pattern"></div>

    <!-- Login card -->
    <div class="login-card">
      <!-- Logo/Brand -->
      <div class="brand-section">
        <div class="logo">
          <svg class="film-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <rect x="2" y="3" width="20" height="18" rx="2" stroke-width="2"/>
            <line x1="7" y1="3" x2="7" y2="21" stroke-width="2"/>
            <line x1="17" y1="3" x2="17" y2="21" stroke-width="2"/>
            <line x1="2" y1="9" x2="7" y2="9" stroke-width="2"/>
            <line x1="2" y1="15" x2="7" y2="15" stroke-width="2"/>
            <line x1="17" y1="9" x2="22" y2="9" stroke-width="2"/>
            <line x1="17" y1="15" x2="22" y2="15" stroke-width="2"/>
          </svg>
        </div>
        <h1 class="brand-title">CINESTREAM</h1>
        <p class="brand-subtitle">Entre para continuar assistindo</p>
      </div>

      <!-- Login form -->
      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <input
            v-model="form.email"
            type="email"
            required
            class="form-input"
            placeholder="E-mail"
            autocomplete="email"
          />
        </div>

        <div class="form-group">
          <input
            v-model="form.password"
            type="password"
            required
            class="form-input"
            placeholder="Senha"
            autocomplete="current-password"
          />
        </div>

        <div v-if="error" class="error-message">
          <svg class="error-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          {{ error }}
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="submit-button"
        >
          <span v-if="loading" class="loading-spinner"></span>
          {{ loading ? 'Entrando...' : 'Entrar' }}
        </button>
      </form>

      <!-- Register link -->
      <!-- <div class="register-section">
        <span class="register-text">Novo por aqui?</span>
        <router-link to="/register" class="register-link">
          Criar conta
        </router-link>
      </div> -->

      <!-- Test credentials -->
      <div class="test-credentials">
        <div class="credentials-header">
          <svg class="key-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/>
          </svg>
          Credenciais de Teste
        </div>
        <div class="credentials-list">
          <div class="credential-item">
            <span class="credential-role">Admin:</span>
            <span class="credential-value">admin@locadora.com / password</span>
          </div>
          <div class="credential-item">
            <span class="credential-role">Atendente:</span>
            <span class="credential-value">atendente@locadora.com / password</span>
          </div>
          <div class="credential-item">
            <span class="credential-role">Cliente:</span>
            <span class="credential-value">cliente@teste.com / password</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
  overflow: hidden;
}

.background-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.3), transparent 50%),
              radial-gradient(circle at 80% 80%, rgba(138, 35, 135, 0.3), transparent 50%);
  animation: pulse 15s ease-in-out infinite;
}

.background-pattern {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: 
    linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
  background-size: 100px 100px;
  opacity: 0.5;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}

.login-card {
  position: relative;
  z-index: 10;
  width: 100%;
  max-width: 450px;
  padding: 48px 40px;
  margin: 20px;
  background: rgba(20, 20, 30, 0.85);
  backdrop-filter: blur(20px);
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
}

.brand-section {
  text-align: center;
  margin-bottom: 40px;
}

.logo {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.film-icon {
  width: 64px;
  height: 64px;
  color: #a78bfa;
  filter: drop-shadow(0 0 20px rgba(167, 139, 250, 0.5));
}

.brand-title {
  font-size: 32px;
  font-weight: 800;
  letter-spacing: 2px;
  background: linear-gradient(135deg, #a78bfa 0%, #ec4899 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 8px;
}

.brand-subtitle {
  color: rgba(255, 255, 255, 0.7);
  font-size: 14px;
  font-weight: 300;
}

.login-form {
  margin-bottom: 32px;
}

.form-group {
  margin-bottom: 20px;
}

.form-input {
  width: 100%;
  padding: 16px 20px;
  font-size: 15px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 6px;
  color: #ffffff;
  transition: all 0.3s ease;
  outline: none;
}

.form-input::placeholder {
  color: rgba(255, 255, 255, 0.4);
}

.form-input:focus {
  background: rgba(255, 255, 255, 0.08);
  border-color: #a78bfa;
  box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.1);
}

.error-message {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  margin-bottom: 20px;
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.3);
  border-radius: 6px;
  color: #fca5a5;
  font-size: 14px;
}

.error-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

.submit-button {
  width: 100%;
  padding: 16px 24px;
  font-size: 16px;
  font-weight: 600;
  color: #ffffff;
  background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%);
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.submit-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.submit-button:hover:not(:disabled)::before {
  opacity: 1;
}

.submit-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(167, 139, 250, 0.4);
}

.submit-button:active:not(:disabled) {
  transform: translateY(0);
}

.submit-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.submit-button span {
  position: relative;
  z-index: 1;
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

.register-section {
  text-align: center;
  padding: 24px 0;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.register-text {
  color: rgba(255, 255, 255, 0.6);
  font-size: 14px;
  margin-right: 8px;
}

.register-link {
  color: #a78bfa;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.3s ease;
}

.register-link:hover {
  color: #c4b5fd;
  text-decoration: underline;
}

.test-credentials {
  margin-top: 24px;
  padding: 20px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 6px;
}

.credentials-header {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #a78bfa;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.key-icon {
  width: 16px;
  height: 16px;
}

.credentials-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.credential-item {
  display: flex;
  flex-direction: column;
  gap: 2px;
  font-size: 12px;
}

.credential-role {
  color: rgba(255, 255, 255, 0.5);
  font-weight: 600;
}

.credential-value {
  color: rgba(255, 255, 255, 0.8);
  font-family: 'Courier New', monospace;
}

/* Responsive */
@media (max-width: 640px) {
  .login-card {
    padding: 36px 24px;
    margin: 16px;
  }

  .brand-title {
    font-size: 28px;
  }

  .film-icon {
    width: 48px;
    height: 48px;
  }
}
</style>