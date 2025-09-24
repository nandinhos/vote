# Melhores Práticas - Sistema de Votação

## Visão Geral

Este documento consolida as melhores práticas específicas implementadas no sistema de votação, baseadas na metodologia MALT e nas necessidades do projeto.

## 1. Arquitetura e Organização

### 1.1 Estrutura de Diretórios

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/           # Controllers administrativos
│   │   ├── Auth/            # Controllers de autenticação
│   │   └── VotingController.php
│   ├── Requests/            # Form Requests para validação
│   └── Middleware/          # Middleware customizado
├── Models/                  # Eloquent Models
├── Services/                # Lógica de negócio
├── Exceptions/              # Exceções customizadas
└── Policies/                # Políticas de autorização
```

### 1.2 Princípios Arquiteturais

- **Single Responsibility**: Cada classe tem uma responsabilidade específica
- **Dependency Injection**: Injeção de dependências via constructor
- **Interface Segregation**: Interfaces pequenas e específicas
- **Open/Closed**: Aberto para extensão, fechado para modificação

## 2. Padrões de Código

### 2.1 Controllers

**✅ Correto:**
```php
class VotingController extends Controller
{
    protected VotingService $votingService;

    public function __construct(VotingService $votingService)
    {
        $this->votingService = $votingService;
    }

    public function vote(VoteRequest $request, Photo $photo)
    {
        try {
            $this->votingService->vote($photo);
            return back()->with('success', 'Voto registrado!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
```

**❌ Incorreto:**
```php
public function vote(Request $request, Photo $photo)
{
    // Validação manual no controller
    if (!$photo->project->is_active) {
        return back()->withErrors(['error' => 'Projeto inativo']);
    }
    
    // Lógica de negócio no controller
    Vote::create(['user_id' => Auth::id(), 'photo_id' => $photo->id]);
}
```

### 2.2 Services

**✅ Correto:**
```php
class VotingService
{
    public function vote(Photo $photo): Vote
    {
        return DB::transaction(function () use ($photo) {
            $this->validateVote($photo, Auth::id());
            return Vote::create([
                'user_id' => Auth::id(),
                'photo_id' => $photo->id
            ]);
        });
    }

    private function validateVote(Photo $photo, int $userId): void
    {
        if (!$photo->project->is_active) {
            throw VotingException::inactiveProject();
        }
        // Outras validações...
    }
}
```

### 2.3 Form Requests

**✅ Correto:**
```php
class VoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'photo_id' => [
                'required',
                'exists:photos,id',
                function ($attribute, $value, $fail) {
                    // Validação customizada com lógica específica
                }
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'photo_id.required' => 'A foto é obrigatória.',
            'photo_id.exists' => 'A foto selecionada não existe.',
        ];
    }
}
```

## 3. Tratamento de Erros

### 3.1 Exceções Customizadas

**✅ Padrão Implementado:**
```php
class VotingException extends Exception
{
    public static function alreadyVoted(): self
    {
        return new self('Você já votou nesta foto.');
    }

    public function render()
    {
        return back()->withErrors(['error' => $this->getMessage()]);
    }
}
```

### 3.2 Hierarquia de Tratamento

1. **Form Request**: Validação de entrada
2. **Service**: Lógica de negócio e exceções específicas
3. **Controller**: Captura e resposta
4. **Exception Handler**: Tratamento global

## 4. Banco de Dados

### 4.1 Queries Otimizadas

**✅ Correto:**
```php
// Eager loading para evitar N+1
$projects = Project::active()
    ->with(['photos' => function($query) {
        $query->withCount('votes');
    }])
    ->get();

// whereHas para filtros com relacionamentos
$photos = Photo::whereHas('votes')->get();
```

**❌ Incorreto:**
```php
// N+1 problem
$projects = Project::active()->get();
foreach ($projects as $project) {
    $project->photos; // Query adicional para cada projeto
}

// having sem GROUP BY (incompatível com SQLite)
$photos = Photo::having('votes_count', '>', 0)->get();
```

### 4.2 Transações

**✅ Correto:**
```php
public function vote(Photo $photo): Vote
{
    return DB::transaction(function () use ($photo) {
        $this->validateVote($photo, Auth::id());
        return Vote::create([...]);
    });
}
```

## 5. Frontend (Vue.js)

### 5.1 Componentes

**✅ Estrutura Recomendada:**
```vue
<template>
    <!-- Template limpo e semântico -->
</template>

<script setup>
// Composition API
import { ref, computed } from 'vue'

// Props tipadas
const props = defineProps({
    photos: Array,
    userVotes: Array
})

// Estado reativo
const loading = ref(false)

// Computed properties
const votedPhotos = computed(() => {
    return props.photos.filter(photo => 
        props.userVotes.includes(photo.id)
    )
})
</script>
```

### 5.2 Comunicação com Backend

**✅ Correto:**
```javascript
// Usando Inertia.js para navegação
import { router } from '@inertiajs/vue3'

const vote = (photoId) => {
    router.post(`/vote/${photoId}`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Feedback de sucesso
        },
        onError: (errors) => {
            // Tratamento de erro
        }
    })
}
```

## 6. Segurança

### 6.1 Autenticação e Autorização

**✅ Implementado:**
```php
// Middleware customizado
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check() || Auth::user()->role !== $role) {
            abort(403, 'Acesso negado.');
        }
        return $next($request);
    }
}

