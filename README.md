# 🎬 Sistema de Locadora de Filmes Online

Prova técnica para vaga de **Desenvolvedor PHP Pleno** - Preâmbulo Office

## 🚀 Stack Tecnológica

- **PHP 8.3**
- **Laravel 12**
- **Vue 3 + Vite**
- **PostgreSQL 15**
- **Redis 7**
- **nginx**
- **Docker & Docker Compose**
- **PHPUnit**
- **Mailhog**

---

## 📋 Pré-requisitos

- Docker (20.10+)
- Docker Compose (2.0+)

---

## ⚙️ Instalação e Configuração

### 1️⃣ Clone o repositório

```bash
git clone https://github.com/samuelmarnatti/preambulo-office-php-pleno-samuel-marnatti.git
cd preambulo-office-php-pleno-samuel-marnatti
```

### 2️⃣ Crie o projeto Laravel

```bash
# Criar pasta backend
mkdir backend

# Criar o projeto Laravel dentro de backend
docker run --rm -v $(pwd)/backend:/app composer create-project laravel/laravel .

# Ou se preferir sem Docker (com Composer instalado):
cd backend
composer create-project laravel/laravel .
cd ..
```

### 3️⃣ Configure o .env

```bash
cp backend/.env.example backend/.env
```

Edite `backend/.env` com as seguintes configurações:

```env
APP_NAME="Locadora de Filmes"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=locadora
DB_USERNAME=locadora_user
DB_PASSWORD=secret

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=redis
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@locadora.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 4️⃣ Suba os containers

```bash
docker-compose up -d
```

### 5️⃣ Instale as dependências do Laravel

```bash
docker-compose exec php composer install
```

### 6️⃣ Gere a chave da aplicação

```bash
docker-compose exec php php artisan key:generate
```

### 7️⃣ Execute as migrations e seeders

```bash
docker-compose exec php php artisan migrate --seed
```

---

## 🔐 Credenciais de Acesso

Após rodar os seeders, você terá os seguintes usuários:

| Perfil | Email | Senha |
|--------|-------|-------|
| **Administrador** | admin@locadora.com | password |
| **Atendente** | atendente@locadora.com | password |
| **Cliente** | cliente@teste.com | password |

---

## 🌐 URLs de Acesso

- **Aplicação:** http://localhost:8080
- **Mailhog (E-mails):** http://localhost:8025
- **PostgreSQL:** localhost:5432
- **Redis:** localhost:6379

---

## 🧪 Executar Testes

```bash
docker-compose exec php php artisan test
```

### Testes obrigatórios implementados:

✅ Cliente com devolução pendente não pode alugar  
✅ Cálculo correto da multa diária de atraso  
✅ Fluxo de locação → devolução → atualização de estoque  
✅ Cache invalidado após atualização de filme  
✅ Envio de notificação assíncrona via fila

---

## 📊 Funcionalidades Implementadas

### ✅ Parte 1 - Escopo Mínimo (Obrigatório)

- [x] Estrutura Docker completa
- [x] Autenticação e perfis de acesso (Cliente, Atendente, Administrador)
- [x] CRUD de filmes com cache-aside (Redis)
- [x] CRUD de clientes
- [x] Fluxo de locação e devolução
- [x] Cálculo de multas por atraso (R$ 5,00/dia/filme)
- [x] Notificações assíncronas por e-mail (Queue + Redis)
- [x] Frontend Vue 3 SPA com painéis por perfil
- [x] Testes PHPUnit mínimos
- [x] Tela exclusiva do administrador (devoluções do dia)

### 🎁 Parte 2 - Escopo Bônus (Diferencial)

- [ ] Relatórios avançados
- [ ] Painéis com gráficos
- [ ] Cobertura de testes > 70%
- [ ] Clean Architecture / DDD

---

## 🏗️ Arquitetura e Decisões Técnicas

### Estrutura de Pastas

```
├── backend/               # Aplicação Laravel
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   ├── Services/      # Lógica de negócio
│   │   ├── Jobs/          # Jobs assíncronos
│   ├── database/
│   │   ├── migrations/
│   │   ├── seeders/
│   │   ├── factories/
│   ├── routes/
│   │   ├── api.php        # Rotas da API REST
│   ├── tests/
│       ├── Feature/
│       ├── Unit/
├── frontend/              # Aplicação Vue 3
│   ├── src/
│   │   ├── components/    # Componentes reutilizáveis
│   │   ├── views/         # Páginas da aplicação
│   │   ├── router/        # Configuração de rotas
│   │   ├── stores/        # Pinia stores (state management)
│   │   ├── services/      # API services
├── docker/
│   ├── nginx/
│   ├── php/
├── docker-compose.yml
└── README.md
```

### Padrões Utilizados

- **Service Layer:** Lógica de negócio isolada em Services
- **Jobs & Queues:** Processamento assíncrono com Redis
- **Cache-Aside:** Cache de catálogo de filmes com invalidação automática
- **API REST:** Backend expõe API para consumo pelo Vue SPA
- **SPA (Single Page Application):** Frontend Vue 3 com Vue Router
- **State Management:** Pinia para gerenciamento de estado global

### Performance

- **Redis:** Sessões, cache e filas
- **Eager Loading:** Prevenção de N+1 queries
- **Indexes:** Colunas frequentemente consultadas
- **Queue Worker:** Processamento assíncrono de e-mails
- **Vite:** Build tool moderno para desenvolvimento rápido

---

## 🐛 Troubleshooting

### Permissões de arquivos

```bash
docker-compose exec php chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker-compose exec php chmod -R 775 /var/www/storage /var/www/bootstrap/cache
```

### Limpar cache

```bash
docker-compose exec php php artisan cache:clear
docker-compose exec php php artisan config:clear
docker-compose exec php php artisan route:clear
```

### Recriar banco de dados

```bash
docker-compose exec php php artisan migrate:fresh --seed
```

---

## 📝 Comandos Úteis

```bash
# Ver logs dos containers
docker-compose logs -f

# Acessar container PHP
docker-compose exec php bash

# Rodar Tinker (REPL do Laravel)
docker-compose exec php php artisan tinker

# Monitorar fila
docker-compose exec php php artisan queue:listen

# Parar containers
docker-compose down

# Parar e remover volumes
docker-compose down -v
```

---

## 👨‍💻 Autor

**Samuel Marnatti**  
Desenvolvedor PHP Pleno  
samuelmarnatti@email.com

---

## 📄 Licença

Este projeto foi desenvolvido como prova técnica para a vaga de Desenvolvedor PHP Pleno na Preâmbulo Office.