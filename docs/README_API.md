# 📚 API Documentation - Plataforma de Cursos

## 🚀 **Visão Geral**

Esta é a documentação completa da API RESTful da Plataforma de Cursos, desenvolvida em Laravel 12 com autenticação via Laravel Sanctum.

**Base URL (desenvolvimento)**: `http://localhost:8080/api` (ajuste conforme `APP_URL` do ambiente; o prefixo da API é `/api`).

---

## 🔐 **Autenticação e Autorização**

A API utiliza **Laravel Sanctum** para autenticação via tokens. Para acessar rotas protegidas, inclua o token no header:

```
Authorization: Bearer {seu_token_aqui}
```

### **Arquitetura de Autorização**

A API utiliza uma arquitetura **unificada** baseada em **Policies** do Laravel:

- **Rotas Unificadas**: Uma mesma rota serve tanto admins quanto estudantes
- **Autorização via Policies**: Cada modelo possui uma Policy que determina quem pode fazer o quê
- **Serialização Contextual**: API Resources mostram diferentes dados conforme o tipo de usuário
- **Escalável**: Fácil adicionar novos tipos de usuários ou permissões

Exemplo:
- `GET /courses` - Admin vê todos os cursos; Estudante vê apenas cursos ativos
- `POST /courses` - Apenas Admin pode criar
- `GET /questions/{id}` - Admin vê resposta correta; Estudante não vê

### **Usuários de Teste**

| Tipo | Email | Senha | Acesso |
|------|-------|-------|--------|
| **Admin** | `admin@curso-platform.com` | `password` | CRUD completo de todos os recursos |
| **Student** | `student@curso-platform.com` | `password` | Leitura de cursos ativos, atividades matriculadas e certificados próprios |

---

## 📋 **Rotas Públicas**

### **1. Autenticação**

#### **POST** `/auth/register`
Registra um novo usuário na plataforma. Suporta registro tradicional (com senha) e registro via Google (com google_id).

**Request Body (Registro Tradicional):**
```json
{
  "name": "string",
  "email": "string",
  "password": "string",
  "password_confirmation": "string",
  "cpf": "string",
  "phone": "string (opcional)",
  "education_level": "string (opcional)"
}
```

**Request Body (Registro via Google):**
```json
{
  "name": "string",
  "email": "string",
  "google_id": "string",
  "cpf": "string",
  "phone": "string (opcional)",
  "education_level": "string (opcional)"
}
```

**Comportamento**:
- ✅ Se `google_id` for fornecido, `password` não é obrigatório
- ✅ Se `google_id` não for fornecido, `password` é obrigatório
- ✅ `cpf` é sempre obrigatório em ambos os casos
- ✅ Verifica automaticamente se o CPF está na tabela `external_customers`
- ✅ Define `is_customer = true` se o CPF existir na tabela, caso contrário `false`

**Valores permitidos para `education_level`:**
- `fundamental` - Ensino Fundamental
- `medio_incompleto` - Ensino Médio Incompleto
- `medio_completo` - Ensino Médio Completo
- `superior_incompleto` - Ensino Superior Incompleto
- `superior_completo` - Ensino Superior Completo
- `pos_graduacao` - Pós-graduação

**Response (201):**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "Nome do Usuário",
    "email": "email@exemplo.com",
    "cpf": "12345678901",
    "type": "student",
    "is_customer": true,
    "phone": "(11) 99999-9999",
    "education_level": "superior_completo",
    "education_level_label": "Ensino Superior Completo",
    "education_level_index": 5,
    "email_verified_at": null,
    "created_at": "2025-09-16T20:18:38.000000Z",
    "updated_at": "2025-09-16T20:18:38.000000Z"
  },
  "token": "1|token_aqui"
}
```

**Nota:** O campo `is_customer` é definido automaticamente durante o registro:
- `true`: Se o CPF informado existe na tabela `external_customers`
- `false`: Se o CPF informado não existe na tabela `external_customers`

#### **POST** `/auth/login`
Autentica um usuário existente.

**Request Body:**
```json
{
  "email": "string",
  "password": "string"
}
```

**Response (200):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Nome do Usuário",
    "email": "email@exemplo.com",
    "cpf": "12345678901",
    "type": "admin",
    "phone": "(11) 99999-9999",
    "education_level": "superior_completo",
    "education_level_label": "Ensino Superior Completo",
    "education_level_index": 5,
    "email_verified_at": null,
    "created_at": "2025-09-16T20:18:38.000000Z",
    "updated_at": "2025-09-16T20:18:38.000000Z"
  },
  "token": "1|token_aqui"
}
```

### **2. Cursos Públicos**

#### **GET** `/public/courses`
Lista todos os cursos ativos disponíveis publicamente.

**Query Parameters:**
- `page` - Número da página (padrão: 1)
- `per_page` - Itens por página (padrão: 15)
- `category` - Filtrar por categoria
- `difficulty` - Filtrar por nível de dificuldade
- `is_free` - Filtrar por preço: use `1` ou `true` para apenas gratuitos (`price = 0`); `0` para apenas pagos. Evite o valor literal `false` na query string (em PHP pode ser interpretado incorretamente).
- `search` - Buscar por título ou descrição

**Response (200):**
```json
{
  "courses": [
    {
      "id": 10,
      "title": "Laravel do Zero ao Avançado",
      "description": "Aprenda Laravel desde o básico até conceitos avançados...",
      "short_description": "Curso completo de Laravel com projetos práticos...",
      "long_description": "<h2>O que você vai aprender:</h2><ul><li>Fundamentos do Laravel</li><li>Eloquent ORM</li><li>APIs RESTful</li><li>Autenticação e Autorização</li><li>Testes Automatizados</li><li>Deploy em Produção</li></ul>",
      "cover_image_url": "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&h=600&fit=crop",
      "workload": 60,
      "modules_count": 12,
      "difficulty_level": "avancado",
      "difficulty_level_label": "Avançado",
      "category": "programacao",
      "category_label": "Programação",
      "price": "299.90",
      "promotional_price": "199.90",
      "discount_percentage": 33,
      "final_price": 199.9,
      "discount_amount": 100.0,
      "is_free": false,
      "has_discount": true,
      "total_duration": 610,
      "created_at": "2025-09-18T20:44:36.000000Z",
      "updated_at": "2025-09-18T20:44:36.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 15,
    "total": 16,
    "from": 1,
    "to": 15
  },
  "filters": {
    "categories": {
      "programacao": "Programação",
      "banco_dados": "Banco de Dados",
      "produtividade": "Produtividade",
      "lideranca": "Liderança",
      "marketing": "Marketing",
      "design": "Design",
      "negocios": "Negócios",
      "tecnologia": "Tecnologia",
      "outros": "Outros"
    },
    "difficulty_levels": {
      "iniciante": "Iniciante",
      "intermediario": "Intermediário",
      "avancado": "Avançado"
    }
  }
}
```

#### **GET** `/public/courses/{id}`
Exibe detalhes de um curso específico.

**Response (200):**
```json
{
  "course": {
    "id": 10,
    "title": "Laravel do Zero ao Avançado",
    "description": "Aprenda Laravel desde o básico até conceitos avançados...",
    "short_description": "Curso completo de Laravel com projetos práticos...",
    "long_description": "<h2>O que você vai aprender:</h2><ul><li>Fundamentos do Laravel</li><li>Eloquent ORM</li><li>APIs RESTful</li><li>Autenticação e Autorização</li><li>Testes Automatizados</li><li>Deploy em Produção</li></ul>",
    "cover_image_url": "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&h=600&fit=crop",
    "workload": 60,
    "modules_count": 12,
    "difficulty_level": "avancado",
    "difficulty_level_label": "Avançado",
    "category": "programacao",
    "category_label": "Programação",
    "price": "299.90",
    "promotional_price": "199.90",
    "discount_percentage": 33,
    "final_price": 199.9,
    "discount_amount": 100.0,
    "is_free": false,
    "has_discount": true,
    "modules": [
      {
        "id": 26,
        "title": "Introdução ao Laravel",
        "order": 1,
        "type": "video",
        "duration": 30
      },
      {
        "id": 27,
        "title": "Configuração do Ambiente",
        "order": 2,
        "type": "video",
        "duration": 45
      },
      {
        "id": 28,
        "title": "Rotas e Controllers",
        "order": 3,
        "type": "video",
        "duration": 60
      }
    ],
    "total_duration": 610,
    "created_at": "2025-09-18T20:44:36.000000Z",
    "updated_at": "2025-09-18T20:44:36.000000Z"
  }
}
```

#### **GET** `/public/courses/categories`
Lista todas as categorias disponíveis.

**Response (200):**
```json
{
  "categories": {
    "programacao": "Programação",
    "banco_dados": "Banco de Dados",
    "produtividade": "Produtividade",
    "lideranca": "Liderança",
    "marketing": "Marketing",
    "design": "Design",
    "negocios": "Negócios",
    "tecnologia": "Tecnologia",
    "outros": "Outros"
  }
}
```

#### **GET** `/public/courses/difficulty-levels`
Lista todos os níveis de dificuldade disponíveis.

**Response (200):**
```json
{
  "difficulty_levels": {
    "iniciante": "Iniciante",
    "intermediario": "Intermediário",
    "avancado": "Avançado"
  }
}
```

### **📚 Tipos de Módulos e Durações**

Os cursos contêm diferentes tipos de módulos com durações específicas:

| Tipo | Descrição | Duração |
|------|-----------|---------|
| **video** | Aulas em vídeo | Duração real (30-120 min) |
| **article** | Artigos e leituras | 10 minutos estimados |
| **quiz** | Questionários e testes | 5 minutos estimados |
| **mini_game** | Jogos e desafios práticos | 15 minutos estimados |

**Nota:** A duração total do curso (`total_duration`, em **minutos**) é calculada automaticamente somando as durações das atividades (vídeos convertidos de segundos para minutos; demais tipos usam estimativas em minutos).

**Nota sobre o array `modules` em `GET /public/courses/{id}`:** cada item inclui `type` (`video`, `article`, `quiz`, `mini_game`, etc.). Para atividades do tipo **`video`**, o campo `duration` retornado é a duração do vídeo em **segundos** (conforme `VideoActivity.duration`). Para os demais tipos, `duration` é a estimativa em **minutos** usada no cálculo do curso.

### **3. Validação de Certificados**

#### **GET** `/certificates/validate/{uuid}`
Valida um certificado pelo UUID (público, sem autenticação). A API recalcula o hash de integridade com base em `uuid`, `user_id`, `course_id`, `issue_date` e `APP_KEY`; se não coincidir com `hash_validation` armazenado, o certificado é considerado inválido.

**Response (200):**
```json
{
  "valid": true,
  "certificate": {
    "uuid": "12345678-1234-1234-1234-123456789012",
    "student_name": "Nome do Usuário",
    "course_title": "Nome do Curso",
    "course_total_duration": 610,
    "issue_date": "2025-09-16T20:18:38.000000Z"
  }
}
```

