# Docker e Deploy - Sistema de Votação

## 🐳 Configuração Docker

### Visão Geral
O sistema está configurado para deploy usando Docker com as seguintes características:
- **Container otimizado** para Laravel + Vue.js
- **Multi-stage build** para reduzir tamanho da imagem
- **Nginx + PHP-FPM** para performance em produção
- **Supervisor** para gerenciamento de processos
- **Suporte a SQLite e PostgreSQL**

### Arquivos de Configuração

#### 📦 Dockerfile
- **Base:** Ubuntu 22.04 LTS
- **Serviços:** Nginx, PHP 8.2, Node.js 20, Supervisor
- **Otimizações:** Cache de dependências, build multi-stage
- **Segurança:** Usuário não-root, permissões adequadas

#### 🔧 docker-compose.yml
```yaml
services:
  app:
    build: .
    ports:
      - "8011:80"
    volumes:
      - ./storage:/var/www/html/storage
      - ./database:/var/www/html/database
    environment:
      - APP_ENV=production
```

#### 🌐 nginx.conf
- **Performance:** Gzip, cache de assets, rate limiting
- **Segurança:** Headers de segurança, proteção contra ataques
- **Laravel:** Configuração otimizada para rotas do Laravel

#### ⚙️ supervisord.conf
- **Processos:** php-fpm, nginx, laravel-worker
- **Logs:** Centralizados e rotacionados
- **Restart:** Automático em caso de falha

#### 🐘 php.ini
- **Produção:** Configurações otimizadas para performance
- **Segurança:** Desabilitação de funções perigosas
- **Upload:** Limites adequados para fotos
- **OPcache:** Habilitado para melhor performance

## 🚀 Deploy Automatizado

### Script deploy.sh
O script `deploy.sh` automatiza todo o processo de deploy:

```bash
./deploy.sh
```

#### Funcionalidades do Script:
1. **Verificação de dependências** (Docker, Docker Compose)
2. **Configuração de ambiente** (.env, APP_KEY)
3. **Build da imagem** Docker otimizada
4. **Execução de migrações** e seeders
5. **Otimização de cache** e assets
6. **Teste de conectividade**

### Comandos Manuais

#### Build e Start
```bash
# Build da imagem
docker-compose build

# Iniciar serviços
docker-compose up -d

# Verificar status
docker-compose ps
```

#### Manutenção
```bash
# Logs em tempo real
docker-compose logs -f

# Executar comandos Laravel
docker-compose exec app php artisan migrate

# Parar serviços
docker-compose down
```

## 🌍 Ambientes

### Desenvolvimento Local
```bash
# Servidor Laravel nativo
php artisan serve --host=0.0.0.0 --port=8000
```

### Docker Local
```bash
# Usando docker-compose
docker-compose up -d
# Acesso: http://localhost:8011
```

### Produção
```bash
# Deploy completo
./deploy.sh
# Configurar .env.production conforme necessário
```

## 📋 Variáveis de Ambiente

### .env.production (Template)
```env
APP_NAME="Sistema de Votação"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8011

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

# Configurar conforme ambiente de produção
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

### Variáveis Importantes
- `APP_KEY`: Gerado automaticamente pelo deploy.sh
- `DB_CONNECTION`: sqlite (padrão) ou pgsql
- `APP_URL`: URL de acesso da aplicação
- `APP_DEBUG`: false em produção

## 💾 Backup e Segurança

### Backup Automático
O sistema inclui backup automático do banco de dados:
```bash
# Backup SQLite
cp database/database.sqlite database/backup_$(date +%Y%m%d_%H%M%S).sqlite

# Dump SQL
sqlite3 database/database.sqlite .dump > database/backup_$(date +%Y%m%d_%H%M%S).sql
```

### Volumes Persistentes
- `./storage`: Arquivos de upload e logs
- `./database`: Banco de dados SQLite

### Segurança
- **Headers de segurança** configurados no Nginx
- **Rate limiting** para proteção contra ataques
- **Usuário não-root** no container
- **Variáveis sensíveis** em .env

## 🔍 Monitoramento

### Health Checks
```bash
# Verificar saúde da aplicação
curl http://localhost:8011/health

# Logs do container
docker-compose logs app

# Status dos processos
docker-compose exec app supervisorctl status
```

### Logs Importantes
- **Laravel:** `/var/www/html/storage/logs/laravel.log`
- **Nginx:** `/var/log/nginx/access.log`, `/var/log/nginx/error.log`
- **PHP-FPM:** `/var/log/php8.2-fpm.log`

## 🛠️ Troubleshooting

### Problemas Comuns

#### Container não inicia
```bash
# Verificar logs
docker-compose logs app

# Rebuild sem cache
docker-compose build --no-cache
```

#### Permissões de arquivo
```bash
# Corrigir permissões storage
docker-compose exec app chown -R www-data:www-data storage
```

#### Banco de dados
```bash
# Executar migrações
docker-compose exec app php artisan migrate

# Verificar conexão
docker-compose exec app php artisan tinker
```

### Comandos Úteis
```bash
# Entrar no container
docker-compose exec app bash

# Limpar cache
docker-compose exec app php artisan cache:clear

# Recompilar assets
docker-compose exec app npm run build

# Verificar configuração
docker-compose exec app php artisan config:show
```

## 📈 Performance

### Otimizações Implementadas
- **OPcache** habilitado para PHP
- **Gzip** compression no Nginx
- **Cache de assets** com headers apropriados
- **Build otimizado** do Vue.js para produção
- **Supervisor** para gerenciamento eficiente de processos

### Métricas Recomendadas
- **Tempo de resposta:** < 200ms para páginas principais
- **Uso de memória:** < 512MB por container
- **CPU:** < 50% em operação normal
- **Disco:** Monitorar crescimento do banco de dados

## 🔄 Atualizações

### Processo de Atualização
1. **Backup** do banco de dados atual
2. **Pull** das mudanças do código
3. **Rebuild** da imagem Docker
4. **Execução** de migrações
5. **Teste** das funcionalidades principais

### Rollback
```bash
# Parar serviços
docker-compose down

# Restaurar backup
cp database/backup_YYYYMMDD_HHMMSS.sqlite database/database.sqlite

# Reiniciar
docker-compose up -d
```