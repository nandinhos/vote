# Troubleshooting - Deploy da Aplicação Vote

Este documento contém soluções para problemas comuns encontrados durante o deploy da aplicação.

## 🔧 Problemas Comuns e Soluções

### 1. Erro 503 Service Temporarily Unavailable

**Sintomas:**
- Página retorna erro 503 ao acessar a aplicação
- Nginx bloqueia requisições frequentes
- Logs mostram "limiting requests, excess"

**Causa:**
- Configuração de rate limiting muito restritiva no Nginx
- Configuração padrão: 5 req/min com burst 3

**Solução:**
```bash
# 1. Editar configuração do Nginx
nano docker/nginx.conf

# 2. Localizar e ajustar as linhas:
limit_req_zone $binary_remote_addr zone=api:10m rate=60r/m;
limit_req zone=api burst=10 nodelay;

# 3. Reconstruir e reiniciar containers
docker-compose down
docker-compose up --build -d
```

**Configurações recomendadas:**
- **Desenvolvimento:** 60 req/min, burst 10
- **Produção:** 30 req/min, burst 5

---

### 2. Textos em Inglês na Interface (Previous/Next)

**Sintomas:**
- Paginação mostra "Previous" e "Next" em vez de "Anterior" e "Próximo"
- Interface parcialmente em inglês apesar de APP_LOCALE=pt_BR

**Causa:**
- Ausência de arquivos de tradução em português brasileiro
- Laravel usa traduções padrão em inglês quando não encontra traduções localizadas

**Solução:**
```bash
# 1. Criar diretório de traduções
mkdir -p lang/pt_BR

# 2. Criar arquivo de traduções de paginação
cat > lang/pt_BR/pagination.php << 'EOF'
<?php
return [
    'previous' => '&laquo; Anterior',
    'next' => 'Próximo &raquo;',
];
EOF

# 3. Criar arquivo de traduções de validação
cat > lang/pt_BR/validation.php << 'EOF'
<?php
return [
    // Traduções de validação...
];
EOF

# 4. Sincronizar com container Docker
docker cp lang/pt_BR vote_app:/var/www/html/lang/

# 5. Limpar cache do Laravel
docker exec vote_app php artisan optimize:clear
```

**Verificação:**
- Confirmar que APP_LOCALE=pt_BR está definido no .env
- Verificar se os arquivos foram criados corretamente
- Testar paginação em páginas como "Gerenciar Usuários"

---

### 3. Problemas de Permissão de Banco de Dados SQLite

### Sintomas
- Erro HTTP 500 ao tentar fazer operações de escrita (PUT, POST, DELETE)
- Logs mostram: `SQLSTATE[HY000]: General error: 8 attempt to write a readonly database`
- Aplicação funciona para operações de leitura (GET)

### Causa
O arquivo `database.sqlite` é criado com permissões do usuário host (UID 1000), mas o PHP-FPM roda como usuário `www-data` dentro do container, causando conflito de permissões.

### Solução
Adicionar comandos de correção de permissões no script `init.sh`:

```bash
# Corrigir permissões do banco de dados
echo "Corrigindo permissões do banco de dados..."
chown www-data:www-data /var/www/html/database/database.sqlite
chown www-data:www-data /var/www/html/database/
chmod 664 /var/www/html/database/database.sqlite
chmod 775 /var/www/html/database/
```

### Verificação
Para verificar se as permissões estão corretas:

```bash
# Verificar permissões do arquivo de banco
docker exec vote_app ls -la /var/www/html/database/database.sqlite

# Deve mostrar:
# -rw-rw-r-- 1 www-data www-data [tamanho] [data] database.sqlite

# Testar escrita no banco
docker exec vote_app sqlite3 /var/www/html/database/database.sqlite "CREATE TABLE IF NOT EXISTS test_table (id INTEGER PRIMARY KEY, name TEXT); INSERT INTO test_table (name) VALUES ('test_write');"
```

## Problemas de Configuração de Usuário

### Sintomas
- Erro: `getpwnam("www") failed`
- Supervisor não consegue iniciar serviços
- Nginx falha ao iniciar

### Causa
Alpine Linux não possui usuário `www` por padrão, apenas `www-data`.

### Solução
Alterar todas as referências de `www` para `www-data` nos arquivos:

1. **supervisord.conf**:
```ini
[program:laravel-worker]
user=www-data
```

2. **nginx.conf**:
```nginx
user www-data;
```

## Problemas de Diretório de Logs

### Sintomas
- Supervisor falha ao iniciar
- Erro: `cannot open file for writing`

### Causa
Diretório `/var/log/supervisor` não existe no container.

### Solução
Criar diretório no `init.sh`:

```bash
# Criar diretório de logs do supervisor
mkdir -p /var/log/supervisor
```

## Verificação Geral do Deploy

### Status dos Containers
```bash
docker-compose ps
```

### Logs da Aplicação
```bash
# Logs gerais
docker logs vote_app --tail 50

# Logs específicos do Laravel
docker exec vote_app tail -f /var/www/html/storage/logs/laravel.log
```

### Teste de Conectividade
```bash
# Teste básico
curl -I http://localhost:8011

# Deve retornar HTTP/1.1 302 Found (redirecionamento para login)
```

### Verificação de Serviços
```bash
# Verificar processos rodando no container
docker exec vote_app ps aux

# Deve mostrar:
# - nginx: master process
# - php-fpm: master process
# - php-fpm: pool www (como www-data)
# - supervisord
# - artisan queue:work (como www-data)
```

## Rebuild Completo

Se houver problemas persistentes, fazer rebuild completo:

```bash
# Parar container
docker stop vote_app

# Rebuild da imagem
docker-compose build app

# Iniciar container
docker-compose up -d app

# Aguardar inicialização (10-15 segundos)
sleep 15

# Verificar status
docker-compose ps
docker logs vote_app --tail 20
```