# Guia Completo: Como Gerar Credenciais do Azure para Integração Outlook

## 📋 Pré-requisitos

- Conta Microsoft (pessoal ou corporativa)
- Acesso ao Azure Portal (https://portal.azure.com)

## 🚀 Passo a Passo

### Passo 1: Acessar o Azure Portal

1. Acesse: https://portal.azure.com
2. Faça login com sua conta Microsoft

### Passo 2: Criar App Registration

1. No menu lateral esquerdo, procure por **"Azure Active Directory"** ou **"Microsoft Entra ID"**
2. Clique em **"App registrations"** (Registros de aplicativo)
3. Clique no botão **"+ New registration"** (+ Novo registro)

 Passo 3: Configurar o App Registration

Preencha os campos:

- **Name** (Nome): 
  - Exemplo: `Emerst Outlook Integration` ou `Sistema Emerst`
  
- **Supported account types** (Tipos de conta suportados):
  - Selecione: **"Accounts in any organizational directory and personal Microsoft accounts (e.g. Skype, Xbox)"**
  - Isso permite login com contas pessoais e corporativas

- **Redirect URI** (URI de redirecionamento):
  - Platform: Selecione **"Web"**
  - URL: `https://seu-dominio.com/auth/microsoft/callback`
  - **IMPORTANTE**: Substitua `seu-dominio.com` pelo seu domínio real
  - Exemplo: `https://emerst.com.br/auth/microsoft/callback`
  - Para desenvolvimento local: `http://localhost:8000/auth/microsoft/callback`

4. Clique em **"Register"** (Registrar)

### Passo 4: Copiar o Client ID

Após criar o registro:

1. Na página de **Overview** (Visão geral) do seu app
2. Você verá o **Application (client) ID**
3. **Copie este valor** - será o `MICROSOFT_CLIENT_ID` no seu `.env`

### Passo 5: Criar Client Secret

1. No menu lateral esquerdo do seu app, clique em **"Certificates & secrets"** (Certificados e segredos)
2. Na aba **"Client secrets"**, clique em **"+ New client secret"** (+ Novo segredo do cliente)
3. Preencha:
   - **Description** (Descrição): Exemplo: `Emerst Integration Secret`
   - **Expires** (Expira em): 
     - Para produção: Recomendo **24 months** (24 meses)
     - Para desenvolvimento: **12 months** (12 meses)
4. Clique em **"Add"** (Adicionar)
5. **IMPORTANTE**: Copie o **Value** (Valor) do secret imediatamente
   - ⚠️ **Você só verá este valor uma vez!** Se perder, precisará criar um novo secret
6. Este valor será o `MICROSOFT_CLIENT_SECRET` no seu `.env`

### Passo 6: Configurar Permissões da API

1. No menu lateral esquerdo, clique em **"API permissions"** (Permissões de API)
2. Clique em **"+ Add a permission"** (+ Adicionar uma permissão)
3. Selecione **"Microsoft Graph"**
4. Selecione **"Delegated permissions"** (Permissões delegadas)
5. Procure e marque as seguintes permissões:
   - ✅ `Mail.Read` - Ler emails do usuário
   - ✅ `Mail.ReadWrite` - Ler e escrever emails do usuário
   - ✅ `offline_access` - Manter acesso aos dados do usuário
6. Clique em **"Add permissions"** (Adicionar permissões)

### Passo 7: Configurar Redirect URIs Adicionais (Opcional)

Se você precisar de múltiplos ambientes (desenvolvimento, staging, produção):

1. No menu lateral, clique em **"Authentication"** (Autenticação)
2. Em **"Redirect URIs"**, você pode adicionar múltiplas URLs:
   - `http://localhost:8000/auth/microsoft/callback` (desenvolvimento)
   - `https://staging.seudominio.com/auth/microsoft/callback` (staging)
   - `https://seudominio.com/auth/microsoft/callback` (produção)
3. Clique em **"Save"** (Salvar)

### Passo 8: Configurar no Laravel

Adicione as credenciais ao seu arquivo `.env`:

```env
MICROSOFT_CLIENT_ID=seu-client-id-aqui
MICROSOFT_CLIENT_SECRET=seu-client-secret-aqui
MICROSOFT_REDIRECT_URI=https://seu-dominio.com/auth/microsoft/callback
```

**Exemplo real:**

```env
MICROSOFT_CLIENT_ID=12345678-1234-1234-1234-123456789abc
MICROSOFT_CLIENT_SECRET=abc~1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ
MICROSOFT_REDIRECT_URI=https://emerst.com.br/auth/microsoft/callback
```

## 🔒 Segurança

### Boas Práticas

1. **Nunca commite o `.env`** no Git
2. **Use secrets diferentes** para cada ambiente (dev, staging, prod)
3. **Renove os secrets** antes de expirarem
4. **Use HTTPS** em produção (obrigatório para OAuth)

### Rotação de Secrets

- Os secrets têm data de expiração
- Antes de expirar, crie um novo secret
- Atualize o `.env` com o novo valor
- O antigo continuará funcionando até expirar

## 🧪 Testando a Configuração

Após configurar:

1. Acesse a página **"Caixa de Entrada"** no seu sistema
2. Clique em **"Conectar Microsoft"**
3. Você será redirecionado para login da Microsoft
4. Após autorizar, será redirecionado de volta
5. Se funcionar, verá "Conta Microsoft conectada com sucesso!"

## ❌ Troubleshooting

### Erro: "Invalid client"
- Verifique se o Client ID está correto no `.env`
- Confirme que não há espaços extras

### Erro: "Invalid redirect URI"
- O Redirect URI no Azure deve ser **exatamente igual** ao do `.env`
- Verifique se está usando `http://` ou `https://` corretamente
- Confirme que não há barra `/` no final

### Erro: "Insufficient privileges"
- Verifique se as permissões foram adicionadas corretamente
- Confirme que você clicou em "Grant admin consent" se necessário (para contas corporativas)

### Erro: "Token expired"
- O sistema renova automaticamente, mas se persistir:
- Desconecte e reconecte a conta Microsoft
- Verifique se o `offline_access` está configurado

## 📸 Screenshots de Referência

### Onde encontrar o Client ID:
- Azure Portal → App registrations → Seu App → Overview → Application (client) ID

### Onde criar o Secret:
- Azure Portal → App registrations → Seu App → Certificates & secrets → Client secrets → New client secret

### Onde configurar permissões:
- Azure Portal → App registrations → Seu App → API permissions → Add a permission → Microsoft Graph

## 📞 Suporte

Se tiver problemas:
1. Verifique os logs: `storage/logs/laravel.log`
2. Confirme que todas as variáveis estão no `.env`
3. Execute `php artisan config:clear` após alterar o `.env`
4. Verifique se o Redirect URI está correto em ambos os lugares

