# Integração Outlook/Email - Guia de Configuração

## Arquivos Criados

### Migrations
- `database/migrations/2025_01_15_000001_create_emails_table.php`
- `database/migrations/2025_01_15_000002_add_email_id_to_service_orders_table.php`
- `database/migrations/2025_01_15_000003_add_microsoft_oauth_to_users_table.php`

### Models
- `app/Models/Email.php`
- Modificações em `app/Models/User.php` e `app/Models/ServiceOrder.php`

### Services
- `app/Services/MicrosoftGraphService.php`

### Controllers
- `app/Http/Controllers/MicrosoftAuthController.php`

### Filament Pages
- `app/Filament/Pages/Inbox.php`
- `resources/views/filament/pages/inbox.blade.php`

## Configuração Necessária

### 1. Variáveis de Ambiente (.env)

Adicione as seguintes variáveis ao seu arquivo `.env`:

```env
MICROSOFT_CLIENT_ID=seu_client_id_aqui
MICROSOFT_CLIENT_SECRET=seu_client_secret_aqui
MICROSOFT_REDIRECT_URI=https://seu-dominio.com/auth/microsoft/callback
```

### 2. Registrar o Event Listener do SocialiteProviders

Adicione ao `app/Providers/EventServiceProvider.php` (ou crie se não existir):

```php
use SocialiteProviders\Manager\SocialiteWasCalled;

protected $listen = [
    SocialiteWasCalled::class => [
        'SocialiteProviders\\Microsoft\\MicrosoftExtendSocialite@handle',
    ],
];
```

### 3. Executar Migrations

```bash
php artisan migrate
```

### 4. Registrar a Página Inbox no Filament

A página já está criada e será descoberta automaticamente pelo Filament.

## Fluxo de Uso

1. **Conectar Microsoft**: Usuário clica em "Conectar Microsoft" na página Inbox
2. **Sincronizar Emails**: Após conectar, pode sincronizar emails
3. **Visualizar Email**: Clica em "Visualizar" para ver o conteúdo
4. **Registrar**: Clica em "Registrar" e seleciona/cria processo
5. **Criar Ordem de Serviço**: Redireciona para criação com email vinculado
6. **Visualizar Email na OS**: Sempre visível no lado direito do formulário

## Próximos Passos

1. Modificar `CreateServiceOrder` para aceitar `email_id` e `process_id` via query params
2. Adicionar visualização do email no lado direito do formulário de ServiceOrder
3. Adicionar botão "Ver Email" na página de edição/visualização de ServiceOrder

