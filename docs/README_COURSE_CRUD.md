# Passo a passo: criar um curso completo via API

Este guia descreve a ordem das chamadas HTTP para montar um curso do zero, com **JSONs de exemplo** (placeholders). Substitua `BASE_URL`, `TOKEN` e os IDs retornados pelas respostas reais.

**Pré-requisitos**

- Usuário **admin** (apenas admin cria cursos e atividades).
- `ActivityTypeSeeder` executado (tabela `activity_types` com `video`, `article`, `quiz`, `embed_content`, `support_material`, `final_exam`, etc.).
- Prefixo da API: `/api` (ex.: `BASE_URL=http://localhost:8080/api`).

**Variáveis úteis (bash)**

```bash
export BASE_URL="http://localhost:8080/api"
export TOKEN="cole_o_token_aqui"
```

**Importante:** nas rotas de questões, **`{id}`** é o ID do registro **`quiz_activities`** ou **`final_exam_activities`**, **não** o ID da linha em `activities`. Esse ID aparece no objeto retornado em `activity.quiz` ou `activity.final_exam` após criar a atividade.

**Tipos de atividade suportados pela API** (`POST /courses/{courseId}/activities`):  
`video`, `article`, `embed_content`, `support_material`, `quiz`, `final_exam`.  
O tipo `mini_game` existe no banco, mas **não** está habilitado na validação deste endpoint.

---

## 0. Obter token (admin)

```bash
curl -s -X POST "$BASE_URL/auth/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@curso-platform.com",
    "password": "password"
  }'
```

Copie o `token` da resposta para `TOKEN`.

---

## 1. Criar o curso

`POST /courses`

**Content-Type:** `multipart/form-data`

**Campos (placeholders):**

- `title`
- `description`
- `short_description` (opcional)
- `long_description` (opcional)
- `cover_image` (**arquivo de imagem obrigatório**)
- `workload`
- `modules_count` (opcional)
- `difficulty_level` (opcional)
- `category` (opcional)
- `price`
- `promotional_price` (opcional)
- `discount_percentage` (opcional)
- `is_active` (opcional)

**Exemplo curl:**

```bash
curl -s -X POST "$BASE_URL/courses" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" \
  -F "title=Curso API Placeholder — Laravel do Zero ao Avançado" \
  -F "description=Descrição longa do curso para listagens e detalhes. Texto de exemplo com conteúdo fictício para demonstração." \
  -F "short_description=Curso completo de Laravel com projetos práticos e boas práticas de desenvolvimento." \
  -F "long_description=<h2>O que você vai aprender</h2><ul><li>Fundamentos do Laravel</li><li>Eloquent ORM</li><li>APIs RESTful</li></ul>" \
  -F "cover_image=@/caminho/para/capa.jpg" \
  -F "workload=60" \
  -F "modules_count=0" \
  -F "difficulty_level=avancado" \
  -F "category=programacao" \
  -F "price=299.9" \
  -F "promotional_price=199.9" \
  -F "discount_percentage=33" \
  -F "is_active=1"
```

**Guardar:** `id` do curso → `COURSE_ID` (ex.: `10`).

---

## 2. Aula em vídeo (atividade 1)

`POST /courses/{COURSE_ID}/activities`

```json
{
  "type": "video",
  "title": "Introdução ao Laravel",
  "order": 1,
  "video": {
    "description": "Neste vídeo você verá os objetivos do curso e a estrutura do projeto.",
    "transcript": "Olá, bem-vindos ao curso de Laravel. Neste vídeo apresentamos o cronograma.",
    "link": "https://player.vimeo.com/video/1137782131", // URL para o vídeo incorporado
    "duration": 1800
  }
}
```

- `duration`: duração do vídeo em **segundos** (ex.: `1800` = 30 minutos). Opcional na validação, mas recomendado para o cálculo de `total_duration` do curso.

---

## 3. Artigo (atividade 2)

```json
{
  "type": "article",
  "title": "Conceitos de rotas e controllers",
  "order": 2,
  "article": {
    "description": "Leitura rápida sobre rotas HTTP.",
    "content_richtext": "<p>Este é um <strong>artigo de exemplo</strong> com HTML.</p><p>Parágrafo com conteúdo placeholder.</p>"
  }
}
```

---

## 4. Conteúdo incorporado (Genially / iframe) — atividade 3

```json
{
  "type": "embed_content",
  "title": "Conteúdo interativo (embed)",
  "order": 3,
  "embed_content": {
    "description": "Apresentação interativa de exemplo.",
    "embed_code": "<iframe src=\"https://example.com/embed/placeholder\" width=\"100%\" height=\"480\" frameborder=\"0\" allowfullscreen></iframe>"
  }
}
```

---

## 5. Material de apoio — atividade 4

