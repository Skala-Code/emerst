# Integração Outlook/Email - Documentação Completa

## ✅ O que foi implementado

### 1. Estrutura de Banco de Dados
- ✅ Tabela `emails` para armazenar emails do Outlook
- ✅ Campo `email_id` na tabela `service_orders`
- ✅ Campos OAuth Microsoft na tabela `users`

### 2. Autenticação OAuth Microsoft
- ✅ Controller `MicrosoftAuthController` para gerenciar OAuth
- ✅ Rotas de autenticação configuradas
- ✅ Configuração no `config/services.php`

### 3. Serviço de Integração
- ✅ `MicrosoftGraphService` para buscar emails da API Microsoft Graph
- ✅ Sincronização automática de emails
- ✅ Refresh automático de tokens

### 4. Página de Inbox
- ✅ Página Filament com listagem de emails
- ✅ Visualização de email selecionado
- ✅ Botões: Visualizar, Registrar, Arquivar
- ✅ Sincronização manual de emails

### 5. Integração com ServiceOrder
- ✅ Campo `email_id` no formulário (hidden)
- ✅ Visualização do email no lado direito durante criação
- ✅ Botão "Ver Email" na página de edição
- ✅ Preenchimento automático de dados do email

## 📋 Configuração Necessária

### 1. Variáveis de Ambiente

Adicione ao arquivo `.env`:

```env
MICROSOFT_CLIENT_ID=seu_client_id_aqui
MICROSOFT_CLIENT_SECRET=seu_client_secret_aqui
MICROSOFT_REDIRECT_URI=https://seu-dominio.com/auth/microsoft/callback
```

### 2. Registrar App no Azure Portal

1. Acesse https://portal.azure.com
2. Vá em "Azure Active Directory" > "App registrations"
3. Clique em "New registration"
4. Configure:
   - Name: Nome do seu app
   - Supported account types: Accounts in any organizational directory and personal Microsoft accounts
   - Redirect URI: `https://seu-dominio.com/auth/microsoft/callback`
5. Após criar, vá em "Certificates & secrets" e crie um "New client secret"
6. Copie o Client ID e Client Secret para o `.env`

### 3. Configurar Permissões da API

No Azure Portal, vá em "API permissions" e adicione:
- `Mail.Read` (Delegated)
- `Mail.ReadWrite` (Delegated)
- `offline_access` (Delegated)

### 4. Executar Migrations

```bash
php artisan migrate
```

## 🚀 Como Usar

### Conectar Conta Microsoft

1. Acesse a página "Caixa de Entrada" no menu lateral
2. Clique em "Conectar Microsoft"
3. Faça login com sua conta Microsoft/Outlook
4. Autorize as permissões solicitadas

### Sincronizar Emails

1. Após conectar, clique em "Sincronizar Emails"
2. Os últimos 50 emails serão importados

### Registrar Email como Ordem de Serviço

1. Na lista de emails, clique em "Registrar" no email desejado
2. Selecione ou crie um processo
3. Será redirecionado para criar a ordem de serviço
4. O email aparecerá no lado direito do formulário
5. Preencha os dados e salve

### Visualizar Email Vinculado

- Na página de criação: Email aparece automaticamente no lado direito
- Na página de edição: Clique no botão "Ver Email" no cabeçalho

## 📁 Arquivos Criados/Modificados

### Novos Arquivos
- `app/Models/Email.php`
- `app/Services/MicrosoftGraphService.php`
- `app/Http/Controllers/MicrosoftAuthController.php`
- `app/Filament/Pages/Inbox.php`
- `resources/views/filament/pages/inbox.blade.php`
- `resources/views/filament/resources/service-order-resource/pages/create-service-order.blade.php`
- `resources/views/filament/components/email-view.blade.php`

### Migrations
- `database/migrations/2025_01_15_000001_create_emails_table.php`
- `database/migrations/2025_01_15_000002_add_email_id_to_service_orders_table.php`
- `database/migrations/2025_01_15_000003_add_microsoft_oauth_to_users_table.php`

### Arquivos Modificados
- `app/Models/User.php` - Adicionados campos OAuth
- `app/Models/ServiceOrder.php` - Adicionado relacionamento com Email
- `app/Filament/Resources/ServiceOrderResource.php` - Adicionado campo email_id
- `app/Filament/Resources/ServiceOrderResource/Pages/CreateServiceOrder.php` - Lógica de email
- `app/Filament/Resources/ServiceOrderResource/Pages/EditServiceOrder.php` - Botão ver email
- `config/services.php` - Configuração Microsoft
- `routes/web.php` - Rotas OAuth
- `app/Providers/AppServiceProvider.php` - Event listener SocialiteProviders

## 🔧 Dependências Instaladas

- `laravel/socialite` - OAuth genérico
- `socialiteproviders/microsoft` - Provider específico Microsoft
- `microsoft/microsoft-graph` - SDK Microsoft Graph (opcional, não usado diretamente)

## ⚠️ Observações Importantes

1. **Redirect URI**: Deve ser exatamente igual ao configurado no Azure Portal
2. **HTTPS**: Em produção, use HTTPS obrigatoriamente
3. **Tokens**: Os tokens são armazenados criptografados no banco
4. **Refresh Token**: O sistema renova automaticamente os tokens expirados
5. **Limite de Emails**: Por padrão, sincroniza os últimos 50 emails

## 🐛 Troubleshooting

### Erro ao conectar Microsoft
- Verifique se o Client ID e Secret estão corretos no `.env`
- Confirme que o Redirect URI está correto
- Verifique as permissões da API no Azure Portal

### Emails não sincronizam
- Verifique se o token não expirou
- Confirme que as permissões `Mail.Read` foram concedidas
- Verifique os logs em `storage/logs/laravel.log`

### Email não aparece no formulário
- Verifique se o `email_id` está sendo passado na URL
- Confirme que o email existe no banco de dados

