# Changelog - Sistema de Votação

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

## [1.1.0] - 2025-09-30

### 🔧 Corrigido
- **Rate Limiting 503 Error** - Ajustada configuração Nginx muito restritiva que causava erro 503 em desenvolvimento
  - Alterado de 5 req/min (burst 3) para 60 req/min (burst 10)
  - Arquivo: `docker/nginx.conf`
- **Traduções de Paginação** - Implementado suporte completo ao português brasileiro
  - Criados arquivos `lang/pt_BR/pagination.php` e `lang/pt_BR/validation.php`
  - Textos "Previous/Next" agora aparecem como "Anterior/Próximo"
- **Reset de Votos** - Implementada funcionalidade para limpar todos os votos via Laravel Tinker
  - Comando: `App\Models\Vote::truncate()`

### ✨ Melhorado
- **Configuração de Desenvolvimento** - Rate limiting otimizado para ambiente de desenvolvimento
- **Experiência do Usuário** - Interface completamente traduzida para português brasileiro
- **Administração** - Facilidade para reset de dados de votação

---

## [1.0.1] - 2024-09-24

### 🔧 Corrigido
- **Validação de SARAM no registro** - Corrigida validação incorreta de email no RegisteredUserController que impedia registro com SARAM de 7 dígitos
- **Testes de autenticação falhando** - Resolvidos problemas de CSRF em testes que faziam requisições POST diretas
- **TypeError em VotingService::getUserVotes()** - Corrigido erro onde `Auth::id()` retornava string mas método esperava integer
- **Validação desnecessária em VoteRequest** - Removida validação conflitante de `photo_id` que interferia com route model binding
- **Erro "A foto é obrigatória" no unvote** - Corrigida validação desnecessária em UnvoteRequest que impedia remoção de votos

### ✨ Melhorado
- **Sistema de Autenticação** - Implementado suporte completo para SARAM de 7 dígitos conforme especificação
- **Testes Automatizados** - Configurados testes de registro funcionando corretamente com middleware apropriado
- **Type Safety** - Implementado cast explícito de `Auth::id()` para integer em VotingController
- **Route Model Binding** - Otimizado uso do Laravel route model binding para parâmetros de foto
- **Validação de Requests** - Simplificadas validações removendo campos redundantes

### 🧪 Testes
- **RegistrationTest** - Corrigido teste de registro de usuários com desabilitação apropriada de middleware CSRF
- **Usuários de Teste** - Criados usuários admin e voter para testes de funcionalidade

### 📝 Documentação
- Criado arquivo de progresso do projeto
- Documentadas todas as correções implementadas
- Adicionadas boas práticas seguidas

---

## [1.0.0] - 2024-01-XX - Versão Inicial

### ✨ Adicionado
- **Sistema de Votação Completo**
  - Votação em múltiplas fotos
  - Remoção de votos (unvote)
  - Contagem de votos em tempo real
  - Validações de negócio

- **Autenticação de Usuários**
  - Sistema de login/logout
  - Registro de novos usuários
  - Middleware de proteção

- **Interface de Usuário**
  - Gallery responsiva com Vue.js
  - Modal de visualização de fotos
  - Feedback visual para ações
  - Estados de loading

- **Arquitetura Backend**
  - Controllers organizados (VotingController, PhotoController, ProjectController)
  - Services para lógica de negócio (VotingService)
  - Models com relacionamentos (User, Photo, Project, Vote)
  - Form Requests para validação

- **Arquitetura Frontend**
  - Vue.js com Inertia.js
  - Componentes reutilizáveis
  - Layouts responsivos
  - Integração com Laravel backend

### 🛡️ Segurança
- Proteção CSRF implementada
- Sanitização de entrada de dados
- Middleware de autenticação
- Proteção contra SQL Injection via Eloquent

### 🏗️ Infraestrutura
- Laravel 10.x
- Vue.js 3.x
- Inertia.js para SPA
- SQLite para desenvolvimento
- Vite para build de assets

---

## Detalhes das Correções Recentes

### TypeError em VotingService::getUserVotes()
**Arquivo:** `app/Http/Controllers/VotingController.php`
**Linhas:** 115, 127
```php
// Antes
$userVotes = $this->votingService->getUserVotes(Auth::id());

// Depois
$userVotes = $this->votingService->getUserVotes((int)Auth::id());
```

### Validação em VoteRequest
**Arquivo:** `app/Http/Requests/VoteRequest.php`
```php
// Removido
'photo_id' => 'required|exists:photos,id',

// Mantido apenas validação de negócio no VotingService
```

### Validação em UnvoteRequest
**Arquivo:** `app/Http/Requests/UnvoteRequest.php`
```php
// Removido
'photo_id' => 'required|exists:photos,id',

// Route model binding cuida da validação de existência
```

## Próximas Versões Planejadas

### [1.1.0] - Melhorias de Performance
- [ ] Cache de contagem de votos
- [ ] Otimização de queries
- [ ] Lazy loading de imagens

### [1.2.0] - Funcionalidades Avançadas
- [ ] Sistema de comentários
- [ ] Categorias de fotos
- [ ] Ranking de usuários

### [2.0.0] - Refatoração Major
- [ ] API REST completa
- [ ] Testes automatizados
- [ ] Deploy em produção