- `course_total_duration`: duração total do curso em **minutos** (coluna `courses.total_duration`).
- `issue_date`: data/hora de emissão (ISO 8601).

**Response (404)** — certificado não encontrado:
```json
{
  "valid": false,
  "message": "Certificate not found"
}
```

**Response (400)** — UUID existe, mas a validação de hash falhou (dados alterados ou inconsistência):
```json
{
  "valid": false,
  "message": "Certificate validation failed"
}
```

---

## 🔒 **Rotas Protegidas**

### **Autenticação (Requer Token)**

#### **GET** `/auth/user`
Retorna os dados do usuário autenticado.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "user": {
    "id": 1,
    "name": "Nome do Usuário",
    "email": "email@exemplo.com",
    "cpf": "12345678901",
    "type": "admin",
    "phone": "(11) 99999-9999",
    "education_level": "superior_completo",
    "education_level_label": "Ensino Superior Completo",
    "education_level_index": 5,
    "email_verified_at": null,
    "created_at": "2025-09-16T20:18:38.000000Z",
    "updated_at": "2025-09-16T20:18:38.000000Z"
  }
}
```

#### **POST** `/auth/logout`
Desloga o usuário (invalida o token atual).

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

#### **PUT** `/auth/profile`
Atualiza dados do perfil do usuário autenticado.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "string (opcional)",
  "email": "string (opcional)",
  "phone": "string (opcional, máximo 20 caracteres)",
  "education_level": "string (opcional)"
}
```

**Valores permitidos para `education_level`:**
- `fundamental` - Ensino Fundamental
- `medio_incompleto` - Ensino Médio Incompleto
- `medio_completo` - Ensino Médio Completo
- `superior_incompleto` - Ensino Superior Incompleto
- `superior_completo` - Ensino Superior Completo
- `pos_graduacao` - Pós-graduação

**Nota:** O campo `cpf` não pode ser atualizado por motivos de segurança.

**Response (200):**
```json
{
  "message": "Profile updated successfully",
  "user": {
    "id": 1,
    "name": "Nome Atualizado",
    "email": "email.atualizado@exemplo.com",
    "cpf": "12345678901",
    "type": "student",
    "phone": "(11) 88888-8888",
    "education_level": "pos_graduacao",
    "education_level_label": "Pós-graduação",
    "education_level_index": 6,
    "email_verified_at": null,
    "created_at": "2025-09-16T20:18:38.000000Z",
    "updated_at": "2025-09-18T17:12:10.000000Z"
  }
}
```

#### **PUT** `/auth/password`
Altera a senha do usuário autenticado.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "current_password": "string",
  "password": "string",
  "password_confirmation": "string"
}
```

**Response (200):**
```json
{
  "message": "Password changed successfully"
}
```

**Response (422) - Senha atual incorreta:**
```json
{
  "message": "The given data was invalid.",
  // "errors": {
  //   "current_password": ["The current password is incorrect."]
  // }
}
```

#### **DELETE** `/auth/account`
Deleta a conta do usuário autenticado.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "password": "string"
}
```

**Response (200):**
```json
{
  "message": "Account deleted successfully"
}
```

**Response (422) - Senha incorreta:**
```json
{
  "message": "The given data was invalid.",
  // "errors": {
  //   "password": ["The password is incorrect."]
  // }
}
```

---

## 🔒 **Rotas Protegidas (Autenticadas)**

*Requer autenticação via token. Autorização determinada por Policies.*

### **Cursos**

#### **GET** `/courses`
Lista cursos conforme permissão do usuário.

