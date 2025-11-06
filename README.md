# 🎵 MusicOrg API

API REST para gerenciamento de eventos musicais desenvolvida com Laravel. Sistema multi-tenant que permite que bandas criem e gerenciem seus eventos, músicos e setlists de forma isolada e segura.

## 📋 Sobre o Projeto

O MusicOrg é uma plataforma SaaS multi-tenant que permite que diferentes bandas gerenciem seus eventos musicais de forma independente. Cada banda tem acesso apenas aos seus próprios dados, garantindo total isolamento e segurança.

## 🛠️ Tecnologias

- **Laravel 12** - Framework PHP
- **Laravel Sanctum** - Autenticação via API tokens
- **PostgreSQL** - Banco de dados (produção)
- **SQLite** - Banco de dados (desenvolvimento)

## ✨ Funcionalidades

- ✅ **Autenticação** - Registro e login de bandas com tokens
- ✅ **CRUD de Eventos** - Criar, listar, atualizar e excluir eventos
- ✅ **Gerenciamento de Músicos** - Adicionar músicos aos eventos
- ✅ **Gerenciamento de Setlist** - Criar e ordenar músicas dos eventos
- ✅ **Multi-Tenant** - Isolamento completo de dados por banda
- ✅ **Performance** - Índices otimizados e queries eficientes

## 🏗️ Estrutura do Projeto

```
app/
├── Http/Controllers/
│   ├── Auth/BandaAuthController.php    # Autenticação
│   ├── EventoController.php            # CRUD de eventos
│   ├── MusicoEventoController.php      # Gerenciamento de músicos
│   └── MusicaEventoController.php      # Gerenciamento de músicas
├── Models/
│   ├── Banda.php                       # Modelo de banda (tenant)
│   ├── Evento.php                      # Modelo de evento
│   ├── MusicoEvento.php               # Modelo de músico
│   └── MusicaEvento.php               # Modelo de música
└── Traits/
    └── BelongsToTenant.php            # Trait para validação de tenant

database/migrations/                    # Migrations do banco
routes/api.php                          # Rotas da API
```

## 🚀 Instalação

### Pré-requisitos

- PHP 8.2 ou superior
- Composer
- PostgreSQL (produção) ou SQLite (desenvolvimento)

### Passos

```bash
# 1. Clonar o repositório
git clone [url-do-repositorio]
cd musicorg-back

# 2. Instalar dependências
composer install

# 3. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 4. Configurar banco de dados no .env
DB_CONNECTION=sqlite  # ou pgsql para PostgreSQL
DB_DATABASE=database/database.sqlite  # para SQLite

# 5. Executar migrations
php artisan migrate

# 6. Iniciar servidor
php artisan serve
```

A API estará disponível em `http://localhost:8000/api`

## 📡 Endpoints Principais

### Autenticação

- `POST /api/register` - Registrar nova banda
- `POST /api/login` - Login da banda
- `POST /api/logout` - Logout (requer token)
- `GET /api/me` - Obter banda autenticada (requer token)

### Eventos

- `GET /api/eventos` - Listar eventos da banda
- `GET /api/eventos/{id}` - Ver evento específico
- `POST /api/eventos` - Criar evento
- `PUT /api/eventos/{id}` - Atualizar evento
- `DELETE /api/eventos/{id}` - Excluir evento

### Músicos

- `GET /api/eventos/{eventoId}/musicos` - Listar músicos
- `POST /api/eventos/{eventoId}/musicos` - Adicionar músico
- `GET /api/eventos/{eventoId}/musicos/{musicoId}` - Ver músico
- `PUT /api/eventos/{eventoId}/musicos/{musicoId}` - Atualizar músico
- `DELETE /api/eventos/{eventoId}/musicos/{musicoId}` - Remover músico

### Músicas (Setlist)

- `GET /api/eventos/{eventoId}/musicas` - Listar músicas
- `POST /api/eventos/{eventoId}/musicas` - Adicionar música
- `POST /api/eventos/{eventoId}/musicas/reorder` - Reordenar setlist
- `GET /api/eventos/{eventoId}/musicas/{musicaId}` - Ver música
- `PUT /api/eventos/{eventoId}/musicas/{musicaId}` - Atualizar música
- `DELETE /api/eventos/{eventoId}/musicas/{musicaId}` - Remover música

## 🔐 Autenticação

Todas as rotas (exceto `/register` e `/login`) requerem autenticação via token Bearer:

```
Authorization: Bearer {token}
```

O token é retornado após login ou registro e deve ser enviado em todas as requisições protegidas.

## 🏢 Multi-Tenant

O sistema implementa isolamento completo de dados por banda:

- ✅ Cada banda só vê seus próprios eventos
- ✅ Validação explícita de ownership em todas as operações
- ✅ Global Scope automático para filtragem por tenant
- ✅ Proteção contra acesso cruzado entre tenants

## ⚡ Performance

- Índices otimizados em `band_id` e `event_id`
- Eager loading opcional via query parameters
- Paginação disponível
- Queries otimizadas para multi-tenant

**Exemplo:**
```bash
# Listar apenas eventos (rápido)
GET /api/eventos

# Listar com relacionamentos (quando necessário)
GET /api/eventos?with=musicos,musicas

# Com paginação
GET /api/eventos?page=1&per_page=15
```

## 🌐 Deploy na Render

### Variáveis de Ambiente Necessárias

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
APP_URL=https://seu-backend.onrender.com

DB_CONNECTION=pgsql
DB_URL=postgresql://usuario:senha@host:porta/database

FRONTEND_URL=https://seu-frontend.onrender.com
```

### Build e Start Commands

**Build Command:**
```bash
composer install --no-dev --optimize-autoloader && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

**Start Command:**
```bash
php artisan serve --host=0.0.0.0 --port=$PORT
```

## 📊 Estrutura do Banco de Dados

### Tabelas Principais

- `bandas` - Bandas (tenants)
- `eventos` - Eventos musicais
- `musicos_evento` - Músicos por evento
- `musicas_evento` - Músicas por evento (setlist)
- `personal_access_tokens` - Tokens de autenticação (Sanctum)
- `migrations` - Controle de versão do banco

## 🔒 Segurança

- ✅ Autenticação obrigatória em todas as rotas protegidas
- ✅ Validação explícita de tenant ownership
- ✅ Global Scope para filtragem automática
- ✅ Proteção contra SQL injection (Eloquent ORM)
- ✅ Validação de entrada em todos os endpoints
- ✅ CORS configurado para frontend

## 📝 Exemplo de Uso

### Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "banda@exemplo.com",
    "password": "senha123"
  }'
```

### Criar Evento

```bash
curl -X POST http://localhost:8000/api/eventos \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "titulo": "Show de Rock",
    "data": "2025-11-15",
    "hora": "20:00",
    "local": "Praça Central"
  }'
```

### Listar Eventos

```bash
curl -X GET http://localhost:8000/api/eventos \
  -H "Authorization: Bearer {token}"
```

## 🧪 Testes

```bash
# Executar testes
php artisan test
```

## 📦 Dependências Principais

- `laravel/framework: ^12.0`
- `laravel/sanctum: ^4.2`
- `laravel/tinker: ^2.10.1`

## 🤝 Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT.

---

