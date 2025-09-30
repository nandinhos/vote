# Boas Práticas - Deploy Docker Laravel

Este documento estabelece as melhores práticas para deploy de aplicações Laravel em containers Docker.

## 1. Gerenciamento de Permissões

### 1.1 Usuários e Grupos

**✅ FAZER:**
- Sempre usar `www-data` como usuário para serviços web (Nginx, PHP-FPM)
- Manter consistência de UID/GID entre host e container quando necessário
- Definir usuário explicitamente em todos os serviços

**❌ NÃO FAZER:**
- Usar usuário `www` (não existe no Alpine Linux)
- Rodar serviços web como `root`
- Misturar diferentes usuários para o mesmo tipo de serviço

```bash
# ✅ Correto
user=www-data

# ❌ Incorreto
user=www
user=root
```

### 1.2 Permissões de Arquivos

**✅ FAZER:**
- Corrigir permissões no script de inicialização
- Usar `chown` e `chmod` apropriados para cada tipo de arquivo
- Verificar permissões após cada deploy

```bash
# ✅ Exemplo correto para SQLite
chown www-data:www-data /var/www/html/database/database.sqlite
chmod 664 /var/www/html/database/database.sqlite
chown www-data:www-data /var/www/html/database/
chmod 775 /var/www/html/database/
```

**❌ NÃO FAZER:**
- Assumir que permissões do host serão mantidas no container
- Usar `chmod 777` como solução rápida
- Ignorar permissões de diretórios pais

### 1.3 Banco de Dados SQLite

**✅ FAZER:**
- Sempre corrigir permissões do arquivo `.sqlite` no init
- Verificar permissões do diretório pai
- Testar operações de escrita após deploy

```bash
# ✅ Verificação de permissões
ls -la /var/www/html/database/database.sqlite
# Deve mostrar: -rw-rw-r-- 1 www-data www-data

# ✅ Teste de escrita
sqlite3 database.sqlite "INSERT INTO test_table (name) VALUES ('test');"
```

## 2. Configuração de Containers

### 2.1 Scripts de Inicialização

**✅ FAZER:**
- Usar shebang correto (`#!/bin/bash`)
- Executar migrações antes de corrigir permissões
- Criar diretórios necessários antes de iniciar serviços
- Usar `exec` para o comando final

```bash
#!/bin/bash

# Migrações
php artisan migrate --force

# Permissões
chown www-data:www-data /var/www/html/database/database.sqlite

# Diretórios
mkdir -p /var/log/supervisor

# Iniciar supervisor (último comando)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
```

**❌ NÃO FAZER:**
- Usar shebang incorreto (`#!/bin/sh` quando usar bash features)
- Corrigir permissões antes das migrações
- Esquecer de criar diretórios necessários

### 2.2 Supervisor Configuration

**✅ FAZER:**
- Definir usuário explicitamente para cada programa
- Configurar logs apropriados
- Usar `nodaemon=true` para containers
- Configurar restart automático

```ini
[program:laravel-worker]
command=php /var/www/html/artisan queue:work
user=www-data
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
```

**❌ NÃO FAZER:**
- Omitir configuração de usuário
- Usar daemon mode em containers
- Ignorar configuração de logs

### 2.3 Nginx Configuration

**✅ FAZER:**
- Definir usuário no início do arquivo
- Configurar worker_processes apropriadamente
- Usar fastcgi_pass correto para PHP-FPM

```nginx
user www-data;
worker_processes auto;

server {
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }
}
```

## 3. Processo de Deploy

### 3.1 Ordem de Operações

**✅ SEQUÊNCIA CORRETA:**
1. Build da imagem
2. Stop do container anterior
3. Start do novo container
4. Aguardar inicialização (10-15s)
5. Verificar health check
6. Testar conectividade
7. Verificar logs