**Comportamento:**
- **Admin**: Vê todos os cursos (ativos e inativos)
- **Student**: Vê apenas cursos ativos

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (opcional): Número da página
- `per_page` (opcional): Itens por página

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "title": "Nome do Curso",
      "description": "Descrição do curso",
      "workload": 40,
      "is_active": true,
      "activities_count": 5,
      "activities": [],
      "created_at": "2025-09-16T20:18:38.000000Z",
      "updated_at": "2025-09-16T20:18:38.000000Z"
    }
  ],
  "first_page_url": "http://localhost:8080/api/courses?page=1",
  "from": 1,
  "last_page": 1,
  "last_page_url": "http://localhost:8080/api/courses?page=1",
  "links": [...],
  "next_page_url": null,
  "path": "http://localhost:8080/api/courses",
  "per_page": 15,
  "prev_page_url": null,
  "to": 1,
  "total": 1
}
```

#### **GET** `/courses/{id}`
Retorna um curso específico.

**Comportamento:**
- **Admin**: Vê todos os dados do curso
- **Student**: Vê apenas se o curso estiver ativo + informações de matrícula (se matriculado)

**Response (200):**
```json
{
  "course": {
    "id": 1,
    "title": "Laravel do Zero ao Avançado",
    "description": "Aprenda Laravel desde o básico até conceitos avançados, incluindo APIs RESTful, autenticação, testes e deploy.",
    "short_description": "Curso completo de Laravel com projetos práticos e boas práticas de desenvolvimento.",
    "long_description": "<h2>O que você vai aprender:</h2><ul><li>Fundamentos do Laravel</li><li>Eloquent ORM</li><li>APIs RESTful</li><li>Autenticação e Autorização</li><li>Testes Automatizados</li><li>Deploy em Produção</li></ul>",
    "cover_image_url": "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&h=600&fit=crop",
    "workload": 60,
    "modules_count": 12,
    "difficulty_level": "avancado",
    "difficulty_level_label": "Avançado",
    "category": "programacao",
    "category_label": "Programação",
    "price": 299.90,
    "promotional_price": 199.90,
    "discount_percentage": 33,
    "final_price": 199.90,
    "discount_amount": 100.00,
    "is_free": false,
    "has_discount": true,
    "is_active": true,
    "activities_count": 5,
    "activities": [
      {
        "id": 1,
        "title": "Atividade 1",
        "order": 1,
        "activity_type": {
          "id": 1,
          "name": "video"
        },
        "activityable": {
          "type": "VideoActivity",
          "data": {
            "id": 1,
            "url": "https://exemplo.com/video.mp4",
            "duration": 300
          }
        },
        "course_id": 1,
        "created_at": "2025-09-16T20:18:38.000000Z",
        "updated_at": "2025-09-16T20:18:38.000000Z"
      }
    ],
    "created_at": "2025-09-16T20:18:38.000000Z",
    "updated_at": "2025-09-16T20:18:38.000000Z"
  }
}
```

#### **POST** `/courses`
Cria um novo curso.

**Autorização**: Apenas Admin

**Content-Type**: `multipart/form-data`

**Request Fields:**
- `title` (string, obrigatório)
- `description` (string, obrigatório)
- `short_description` (string, opcional, máximo 500 caracteres)
- `long_description` (string HTML, opcional)
- `cover_image` (arquivo de imagem, obrigatório)
- `workload` (integer, obrigatório)
- `modules_count` (integer, opcional, mínimo 0)
- `difficulty_level` (string, opcional: iniciante, intermediario, avancado)
- `category` (string, opcional, máximo 100 caracteres)
- `price` (decimal, obrigatório, mínimo 0)
- `promotional_price` (decimal, opcional, mínimo 0)
- `discount_percentage` (integer, opcional, 0-100)
- `is_active` (boolean, opcional)

**Valores permitidos para `difficulty_level`:**
- `iniciante` - Iniciante
- `intermediario` - Intermediário
- `avancado` - Avançado

**Categorias sugeridas:**
- `programacao` - Programação
- `banco_dados` - Banco de Dados
- `produtividade` - Produtividade
- `lideranca` - Liderança
- `marketing` - Marketing
- `design` - Design
- `negocios` - Negócios
- `tecnologia` - Tecnologia
- `outros` - Outros

**Response (201):**
```json
{
  "message": "Course created successfully",
  "course": {
    "id": 1,
    "title": "Laravel do Zero ao Avançado",
    "description": "Aprenda Laravel desde o básico até conceitos avançados, incluindo APIs RESTful, autenticação, testes e deploy.",
    "short_description": "Curso completo de Laravel com projetos práticos e boas práticas de desenvolvimento.",
    "long_description": "<h2>O que você vai aprender:</h2><ul><li>Fundamentos do Laravel</li><li>Eloquent ORM</li><li>APIs RESTful</li><li>Autenticação e Autorização</li><li>Testes Automatizados</li><li>Deploy em Produção</li></ul>",
    "cover_image_url": "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&h=600&fit=crop",
    "workload": 60,
    "modules_count": 12,
    "difficulty_level": "avancado",
    "difficulty_level_label": "Avançado",
    "category": "programacao",
    "category_label": "Programação",
    "price": "299.90",
    "promotional_price": "199.90",
    "discount_percentage": 33,
    "final_price": "199.90",
    "discount_amount": "100.00",
    "is_free": false,
    "has_discount": true,
    "is_active": true,
    "activities_count": 0,
    "activities": [],
    "created_at": "2025-09-16T20:18:38.000000Z",
    "updated_at": "2025-09-16T20:18:38.000000Z"
  }
}
```

#### **PUT** `/courses/{id}`
Atualiza um curso existente.

**Autorização**: Apenas Admin

**Content-Type**: `multipart/form-data`

**Request Fields (todos opcionais):**
- `title` (string, máximo 255 caracteres)
- `description` (string)
- `short_description` (string, máximo 500 caracteres)
- `long_description` (string HTML)
- `cover_image` (arquivo de imagem, opcional)
- `workload` (integer, mínimo 1)
- `modules_count` (integer, mínimo 0)
- `difficulty_level` (iniciante, intermediario, avancado)
- `category` (programacao, banco_dados, produtividade, lideranca, marketing, design, negocios, tecnologia, outros)
- `price` (decimal, mínimo 0)
- `promotional_price` (decimal, mínimo 0)
- `discount_percentage` (integer, 0-100)
- `is_active` (boolean)

**Exemplo de atualização (multipart):**
- Enviar os campos desejados em `multipart/form-data`.
- Incluir `cover_image` apenas quando quiser substituir a capa atual.

**Response (200):**
```json
{
  "message": "Course updated successfully",
  "course": {
    "id": 1,
    "title": "Laravel do Zero ao Avançado - Atualizado",
    "description": "Aprenda Laravel desde o básico até conceitos avançados, incluindo APIs RESTful, autenticação, testes e deploy.",
    "short_description": "Curso completo de Laravel com projetos práticos e boas práticas de desenvolvimento.",
    "long_description": "<h2>O que você vai aprender:</h2><ul><li>Fundamentos do Laravel</li><li>Eloquent ORM</li><li>APIs RESTful</li><li>Autenticação e Autorização</li><li>Testes Automatizados</li><li>Deploy em Produção</li></ul>",
    "cover_image_url": "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&h=600&fit=crop",
    "workload": 60,
    "modules_count": 12,
    "difficulty_level": "avancado",
    "difficulty_level_label": "Avançado",
    "category": "programacao",
    "category_label": "Programação",
    "price": "299.90",
    "promotional_price": "199.90",
    "discount_percentage": 33,
    "final_price": "199.90",
    "discount_amount": "100.00",
    "is_free": false,
    "has_discount": true,
    "is_active": true,
    "activities_count": 0,
    "activities": [],
    "created_at": "2025-09-16T20:18:38.000000Z",
    "updated_at": "2025-09-20T15:30:00.000000Z"
  }
}
```

#### **DELETE** `/courses/{id}`
Remove um curso (soft delete).

**Autorização**: Apenas Admin

**Response (200):**
```json
{
  "message": "Course deleted successfully"
}
```

### **Matrícula em Curso**

#### **GET** `/courses/enrolled`
Lista os cursos nos quais o usuário autenticado está matriculado.

**Autorização**: Requer autenticação (Student vê apenas seus próprios cursos)

**Query Parameters:**
- `per_page` (opcional): Número de itens por página (padrão: 15)

**Response (200):**
```json
{
  "data": [
    {
      "id": 33,
      "title": "Turbine sua Produtividade com IA",
      "description": "Aprenda a usar IA para aumentar sua produtividade",
      "short_description": "Curso completo sobre produtividade com IA",
      "cover_image_url": "https://example.com/image.jpg",
      "total_duration": 55,
      "modules_count": 10,
      "difficulty_level": "iniciante",
      "difficulty_level_label": "Iniciante",
      "category": "tecnologia",
      "category_label": "Tecnologia",
      "is_free": false,
      "enrollment": {
        "progress": 100,
        "completed_at": "2025-12-04T07:28:40.000000Z",
        "is_completed": true
      },
      "created_at": "2025-09-16T20:18:38.000000Z",
      "updated_at": "2025-09-20T15:30:00.000000Z"
    }
  ],
  "current_page": 1,
  "last_page": 1,
  "per_page": 15,
  "total": 1
}
```

#### **POST** `/courses/{id}/enroll`
Matricula o usuário autenticado em um curso.

**Autorização**: Apenas Student em cursos ativos

**Response (201):**
```json
{
  "message": "Successfully enrolled in course",
  "enrollment": {
    "user_id": 2,
    "course_id": 1,
    "progress": 0,
    "created_at": "2025-12-01T10:00:00.000000Z"
  }
}
```

**Response (409) - Já matriculado:**
```json
{
  "message": "User is already enrolled in this course"
}
```

---

### **Atividades**

As atividades são os componentes de conteúdo de cada curso. Existem 6 tipos de atividades:

| Tipo | Descrição |
|------|-----------|
| **video** | Vídeos armazenados externamente com link, descrição e transcrição |
| **article** | Artigos com conteúdo RichText |
| **embed_content** | Conteúdo incorporado (Genially) via iframe ou script |
| **support_material** | Material de apoio com conteúdo RichText |
| **quiz** | Quiz com questões de múltipla escolha |
| **final_exam** | Prova final do curso (obrigatória, única e sempre última) |

#### **GET** `/courses/{courseId}/activities`
Lista atividades de um curso.

**Comportamento:**
- **Admin**: Vê todas as atividades de qualquer curso
- **Student**: Vê atividades apenas de cursos matriculados e ativos

**Response (200):**
```json
{
  "course": {
    "id": 1,
    "title": "Laravel Avançado"
  },
  "activities": [
    {
      "id": 1,
      "course_id": 1,
      "type": "video",
      "title": "Introdução ao Curso",
      "order": 1,
      "video": {
        "id": 1,
        "description": "Neste vídeo você aprenderá os fundamentos...",
        "transcript": "Olá, bem-vindos ao curso...",
        "link": "https://youtube.com/watch?v=abc123"
      },
      "created_at": "2025-12-01T10:00:00.000000Z",
      "updated_at": "2025-12-01T10:00:00.000000Z"
    },
    {
      "id": 2,
      "course_id": 1,
      "type": "quiz",
      "title": "Quiz Módulo 1",
      "order": 2,
      "quiz": {
        "id": 1,
        "description": "Teste seus conhecimentos",
        "duration_minutes": 15
      },
      "created_at": "2025-12-01T10:05:00.000000Z",
      "updated_at": "2025-12-01T10:05:00.000000Z"
    }
  ]
}
```

#### **POST** `/courses/{courseId}/activities`
Cria uma nova atividade.

**Autorização**: Apenas Admin

A requisição varia conforme o tipo de atividade.

**Campos Comuns (todos os tipos):**
- `type` - Tipo da atividade (obrigatório)
- `title` - Título da atividade (obrigatório)
- `order` - Ordem da atividade (opcional, será calculado automaticamente se omitido)

---

**Tipo: VIDEO**

**Request Body:**
```json
{
  "type": "video",
  "title": "Introdução ao Laravel",
  "order": 1,
  "video": {
    "description": "Neste vídeo você aprenderá...",
    "transcript": "Olá, bem-vindos ao curso...",
    "link": "https://youtube.com/watch?v=abc123"
  }
}
```

**Nota:** O `course_id` é extraído automaticamente da URL (`{courseId}`), não precisa ser enviado no body.

**Campos do vídeo:**
- `video.description` - Descrição do vídeo (opcional)
- `video.transcript` - Transcrição do vídeo (opcional)
- `video.link` - Link do vídeo (obrigatório, URL válida)

---

**Tipo: ARTICLE**

**Request Body:**
```json
{
  "type": "article",
  "title": "O que é MVC?",
  "order": 2,
  "article": {
    "description": "Neste módulo, vamos construir a base de conhecimento. Você vai entender o que é a IA, como ela evoluiu e, o mais importante, como ela funciona por dentro de forma simples, sem precisar ser um cientista de dados.",
    "content_richtext": "<h2>Model-View-Controller</h2><p>O padrão MVC...</p>"
  }
}
```

**Campos do artigo:**
- `article.description` - Descrição do artigo (opcional)
- `article.content_richtext` - Conteúdo em HTML (obrigatório)

---

**Tipo: EMBED_CONTENT (Genially)**

**Request Body:**
```json
{
  "type": "embed_content",
  "title": "Apresentação Interativa",
  "order": 3,
  "embed_content": {
    "description": "Apresentação sobre Laravel",
    "embed_code": "<iframe src='https://genial.ly/...'></iframe>"
  }
}
```

**Campos do embed:**
- `embed_content.description` - Descrição do conteúdo (opcional)
- `embed_content.embed_code` - Código iframe ou script (obrigatório)

---

**Tipo: SUPPORT_MATERIAL**

**Request Body:**
```json
{
  "type": "support_material",
  "title": "Material Complementar",
  "order": 4,
  "support_material": {
    "description": "Links e recursos adicionais",
    "content_richtext": "<table><tr><td>Documentação</td><td><a href='...'>Link</a></td></tr></table>"
  }
}
```

**Campos do material:**
- `support_material.description` - Descrição do material (opcional)
- `support_material.content_richtext` - Conteúdo em HTML (obrigatório)

---

**Tipo: QUIZ**

**Request Body:**
```json
{
  "type": "quiz",
  "title": "Quiz do Módulo 1",
  "order": 5,
  "quiz": {
    "description": "Teste seus conhecimentos sobre MVC",
    "duration_minutes": 15
  }
}
```

**Campos do quiz:**
- `quiz.description` - Descrição do quiz (opcional)
- `quiz.duration_minutes` - Duração em minutos (opcional)

**Nota:** As questões do quiz são adicionadas separadamente via endpoint `/quiz/{id}/questions`.

---

**Tipo: FINAL_EXAM (Prova Final)**

**Request Body:**
```json
{
  "type": "final_exam",
  "title": "Prova Final do Curso",
  "final_exam": {
    "description": "Avaliação final obrigatória",
    "max_attempts": 3,
    "duration_minutes": 60
  }
}
```

**Campos da prova final:**
- `final_exam.description` - Descrição da prova (opcional)
- `final_exam.max_attempts` - Número máximo de tentativas (obrigatório, 1 a 3)
- `final_exam.duration_minutes` - Duração em minutos (obrigatório)

**Regras especiais:**
- Apenas UMA prova final por curso
- Sempre criada como ÚLTIMA atividade (ordem automática)
- Não pode ser reordenada para posição que não seja a última

**Nota:** As questões da prova são adicionadas separadamente via endpoint `/final_exam/{id}/questions`.

---

**Response (201) - Sucesso:**
```json
{
  "message": "Activity created successfully",
  "activity": {
    "id": 1,
    "course_id": 1,
    "type": "video",
    "title": "Introdução ao Laravel",
    "order": 1,
    "video": {
      "id": 1,
      "description": "Neste vídeo você aprenderá...",
      "transcript": "Olá, bem-vindos ao curso...",
      "link": "https://youtube.com/watch?v=abc123"
    },
    "created_at": "2025-12-01T10:00:00.000000Z",
    "updated_at": "2025-12-01T10:00:00.000000Z"
  }
}
```

**Response (422) - Erro: Prova Final Duplicada:**
```json
{
  "message": "Este curso já possui uma Prova Final. Não é possível cadastrar mais de uma por curso."
}
```

---

#### **GET** `/activities/{id}`
Retorna uma atividade específica.

**Comportamento:**
- **Admin**: Vê qualquer atividade
- **Student**: Vê apenas atividades de cursos matriculados

**Response (200):**
```json
{
  "activity": {
    "id": 1,
    "course_id": 1,
    "type": "video",
    "title": "Introdução ao Laravel",
    "order": 1,
    "video": {
      "id": 1,
      "description": "Neste vídeo você aprenderá...",
      "transcript": "Olá, bem-vindos ao curso...",
      "link": "https://youtube.com/watch?v=abc123"
    },
    "created_at": "2025-12-01T10:00:00.000000Z",
    "updated_at": "2025-12-01T10:00:00.000000Z"
  }
}
```

---

#### **PUT** `/activities/{id}`
Atualiza uma atividade existente.

**Autorização**: Apenas Admin

**Request Body (exemplo para vídeo):**
```json
{
  "title": "Introdução ao Laravel - Atualizado",
  "order": 2,
  "video": {
    "description": "Descrição atualizada",
    "transcript": "Nova transcrição",
    "link": "https://youtube.com/watch?v=xyz789"
  }
}
```

**Notas:**
- Todos os campos são opcionais
- Para atualizar dados específicos do tipo (video, article, etc.), envie o objeto correspondente
- Prova Final não pode ter ordem alterada para valor menor que a última posição
- **Reordenação automática**: Ao alterar o `order`, as outras atividades são automaticamente reposicionadas (shift):
  - Mover para cima (ex: order 5 → 2): atividades com order 2, 3, 4 são incrementadas para 3, 4, 5
  - Mover para baixo (ex: order 2 → 5): atividades com order 3, 4, 5 são decrementadas para 2, 3, 4

**Response (200):**
```json
{
  "message": "Activity updated successfully",
  "activity": {
    "id": 1,
    "course_id": 1,
    "type": "video",
    "title": "Introdução ao Laravel - Atualizado",
    "order": 2,
    "video": {
      "id": 1,
      "description": "Descrição atualizada",
      "transcript": "Nova transcrição",
      "link": "https://youtube.com/watch?v=xyz789"
    },
    "created_at": "2025-12-01T10:00:00.000000Z",
    "updated_at": "2025-12-01T15:30:00.000000Z"
  }
}
```

---

#### **PUT** `/courses/{courseId}/activities/reorder`
Reordena as atividades de um curso.

**Autorização**: Apenas Admin

**Request Body:**
```json
{
  "activities": [
    {"id": 1, "order": 1},
    {"id": 3, "order": 2},
    {"id": 2, "order": 3}
  ]
}
```

**Notas:**
- A Prova Final (se existir) será automaticamente reposicionada como última, independente da ordem fornecida

**Response (200):**
```json
{
  "message": "Activities reordered successfully"
}
```

---

#### **DELETE** `/activities/{id}`
Remove uma atividade e todos os seus dados relacionados.

**Autorização**: Apenas Admin

**Response (200):**
```json
{
  "message": "Activity deleted successfully"
}
```

---

### **Questões (Quiz e Prova Final)**

Questões são objetos com enunciado e 4 alternativas (a, b, c, d), sendo uma correta.

#### **GET** `/{type}/{id}/questions`
Lista questões de um Quiz ou Prova Final.

**Comportamento:**
- **Admin**: Vê todas as questões incluindo a resposta correta (`correct_option`)
- **Student**: Vê questões apenas de cursos matriculados, SEM a resposta correta

**Parâmetros:**
- `type` - Tipo: `quiz` ou `final_exam`
- `id` - ID do Quiz ou Prova Final (campo `activityable.id` da atividade)

**Exemplos:**
- `GET /quiz/1/questions` - Lista questões do Quiz ID 1
- `GET /final_exam/1/questions` - Lista questões da Prova Final ID 1

**Response (200) - Admin:**
```json
{
  "type": "quiz",
  "id": 1,
  "questions": [
    {
      "id": 1,
      "questionable_id": 1,
      "questionable_type": "App\\Models\\QuizActivity",
      "statement": "Qual é a capital do Brasil?",
      "option_a": "Rio de Janeiro",
      "option_b": "Brasília",
      "option_c": "São Paulo",
      "option_d": "Salvador",
      "correct_option": "b",
      "order": 1,
      "created_at": "2025-12-01T10:00:00.000000Z",
      "updated_at": "2025-12-01T10:00:00.000000Z"
    },
    {
      "id": 2,
      "questionable_id": 1,
      "questionable_type": "App\\Models\\QuizActivity",
      "statement": "Quanto é 2 + 2?",
      "option_a": "3",
      "option_b": "4",
      "option_c": "5",
      "option_d": "6",
      "correct_option": "b",
      "order": 2,
      "created_at": "2025-12-01T10:05:00.000000Z",
      "updated_at": "2025-12-01T10:05:00.000000Z"
    }
  ]
}
```

**Response (200) - Student (sem `correct_option`):**
```json
{
  "type": "quiz",
  "id": 1,
  "questions": [
    {
      "id": 1,
      "statement": "Qual é a capital do Brasil?",
      "option_a": "Rio de Janeiro",
      "option_b": "Brasília",
      "option_c": "São Paulo",
      "option_d": "Salvador",
      "order": 1,
      "created_at": "2025-12-01T10:00:00.000000Z",
      "updated_at": "2025-12-01T10:00:00.000000Z"
    }
  ]
}
```

---

#### **POST** `/{type}/{id}/questions`
Cria uma ou mais questões para um Quiz ou Prova Final.

**Autorização**: Apenas Admin

**Exemplos:**
- `POST /quiz/1/questions` - Cria questões no Quiz ID 1
- `POST /final_exam/1/questions` - Cria questões na Prova Final ID 1

**Request Body (uma questão):**
```json
{
  "questions": [
    {
      "statement": "O que é MVC?",
      "option_a": "Model View Controller",
      "option_b": "Multiple View Control",
      "option_c": "Master View Component",
      "option_d": "Managed Visual Code",
      "correct_option": "a"
    }
  ]
}
```

**Request Body (múltiplas questões):**
```json
{
  "questions": [
    {
      "statement": "O que é MVC?",
      "option_a": "Model View Controller",
      "option_b": "Multiple View Control",
      "option_c": "Master View Component",
      "option_d": "Managed Visual Code",
      "correct_option": "a"
    },
    {
      "statement": "Qual é a capital do Brasil?",
      "option_a": "São Paulo",
      "option_b": "Rio de Janeiro",
      "option_c": "Brasília",
      "option_d": "Salvador",
      "correct_option": "c"
    }
  ]
}
```

**Campos de cada questão:**
- `statement` - Enunciado da questão (obrigatório)
- `option_a`, `option_b`, `option_c`, `option_d` - Alternativas (obrigatórias, máx 500 caracteres)
- `correct_option` - Alternativa correta: `a`, `b`, `c` ou `d` (obrigatório)
- `order` - Ordem da questão (opcional, será calculado automaticamente se omitido)

**Response (201) - Uma questão:**
```json
{
  "message": "Question created successfully",
  "questions": [
    {
      "id": 1,
      "questionable_id": 1,
      "questionable_type": "App\\Models\\QuizActivity",
      "statement": "O que é MVC?",
      "option_a": "Model View Controller",
      "option_b": "Multiple View Control",
      "option_c": "Master View Component",
      "option_d": "Managed Visual Code",
      "correct_option": "a",
      "order": 1,
      "created_at": "2025-12-01T10:00:00.000000Z",
      "updated_at": "2025-12-01T10:00:00.000000Z"
    }
  ]
}
```

**Response (201) - Múltiplas questões:**
```json
{
  "message": "2 questions created successfully",
  "questions": [
    {
      "id": 1,
      "questionable_id": 1,
      "questionable_type": "App\\Models\\QuizActivity",
      "statement": "O que é MVC?",
      "option_a": "Model View Controller",
      "option_b": "Multiple View Control",
      "option_c": "Master View Component",
      "option_d": "Managed Visual Code",
      "correct_option": "a",
      "order": 1,
      "created_at": "2025-12-01T10:00:00.000000Z",
      "updated_at": "2025-12-01T10:00:00.000000Z"
    },
    {
      "id": 2,
      "questionable_id": 1,
      "questionable_type": "App\\Models\\QuizActivity",
      "statement": "Qual é a capital do Brasil?",
      "option_a": "São Paulo",
      "option_b": "Rio de Janeiro",
      "option_c": "Brasília",
      "option_d": "Salvador",
      "correct_option": "c",
      "order": 2,
      "created_at": "2025-12-01T10:00:00.000000Z",
      "updated_at": "2025-12-01T10:00:00.000000Z"
    }
  ]
}
```

---

#### **GET** `/questions/{questionId}`
Retorna uma questão específica.

**Comportamento:**
- **Admin**: Vê questão completa com resposta correta
- **Student**: Vê questão sem resposta correta (se matriculado no curso)

**Response (200):**
```json
{
  "question": {
    "id": 1,
    "questionable_id": 1,
    "questionable_type": "App\\Models\\QuizActivity",
    "statement": "O que é MVC?",
    "option_a": "Model View Controller",
    "option_b": "Multiple View Control",
    "option_c": "Master View Component",
    "option_d": "Managed Visual Code",
    "correct_option": "a",
    "order": 1,
    "created_at": "2025-12-01T10:00:00.000000Z",
    "updated_at": "2025-12-01T10:00:00.000000Z"
  }
}
```

---

#### **PUT** `/questions/{questionId}`
Atualiza uma questão existente.

**Autorização**: Apenas Admin

**Request Body (todos os campos são opcionais):**
```json
{
  "statement": "O que é o padrão MVC?",
  "option_a": "Model View Controller",
  "correct_option": "a",
  "order": 2
}
```

**Response (200):**
```json
{
  "message": "Question updated successfully",
  "question": {
    "id": 1,
    "questionable_id": 1,
    "questionable_type": "App\\Models\\QuizActivity",
    "statement": "O que é o padrão MVC?",
    "option_a": "Model View Controller",
    "option_b": "Multiple View Control",
    "option_c": "Master View Component",
    "option_d": "Managed Visual Code",
    "correct_option": "a",
    "order": 2,
    "created_at": "2025-12-01T10:00:00.000000Z",
    "updated_at": "2025-12-01T15:30:00.000000Z"
  }
}
```

---

#### **PUT** `/{type}/{id}/questions/reorder`
Reordena as questões de um Quiz ou Prova Final.

**Autorização**: Apenas Admin

**Exemplos:**
- `PUT /quiz/1/questions/reorder` - Reordena questões do Quiz ID 1
- `PUT /final_exam/1/questions/reorder` - Reordena questões da Prova Final ID 1

**Request Body:**
```json
{
  "questions": [
    {"id": 3, "order": 1},
    {"id": 1, "order": 2},
    {"id": 2, "order": 3}
  ]
}
```

**Response (200):**
```json
{
  "message": "Questions reordered successfully"
}
```

---

#### **DELETE** `/questions/{questionId}`
Remove uma questão.

**Autorização**: Apenas Admin

**Response (200):**
```json
{
  "message": "Question deleted successfully"
}
```

---

### **Certificados**

#### **GET** `/certificates`
Lista certificados.

**Comportamento:**
- **Admin**: Vê todos os certificados do sistema
- **Student**: Vê apenas seus próprios certificados

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "uuid": "12345678-1234-1234-1234-123456789012",
      "issue_date": "2025-09-16T20:18:38.000000Z",
      "hash_validation": "80bc75888c7898d353e3d96c880724d74ec4806799d174510bc1d3a986ed8bf9",
      "final_exam_score": {
        "score": 100,
        "correct_answers": 10,
        "total_questions": 10,
        "submitted_at": "2025-12-03T21:55:29.000000Z"
      },
      "course": {
        "id": 1,
        "title": "Laravel do Zero ao Avançado",
        "total_duration": 60,
        "modules_count": 10,
        "difficulty_level": "iniciante",
        "category": "tecnologia"
      },
      "created_at": "2025-09-16T20:18:38.000000Z",
      "updated_at": "2025-09-16T20:18:38.000000Z",
      "final_exam_score": {
        "score": 100,
        "correct_answers": 10,
        "total_questions": 10,
        "submitted_at": "2025-09-16T20:18:38.000000Z"
      }
    }
  ],
  "first_page_url": "http://localhost:8080/api/certificates?page=1",
  "from": 1,
  "last_page": 1,
  "last_page_url": "http://localhost:8080/api/certificates?page=1",
  "links": [...],
  "next_page_url": null,
  "path": "http://localhost:8080/api/certificates",
  "per_page": 15,
  "prev_page_url": null,
  "to": 1,
  "total": 1
}
```

