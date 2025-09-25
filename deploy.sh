#!/bin/bash

# Script de Deploy - Sistema de Votação
# Uso: ./deploy.sh [ambiente]

set -e

ENVIRONMENT=${1:-production}
APP_NAME="vote_app"
COMPOSE_FILE="docker-compose.yml"

echo "🚀 Iniciando deploy do Sistema de Votação..."
echo "📋 Ambiente: $ENVIRONMENT"

# Verificar se Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker não está instalado!"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose não está instalado!"
    exit 1
fi

# Verificar se .env existe
if [ ! -f .env ]; then
    echo "⚠️  Arquivo .env não encontrado. Copiando de .env.example..."
    cp .env.example .env
    echo "📝 Configure o arquivo .env antes de continuar!"
    exit 1
fi

# Gerar APP_KEY se não existir
if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env; then
    echo "🔑 Gerando APP_KEY..."
    php artisan key:generate --no-interaction
fi

# Parar containers existentes
echo "🛑 Parando containers existentes..."
docker-compose down --remove-orphans

# Build da imagem
echo "🔨 Construindo imagem Docker..."
docker-compose build --no-cache

# Executar migrações
echo "📊 Executando migrações do banco..."
docker-compose run --rm app php artisan migrate --force

# Executar seeders se necessário
if [ "$ENVIRONMENT" = "development" ]; then
    echo "🌱 Executando seeders..."
    docker-compose run --rm app php artisan db:seed --force
fi

# Criar link simbólico para storage
echo "🔗 Criando link simbólico para storage..."
docker-compose run --rm app php artisan storage:link

# Limpar e otimizar cache
echo "🧹 Limpando e otimizando cache..."
docker-compose run --rm app php artisan config:cache
docker-compose run --rm app php artisan route:cache

# Iniciar containers
echo "▶️  Iniciando containers..."
docker-compose up -d

# Aguardar containers ficarem prontos
echo "⏳ Aguardando containers ficarem prontos..."
sleep 10

# Verificar status
echo "🔍 Verificando status dos containers..."
docker-compose ps

# Teste de conectividade
echo "🌐 Testando conectividade..."
if curl -f http://localhost:8080/health > /dev/null 2>&1; then
    echo "✅ Deploy concluído com sucesso!"
    echo "🌍 Aplicação disponível em: http://localhost:8080"
    echo ""
    echo "👥 Usuários de teste:"
    echo "   Admin: SARAM 1234567 / Senha: password"
    echo "   Voter: SARAM 7654321 / Senha: password"
else
    echo "❌ Falha no teste de conectividade!"
    echo "📋 Logs do container:"
    docker-compose logs app
    exit 1
fi

echo ""
echo "📚 Comandos úteis:"
echo "   Ver logs: docker-compose logs -f"
echo "   Parar: docker-compose down"
echo "   Reiniciar: docker-compose restart"
echo "   Shell: docker-compose exec app sh"