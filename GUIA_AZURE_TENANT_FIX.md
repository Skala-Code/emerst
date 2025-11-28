# Correção do Erro AADSTS700016 - Tenant Incorreto

## 🔴 Problema

Erro: `AADSTS700016: Application with identifier '...' was not found in the directory 'EMERST'`

Este erro ocorre quando o Azure AD está tentando encontrar a aplicação em um tenant específico ('EMERST'), mas a aplicação não está registrada nesse tenant ou a configuração está incorreta.

## ✅ Solução

### 1. Verificar Configuração no Azure Portal

No Azure Portal, ao criar o App Registration, certifique-se de que:

- **Supported account types** está configurado como:
  - ✅ **"Accounts in any organizational directory and personal Microsoft accounts (e.g. Skype, Xbox)"**
  
  Isso permite que o app funcione com qualquer conta Microsoft (pessoal ou corporativa).

### 2. Adicionar Variável de Ambiente

Adicione ao seu arquivo `.env`:

```env
MICROSOFT_TENANT=common
```

**Opções de Tenant:**
- `common` - Permite login com qualquer conta Microsoft (pessoal ou corporativa) - **RECOMENDADO**
- `organizations` - Apenas contas corporativas/organizacionais
- `consumers` - Apenas contas pessoais Microsoft
- `{tenant-id}` - ID específico de um tenant (mais restritivo)

### 3. Limpar Cache

Após alterar o `.env`, execute:

```bash
php artisan config:clear
php artisan cache:clear
```

### 4. Verificar Client ID

Certifique-se de que o `MICROSOFT_CLIENT_ID` no `.env` corresponde exatamente ao **Application (client) ID** no Azure Portal.

## 🔍 Verificação Rápida

1. **Azure Portal** → **App registrations** → Seu App
2. Verifique **"Supported account types"**:
   - Deve estar como: "Accounts in any organizational directory and personal Microsoft accounts"
3. Verifique **"Redirect URIs"**:
   - Deve incluir: `https://seu-dominio.com/auth/microsoft/callback`
   - Ou para desenvolvimento: `http://localhost:8000/auth/microsoft/callback`

## 📝 Exemplo de `.env` Correto

```env
MICROSOFT_CLIENT_ID=3fa24287-c7d0-434e-bc7d-66110b768ae7
MICROSOFT_CLIENT_SECRET=seu-secret-aqui
MICROSOFT_REDIRECT_URI=https://seu-dominio.com/auth/microsoft/callback
MICROSOFT_TENANT=common
```

## ⚠️ Importante

- O `MICROSOFT_TENANT=common` permite que qualquer usuário com conta Microsoft faça login
- Se você quiser restringir apenas para sua organização, use o ID do tenant específico
- O tenant 'common' é o mais flexível e recomendado para aplicações que precisam funcionar com múltiplos tipos de conta

## 🐛 Se o Problema Persistir

1. Verifique se o Client ID está correto
2. Confirme que o Redirect URI está exatamente igual no Azure e no `.env`
3. Verifique se as permissões da API foram concedidas
4. Tente desconectar e reconectar a conta Microsoft
5. Verifique os logs: `storage/logs/laravel.log`