#### **GET** `/certificates/{id}`
Retorna um certificado específico.

**Comportamento:**
- **Admin**: Pode ver qualquer certificado
- **Student**: Pode ver apenas seus próprios certificados

**Response (200):**
```json
{
  "id": 1,
  "uuid": "12345678-1234-1234-1234-123456789012",
  "issue_date": "2025-09-16T20:18:38.000000Z",
  "hash_validation": "80bc75888c7898d353e3d96c880724d74ec4806799d174510bc1d3a986ed8bf9",
  "final_exam_score": {
    "score": 100,
    "correct_answers": 10,
    "total_questions": 10,
    "submitted_at": "2025-12-03T21:55:29.000000Z"
  },
  "course": {
    "id": 1,
    "title": "Laravel do Zero ao Avançado",
    "total_duration": 60,
    "modules_count": 10,
    "difficulty_level": "iniciante",
    "category": "tecnologia"
  },
  "created_at": "2025-09-16T20:18:38.000000Z",
  "updated_at": "2025-09-16T20:18:38.000000Z"
}
```

---

#### **GET** `/certificates/course/{courseId}`
Retorna o certificado do usuário para um curso específico.

**Autorização**: Student (retorna apenas seus próprios certificados)

**Path Parameters**:
- `courseId` (integer): ID do curso

