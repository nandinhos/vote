#!/bin/bash

# Script de Backup do Sistema de Votação
# Cria backup do banco SQLite e arquivos importantes

set -e

BACKUP_DIR="/var/www/html/storage/backups"
DATE=$(date +"%Y%m%d_%H%M%S")
DB_PATH="/var/www/html/database/database.sqlite"

# Criar diretório de backup se não existir
mkdir -p "$BACKUP_DIR"

echo "🗄️  Iniciando backup do sistema..."

# Backup do banco SQLite
if [ -f "$DB_PATH" ]; then
    echo "📊 Fazendo backup do banco de dados..."
    cp "$DB_PATH" "$BACKUP_DIR/database_$DATE.sqlite"
    echo "✅ Backup do banco salvo em: $BACKUP_DIR/database_$DATE.sqlite"
else
    echo "⚠️  Banco de dados não encontrado em: $DB_PATH"
fi

# Backup do arquivo .env
if [ -f "/var/www/html/.env" ]; then
    echo "⚙️  Fazendo backup das configurações..."
    cp "/var/www/html/.env" "$BACKUP_DIR/env_$DATE.backup"
    echo "✅ Backup das configurações salvo em: $BACKUP_DIR/env_$DATE.backup"
fi

# Limpar backups antigos (manter apenas os últimos 7 dias)
echo "🧹 Limpando backups antigos..."
find "$BACKUP_DIR" -name "database_*.sqlite" -mtime +7 -delete 2>/dev/null || true
find "$BACKUP_DIR" -name "env_*.backup" -mtime +7 -delete 2>/dev/null || true

echo "✅ Backup concluído com sucesso!"
echo "📁 Diretório de backups: $BACKUP_DIR"

# Listar backups disponíveis
echo ""
echo "📋 Backups disponíveis:"
ls -la "$BACKUP_DIR" | grep -E "(database_|env_)" || echo "Nenhum backup encontrado"