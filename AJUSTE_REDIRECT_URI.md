# Ajuste do Redirect URI para /auth

## ✅ Correção Aplicada

O sistema agora aceita o callback em `/auth` (conforme configurado no Azure Portal).

## 📝 Atualizar `.env`

Certifique-se de que seu `.env` tenha:

```env
MICROSOFT_CLIENT_ID=seu-client-id
MICROSOFT_CLIENT_SECRET=seu-client-secret
MICROSOFT_REDIRECT_URI=https://emerst-site.t0njch.easypanel.host/auth
MICROSOFT_TENANT=common
```

**Importante:** O `MICROSOFT_REDIRECT_URI` deve ser exatamente igual ao configurado no Azure Portal.

## 🔄 Limpar Cache

Após atualizar o `.env`, execute:

```bash
php artisan config:clear
php artisan cache:clear
```

## ✅ Verificação no Azure Portal

No Azure Portal, o Redirect URI deve estar configurado como:
- `https://emerst-site.t0njch.easypanel.host/auth`

## 🎯 Como Funciona Agora

1. Usuário clica em "Conectar Microsoft"
2. É redirecionado para login Microsoft
3. Após autorizar, Microsoft redireciona para `/auth` com `code` e `state`
4. O sistema processa o OAuth e conecta a conta
5. Usuário é redirecionado para a Caixa de Entrada

## ⚠️ Nota

Se você quiser usar `/auth/microsoft/callback` no futuro, basta:
1. Atualizar o Redirect URI no Azure Portal
2. Atualizar `MICROSOFT_REDIRECT_URI` no `.env`
3. Limpar o cache