**Comportamento**:
- Retorna certificado diretamente sem necessidade de paginação
- Útil após completar um curso para acessar o certificado gerado
- Retorna 404 se o certificado não existir para aquele curso

**Response (200):**
```json
{
  "certificate": {
    "id": 1,
    "uuid": "12345678-1234-1234-1234-123456789012",
    "issue_date": "2025-12-03T15:30:00.000000Z",
    "hash_validation": "80bc75888c7898d353e3d96c880724d74ec4806799d174510bc1d3a986ed8bf9",
    "final_exam_score": {
      "score": 100,
      "correct_answers": 10,
      "total_questions": 10,
      "submitted_at": "2025-12-03T21:55:29.000000Z"
    },
    "course": {
      "id": 2,
      "title": "Laravel do Zero ao Avançado",
      "total_duration": 60,
      "modules_count": 10,
      "difficulty_level": "iniciante",
      "category": "tecnologia"
    },
    "created_at": "2025-12-03T15:30:00.000000Z",
    "updated_at": "2025-12-03T15:30:00.000000Z"
  }
}
```

**Response (404):**
```json
{
  "message": "Certificate not found for this course"
}
```

**Response (403):**
```json
{
  "message": "This action is unauthorized."
}
```

---

### **Progresso do Usuário**

#### **POST** `/activities/{activityId}/complete`
Marca uma atividade como concluída.

**Autorização**: Student (deve estar matriculado no curso)

**Path Parameters**:
- `activityId` (integer): ID da atividade

**Comportamento**:
- ✅ Cria registro em `user_activities` com `completed = true`
- ✅ Recalcula automaticamente o progresso do curso (0-100%)
- ✅ Se atingir 100%, marca curso como completo e gera certificado

**Response (200):**
```json
{
  "message": "Activity marked as completed",
  "activity": {
    "id": 1,
    "user_id": 5,
    "activity_id": 12,
    "completed": true,
    "completed_at": "2025-12-03T10:30:00.000000Z",
    "created_at": "2025-12-03T10:30:00.000000Z",
    "updated_at": "2025-12-03T10:30:00.000000Z"
  }
}
```

**Response (404):**
```json
{
  "message": "Activity not found"
}
```

**Response (403):**
```json
{
  "message": "You are not enrolled in this course"
}
```

---

#### **DELETE** `/activities/{activityId}/complete`
Remove a marcação de conclusão de uma atividade.

**Autorização**: Student (deve estar matriculado no curso)

**Path Parameters**:
- `activityId` (integer): ID da atividade

**Comportamento**:
- ✅ Remove registro de `user_activities`
- ✅ Recalcula automaticamente o progresso do curso
- ✅ Se estava completo e volta abaixo de 100%, remove data de conclusão

**Response (200):**
```json
{
  "message": "Activity marked as incomplete"
}
```

**Response (404):**
```json
{
  "message": "Activity not found"
}
```

**Response (403):**
```json
{
  "message": "You are not enrolled in this course"
}
```

---

#### **GET** `/courses/{courseId}/completed-activities`
Lista atividades concluídas pelo usuário em um curso.

**Autorização**: Student (deve estar matriculado no curso)

**Path Parameters**:
- `courseId` (integer): ID do curso

**Response (200):**
```json
{
  "completed_activities": [
    {
      "id": 1,
      "user_id": 5,
      "activity_id": 12,
      "completed": true,
      "completed_at": "2025-12-03T10:30:00.000000Z",
      "created_at": "2025-12-03T10:30:00.000000Z",
      "updated_at": "2025-12-03T10:30:00.000000Z",
      "activity": {
        "id": 12,
        "course_id": 2,
        "title": "Introdução ao Laravel",
        "order": 1,
        "activityable_type": "App\\Models\\VideoActivity",
        "activityable_id": 8
      }
    }
  ],
  "completed_activity_ids": [12, 15],
  "total_completed": 2
}
```

**Response (403):**
```json
{
  "message": "You are not enrolled in this course"
}
```

---

#### **GET** `/courses/{courseId}/progress`
Retorna progresso detalhado do usuário em um curso.

**Autorização**: Student (deve estar matriculado no curso)

**Path Parameters**:
- `courseId` (integer): ID do curso

**Response (200):**
```json
{
  "progress": 40,
  "total_activities": 10,
  "completed_activities": 4,
  "activities": [
    {
      "id": 12,
      "title": "Introdução ao Laravel",
      "order": 1,
      "completed": true
    },
    {
      "id": 13,
      "title": "Instalação e Configuração",
      "order": 2,
      "completed": true
    },
    {
      "id": 14,
      "title": "Conceitos Básicos",
      "order": 3,
      "completed": false
    },
    {
      "id": 15,
      "title": "Rotas e Controllers",
      "order": 4,
      "completed": true
    }
  ],
  "completed_at": null,
  "is_completed": false
}
```

**Response (403):**
```json
{
  "message": "You are not enrolled in this course"
}
```

---

### **Submissão de Quiz e Prova Final**

#### **POST** `/quiz/{quizId}/submit`
Submete respostas de um quiz.

**Autorização**: Student (deve estar matriculado no curso)

**Path Parameters**:
- `quizId` (integer): ID do Quiz (campo `activityable.id` da atividade)

**Request Body:**
```json
{
  "answers": [
    {
      "question_id": 1,
      "selected_option": "a"
    },
    {
      "question_id": 2,
      "selected_option": "b"
    },
    {
      "question_id": 3,
      "selected_option": "c"
    }
  ]
}
```

**Comportamento**:
- ✅ **Tentativas ilimitadas** - aluno pode refazer quantas vezes quiser
- ✅ **Sempre completa** - marca atividade como concluída independente da nota
- ✅ **Score informativo** - mostra quantas questões acertou
- ✅ **Cálculo automático** - compara respostas com gabarito

**Response (201):**
```json
{
  "message": "Quiz submitted successfully",
  "attempt": {
    "id": 1,
    "attempt_number": 1,
    "total_questions": 10,
    "correct_answers": 7,
    "score": 70.00,
    "passed": true,
    "submitted_at": "2025-12-03T15:30:00.000000Z"
  },
  "details": [
    {
      "question_id": 1,
      "selected_option": "a",
      "is_correct": true
    },
    {
      "question_id": 2,
      "selected_option": "b",
      "is_correct": false
    }
  ]
}
```

**Response (403):**
```json
{
  "message": "You are not enrolled in this course"
}
```

**Response (400):**
```json
{
  "message": "This quiz has no questions"
}
```

---

#### **POST** `/final_exam/{examId}/submit`
Submete respostas de uma prova final.

**Autorização**: Student (deve estar matriculado no curso)

**Path Parameters**:
- `examId` (integer): ID do Final Exam (campo `activityable.id` da atividade)

**Request Body:**
```json
{
  "answers": [
    {
      "question_id": 5,
      "selected_option": "c"
    },
    {
      "question_id": 6,
      "selected_option": "d"
    },
    {
      "question_id": 7,
      "selected_option": "a"
    }
  ]
}
```

**Comportamento**:
- ✅ **Tentativas limitadas** - 1 a 3 tentativas (configurável em `max_attempts`)
- ✅ **Nota de corte** - precisa atingir `passing_score` para passar (padrão: 70%)
- ✅ **Conclusão condicional** - só marca como concluída se passar
- ✅ **Bloqueia após limite** - não permite mais tentativas após esgotar o limite

**Response (201) - Passou:**
```json
{
  "message": "Final exam passed!",
  "attempt": {
    "id": 2,
    "attempt_number": 1,
    "total_questions": 15,
    "correct_answers": 12,
    "score": 80.00,
    "passing_score": 70.00,
    "passed": true,
    "attempts_remaining": 2,
    "submitted_at": "2025-12-03T16:45:00.000000Z"
  },
  "details": [
    {
      "question_id": 5,
      "selected_option": "c",
      "is_correct": true
    },
    {
      "question_id": 6,
      "selected_option": "d",
      "is_correct": false
    }
  ]
}
```

**Response (201) - Não Passou:**
```json
{
  "message": "Final exam not passed. Try again.",
  "attempt": {
    "id": 3,
    "attempt_number": 1,
    "total_questions": 15,
    "correct_answers": 8,
    "score": 53.33,
    "passing_score": 70.00,
    "passed": false,
    "attempts_remaining": 2,
    "submitted_at": "2025-12-03T16:45:00.000000Z"
  },
  "details": [
    {
      "question_id": 5,
      "selected_option": "a",
      "is_correct": false
    }
  ]
}
```

**Response (403) - Tentativas Esgotadas:**
```json
{
  "message": "You have reached the maximum number of attempts (3)"
}
```

**Response (403) - Não Matriculado:**
```json
{
  "message": "You are not enrolled in this course"
}
```

---

#### **GET** `/quiz/{quizId}/attempts`
Lista todas as tentativas do usuário em um quiz.

**Autorização**: Student

**Path Parameters**:
- `quizId` (integer): ID do Quiz

