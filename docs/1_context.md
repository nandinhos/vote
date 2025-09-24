# 1. Contexto e Requisitos Funcionais

## Visão Geral do Sistema

O **Sistema de Votação de Fotos** é uma aplicação web desenvolvida para organizações militares que permite a realização de votações estruturadas em fotos organizadas por projetos. O sistema implementa autenticação baseada em SARAM (número de registro militar) e controle de acesso baseado em roles.

## Objetivo Principal

Facilitar a votação democrática e transparente em concursos fotográficos militares, garantindo que cada eleitor vote exatamente uma vez seguindo regras específicas de distribuição de votos por projeto.

## Personas e Perfis de Acesso

### 1. Administrador (Admin)
- **Responsabilidades**: Gestão completa do sistema
- **Permissões**:
  - CRUD completo de projetos
  - Upload e gestão de fotos
  - Visualização de dashboard com resultados em tempo real
  - Gestão de usuários
  - Acesso a relatórios e estatísticas

### 2. Eleitor (Voter)
- **Responsabilidades**: Participação no processo de votação
- **Permissões**:
  - Visualização de projetos ativos e suas fotos
  - Votação única seguindo regras específicas
  - Visualização de confirmação pós-votação
- **Restrições**:
  - Não pode visualizar resultados
  - Não pode votar mais de uma vez
  - Deve seguir regras de distribuição de votos

## Requisitos Funcionais

### RF001 - Autenticação por SARAM
- O sistema deve autenticar usuários exclusivamente pelo número SARAM
- Não deve utilizar email como identificador
- Deve manter sessão segura pós-autenticação

### RF002 - Gestão de Projetos (Admin)
- Criar, editar, visualizar e excluir projetos
- Ativar/desativar projetos para votação
- Cada projeto deve ter nome único e descrição

### RF003 - Gestão de Fotos (Admin)
- Upload múltiplo de fotos por projeto
- Associação obrigatória de foto a projeto
- Adição de legendas opcionais
- Exclusão de fotos

### RF004 - Processo de Votação (Eleitor)
- Visualização de todas as fotos de projetos ativos
- Seleção de exatamente 10 fotos no total
- Obrigatoriedade de votar em pelo menos 1 foto por projeto ativo
- Votação única por usuário
- Confirmação visual do número de fotos selecionadas

### RF005 - Dashboard Administrativo
- Ranking em tempo real das fotos mais votadas
- Estatísticas gerais do sistema
- Visualização de projetos recentes
- Histórico de votos

### RF006 - Controle de Acesso
- Middleware de role para proteção de rotas
- Redirecionamento baseado em perfil após login
- Bloqueio de acesso a resultados para eleitores

## Regras de Negócio

### RN001 - Regra de Votação Única
- Cada usuário pode votar apenas uma vez no sistema
- Após votar, o acesso à página de votação deve ser bloqueado

### RN002 - Regra de Distribuição de Votos
- O eleitor deve selecionar exatamente 10 fotos
- Deve votar em pelo menos 1 foto de cada projeto ativo
- Não pode votar na mesma foto mais de uma vez

### RN003 - Regra de Projetos Ativos
- Apenas projetos marcados como ativos aparecem na votação
- Projetos inativos não devem ser visíveis para eleitores

### RN004 - Regra de Integridade de Dados
- Constraint única composta (user_id, photo_id) na tabela votes
- Cascata de exclusão para manter integridade referencial

## Fluxos Principais

### Fluxo de Votação
1. Eleitor faz login com SARAM
2. Sistema verifica se já votou
3. Se não votou, exibe galeria de fotos por projeto
4. Eleitor seleciona fotos (máx. 10, mín. 1 por projeto)
5. Sistema valida regras de votação
6. Registra votos no banco de dados
7. Exibe confirmação de voto computado
8. Bloqueia acesso futuro à votação

### Fluxo Administrativo
1. Admin faz login com SARAM
2. Acessa dashboard com estatísticas
3. Gerencia projetos e fotos
4. Monitora votação em tempo real
5. Visualiza relatórios e rankings

## Requisitos Não Funcionais

### RNF001 - Segurança
- Autenticação obrigatória para todas as funcionalidades
- Controle de acesso baseado em roles
- Proteção contra votação múltipla
- Validação server-side de todas as regras

### RNF002 - Performance
- Interface responsiva para galeria de fotos
- Carregamento otimizado de imagens
- Dashboard com atualizações em tempo real

### RNF003 - Usabilidade
- Interface intuitiva para votação
- Feedback visual do progresso de votação
- Mensagens claras de erro e sucesso

### RNF004 - Compatibilidade
- Suporte a navegadores modernos
- Interface responsiva para dispositivos móveis