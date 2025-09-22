# Deploy to Easypanel

Este guia detalha como implantar o sistema Emerst no Easypanel.

## Pré-requisitos

1. Conta no Easypanel
2. Servidor configurado no Easypanel
3. Banco de dados MySQL configurado
4. Redis configurado (opcional, mas recomendado)

## Passo a Passo

### 1. Criar Aplicação no Easypanel

1. Acesse o painel do Easypanel
2. Clique em "Create Service" > "App"
3. Escolha "Source" como tipo de deploy
4. Configure:
   - **Name**: `emerst-sistema`
   - **Repository**: URL do seu repositório Git
   - **Branch**: `main`
   - **Build Pack**: `Dockerfile`

### 2. Configurar Variáveis de Ambiente

No painel do Easypanel, adicione as seguintes variáveis de ambiente (baseadas no arquivo `.env.easypanel`):

**Aplicação (OBRIGATÓRIO):**
```
APP_NAME=Emerst - Sistema de Gestão de Prazos
APP_ENV=production
APP_KEY=base64:SuaChaveGeradaAquiDe32Caracteres==
APP_DEBUG=false
APP_URL=https://seu-dominio.com
APP_TIMEZONE=America/Sao_Paulo
```

**⚠️ IMPORTANTE: Para gerar a APP_KEY, use:**
```bash
php artisan key:generate --show
```
Ou gere online: `base64:` + uma string de 32 caracteres aleatórios

**Banco de Dados:**
```
DB_CONNECTION=mysql
DB_HOST=seu-host-mysql
DB_PORT=3306
DB_DATABASE=emerst
DB_USERNAME=seu-usuario
DB_PASSWORD=sua-senha
```

**Sessão e Cache:**
```
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=
SESSION_CONNECTION=
SESSION_STORE=
SESSION_SECURE_COOKIE=false
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

**Se usar Redis (opcional):**
```
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=seu-host-redis
REDIS_PORT=6379
```

**Configuração Inicial:**
```
ADMIN_EMAIL=admin@emerst.com
ADMIN_PASSWORD=sua-senha-segura
SEED_DATABASE=true
```

### 3. Configurar Domínio

1. No Easypanel, vá em "Domains"
2. Adicione seu domínio
3. Configure SSL (Let's Encrypt)

### 4. Deploy

1. Clique em "Deploy" no painel
2. Aguarde o build completar
3. Verifique os logs para confirmar que não há erros

### 5. Primeiro Acesso

Após o deploy bem-sucedido:

1. Acesse `https://seu-dominio.com/admin`
2. Faça login com:
   - **Email**: O definido em `ADMIN_EMAIL`
   - **Senha**: A definida em `ADMIN_PASSWORD`

## Estrutura do Container

O Dockerfile cria um container otimizado com:

- **PHP 8.3** com todas as extensões necessárias
- **Nginx** como servidor web
- **Supervisor** para gerenciar processos
- **Laravel Worker** para processar filas
- **Multi-stage build** para otimizar o tamanho da imagem

## Monitoramento

O container inclui:

- Logs do Nginx em `/var/log/nginx/`
- Logs do PHP-FPM em `/var/log/php-fpm.log`
- Logs do Laravel Worker em `/var/log/worker.log`
- Logs do Supervisor em `/var/log/supervisor/`

## Comandos Artisan (se necessário)

Para executar comandos Artisan no container em produção:

```bash
# Executar através do Easypanel Terminal
php artisan migrate
php artisan cache:clear
php artisan config:cache
```

## Backup

Configure backups regulares no Easypanel para:
- Banco de dados MySQL
- Arquivos de storage (`/var/www/html/storage/app`)

## Troubleshooting

### Problemas Comuns

1. **Erro 500**: Verifique se a `APP_KEY` foi gerada
2. **Erro de Banco**: Confirme as credenciais do banco de dados
3. **Permissões**: O container já configura as permissões automaticamente
4. **Cache**: Limpe o cache com `php artisan cache:clear`

### Logs

Para verificar logs no Easypanel:
1. Vá em "Logs" no painel da aplicação
2. Monitore os logs em tempo real durante o deploy

## Atualizações

Para atualizar a aplicação:
1. Faça push das alterações para o repositório
2. No Easypanel, clique em "Deploy" novamente
3. O sistema será atualizado automaticamente

## Recursos Incluídos

- ✅ Gestão de Empresas, Escritórios e Advogados
- ✅ Controle de Processos e Ordens de Serviço
- ✅ Campos personalizáveis com permissões por usuário
- ✅ Sistema de workflow para ordens de serviço
- ✅ Exportação para Excel (template UNIMED)
- ✅ Gestão de documentos com controle de acesso
- ✅ Sistema de roles e permissões
- ✅ Interface administrativa completa (Filament)
- ✅ Cálculos trabalhistas automatizados
- ✅ Controle de prazos e deadlines