**Response (200):**
```json
{
  "attempts": [
    {
      "id": 3,
      "attempt_number": 3,
      "total_questions": 10,
      "correct_answers": 9,
      "score": 90.00,
      "submitted_at": "2025-12-03T17:00:00.000000Z"
    },
    {
      "id": 2,
      "attempt_number": 2,
      "total_questions": 10,
      "correct_answers": 7,
      "score": 70.00,
      "submitted_at": "2025-12-03T16:30:00.000000Z"
    },
    {
      "id": 1,
      "attempt_number": 1,
      "total_questions": 10,
      "correct_answers": 5,
      "score": 50.00,
      "submitted_at": "2025-12-03T16:00:00.000000Z"
    }
  ],
  "total_attempts": 3,
  "best_score": 90.00
}
```

---

#### **GET** `/final_exam/{examId}/attempts`
Lista todas as tentativas do usuário em uma prova final.

**Autorização**: Student

**Path Parameters**:
- `examId` (integer): ID do Final Exam

**Response (200):**
```json
{
  "attempts": [
    {
      "id": 2,
      "attempt_number": 2,
      "total_questions": 15,
      "correct_answers": 12,
      "score": 80.00,
      "passed": true,
      "submitted_at": "2025-12-03T17:30:00.000000Z"
    },
    {
      "id": 1,
      "attempt_number": 1,
      "total_questions": 15,
      "correct_answers": 9,
      "score": 60.00,
      "passed": false,
      "submitted_at": "2025-12-03T17:00:00.000000Z"
    }
  ],
  "total_attempts": 2,
  "max_attempts": 3,
  "attempts_remaining": 1,
  "passing_score": 70.00,
  "best_score": 80.00,
  "has_passed": true
}
```

---

### **Clientes Externos (External Customers)**

Sistema para sincronização de CPFs de uma base remota. Utilizado para identificar usuários que já eram clientes da empresa antes de se cadastrar na plataforma.

#### **POST** `/external-customers/bulk-sync`
Sincroniza CPFs em lote a partir de uma base remota. Esta rota será chamada por uma rotina automatizada (CRON) diariamente para adicionar novos CPFs cadastrados na base remota.

**Autorização**: Requer autenticação (recomendado para uso via CRON com token de API)

**Request Body:**
```json
{
  "cpfs": [
    "12345678901",
    "98765432100",
    "11122233344"
  ]
}
```

**Comportamento**:
- ✅ Adiciona apenas CPFs que não existem na tabela (evita duplicatas)
- ✅ Retorna estatísticas de sincronização (adicionados, ignorados, erros)
- ✅ Usa transação para garantir consistência
- ✅ Log de erros para debugging

**Response (200):**
```json
{
  "message": "CPFs synchronized successfully",
  "added": 2,
  "skipped": 1,
  "total_received": 3,
  "errors": []
}
```

**Response (422) - Validação:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "cpfs": ["The cpfs field is required."],
    "cpfs.0": ["The cpfs.0 field is required."]
  }
}
```

**Response (500) - Erro Interno:**
```json
{
  "message": "Failed to synchronize CPFs",
  "error": "Database error message"
}
```

---

#### **GET** `/external-customers`
Lista todos os CPFs cadastrados na tabela de clientes externos.

**Autorização**: Requer autenticação

**Query Parameters:**
- `per_page` (opcional): Número de itens por página (padrão: 15)

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "cpf": "12345678901"
    },
    {
      "id": 2,
      "cpf": "98765432100"
    }
  ],
  "current_page": 1,
  "last_page": 1,
  "per_page": 15,
  "total": 2
}
```

---

#### **POST** `/external-customers/check`
Verifica se um CPF específico existe na tabela de clientes externos.

**Autorização**: Requer autenticação

**Request Body:**
```json
{
  "cpf": "12345678901"
}
```

**Response (200):**
```json
{
  "cpf": "12345678901",
  "exists": true
}
```

**Response (422) - Validação:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "cpf": ["The cpf field is required."]
  }
}
```

---

## 📊 **Códigos de Status HTTP**

| Código | Significado |
|--------|-------------|
| **200** | Sucesso |
| **201** | Criado com sucesso |
| **400** | Dados inválidos |
| **401** | Não autenticado |
| **403** | Acesso negado |
| **404** | Recurso não encontrado |
| **409** | Conflito (ex: já matriculado) |
| **422** | Erro de validação |
| **500** | Erro interno do servidor |

---

## 🔧 **Exemplos de Uso**

### **1. Fluxo Completo de Login e Acesso**

```bash
# 1. Login como admin
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@curso-platform.com","password":"password"}'

# 2. Usar o token retornado para acessar rotas protegidas
curl -X GET http://localhost:8080/api/courses \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {token_aqui}"
```

### **2. Registro de Usuário com Novos Campos**

```bash
curl -X POST http://localhost:8080/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@exemplo.com",
    "password": "senha123",
    "password_confirmation": "senha123",
    "cpf": "12345678901",
    "phone": "(11) 99999-9999",
    "education_level": "superior_completo"
  }'
```

### **3. Atualizar Perfil do Usuário**

```bash
curl -X PUT http://localhost:8080/api/auth/profile \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token_aqui}" \
  -d '{
    "name": "João Silva Santos",
    "phone": "(11) 88888-8888",
    "education_level": "pos_graduacao"
  }'
```

### **4. Alterar Senha**

```bash
curl -X PUT http://localhost:8080/api/auth/password \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token_aqui}" \
  -d '{
    "current_password": "senha123",
    "password": "novasenha456",
    "password_confirmation": "novasenha456"
  }'
```

### **5. Criar um Curso**

```bash
curl -X POST http://localhost:8080/api/courses \
  -H "Authorization: Bearer {admin_token}" \
  -H "Accept: application/json" \
  -F "title=Laravel do Zero ao Avançado" \
  -F "description=Aprenda Laravel desde o básico até conceitos avançados, incluindo APIs RESTful, autenticação, testes e deploy." \
  -F "short_description=Curso completo de Laravel com projetos práticos e boas práticas de desenvolvimento." \
  -F "long_description=<h2>O que você vai aprender:</h2><ul><li>Fundamentos do Laravel</li><li>Eloquent ORM</li><li>APIs RESTful</li><li>Autenticação e Autorização</li><li>Testes Automatizados</li><li>Deploy em Produção</li></ul>" \
  -F "cover_image=@/caminho/para/capa.jpg" \
  -F "workload=60" \
  -F "modules_count=12" \
  -F "difficulty_level=avancado" \
  -F "category=programacao" \
  -F "price=299.90" \
  -F "promotional_price=199.90" \
  -F "discount_percentage=33" \
  -F "is_active=1"
```

### **6. Matricular-se em um Curso**

```bash
curl -X POST http://localhost:8080/api/courses/1/enroll \
  -H "Authorization: Bearer {student_token}"
```

### **7. Listar Cursos Públicos**

```bash
curl -X GET http://localhost:8080/api/public/courses -H "Accept: application/json"
```

### **8. Filtrar Cursos por Categoria e Dificuldade**

```bash
curl -X GET "http://localhost:8080/api/public/courses?category=programacao&difficulty=avancado" -H "Accept: application/json"
```

### **9. Buscar Cursos Gratuitos**

```bash
curl -X GET "http://localhost:8080/api/public/courses?is_free=true" -H "Accept: application/json"
```

### **10. Buscar Cursos por Palavra-chave**

```bash
curl -X GET "http://localhost:8080/api/public/courses?search=laravel" -H "Accept: application/json"
```

### **11. Ver Detalhes de um Curso Específico**

```bash
curl -X GET http://localhost:8080/api/public/courses/10 -H "Accept: application/json"
```

### **12. Listar Categorias Disponíveis**

```bash
curl -X GET http://localhost:8080/api/public/courses/categories -H "Accept: application/json"
```

### **13. Testar Validação de Certificado**

```bash
curl -X GET http://localhost:8080/api/certificates/validate/e74eac06-d6af-4f4e-8b76-602257c199ab
```

### **14. Criar Atividade de Vídeo**

```bash
curl -X POST http://localhost:8080/api/courses/1/activities \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "course_id": 1,
    "type": "video",
    "title": "Introdução ao Laravel",
    "order": 1,
    "video": {
      "description": "Neste vídeo você aprenderá os fundamentos do Laravel",
      "transcript": "Olá, bem-vindos ao curso de Laravel...",
      "link": "https://youtube.com/watch?v=abc123"
    }
  }'
```

### **15. Criar Atividade de Quiz**

```bash
curl -X POST http://localhost:8080/api/courses/1/activities \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "course_id": 1,
    "type": "quiz",
    "title": "Quiz do Módulo 1",
    "quiz": {
      "description": "Teste seus conhecimentos",
      "duration_minutes": 15
    }
  }'
```

### **16. Criar Prova Final**

```bash
curl -X POST http://localhost:8080/api/courses/1/activities \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "course_id": 1,
    "type": "final_exam",
    "title": "Prova Final do Curso",
    "final_exam": {
      "description": "Avaliação final obrigatória",
      "max_attempts": 3,
      "duration_minutes": 60
    }
  }'
```

### **17. Adicionar Questão ao Quiz**

```bash
curl -X POST http://localhost:8080/api/quiz/1/questions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "questions": [
      {
        "statement": "Qual é a capital do Brasil?",
        "option_a": "Rio de Janeiro",
        "option_b": "Brasília",
        "option_c": "São Paulo",
        "option_d": "Salvador",
        "correct_option": "b"
      }
    ]
  }'
```

### **18. Listar Questões de uma Prova Final**

```bash
curl -X GET http://localhost:8080/api/final_exam/1/questions \
  -H "Authorization: Bearer {admin_token}"
```

### **19. Reordenar Atividades de um Curso**

```bash
curl -X PUT http://localhost:8080/api/courses/1/activities/reorder \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "activities": [
      {"id": 3, "order": 1},
      {"id": 1, "order": 2},
      {"id": 2, "order": 3}
    ]
  }'
```

### **20. Reordenar Questões de um Quiz**

```bash
curl -X PUT http://localhost:8080/api/quiz/1/questions/reorder \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "questions": [
      {"id": 5, "order": 1},
      {"id": 3, "order": 2},
      {"id": 1, "order": 3}
    ]
  }'
```

### **21. Marcar Atividade como Concluída**

```bash
curl -X POST http://localhost:8080/api/activities/12/complete \
  -H "Authorization: Bearer {student_token}" \
  -H "Content-Type: application/json"
```

### **22. Desmarcar Atividade (Incompleta)**

```bash
curl -X DELETE http://localhost:8080/api/activities/12/complete \
  -H "Authorization: Bearer {student_token}" \
  -H "Content-Type: application/json"
```

### **23. Listar Atividades Concluídas de um Curso**

```bash
curl -X GET http://localhost:8080/api/courses/2/completed-activities \
  -H "Authorization: Bearer {student_token}" \
  -H "Accept: application/json"
```

### **24. Ver Progresso Detalhado do Curso**

```bash
curl -X GET http://localhost:8080/api/courses/2/progress \
  -H "Authorization: Bearer {student_token}" \
  -H "Accept: application/json"
