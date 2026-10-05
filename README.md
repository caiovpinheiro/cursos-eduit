# Plataforma de Cursos — Deploy

Aplicação **Laravel 12** (PHP 8.2) com interface web (Livewire), API REST (Sanctum), MySQL e Redis. Em produção o container usa **Nginx + PHP-FPM** sob **Supervisor** (veja `Dockerfile` e `docker/`).

---

## Requisitos

- **Docker** e **Docker Compose** (v2), ou
- **PHP 8.2+**, **Composer**, **MySQL 8**, **Redis**, servidor web compatível com PHP-FPM (deploy manual não detalhado aqui; o fluxo abaixo foca em Docker).

---

## Deploy com Docker Compose (recomendado)

### 1. Clonar e entrar no diretório

```bash
git clone <url-do-repositório> curso-platform-api
cd curso-platform-api
```

### 2. Variáveis de ambiente

```bash
cp .env.example .env
```

Ajuste pelo menos:

| Variável | Descrição |
|----------|-----------|
| `APP_NAME` | Nome da aplicação |
| `APP_ENV` | `production` em produção |
| `APP_DEBUG` | `false` em produção |
| `APP_URL` | URL pública (ex.: `https://api.seudominio.com`) |
| `APP_KEY` | Gerada com `php artisan key:generate` (ver passo 4) |
| `DB_*` | Conexão MySQL (no Compose local: host `db`, base `curso_platform`, usuário/senha alinhados ao serviço) |
| `REDIS_HOST` | No Compose: `redis` |
| `SESSION_DRIVER` / `CACHE_STORE` / `QUEUE_CONNECTION` | Em produção costuma-se `redis` para cache e filas; o projeto suporta `database` para sessões/filas em ambientes simples |
| `MAIL_*` | SMTP para e-mails de boas-vindas (cadastro) e recuperação de senha (ver seção abaixo) |

Com **docker-compose** padrão do repositório, o serviço `app` injeta `DB_HOST=db` e `REDIS_HOST=redis`. Garanta que o `.env` use o mesmo host de banco que o container enxerga.

### E-mail transacional (cadastro e senha)

A aplicação envia:

- **Boas-vindas** após criar conta (formulário ou conclusão do cadastro Google).
- **Link de redefinição** em `/forgot-password`.

Configure no `.env` (mesmas chaves em dev e produção; só mudam host/credenciais):

| Variável | Exemplo dev (Gmail) | Produção |
|----------|---------------------|----------|
| `MAIL_MAILER` | `smtp` | `smtp` |
| `MAIL_HOST` | `smtp.gmail.com` | SMTP do provedor da empresa |
| `MAIL_PORT` | `587` | conforme provedor |
| `MAIL_SCHEME` | `tls` | `tls` ou `ssl` |
| `MAIL_USERNAME` | seu e-mail | conta de envio |
| `MAIL_PASSWORD` | senha de app | senha/API do provedor |
| `MAIL_FROM_ADDRESS` | mesmo remetente | `noreply@empresa.com` |
| `MAIL_FROM_NAME` | `${APP_NAME}` | nome exibido |

Gmail exige [senha de app](https://myaccount.google.com/apppasswords) com verificação em duas etapas. Para só inspecionar sem enviar: `MAIL_MAILER=log` (mensagens em `storage/logs/laravel.log`).

Após alterar `.env`:

```bash
docker compose exec app php artisan config:clear
```

**Fila de e-mails:** as notificações usam fila. No `.env`:

- `QUEUE_CONNECTION=sync` — envia na hora (recomendado para testar SMTP local).
- `QUEUE_CONNECTION=database` — grava jobs; rode o worker:

```bash
docker compose exec app php artisan queue:work
```

Teste rápido de SMTP:

```bash
docker compose exec app php artisan tinker --execute="Mail::raw('teste', fn(\$m) => \$m->to('seu@email.com')->subject('teste eduit'));"
```

### 3. Subir os serviços

```bash
docker compose build --no-cache
docker compose up -d
```

Serviços definidos em `docker-compose.yml`:

- **app** — aplicação (porta host `8080` → `80` no container)
- **db** — MySQL 8 (porta host `3307` → `3306`)
- **redis** — Redis (porta host `6380` → `6379`)

O `docker/entrypoint.sh` cria pastas em `storage/` e `bootstrap/cache` e ajusta permissões para o usuário **www-data** (importante com bind mount do código).

### 4. Instalação Laravel dentro do container

```bash
docker compose exec app bash -lc "composer install --no-dev --optimize-autoloader"
docker compose exec app bash -lc "php artisan key:generate --force"
docker compose exec app bash -lc "php artisan migrate --force"
```

Opcional (dados de exemplo / usuários admin):

```bash
docker compose exec app bash -lc "php artisan db:seed --force"
```

Link simbólico de storage (se ainda não existir):

```bash
docker compose exec app bash -lc "php artisan storage:link"
```

### 5. Verificar

- Web: `http://localhost:8080` (ou a URL exposta pelo proxy em produção)
- API: `http://localhost:8080/api` (prefixo definido em `bootstrap/app.php` / rotas)

---

## Imagem Docker sem bind mount (build “fechado”)

O `Dockerfile` já executa `composer install --no-dev` na **build**. Em um deploy só com imagem (sem montar `./` em `/var/www/html`), não é necessário `composer install` no runtime, mas **migrações e `APP_KEY`** continuam obrigatórios no primeiro deploy.

---

## Produção — checklist rápido

1. **`APP_ENV=production`**, **`APP_DEBUG=false`**, **`APP_URL`** correto (HTTPS).
2. **`APP_KEY`** definido e estável (não versionar `.env`).
3. Banco e Redis acessíveis apenas na rede interna; senhas fortes.
4. Rodar **`php artisan migrate --force`** em cada release.
5. **`php artisan config:cache`** e **`php artisan route:cache`** após deploy (quando aplicável).
6. Garantir escrita em **`storage/`** e **`bootstrap/cache`** (o entrypoint trata isso no Docker).
7. Certificados PDF / QR: dependências PHP (**GD** para geração de QR em PNG) já incluídas na imagem; revisar `config/certificate.php` e variáveis relacionadas se usar certificados em produção.
8. **Sanctum / SPA**: se houver front em outro domínio, configurar `SANCTUM_STATEFUL_DOMAINS` e CORS conforme a documentação Laravel.

---

## Documentação adicional

- API: [`docs/README_API.md`](docs/README_API.md)
- CRUD de curso via API (passo a passo): [`docs/README_COURSE_CRUD.md`](docs/README_COURSE_CRUD.md)

---

## Problemas comuns

| Sintoma | Ação |
|--------|------|
| Erro ao gravar logs ou cache | Conferir dono de `storage/` e `bootstrap/cache` (www-data); o `entrypoint` aplica `chown` na subida do container. |
| 500 após deploy | Ver `storage/logs/laravel.log`; rodar `php artisan config:clear` se mudou `.env`. |
| Banco não conecta | `DB_HOST` deve ser o nome do serviço Docker (`db`) ou o host real do MySQL. |
| E-mail não chega | Conferir `MAIL_*`, `MAIL_FROM_*`, `APP_URL`; com `QUEUE_CONNECTION=database`, subir `queue:work`; ver `storage/logs/laravel.log`. |

---

## Licença

Projeto derivado do esqueleto Laravel; componentes do framework permanecem sob a [licença MIT do Laravel](https://opensource.org/licenses/MIT).
