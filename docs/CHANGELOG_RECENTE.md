# Changelog - Mudanças Recentes

## [25/01/2025] - Migração para Autenticação SARAM e Configuração Docker

### 🔐 Mudanças Críticas de Autenticação

#### 🆔 Migração de Email para SARAM
- **Substituição completa do campo `email` por `saram`**
  - Campo `saram` agora é o identificador único para autenticação
  - Atualização em todos os modelos, controladores e formulários
  - Migração de banco de dados para suportar autenticação militar
  - Validação de unicidade implementada para campo `saram`

#### 🔧 Correções no VotingService
- **Validação de projetos ativos**
  - Implementada verificação se projeto está ativo antes de permitir votação
  - Correção de tipos de dados (cast para integer em Auth::id())
  - Implementação de limite de votos por usuário

#### 🧪 Atualização de Testes Automatizados
- **Correção de testes de autenticação**
  - Atualização para usar campo `saram` ao invés de `email`
  - Desabilitação de testes de registro (funcionalidade removida)
  - Correção de testes de votação para projetos inativos

### 🐳 Configuração Docker Completa

#### 📦 Containerização
- **Dockerfile otimizado para produção**
  - Configuração multi-stage para Laravel + Vue.js
  - Nginx + PHP-FPM + Supervisor
  - Otimização de imagens e dependências
  - Suporte a SQLite e PostgreSQL

#### 🚀 Deploy Automatizado
- **Script de deploy completo (`deploy.sh`)**
  - Verificação de dependências (Docker, Docker Compose)
  - Geração automática de APP_KEY
  - Execução de migrações e seeders
  - Otimização de cache e assets
  - Testes de conectividade

#### ⚙️ Configurações de Ambiente
- **Arquivos de configuração Docker**
  - `nginx.conf` - Configuração otimizada do Nginx
  - `supervisord.conf` - Gerenciamento de processos
  - `php.ini` - Configurações PHP para produção
  - `.env.production` - Template para ambiente de produção

### 💾 Backup e Segurança
- **Backup automático do banco de dados**
  - Backup em formato SQLite (.sqlite)
  - Dump SQL para portabilidade (.sql)
  - Versionamento com timestamp

## [25/01/2025] - Melhorias de UX e Persistência de Filtros

### ✨ Novas Funcionalidades

#### 🔍 Sistema de Filtros Aprimorado
- **Persistência de filtros após deletar foto**
  - Filtros de busca e projeto agora são mantidos após exclusão de fotos
  - Implementado no backend (`PhotoController::destroy()`) e frontend (`Index.vue`)
  - Melhora significativa na experiência do usuário administrativo

#### 🧹 Botão "Limpar Filtros"
- **Botão condicional para resetar filtros**
  - Aparece apenas quando há filtros ativos
  - Reseta busca por legenda e filtro de projeto
  - Redireciona para página limpa mantendo a navegação fluida

### 🎨 Melhorias de Interface

#### 📝 Legendas Vazias
- **Remoção do texto "Sem legenda"**
  - Substituído por espaço em branco para melhor visual
  - Implementado com `&nbsp;` para manter alinhamento perfeito
  - Aplicado em todas as páginas: Dashboard, Votação, Admin

#### 📱 Alinhamento Visual
- **Manutenção de layout consistente**
  - Uso de espaços não-quebráveis (`&nbsp;`) 
  - Preserva estrutura visual mesmo sem conteúdo
  - Melhora a experiência visual em todas as telas

### 🔧 Correções Técnicas

#### Backend (Laravel)
- **PhotoController.php**
  - Método `destroy()` atualizado para preservar filtros
  - Redirecionamento com parâmetros de query mantidos

#### Frontend (Vue.js)
- **Admin/Photos/Index.vue**
  - Método `deletePhoto()` envia filtros atuais
  - Função `clearFilters()` implementada
  - Botão condicional baseado em estado de filtros

- **Dashboard.vue**
  - Substituição de `{{ photo.caption || 'Sem legenda' }}` por `v-html="photo.caption || '&nbsp;'"`

- **Voting/Show.vue**
  - Atualização similar para legendas vazias

- **Admin/Projects/Show.vue**
  - Consistência na exibição de legendas

### 🧪 Testes Realizados

#### Funcionalidades Testadas
- ✅ Persistência de filtros após deletar foto
- ✅ Botão limpar filtros funcionando
- ✅ Legendas vazias com espaçamento correto
- ✅ Alinhamento visual mantido
- ✅ Responsividade em todas as telas

#### Cenários de Erro Corrigidos
- ✅ Filtros perdidos após ações administrativas
- ✅ Layout quebrado com legendas vazias
- ✅ Inconsistência visual entre páginas

### 📊 Impacto das Mudanças

#### Experiência do Usuário
- **Administradores**: Filtros persistentes melhoram eficiência
- **Usuários finais**: Interface mais limpa e consistente
- **Responsividade**: Mantida em todas as alterações

#### Performance
- **Sem impacto negativo**: Mudanças são principalmente de UX
- **Otimização**: Redução de recarregamentos desnecessários
- **Manutenibilidade**: Código mais limpo e consistente

### 🔄 Arquivos Modificados

```
app/Http/Controllers/Admin/PhotoController.php
resources/js/Pages/Admin/Photos/Index.vue
resources/js/Pages/Dashboard.vue
resources/js/Pages/Voting/Show.vue
resources/js/Pages/Admin/Projects/Show.vue
docs/STATUS_FUNCIONALIDADES.md
docs/PROGRESSO_PROJETO.md
```

### 📋 Próximos Passos Sugeridos

1. **Testes automatizados** para as novas funcionalidades
2. **Documentação de API** atualizada
3. **Otimizações de performance** se necessário
4. **Feedback de usuários** sobre as melhorias

---

**Desenvolvido com foco na experiência do usuário e manutenibilidade do código.**