```

### **25. Submeter Respostas de um Quiz**

```bash
curl -X POST http://localhost:8080/api/quiz/1/submit \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {student_token}" \
  -d '{
    "answers": [
      {"question_id": 1, "selected_option": "a"},
      {"question_id": 2, "selected_option": "b"},
      {"question_id": 3, "selected_option": "c"}
    ]
  }'
```

### **26. Submeter Respostas da Prova Final**

```bash
curl -X POST http://localhost:8080/api/final_exam/1/submit \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {student_token}" \
  -d '{
    "answers": [
      {"question_id": 5, "selected_option": "c"},
      {"question_id": 6, "selected_option": "d"},
      {"question_id": 7, "selected_option": "a"}
    ]
  }'
```

### **27. Ver Histórico de Tentativas de um Quiz**

```bash
curl -X GET http://localhost:8080/api/quiz/1/attempts \
  -H "Authorization: Bearer {student_token}" \
  -H "Accept: application/json"
```

### **28. Ver Histórico de Tentativas da Prova Final**

```bash
curl -X GET http://localhost:8080/api/final_exam/1/attempts \
  -H "Authorization: Bearer {student_token}" \
  -H "Accept: application/json"
```

### **29. Sincronizar CPFs em Lote (CRON)**

```bash
curl -X POST http://localhost:8080/api/external-customers/bulk-sync \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {api_token}" \
  -d '{
    "cpfs": [
      "12345678901",
      "98765432100",
      "11122233344"
    ]
  }'
```

### **30. Listar Clientes Externos**

```bash
curl -X GET http://localhost:8080/api/external-customers \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### **31. Verificar se CPF Existe em Clientes Externos**

```bash
curl -X POST http://localhost:8080/api/external-customers/check \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "cpf": "12345678901"
  }'
```

---

## 🚀 **Recursos Avançados**

### **1. Geração Automática de Certificados**
- Certificados são gerados automaticamente quando um curso é completado
- Sistema de eventos e listeners implementado
- Validação via UUID público

### **2. Estrutura Polimórfica de Atividades**
Sistema completo com 6 tipos de atividades:
- **VideoActivity**: Vídeos externos com link, descrição e transcrição
- **ArticleActivity**: Artigos com conteúdo RichText/HTML
- **EmbedContentActivity**: Conteúdo incorporado (Genially) via iframe/script
- **SupportMaterialActivity**: Material de apoio com conteúdo RichText
- **QuizActivity**: Quizzes com questões de múltipla escolha
- **FinalExamActivity**: Prova final obrigatória (única por curso, sempre última)

### **3. Sistema de Questões Polimórfico**
- Questões reutilizáveis entre Quiz e Prova Final
- Estrutura: enunciado + 4 alternativas (a, b, c, d)
- Indicação de alternativa correta
- Ordenação personalizável
- Relacionamento polimórfico com `questionable_type` e `questionable_id`

### **4. Validações Inteligentes**
- **Prova Final**: Apenas uma por curso, sempre como última atividade
- **Max Attempts**: Limite de 1 a 3 tentativas na Prova Final
- **Ordem Automática**: Sugestão automática de próxima ordem disponível
- **Reordenação**: Sistema mantém Prova Final sempre no final

### **5. Sistema de Progresso Automático**
- **Rastreamento Individual**: Cada atividade concluída é registrada em `user_activities`
- **Cálculo Automático**: Progresso calculado como `(atividades_concluídas / total_atividades) * 100`
- **Auto-Conclusão**: Ao atingir 100%, curso é marcado como completo automaticamente
- **Certificado Automático**: Gerado automaticamente na conclusão do curso
- **Endpoints de Progresso**:
  - `POST /activities/{activityId}/complete`: Marca atividade como concluída
  - `DELETE /activities/{activityId}/complete`: Desmarca atividade
  - `GET /courses/{courseId}/completed-activities`: Lista atividades concluídas
  - `GET /courses/{courseId}/progress`: Progresso detalhado com lista de atividades
- **Campos Calculados Automaticamente**:
  - `user_courses.progress`: Atualizado ao completar/descompletar atividades
  - `user_courses.completed_at`: Preenchido automaticamente ao atingir 100%
  - `courses.total_duration`: Soma das durações de todas as atividades
  - `courses.modules_count`: Contagem automática de atividades do curso

### **6. Sistema de Quiz e Prova Final**
- **Submissão em Lote**: Envio de todas as respostas de uma vez
- **Validação Automática**: Comparação com gabarito e cálculo de score
- **Diferenciação**:
  - **Quiz**: Tentativas ilimitadas, sempre completa, score informativo
  - **Final Exam**: Tentativas limitadas (1-3), nota de corte (padrão 70%), conclusão condicional
- **Endpoints de Submissão**:
  - `POST /quiz/{id}/submit`: Submete respostas do quiz
  - `POST /final_exam/{id}/submit`: Submete respostas da prova
  - `GET /quiz/{id}/attempts`: Histórico de tentativas do quiz
  - `GET /final_exam/{id}/attempts`: Histórico de tentativas da prova
- **Histórico Completo**: Armazena todas as tentativas com respostas, score e timestamp
- **Controle Inteligente**: Bloqueia prova após esgotar tentativas, integra com progresso automático

### **7. Validação Robusta**
- Form Requests específicos para cada tipo de atividade
- Validação customizada para questões
- Policies para autorização granular
- Middlewares para controle de acesso

---

## 📝 **Notas Importantes**

1. **Tokens**: Os tokens Sanctum expiram conforme `config/sanctum.php` (`expiration`, em minutos; o padrão do projeto é **1440** minutos = 24 horas, salvo alteração em `.env`/config)
2. **Paginação**: Todas as listagens suportam paginação (15 itens por página)
3. **Soft Delete**: Cursos são removidos com soft delete
4. **Validação de CPF**: Informe **11 dígitos** (recomendado sem pontuação). O cadastro via API valida unicidade do valor enviado; formatações diferentes do mesmo número podem ser tratadas como CPFs distintos.
5. **Eligibility**: Sistema preparado para verificação de elegibilidade por CPF
6. **Progresso Automático**: O progresso do curso é calculado automaticamente ao completar atividades. Não é necessário atualizar manualmente o campo `progress`

---

## 🐛 **Tratamento de Erros**

### **Erro de Validação (422)**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```
(O objeto `errors` lista os campos com falha de validação.)

### **Erro de Autorização (403)**
```json
{
  "message": "Access denied. Admin privileges required."
}
```

### **Erro de Autenticação (401)**
```json
{
  "message": "Unauthenticated."
}
```

---

## 📋 **Campos de Usuário**

### **Campos Obrigatórios**
- `name` - Nome completo do usuário
- `email` - Email único do usuário
- `password` - Senha (mínimo 8 caracteres)
- `cpf` - CPF único do usuário (não pode ser alterado)

### **Campos Opcionais**
- `phone` - Telefone do usuário (máximo 20 caracteres)
- `education_level` - Nível de escolaridade

### **Níveis de Escolaridade**
| Índice | Valor | Label |
|--------|-------|-------|
| 1 | `fundamental` | Ensino Fundamental |
| 2 | `medio_incompleto` | Ensino Médio Incompleto |
| 3 | `medio_completo` | Ensino Médio Completo |
| 4 | `superior_incompleto` | Ensino Superior Incompleto |
| 5 | `superior_completo` | Ensino Superior Completo |
| 6 | `pos_graduacao` | Pós-graduação |

### **Campos Atualizáveis**
- ✅ `name` - Pode ser atualizado
- ✅ `email` - Pode ser atualizado (deve ser único)
- ✅ `phone` - Pode ser atualizado
- ✅ `education_level` - Pode ser atualizado
- ❌ `cpf` - **NÃO pode ser atualizado** (por segurança)
- ❌ `type` - **NÃO pode ser atualizado** (admin/student)
- ❌ `is_customer` - **NÃO pode ser atualizado** (definido apenas no registro)

### **Campo `is_customer`**
- Definido automaticamente durante o registro
- `true`: CPF existe na tabela `external_customers` (usuário já era cliente antes do cadastro)
- `false`: CPF não existe na tabela `external_customers` (novo cliente)
- Não pode ser alterado manualmente após o registro

---

---

## 📊 **Resumo da API**

### **Rotas Disponíveis:**

| Categoria | Quantidade | Exemplos |
|-----------|------------|----------|
| **Públicas** | 7 | Login, Registro, Cursos Públicos, Validação de Certificado |
| **Autenticadas** | 33 | Rotas unificadas com autorização via Policies |
| **Auth** | 5 | Logout, Dados do Usuário, Atualizar Perfil, Alterar Senha, Deletar Conta |
| **External Customers** | 3 | Sincronização em lote, Listagem, Verificação de CPF |
| **Total** | **45 rotas** | **Todas documentadas** |

**Distribuição das Rotas Autenticadas:**
- Cursos: 7 rotas (index, enrolled, show, store, update, destroy, enroll)
- Atividades: 6 rotas (index, show, store, update, destroy, reorder)
- Questões: 6 rotas (index, show, store, update, destroy, reorder)
- Progresso: 4 rotas (complete, incomplete, completed-activities, progress)
- Quiz/Exam: 4 rotas (submit quiz, submit exam, quiz attempts, exam attempts)
- Certificados: 2 rotas (index, show)
- External Customers: 3 rotas (bulk-sync, index, check)
- Validação de Certificado Pública: 2 rotas

### **Tipos de Atividades:**

| Tipo | Descrição | Campos Principais |
|------|-----------|-------------------|
| **video** | Vídeos externos | link, description, transcript |
| **article** | Artigos RichText | content_richtext, description |
| **embed_content** | Genially/Iframe | embed_code, description |
| **support_material** | Material de apoio | content_richtext, description |
| **quiz** | Quiz com questões | duration_minutes, description + questões |
| **final_exam** | Prova final | max_attempts, duration_minutes, passing_score, description + questões |

### **Recursos Implementados:**

- ✅ **Autenticação** com Laravel Sanctum
- ✅ **Autorização Unificada** via Laravel Policies
- ✅ **API Resources** para serialização contextual
- ✅ **Cursos** com campos completos (preços, imagens, categorias, dificuldades)
- ✅ **6 Tipos de Atividades** com estrutura polimórfica completa
- ✅ **Sistema de Questões** polimórfico para Quiz e Prova Final
- ✅ **Ocultação de Respostas** questões sem `correct_option` para estudantes
- ✅ **Prova Final** obrigatória (única por curso, sempre última)
- ✅ **Reordenação** de atividades e questões
- ✅ **Reordenação Automática (Shift)** de atividades ao alterar ordem individual
- ✅ **Criação em Lote** de questões (múltiplas questões em uma requisição)
- ✅ **Validações Inteligentes** (ordem automática, limites de tentativas)
- ✅ **Certificados** com validação por UUID e hash
- ✅ **Rotas Públicas** para vitrine de cursos
- ✅ **Filtros** por categoria, dificuldade, preço e busca
- ✅ **Paginação** em todas as listagens
- ✅ **Validação** robusta com mensagens em português
- ✅ **Form Requests** específicos para cada tipo de atividade
- ✅ **Arquitetura Escalável** sem duplicação de código
- ✅ **Sistema de Progresso Automático** com rastreamento individual de atividades
- ✅ **Cálculo Automático** de progresso baseado em atividades concluídas
- ✅ **Auto-Conclusão** de cursos e geração automática de certificados
- ✅ **Campos Calculados** automaticamente via Observers
- ✅ **Sistema de Submissão de Quiz** com tentativas ilimitadas e score informativo
- ✅ **Sistema de Prova Final** com limite de tentativas e nota de corte
- ✅ **Validação Automática** de respostas contra gabarito
- ✅ **Histórico de Tentativas** para Quiz e Final Exam
- ✅ **Conclusão Condicional** (prova só completa se passar)

