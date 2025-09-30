# Docker Deploy - Aplicação Vote

Este documento descreve o processo completo de deploy da aplicação Vote usando Docker.

## Pré-requisitos

- Docker instalado
- Docker Compose instalado
- Porta 8011 disponível no host

## Estrutura do Projeto

```
vote/
├── docker/
│   ├── Dockerfile
│   ├── init.sh              # Script de inicialização
│   ├── nginx.conf           # Configuração do Nginx
│   ├── supervisord.conf     # Configuração do Supervisor
│   └── php.ini             # Configuração do PHP
├── docker-compose.yml
└── [arquivos da aplicação Laravel]
```

## Configurações Importantes

### 1. Dockerfile

O Dockerfile está configurado para:
- Usar Alpine Linux como base
- Instalar PHP 8.2 com extensões necessárias
- Configurar usuário `www-data` (UID/GID 82)
- Instalar Composer e Node.js
- Configurar permissões corretas

### 2. Script de Inicialização (init.sh)

O `init.sh` executa as seguintes tarefas críticas:

```bash
#!/bin/bash

# Limpar caches
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Executar migrações
echo "Executando migrações..."
php artisan migrate --force

# Cache de configurações
php artisan config:cache
php artisan route:cache

# Verificar conectividade com banco
php artisan tinker --execute="DB::connection()->getPdo();"

# CRÍTICO: Corrigir permissões do banco de dados
echo "Corrigindo permissões do banco de dados..."
chown www-data:www-data /var/www/html/database/database.sqlite
chown www-data:www-data /var/www/html/database/
chmod 664 /var/www/html/database/database.sqlite
chmod 775 /var/www/html/database/

# Criar diretório de logs do supervisor
mkdir -p /var/log/supervisor

# Iniciar supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
```

### 3. Configuração do Supervisor (supervisord.conf)

```ini
[supervisord]
nodaemon=true
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid

[program:php-fpm]
command=php-fpm8.2 -F
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:nginx]
command=nginx -g "daemon off;"
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
```

### 4. Configuração do Nginx (nginx.conf)

```nginx
user www-data;
worker_processes auto;
pid /run/nginx.pid;

events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    
    server {
        listen 80;
        server_name localhost;
        root /var/www/html/public;
        index index.php index.html;
        
        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }
        
        location ~ \.php$ {
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
            include fastcgi_params;
        }
    }
}
```

## Processo de Deploy

### 1. Build da Imagem

```bash
docker-compose build app
```

### 2. Iniciar Container

```bash
docker-compose up -d app
```

### 3. Verificar Status

```bash
# Status dos containers
docker-compose ps

# Logs da aplicação
docker logs vote_app --tail 20

# Verificar saúde do container
docker inspect vote_app | grep -A 5 "Health"
```

### 4. Teste de Conectividade

```bash
# Teste básico
curl -I http://localhost:8011

# Deve retornar:
# HTTP/1.1 302 Found
# Location: http://localhost:8011/login
```

## Comandos Úteis

### Rebuild Completo

```bash
# Parar container
docker stop vote_app

# Rebuild
docker-compose build app

# Iniciar
docker-compose up -d app
```

### Debug

```bash
# Entrar no container
docker exec -it vote_app /bin/sh

# Verificar permissões do banco
ls -la /var/www/html/database/

# Verificar processos
ps aux

# Verificar logs do Laravel
tail -f /var/www/html/storage/logs/laravel.log
```

### Limpeza

```bash
# Parar e remover container
docker-compose down

# Remover imagem
docker rmi vote-app

# Rebuild completo
docker-compose build app --no-cache
```

## Pontos Críticos de Atenção

1. **Permissões do Banco de Dados**: O SQLite deve ter permissões `www-data:www-data`
2. **Usuário nos Serviços**: Sempre usar `www-data`, nunca `www`
3. **Diretórios de Log**: Criar `/var/log/supervisor` antes de iniciar
4. **Ordem de Inicialização**: Migrations → Permissões → Supervisor
5. **Health Check**: Container deve estar "healthy" antes de considerar deploy completo

## Troubleshooting

Para problemas específicos, consulte o arquivo `TROUBLESHOOTING_DEPLOY.md`.

## Monitoramento

### Logs em Tempo Real

```bash
# Logs do container
docker logs vote_app -f

# Logs do Laravel
docker exec vote_app tail -f /var/www/html/storage/logs/laravel.log

# Logs do Nginx
docker exec vote_app tail -f /var/log/nginx/access.log
```

### Métricas de Performance

```bash
# Uso de recursos
docker stats vote_app

# Processos ativos
docker exec vote_app ps aux
```