```bash
# ✅ Processo completo
docker-compose build app
docker stop vote_app
docker-compose up -d app
sleep 15
docker-compose ps
curl -I http://localhost:8011
docker logs vote_app --tail 10
```

### 3.2 Verificações Pós-Deploy

**✅ FAZER:**
- Verificar status do container (`docker-compose ps`)
- Verificar health check (`healthy` status)
- Testar conectividade HTTP
- Verificar logs para erros
- Testar operações críticas

**❌ NÃO FAZER:**
- Assumir que deploy funcionou sem verificar
- Ignorar warnings nos logs
- Pular testes de conectividade

## 4. Debugging e Troubleshooting

### 4.1 Coleta de Informações

**✅ FAZER:**
- Coletar logs completos antes de fazer mudanças
- Verificar permissões de arquivos críticos
- Testar operações específicas que falharam
- Documentar erros encontrados

```bash
# ✅ Coleta de informações
docker logs vote_app --tail 50
docker exec vote_app ls -la /var/www/html/database/
docker exec vote_app ps aux
```

### 4.2 Resolução de Problemas

**✅ FAZER:**
- Identificar causa raiz antes de aplicar correções
- Testar correções em ambiente isolado
- Aplicar uma correção por vez
- Verificar se correção resolve o problema

**❌ NÃO FAZER:**
- Aplicar múltiplas correções simultaneamente
- Fazer mudanças sem entender o problema
- Ignorar logs de erro

## 5. Segurança

### 5.1 Princípios Básicos

**✅ FAZER:**
- Usar usuários não-privilegiados para serviços
- Aplicar princípio do menor privilégio
- Manter permissões restritivas mas funcionais
- Evitar executar como root

**❌ NÃO FAZER:**
- Usar `chmod 777` como solução
- Rodar serviços como root desnecessariamente
- Ignorar warnings de segurança

### 5.2 Arquivos Sensíveis

**✅ FAZER:**
- Proteger arquivos de configuração (`.env`)
- Restringir acesso ao banco de dados
- Usar permissões apropriadas para logs

```bash
# ✅ Permissões seguras
chmod 600 .env                    # Apenas owner pode ler/escrever
chmod 664 database.sqlite         # Owner/group podem escrever, others apenas ler
chmod 755 storage/logs/           # Diretório acessível mas protegido
```

## 6. Monitoramento

### 6.1 Logs

**✅ FAZER:**
- Configurar rotação de logs
- Monitorar logs de erro regularmente
- Usar níveis de log apropriados
- Centralizar logs quando possível

### 6.2 Performance

**✅ FAZER:**
- Monitorar uso de recursos do container
- Verificar performance de queries de banco
- Monitorar tempo de resposta HTTP
- Configurar alertas para problemas críticos

```bash
# ✅ Monitoramento básico
docker stats vote_app
docker exec vote_app top
curl -w "@curl-format.txt" -o /dev/null -s http://localhost:8011
```

## 7. Backup e Recovery

### 7.1 Backup

**✅ FAZER:**
- Fazer backup regular do banco de dados
- Versionar configurações importantes
- Testar procedimentos de restore
- Documentar processo de backup

```bash
# ✅ Backup do SQLite
docker exec vote_app sqlite3 /var/www/html/database/database.sqlite ".backup /tmp/backup.sqlite"
docker cp vote_app:/tmp/backup.sqlite ./backup-$(date +%Y%m%d).sqlite
```

### 7.2 Recovery

**✅ FAZER:**
- Ter plano de recovery documentado
- Testar recovery em ambiente de teste
- Manter backups em local seguro
- Verificar integridade dos backups

## 8. Documentação

### 8.1 Manutenção da Documentação

**✅ FAZER:**
- Atualizar documentação após mudanças
- Incluir exemplos práticos
- Documentar problemas conhecidos e soluções
- Manter histórico de mudanças

**❌ NÃO FAZER:**
- Deixar documentação desatualizada
- Omitir detalhes importantes
- Assumir conhecimento prévio do leitor