---

**Documentação atualizada em**: 2025-12-03  
**Versão da API**: 3.3.0  
**Laravel**: 12.x  
**PHP**: 8.2+

---

## **Changelog**

### **v3.4.0 - Sistema de Clientes Externos e Registro via Google**

**Novas Funcionalidades:**
- ✨ **Sistema de Clientes Externos (External Customers)**:
  - Nova tabela `external_customers` para espelhar CPFs de base remota
  - Sincronização em lote via CRON diário
  - Prevenção de duplicatas automática
  - Verificação de existência de CPF
  
- ✨ **Flag `is_customer` em Usuários**:
  - Nova coluna `is_customer` na tabela `users`
  - Definida automaticamente durante o registro
  - `true` se CPF existe em `external_customers`, `false` caso contrário
  - Identifica usuários que já eram clientes antes do cadastro na plataforma

- ✨ **Registro via Google**:
  - Suporte a registro sem senha usando `google_id`
  - Validação condicional: `password` obrigatório apenas se não houver `google_id`
  - `cpf` permanece obrigatório em ambos os cenários
  - Campo `password` agora é nullable na tabela `users`

- ✨ **3 Novos Endpoints**:
  - `POST /api/external-customers/bulk-sync` - Sincronização em lote de CPFs
  - `GET /api/external-customers` - Listar todos os CPFs cadastrados
  - `POST /api/external-customers/check` - Verificar se CPF existe

**Melhorias:**
- ⚡ **Sincronização Automática**: Rotina CRON pode chamar endpoint diariamente
- ⚡ **Prevenção de Duplicatas**: Sistema ignora CPFs já existentes
- ⚡ **Transações**: Garantia de consistência na sincronização em lote
- ⚡ **Logs**: Registro de erros para debugging

**Estrutura de Dados:**
- Tabela `external_customers`: `id`, `cpf` (unique)
- Coluna `users.is_customer`: boolean (default: false)
- Coluna `users.password`: nullable (para usuários Google)
- Coluna `users.google_id`: adicionada ao fillable

---

### **v3.3.0 - Sistema de Submissão de Quiz e Prova Final**

**Novas Funcionalidades:**
- ✨ **Sistema de Tentativas para Quiz e Final Exam**:
  - Nova tabela `quiz_attempts` para armazenar tentativas
  - Relacionamento polimórfico com QuizActivity e FinalExamActivity
  - Armazena: respostas, score, se passou, número da tentativa
  
- ✨ **Nota de Corte para Prova Final**:
  - Novo campo `passing_score` em `final_exam_activities` (padrão: 70%)
  - Prova só é marcada como concluída se atingir nota mínima
  - Configurável por prova (0-100)

- ✨ **4 Novos Endpoints**:
  - `POST /api/quiz/{id}/submit` - Submeter respostas do quiz
  - `POST /api/final_exam/{id}/submit` - Submeter respostas da prova
  - `GET /api/quiz/{id}/attempts` - Ver tentativas do quiz
  - `GET /api/final_exam/{id}/attempts` - Ver tentativas da prova

- ✨ **Diferenciação Quiz vs Final Exam**:
  - **Quiz**: Tentativas ilimitadas, sempre completa, score informativo
  - **Final Exam**: Tentativas limitadas (1-3), nota de corte, conclusão condicional

- ✨ **Submissão em Lote**:
  - Envia todas as respostas de uma vez
  - Validação automática contra gabarito
  - Retorna detalhes de cada questão (correta/incorreta)

**Melhorias:**
- ⚡ **Cálculo Automático de Score**: Sistema compara respostas com gabarito
- ⚡ **Controle de Tentativas**: Bloqueia após esgotar limite de tentativas
- ⚡ **Histórico Completo**: Permite visualizar todas as tentativas anteriores
- ⚡ **Integração com Progresso**: Quiz/Exam concluído marca atividade como completa automaticamente

**Validações:**
- Verifica se usuário está matriculado no curso
- Valida limite de tentativas da prova final
- Retorna detalhes de acertos/erros (útil para feedback)

---

### **v3.2.0 - Sistema de Progresso Automático**

**Novas Funcionalidades:**
- ✨ **Sistema de Rastreamento de Progresso**: Acompanhamento individual de conclusão de atividades
  - Nova tabela `user_activities` para registrar atividades concluídas por usuário
  - Relacionamento many-to-many entre usuários e atividades
  - Campos: `user_id`, `activity_id`, `completed`, `completed_at`

- ✨ **4 Novos Endpoints de Progresso**:
  - `POST /api/activities/{activityId}/complete` - Marca atividade como concluída
  - `DELETE /api/activities/{activityId}/complete` - Desmarca atividade
  - `GET /api/courses/{courseId}/completed-activities` - Lista atividades concluídas
  - `GET /api/courses/{courseId}/progress` - Progresso detalhado do curso com lista de atividades

- ✨ **Cálculo Automático de Progresso**: 
  - Progresso calculado automaticamente: `(atividades_concluídas / total_atividades) * 100`
  - Campo `user_courses.progress` atualizado via Observer
  - Atualização em tempo real ao completar/descompletar atividades

- ✨ **Auto-Conclusão de Curso**:
  - Ao atingir 100%, curso é marcado automaticamente como completo
  - Campo `user_courses.completed_at` preenchido automaticamente
  - Evento `CourseCompleted` disparado
  - Certificado gerado automaticamente

- ✨ **Campos Calculados Automaticamente**:
  - `courses.total_duration` - Soma das durações de todas as atividades
  - `courses.modules_count` - Contagem automática de atividades
  - Atualizados via Observers ao criar/editar/deletar atividades

**Melhorias:**
- ⚡ **Progresso Real**: Baseado em atividades realmente concluídas, não mais manual
- ⚡ **Automação Completa**: Observers mantêm tudo sincronizado sem intervenção
- ⚡ **Granularidade**: Sistema sabe exatamente quais atividades foram concluídas
- ⚡ **Reversível**: Possibilidade de desmarcar atividades e recalcular progresso

**Observers Implementados:**
- `UserActivityObserver` - Atualiza progresso ao completar/descompletar atividades
- `ActivityObserver` - Atualiza `total_duration` e `modules_count` do curso
- `VideoActivityObserver` - Atualiza `total_duration` quando duração de vídeo muda

---

### **v3.1.0 - Melhorias de Usabilidade**

**Novas Funcionalidades:**
- ✨ **Criação de Múltiplas Questões**: Endpoint `POST /{type}/{id}/questions` agora aceita array de questões
  - Permite criar uma ou várias questões em uma única requisição
  - Formato unificado: `{ "questions": [...] }`
  - Ordem automática calculada para cada questão
  
- ✨ **Reordenação Automática de Atividades**: Sistema de shift inteligente
  - Ao alterar `order` de uma atividade via `PUT /activities/{id}`, outras atividades são reposicionadas automaticamente
  - Mover para cima: incrementa order das atividades intermediárias
  - Mover para baixo: decrementa order das atividades intermediárias
  - Elimina conflitos de ordem duplicada

**Melhorias:**
- ⚡ **Melhor DX**: Facilita implementação no frontend
- ⚡ **Menos Requisições**: Criar várias questões de uma vez
- ⚡ **Integridade Automática**: Ordem sempre consistente

---

### **v3.0.0 - Refatoração para Arquitetura Unificada**

**Mudanças Críticas (Breaking Changes):**
- 🔄 **Rotas Unificadas**: Removidos prefixos `/admin/` e `/student/`
  - Antes: `GET /admin/courses` e `GET /student/courses`
  - Agora: `GET /courses` (comportamento baseado em quem está autenticado)

**Novas Funcionalidades:**
- ✨ **Laravel Policies**: Autorização centralizada e escalável
- ✨ **API Resources**: Serialização contextual (admin vê tudo, student vê só o necessário)
- ✨ **QuestionResource**: Oculta `correct_option` de questões para estudantes
- ✨ **Rotas Simplificadas**: Uma rota, múltiplos comportamentos

**Melhorias:**
- ⚡ **Código 50% Menor**: Eliminação de duplicação
- ⚡ **Manutenção Fácil**: Um controller por recurso
- ⚡ **Escalável**: Fácil adicionar novos tipos de usuários

**Migração:**
- Atualizar todas as chamadas removendo `/admin/` ou `/student/`
- Exemplo: `POST /admin/courses` → `POST /courses`
- Autorização agora é automática baseada no token

---

### **v2.0.0 - Sistema Completo de Atividades**

### **Atividades - Sistema Completo**
- ✨ Adicionados 3 novos tipos: `embed_content`, `support_material`, `final_exam`
- ✨ Reestruturados tipos existentes: `video`, `article`, `quiz`
- ✨ Sistema de criação unificada via `/courses/{courseId}/activities`
- ✨ Campos específicos para cada tipo (description, transcript, embed_code, etc.)
- ✨ Ordem automática sugerida para novas atividades
- ✨ Endpoint de reordenação `/courses/{courseId}/activities/reorder`

### **Sistema de Questões**
- ✨ Nova tabela `questions` com relacionamento polimórfico
- ✨ CRUD completo de questões para Quiz e Prova Final
- ✨ Estrutura: enunciado + 4 alternativas (a, b, c, d)
- ✨ Indicação de alternativa correta
- ✨ Ordenação e reordenação de questões
- ✨ Endpoints: `/{type}/{id}/questions`

### **Prova Final**
- ✨ Tipo especial `final_exam` obrigatório em todos os cursos
- ✨ Validação: apenas UMA por curso
- ✨ Sempre criada como ÚLTIMA atividade
- ✨ Limite de tentativas configurável (1 a 3)
- ✨ Duração obrigatória em minutos
- ✨ Reordenação automática para manter como última

### **Validações e Melhorias**
- ✨ Form Requests específicos para cada tipo de atividade
- ✨ Validação de prova final duplicada
- ✨ Validação de ordem da prova final
- ✨ Mensagens de erro em português
- ✨ Tratamento de erros com DB transactions