// Uso em rotas
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('admin/projects', ProjectController::class);
});
```

### 6.2 Validação de Entrada

- **Sempre usar Form Requests** para validação
- **Sanitização automática** via Laravel
- **Validação de CSRF** habilitada
- **Rate limiting** em endpoints sensíveis

## 7. Performance

### 7.1 Otimizações Implementadas

- **Eager Loading**: Carregamento antecipado de relacionamentos
- **Query Optimization**: Uso de `withCount`, `whereHas`
- **Caching**: Cache de contadores quando apropriado
- **Pagination**: Para listas grandes

### 7.2 Monitoramento

```php
// Log de performance em operações críticas
Log::info('Vote registered', [
    'user_id' => $userId,
    'photo_id' => $photoId,
    'execution_time' => microtime(true) - $startTime
]);
```

## 8. Testes

### 8.1 Estratégia de Testes

- **Unit Tests**: Services e Models
- **Feature Tests**: Controllers e integração
- **Browser Tests**: Fluxos críticos

### 8.2 Exemplo de Teste

```php
class VotingServiceTest extends TestCase
{
    public function test_user_can_vote_for_photo()
    {
        $user = User::factory()->create();
        $photo = Photo::factory()->create();
        
        $this->actingAs($user);
        
        $vote = $this->votingService->vote($photo);
        
        $this->assertInstanceOf(Vote::class, $vote);
        $this->assertEquals($user->id, $vote->user_id);
        $this->assertEquals($photo->id, $vote->photo_id);
    }
}
```

## 9. Documentação

### 9.1 Padrões de Documentação

- **PHPDoc** em todos os métodos públicos
- **README** atualizado com instruções
- **Changelog** para mudanças importantes
- **API Documentation** para endpoints

### 9.2 Comentários de Código

**✅ Correto:**
```php
/**
 * Vote for a photo.
 * 
 * @param Photo $photo The photo to vote for
 * @return Vote The created vote instance
 * @throws VotingException When business rules are violated
 */
public function vote(Photo $photo): Vote
```

## 10. Deploy e Ambiente

### 10.1 Configurações de Ambiente

- **Variáveis de ambiente** para configurações sensíveis
- **Logs estruturados** para debugging
- **Health checks** para monitoramento
- **Backup automático** do banco de dados

### 10.2 CI/CD

```yaml
# Exemplo de pipeline
test:
  script:
    - composer install
    - php artisan test
    - npm run build

deploy:
  script:
    - php artisan migrate --force
    - php artisan config:cache
    - php artisan route:cache
```

## 11. Checklist de Qualidade

### 11.1 Antes de Commit

- [ ] Testes passando
- [ ] Code style verificado
- [ ] Documentação atualizada
- [ ] Logs removidos
- [ ] Performance verificada

### 11.2 Antes de Deploy

- [ ] Migrations testadas
- [ ] Backup realizado
- [ ] Rollback plan definido
- [ ] Monitoramento configurado
- [ ] Health checks funcionando

## 12. Próximas Melhorias

1. **Implementar cache Redis** para contadores
2. **Adicionar rate limiting** por usuário
3. **Criar dashboard de métricas** em tempo real
4. **Implementar notificações** push
5. **Adicionar testes de carga** automatizados