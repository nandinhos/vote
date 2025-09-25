#!/bin/sh

# Script de inicialização para corrigir problemas comuns do Laravel
echo "🚀 Iniciando configuração do Laravel..."

# Aguardar um pouco para garantir que o sistema está pronto
sleep 2

# Verificar e criar arquivo .env se não existir
if [ ! -f /var/www/html/.env ]; then
    echo "📝 Criando arquivo .env para produção..."
    if [ -f /var/www/html/.env.docker ]; then
        cp /var/www/html/.env.docker /var/www/html/.env
        echo "✅ Usando configuração Docker (.env.docker)"
    else
        cp /var/www/html/.env.example /var/www/html/.env
        echo "⚠️ Usando configuração padrão (.env.example)"
    fi
    
    # Gerar APP_KEY se não existir
    if ! grep -q "APP_KEY=base64:" /var/www/html/.env; then
        echo "🔑 Gerando APP_KEY..."
        cd /var/www/html && php artisan key:generate --force
    fi
fi

# Corrigir permissões dos diretórios críticos
echo "🔧 Corrigindo permissões..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Criar diretórios se não existirem
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache

# Aplicar permissões novamente após criar diretórios
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache

# Limpar caches do Laravel
echo "🧹 Limpando caches..."
php artisan optimize:clear 2>/dev/null || true
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Corrigir permissões do banco de dados
echo "🔧 Corrigindo permissões do banco de dados..."
if [ -f /var/www/html/database/database.sqlite ]; then
    chown www-data:www-data /var/www/html/database/database.sqlite
    chmod 664 /var/www/html/database/database.sqlite
fi
chown www-data:www-data /var/www/html/database/
chmod 775 /var/www/html/database/

# Executar migrações
echo "🗄️ Executando migrações..."
php artisan migrate --force 2>/dev/null || true

# Recriar caches otimizados
echo "⚡ Recriando caches otimizados..."
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true

# Verificar se o banco de dados existe e está acessível
echo "🔍 Verificando banco de dados..."
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'DB: OK'; } catch(Exception \$e) { echo 'DB: ERRO - ' . \$e->getMessage(); }" 2>/dev/null || echo "DB: Erro ao verificar"

echo "✅ Configuração concluída!"

# Criar diretório de logs do supervisor
mkdir -p /var/log/supervisor

# Iniciar supervisord
echo "🎯 Iniciando serviços..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf