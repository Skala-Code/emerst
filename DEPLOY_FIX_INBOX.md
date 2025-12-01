# 🔧 Correção Urgente - Erro TextColumn::boolean()

## ⚠️ Problema

O servidor de produção (`/var/www/html/app/Filament/Pages/Inbox.php`) ainda tem o código antigo na **linha 121**:

```php
TextColumn::make('is_read')->boolean()  // ❌ ERRADO - causa erro
```

## ✅ Solução

O código local já está correto. Você precisa fazer deploy do arquivo atualizado.

### Arquivo Correto (Local)

**Linha 14:**
```php
use Filament\Tables\Columns\IconColumn;
```

**Linhas 120-123:**
```php
IconColumn::make('is_read')
    ->label('Lido')
    ->boolean()
    ->sortable(),
```

## 🚀 Como Fazer o Deploy

### Opção 1: Via Git (Recomendado)

```bash
# 1. No seu computador local:
git add app/Filament/Pages/Inbox.php
git commit -m "Fix: Replace TextColumn with IconColumn for is_read field"
git push

# 2. No servidor (SSH):
ssh usuario@servidor
cd /var/www/html
git pull
php artisan optimize:clear
```

### Opção 2: Upload Manual

1. Faça upload do arquivo `app/Filament/Pages/Inbox.php` do seu computador para o servidor
2. Substitua o arquivo em `/var/www/html/app/Filament/Pages/Inbox.php`
3. Execute no servidor:
```bash
php artisan optimize:clear
```

### Opção 3: Editar Diretamente no Servidor

Se tiver acesso SSH, edite o arquivo diretamente:

```bash
# Conecte ao servidor
ssh usuario@servidor

# Edite o arquivo
nano /var/www/html/app/Filament/Pages/Inbox.php
```

**Alterações necessárias:**

1. **Linha 14** - Adicione (ou verifique se existe):
```php
use Filament\Tables\Columns\IconColumn;
```

2. **Linhas 120-123** - Altere de:
```php
TextColumn::make('is_read')
    ->label('Lido')
    ->boolean()
    ->sortable(),
```

**Para:**
```php
IconColumn::make('is_read')
    ->label('Lido')
    ->boolean()
    ->sortable(),
```

3. **Salve o arquivo** (Ctrl+X, depois Y, depois Enter)

4. **Limpe o cache:**
```bash
php artisan optimize:clear
```

## ✅ Verificação

Após o deploy, acesse:
```
https://emerst-site.t0njch.easypanel.host/admin/inbox
```

A página deve carregar sem erros e a coluna "Lido" deve mostrar ícones de check/uncheck.

## 📝 Nota

- O código local está **100% correto**
- O problema é apenas no servidor de produção
- Após o deploy, o erro será resolvido imediatamente


