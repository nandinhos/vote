# Guia de Troubleshooting - Deploy Docker

## 🚨 Problema Resolvido: Erro 500 após Deploy

### Resumo do Caso
**Data:** 25/09/2025  
**Erro:** HTTP 500 Internal Server Error  
**Causa Principal:** Permissões incorretas nos diretórios de cache e storage  

### Sintomas Observados
- ✅ Docker containers rodando normalmente
- ✅ PHP-FPM funcionando
- ✅ Nginx respondendo
- ❌ Laravel retornando erro 500
- ❌ Logs mostrando problemas de permissão

### Diagnóstico Realizado

#### 1. Verificação de Logs
```bash
# Comando usado para verificar logs do Laravel
docker-compose exec app tail -f /var/www/html/storage/logs/laravel.log

# Resultado: Erros de permissão nos diretórios de cache
```

#### 2. Teste de Conectividade
```bash
# Teste básico de conectividade
curl -I http://localhost:8011

# Resultado inicial: HTTP/1.1 500 Internal Server Error
# Resultado após correção: HTTP/1.1 302 Found (redirecionamento para /login)
```

#### 3. Verificação de Conflitos de Porta
```bash
# Verificar serviços nas portas 80 e 8011
sudo netstat -tlnp | grep -E ':80|:8011'

# Descoberto: Apache local rodando na porta 80
```

### Solução Implementada

#### Passo 1: Parar Serviços Conflitantes
```bash
sudo systemctl stop apache2
```

#### Passo 2: Corrigir Permissões
```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
```

#### Passo 3: Limpar Caches
```bash
docker-compose exec app php artisan optimize:clear
docker-compose exec app composer dump-autoload
```

#### Passo 4: Reiniciar Containers
```bash
docker-compose restart
```

### Resultado
✅ **Aplicação funcionando perfeitamente**  
✅ **Redirecionamento para /login funcionando**  
✅ **Assets Vite carregando corretamente**  
✅ **Cookies de sessão sendo configurados**  

## 📋 Checklist de Prevenção

### Antes do Deploy
- [ ] Verificar se não há Apache/Nginx local rodando
- [ ] Confirmar que as portas 8011 e 80 estão livres
- [ ] Verificar se o arquivo .env existe e está configurado
- [ ] Confirmar que as dependências foram instaladas

### Durante o Deploy
- [ ] Executar o script deploy.sh
- [ ] Aguardar containers subirem completamente
- [ ] Verificar logs em tempo real: `docker-compose logs -f`

### Após o Deploy
- [ ] Corrigir permissões automaticamente:
  ```bash
  docker-compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
  ```
- [ ] Limpar caches:
  ```bash
  docker-compose exec app php artisan optimize:clear
  ```
- [ ] Testar conectividade:
  ```bash
  curl -I http://localhost:8011
  ```
- [ ] Verificar se retorna 302 (redirecionamento para login)

## 🔧 Comandos de Diagnóstico Rápido

### Status dos Containers
```bash
docker-compose ps
```

### Logs em Tempo Real
```bash
docker-compose logs -f app
```

### Verificar Permissões
```bash
docker-compose exec app ls -la /var/www/html/storage
docker-compose exec app ls -la /var/www/html/bootstrap/cache
```

### Teste de PHP
```bash
# Criar arquivo de teste
docker-compose exec app echo "<?php phpinfo(); ?>" > /var/www/html/public/test.php

# Testar
curl http://localhost:8011/test.php

# Remover arquivo de teste
docker-compose exec app rm /var/www/html/public/test.php
```

### Verificar Processos no Container
```bash
docker-compose exec app ps aux
```

## 🎯 Lições Aprendidas

1. **Permissões são críticas** - Laravel precisa de permissões específicas nos diretórios de storage e cache
2. **Conflitos de porta são comuns** - Sempre verificar se há serviços locais rodando
3. **Cache pode causar problemas** - Limpar cache após mudanças significativas
4. **Diagnóstico sistemático** - Seguir uma ordem lógica de verificação

## 📝 Script de Deploy Melhorado

Baseado nesta experiência, o script de deploy deve incluir:

```bash
#!/bin/bash

# 1. Verificar conflitos de porta
echo "Verificando conflitos de porta..."
if netstat -tlnp | grep -q ":80 "; then
    echo "⚠️  Serviço rodando na porta 80. Considere parar o Apache local:"
    echo "sudo systemctl stop apache2"
fi

# 2. Deploy normal
docker-compose up -d --build

# 3. Aguardar containers subirem
echo "Aguardando containers..."
sleep 10

# 4. Corrigir permissões automaticamente
echo "Corrigindo permissões..."
docker-compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 5. Limpar caches
echo "Limpando caches..."
docker-compose exec app php artisan optimize:clear

# 6. Teste final
echo "Testando aplicação..."
if curl -s -I http://localhost:8011 | grep -q "302"; then
    echo "✅ Deploy realizado com sucesso!"
    echo "🌐 Aplicação disponível em: http://localhost:8011"
else
    echo "❌ Problema no deploy. Verificar logs:"
    echo "docker-compose logs app"
fi
```

---
**Documento criado em:** 25/09/2025  
**Última atualização:** 25/09/2025  
**Status:** Problema resolvido ✅