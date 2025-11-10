# Onfly Travel API

API para gerenciamento de pedidos de viagem e notificações, desenvolvida com Laravel 12 e autenticação JWT.

## 🚀 Tecnologias

- PHP 8.2+
- Laravel 12
- MySQL 8.0
- JWT Authentication (tymon/jwt-auth)
- Docker & Docker Compose
- Swagger/OpenAPI para documentação

## 📋 Pré-requisitos

- Docker
- Docker Compose

## 🔧 Instalação e Configuração

### 1. Clone o repositório

```bash
git clone <repository-url>
cd onfly-api
```

### 2. Suba os containers Docker

```bash
docker-compose up -d
```

### 3. Instale as dependências dentro do container

```bash
docker exec onfly_app composer install
```

### 4. Configure o arquivo .env

```bash
cp .env.example .env
docker exec onfly_app php artisan key:generate
docker exec onfly_app php artisan jwt:secret
```

### 5. Execute as migrations

```bash
docker exec onfly_app php artisan migrate
```

### 6. (Opcional) Execute os seeders

```bash
docker exec onfly_app php artisan db:seed
```

## 📚 Documentação da API (Swagger)

A documentação completa da API está disponível através do Swagger UI.

### Acessar a Documentação

Após subir os containers, acesse:

```
http://localhost:8000/api/documentation
```

A documentação Swagger inclui:

- ✅ Todas as rotas disponíveis
- ✅ Parâmetros de requisição
- ✅ Exemplos de request/response
- ✅ Autenticação JWT (Bearer Token)
- ✅ Schemas de dados
- ✅ Códigos de status HTTP

## 🔐 Autenticação

A API utiliza JWT (JSON Web Tokens) para autenticação.

### Como Usar

1. **Registrar um usuário:**
   - POST `/api/register`

2. **Fazer login:**
   - POST `/api/login`
   - Receberá um `access_token` na resposta

3. **Usar o token:**
   - Adicione o header em todas as requisições autenticadas:
   ```
   Authorization: Bearer {seu_token_aqui}
   ```

## 📡 Endpoints Principais

### Health Check
- `GET /api/ping` - Verifica se a API está funcionando

### Autenticação
- `POST /api/register` - Registrar novo usuário
- `POST /api/login` - Fazer login
- `POST /api/logout` - Fazer logout (requer autenticação)
- `GET /api/me` - Obter dados do usuário autenticado

### Pedidos de Viagem (Travel Orders)
- `POST /api/orders` - Criar novo pedido
- `GET /api/orders` - Listar pedidos (com filtros)
- `GET /api/orders/{id}` - Obter pedido específico
- `PATCH /api/orders/{id}/status` - Atualizar status (somente admin)

### Notificações
- `GET /api/me/notificacoes` - Listar notificações
- `PATCH /api/me/notificacoes/{id}/read` - Marcar como lida

## 🧪 Testes

Execute os testes dentro do container:

```bash
docker exec onfly_app php artisan test
```

## 🐳 Comandos Docker Úteis

### Ver logs do container
```bash
docker logs onfly_app -f
```

### Acessar o bash do container
```bash
docker exec -it onfly_app bash
```

### Parar os containers
```bash
docker-compose down
```

### Reconstruir os containers
```bash
docker-compose up -d --build
```

## 🔍 Filtros Disponíveis

### Listagem de Pedidos de Viagem

A rota `GET /api/orders` aceita os seguintes filtros via query params:

- `status` - Filtrar por status (solicitado, aprovado, cancelado)
- `destination` - Filtrar por destino
- `departure_date` - Data exata de partida (YYYY-MM-DD)
- `return_date` - Data exata de retorno (YYYY-MM-DD)
- `departure_date_from` - Data de partida início do range
- `departure_date_to` - Data de partida fim do range
- `return_date_from` - Data de retorno início do range
- `return_date_to` - Data de retorno fim do range

**Exemplo:**
```
GET /api/orders?status=aprovado&destination=São Paulo&departure_date_from=2025-12-01
```

## 👥 Perfis de Usuário

### Usuário Normal
- Pode criar pedidos de viagem
- Pode visualizar apenas seus próprios pedidos
- Recebe notificações sobre mudanças de status

### Administrador
- Pode visualizar todos os pedidos
- Pode alterar status dos pedidos
- Usuários normais recebem notificações quando admin altera status

### Criar um Administrador

Para criar um usuário administrador, registre-se normalmente e depois atualize no banco de dados ou registre com o campo `is_admin: true`:

```json
{
  "name": "Admin User",
  "email": "admin@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "is_admin": true
}
```

## 📝 Status dos Pedidos

Os pedidos de viagem podem ter os seguintes status:

- `solicitado` - Pedido criado, aguardando aprovação
- `aprovado` - Pedido aprovado pelo administrador
- `cancelado` - Pedido cancelado

## 🌐 URLs
- **Documentação Swagger:** http://localhost:8000/api/documentation

## 🗄️ Banco de Dados

### Credenciais MySQL

- **Host:** localhost
- **Port:** 3323
- **Database:** onfly
- **Username:** onfly
- **Password:** onfly
- **Root Password:** root

## 📂 Estrutura do Projeto

```
app/
├── Http/
│   ├── Controllers/Api/     # Controladores da API
│   ├── Middleware/           # Middlewares customizados
│   └── Requests/             # Form Requests com validações
├── Models/                   # Models Eloquent
├── Services/                 # Camada de serviço (lógica de negócio)
├── Data/                     # DTOs (Data Transfer Objects)
└── Exceptions/               # Exceções customizadas

routes/
└── api.php                   # Definição das rotas da API

database/
├── migrations/               # Migrations do banco
└── factories/                # Factories para testes

tests/
├── Unit/                     # Testes unitários
└── Feature/                  # Testes de integração

storage/
└── api-docs/                 # Documentação Swagger gerada
    └── api-docs.json
```

## 📖 Documentação Adicional

- [Laravel Documentation](https://laravel.com/docs)
- [JWT Auth Documentation](https://jwt-auth.readthedocs.io/)
- [Swagger/OpenAPI Specification](https://swagger.io/specification/)

