# Regras de Validação e Tratamento de Erros

## Visão Geral

Este documento define as regras de validação e tratamento de erros implementadas no sistema de votação, seguindo os padrões MALT (Modeling, Action, Logic, Testing).

## 1. Form Requests

### 1.1 VoteRequest

**Localização:** `app/Http/Requests/VoteRequest.php`

**Responsabilidades:**
- Validar dados de entrada para votação
- Aplicar regras de negócio específicas de votação
- Retornar mensagens de erro personalizadas

**Regras de Validação:**
```php
'photo_id' => [
    'required',
    'exists:photos,id',
    // Validações customizadas:
    // - Projeto deve estar ativo
    // - Usuário não pode votar duas vezes na mesma foto
    // - Usuário não pode exceder 10 votos totais
]
```

### 1.2 UnvoteRequest

**Localização:** `app/Http/Requests/UnvoteRequest.php`

**Responsabilidades:**
- Validar dados de entrada para remoção de voto
- Verificar se o usuário realmente votou na foto

**Regras de Validação:**
```php
'photo_id' => [
    'required',
    'exists:photos,id',
    // Validação customizada:
    // - Usuário deve ter votado na foto
]
```

## 2. Exceções Customizadas

### 2.1 VotingException

**Localização:** `app/Exceptions/VotingException.php`

**Métodos Estáticos:**
- `alreadyVoted()`: Usuário já votou na foto
- `voteLimit()`: Limite de 10 votos atingido
- `inactiveProject()`: Projeto não está ativo
- `notVoted()`: Usuário não votou na foto
- `photoNotFound()`: Foto não existe

**Características:**
- Herda de `Exception`
- Código HTTP padrão: 422 (Unprocessable Entity)
- Método `render()` personalizado para retornar resposta adequada

## 3. Services

### 3.1 VotingService

**Localização:** `app/Services/VotingService.php`

**Responsabilidades:**
- Centralizar lógica de negócio de votação
- Aplicar regras de negócio de forma consistente
- Gerenciar transações de banco de dados
- Lançar exceções específicas quando necessário

**Métodos Principais:**
- `vote(Photo $photo): Vote` - Registrar voto
- `unvote(Photo $photo): bool` - Remover voto
- `getUserVotes(int $userId): array` - Obter votos do usuário
- `getUserVoteCount(int $userId): int` - Contar votos do usuário
- `hasUserVoted(int $userId, int $photoId): bool` - Verificar se votou

## 4. Regras de Negócio

### 4.1 Votação

1. **Projeto Ativo**: Só é possível votar em projetos ativos
2. **Voto Único**: Usuário não pode votar duas vezes na mesma foto
3. **Limite de Votos**: Máximo de 10 votos por usuário
4. **Autenticação**: Apenas usuários autenticados podem votar

### 4.2 Remoção de Voto

1. **Voto Existente**: Só é possível remover voto que existe
2. **Propriedade**: Usuário só pode remover seus próprios votos

## 5. Tratamento de Erros

### 5.1 Estratégia Geral

- **Form Requests**: Validação de entrada e regras básicas
- **Services**: Lógica de negócio e exceções específicas
- **Controllers**: Captura de exceções e resposta adequada
- **Exceções Customizadas**: Mensagens específicas e códigos HTTP apropriados

### 5.2 Fluxo de Tratamento

```
Request → Form Request (validação) → Controller → Service (lógica) → Exception (se erro)
                ↓                        ↓              ↓              ↓
            Erro de validação    Captura exceção   Lança exceção   Renderiza resposta
```

### 5.3 Tipos de Resposta

- **Sucesso**: Redirect com mensagem de sucesso
- **Erro de Validação**: Redirect com erros de validação
- **Erro de Negócio**: Redirect com mensagem de erro específica
- **Erro Interno**: Log do erro + mensagem genérica

## 6. Boas Práticas Implementadas

### 6.1 Separação de Responsabilidades

- **Form Requests**: Validação de entrada
- **Services**: Lógica de negócio
- **Controllers**: Orquestração e resposta
- **Exceptions**: Tratamento específico de erros

### 6.2 Transações de Banco

- Uso de `DB::transaction()` em operações críticas
- Rollback automático em caso de exceção

### 6.3 Mensagens Consistentes

- Mensagens em português para o usuário final
- Códigos de erro padronizados
- Logs detalhados para debugging

### 6.4 Testabilidade

- Services injetados via dependency injection
- Métodos pequenos e focados
- Exceções específicas facilitam testes

## 7. Exemplos de Uso

### 7.1 Controller Refatorado

```php
public function vote(VoteRequest $request, Photo $photo)
{
    try {
        $this->votingService->vote($photo);
        $userVotes = $this->votingService->getUserVotes(Auth::id());
        
        return back()->with([
            'success' => 'Voto registrado com sucesso!',
            'userVotes' => $userVotes
        ]);
    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()]);
    }
}
```

### 7.2 Validação Customizada

```php
'photo_id' => [
    'required',
    'exists:photos,id',
    function ($attribute, $value, $fail) {
        if ($this->businessRuleViolated($value)) {
            $fail('Mensagem de erro específica');
        }
    },
]
```

## 8. Próximos Passos

1. Implementar testes unitários para todas as validações
2. Adicionar logging estruturado para auditoria
3. Criar middleware para rate limiting de votação
4. Implementar cache para contadores de votos
5. Adicionar métricas de performance