```json
{
  "type": "support_material",
  "title": "Apostila PDF e links",
  "order": 4,
  "support_material": {
    "description": "Material complementar em texto.",
    "content_richtext": "<h3>Resumo</h3><ul><li>Item A</li><li>Item B</li></ul><p>Texto placeholder.</p>"
  }
}
```

---

## 6. Quiz — atividade 5

```json
{
  "type": "quiz",
  "title": "Quiz — Módulo fundamentos",
  "order": 5,
  "quiz": {
    "description": "Questionário rápido sobre o conteúdo visto até aqui.",
    "duration_minutes": 15,
    "passing_score": 60.0
  }
}
```

**Guardar:** na resposta, o ID do modelo quiz (ex.: `activity.quiz.id`) → `QUIZ_ACTIVITY_ID`.

---

## 7. Prova final — última atividade (obrigatória)

Só pode existir **uma** prova final por curso. O backend força a ordem para ser a última; mesmo que envie `order`, ela será posicionada como última atividade.

```json
{
  "type": "final_exam",
  "title": "Prova final — certificação",
  "order": 99,
  "final_exam": {
    "description": "Prova final com nota mínima para aprovação.",
    "max_attempts": 3,
    "duration_minutes": 60,
    "passing_score": 70.0
  }
}
```

**Guardar:** `activity.final_exam.id` → `FINAL_EXAM_ACTIVITY_ID`.

---

## 8. Questões do quiz

`POST /quiz/{QUIZ_ACTIVITY_ID}/questions`

```json
{
  "questions": [
    {
      "statement": "Qual comando Artisan cria um controller em Laravel?",
      "option_a": "php artisan make:controller NomeController",
      "option_b": "php artisan create:controller NomeController",
      "option_c": "php artisan new:controller NomeController",
      "option_d": "laravel create controller NomeController",
      "correct_option": "a",
      "order": 1
    },
    {
      "statement": "O que é Eloquent no Laravel?",
      "option_a": "Servidor web embutido",
      "option_b": "ORM para interagir com o banco de dados",
      "option_c": "Ferramenta apenas para testes",
      "option_d": "Template engine padrão",
      "correct_option": "b",
      "order": 2
    }
  ]
}
```

**Exemplo curl:**

```bash
curl -s -X POST "$BASE_URL/quiz/$QUIZ_ACTIVITY_ID/questions" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{ "questions": [ ... ] }'
```

---

## 9. Questões da prova final

`POST /final_exam/{FINAL_EXAM_ACTIVITY_ID}/questions`

Use o mesmo formato de array `questions` do passo anterior (enunciado + quatro alternativas + `correct_option`).

```json
{
  "questions": [
    {
      "statement": "Explique em uma linha o que é um Service Provider no Laravel (questão de exemplo).",
      "option_a": "Classe que registra serviços e bindings no container",
      "option_b": "Arquivo apenas para rotas",
      "option_c": "Comando para migrar banco",
      "option_d": "Tipo de middleware",
      "correct_option": "a",
      "order": 1
    }
  ]
}
```

---

## 10. (Opcional) Reordenar atividades

`PUT /courses/{COURSE_ID}/activities/reorder`

Envie todos os pares `id` (ID da tabela **`activities`**) + `order` desejado. A prova final continua sendo ajustada para o final se necessário.

```json
{
  "activities": [
    { "id": 101, "order": 1 },
    { "id": 102, "order": 2 },
    { "id": 103, "order": 3 }
  ]
}
```

---

## 11. Conferir o curso

- `GET /courses/{COURSE_ID}` (autenticado) — curso completo com atividades.
- `GET /public/courses/{COURSE_ID}` (público) — apenas se `is_active: true` e curso ativo.

---

## Ordem resumida

| # | Método | Endpoint | Descrição |
|---|--------|----------|-----------|
| 0 | POST | `/auth/login` | Token admin |
| 1 | POST | `/courses` | Criar curso |
| 2 | POST | `/courses/{id}/activities` | Vídeo |
| 3 | POST | `/courses/{id}/activities` | Artigo |
| 4 | POST | `/courses/{id}/activities` | Embed |
| 5 | POST | `/courses/{id}/activities` | Material de apoio |
| 6 | POST | `/courses/{id}/activities` | Quiz |
| 7 | POST | `/courses/{id}/activities` | Prova final (última) |
| 8 | POST | `/quiz/{quizActivityId}/questions` | Questões do quiz |
| 9 | POST | `/final_exam/{finalExamActivityId}/questions` | Questões da prova final |
| 10 | PUT | `/courses/{id}/activities/reorder` | Opcional |

---

## Referência

- Documentação geral dos endpoints: `README_API.md` na raiz do projeto.
- Ajustes de preço, promoção e duração total são recalculados conforme observers ao criar/editar atividades e vídeos.
