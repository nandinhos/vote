# 4. Convenções e Guidelines de Desenvolvimento

## Metodologia MALT

### Aplicação dos 4 Estágios

#### M - Modelagem (Models & Database)
```php
// ✅ CORRETO - Consistência de dados
class Vote extends Model
{
    protected $fillable = ['user_id', 'photo_id'];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Relacionamentos explícitos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }
}

// ❌ INCORRETO - Falta de relacionamentos
class Vote extends Model
{
    protected $fillable = ['user_id', 'photo_id'];
    // Sem relacionamentos definidos
}
```

#### A - Ação (Controllers & Routes)
```php
// ✅ CORRETO - Controller com responsabilidade única
class VotingController extends Controller
{
    public function vote(VoteRequest $request): RedirectResponse
    {
        $this->authorize('vote', $request->photo);
        
        Vote::create([
            'user_id' => auth()->id(),
            'photo_id' => $request->photo_id,
        ]);
        
        return redirect()->back()->with('success', 'Voto registrado!');
    }
}

// ❌ INCORRETO - Lógica de negócio no controller
class VotingController extends Controller
{
    public function vote(Request $request)
    {
        // Validação manual no controller
        if (!$request->photo_id) {
            return back()->withErrors(['photo_id' => 'Required']);
        }
        
        // Lógica complexa no controller
        $user = auth()->user();
        $existingVotes = Vote::where('user_id', $user->id)->count();
        if ($existingVotes >= 10) {
            return back()->withErrors(['limit' => 'Limite excedido']);
        }
        
        Vote::create([...]);
    }
}
```

#### L - Lógica (Services & Business Rules)
```php
// ✅ CORRETO - Service com regras de negócio
class VotingService
{
    public function canUserVote(User $user): bool
    {
        return $user->votes()->count() < 10;
    }
    
    public function hasUserVotedOnPhoto(User $user, Photo $photo): bool
    {
        return $user->votes()->where('photo_id', $photo->id)->exists();
    }
    
    public function validateVotingRules(User $user, Collection $photoIds): array
    {
        $errors = [];
        
        if ($photoIds->count() !== 10) {
            $errors[] = 'Deve selecionar exatamente 10 fotos';
        }
        
        $projects = Photo::whereIn('id', $photoIds)
            ->with('project')
            ->get()
            ->pluck('project.id')
            ->unique();
            
        $activeProjects = Project::active()->count();
        if ($projects->count() < $activeProjects) {
            $errors[] = 'Deve votar em pelo menos uma foto de cada projeto';
        }
        
        return $errors;
    }
}

// ❌ INCORRETO - Lógica espalhada
// Regras de negócio no controller, model ou view
```

#### T - Teste (Testing Strategy)
```php
// ✅ CORRETO - Testes abrangentes
class VotingTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_vote_on_photo(): void
    {
        $user = User::factory()->create(['role' => 'voter']);
        $project = Project::factory()->create(['is_active' => true]);
        $photo = Photo::factory()->create(['project_id' => $project->id]);
        
        $this->actingAs($user)
            ->post(route('vote'), ['photo_id' => $photo->id])
            ->assertRedirect()
            ->assertSessionHas('success');
            
        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'photo_id' => $photo->id,
        ]);
    }
    
    public function test_user_cannot_vote_twice_on_same_photo(): void
    {
        $user = User::factory()->create(['role' => 'voter']);
        $photo = Photo::factory()->create();
        
        Vote::factory()->create([
            'user_id' => $user->id,
            'photo_id' => $photo->id,
        ]);
        
        $this->actingAs($user)
            ->post(route('vote'), ['photo_id' => $photo->id])
            ->assertStatus(422);
    }
}

// ❌ INCORRETO - Testes superficiais ou ausentes
```

## Pilares de Extensibilidade Laravel

### Service Providers
```php
// ✅ CORRETO - Service Provider organizado
class VotingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(VotingService::class);
        $this->app->bind(StatisticsService::class);
        $this->app->bind(ReportService::class);
    }
    
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views/voting', 'voting');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/voting');
        
        Gate::define('vote', [VotingPolicy::class, 'vote']);
        Gate::define('admin-dashboard', [AdminPolicy::class, 'viewDashboard']);
    }
}

// ❌ INCORRETO - Registro direto no AppServiceProvider
```

### Event/Listener System
```php
// ✅ CORRETO - Sistema de eventos desacoplado
class VoteCast extends Event
{
    public function __construct(
        public Vote $vote,
        public User $user,
        public Photo $photo
    ) {}
}

class UpdateStatistics implements ShouldQueue
{
    public function handle(VoteCast $event): void
    {
        Cache::forget("project_stats_{$event->photo->project_id}");
        Cache::forget('dashboard_stats');
    }
}

class NotifyAdmins implements ShouldQueue
{
    public function handle(VoteCast $event): void
    {
        $admins = User::where('role', 'admin')->get();
        
        Notification::send($admins, new VoteNotification($event->vote));
    }
}

// EventServiceProvider
protected $listen = [
    VoteCast::class => [
        UpdateStatistics::class,
        NotifyAdmins::class,
        LogVoteActivity::class,
    ],
];

// ❌ INCORRETO - Lógica acoplada no controller
```

### Blade Components
```php
// ✅ CORRETO - Componentes reutilizáveis
class PhotoCard extends Component
{
    public function __construct(
        public Photo $photo,
        public bool $voted = false,
        public bool $canVote = true
    ) {}
    
    public function render(): View
    {
        return view('components.photo-card');
    }
}

// resources/views/components/photo-card.blade.php
<div class="photo-card group relative overflow-hidden rounded-lg">
    <img src="{{ Storage::url($photo->file_path) }}" 
         alt="{{ $photo->caption }}"
         class="w-full h-64 object-cover transition-transform group-hover:scale-105">
    
    @if($photo->caption)
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
            <p class="text-white text-sm">{{ $photo->caption }}</p>
        </div>
    @endif
    
    @if($canVote)
        <button wire:click="vote({{ $photo->id }})" 
                class="vote-button {{ $voted ? 'voted' : '' }}">
            <x-heroicon-o-heart class="w-5 h-5" />
        </button>
    @endif
    
    <div class="absolute top-2 right-2 bg-black/50 text-white px-2 py-1 rounded text-xs">
        {{ $photo->votes_count }} votos
    </div>
</div>

// ❌ INCORRETO - HTML repetitivo nas views
```

### Modular Structure
```php
// ✅ CORRETO - Estrutura modular
app/
├── Modules/
│   ├── Voting/
│   │   ├── Controllers/
│   │   ├── Models/
│   │   ├── Services/
│   │   ├── Policies/
│   │   ├── Events/
│   │   ├── Listeners/
│   │   └── Tests/
│   ├── Admin/
│   │   ├── Controllers/
│   │   ├── Services/
│   │   ├── Policies/
│   │   └── Tests/
│   └── Auth/
│       ├── Controllers/
│       ├── Middleware/
│       └── Tests/

// ❌ INCORRETO - Tudo misturado em app/
```

## Convenções de Código

### PSR-12 Compliance
```php
// ✅ CORRETO - PSR-12
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Vote;
use Illuminate\Support\Collection;

class VotingService
{
    public function __construct(
        private readonly VoteRepository $voteRepository,
        private readonly StatisticsService $statisticsService
    ) {}
    
    public function processVotes(User $user, array $photoIds): bool
    {
        $errors = $this->validateVotingRules($user, collect($photoIds));
        
        if (!empty($errors)) {
            throw new VotingValidationException($errors);
        }
        
        return $this->saveVotes($user, $photoIds);
    }
    
    private function saveVotes(User $user, array $photoIds): bool
    {
        foreach ($photoIds as $photoId) {
            $this->voteRepository->create([
                'user_id' => $user->id,
                'photo_id' => $photoId,
            ]);
        }
        
        return true;
    }
}

// ❌ INCORRETO - Não segue PSR-12
```

### Naming Conventions
```php
// ✅ CORRETO - Nomenclatura consistente
// Models: PascalCase, singular
class Vote extends Model {}
class Photo extends Model {}

// Controllers: PascalCase + Controller suffix
class VotingController extends Controller {}
class AdminDashboardController extends Controller {}

// Services: PascalCase + Service suffix
class VotingService {}
class StatisticsService {}

// Methods: camelCase, verbos descritivos
public function canUserVote(): bool {}
public function hasUserVotedOnPhoto(): bool {}
public function processVotingSubmission(): void {}

// Variables: camelCase, descritivos
$activeProjects = Project::active()->get();
$userVoteCount = $user->votes()->count();
$votingDeadline = Carbon::parse($project->voting_deadline);

// ❌ INCORRETO - Nomenclatura inconsistente
class vote {} // Deveria ser Vote
class VotingCtrl {} // Deveria ser VotingController
public function check() {} // Muito genérico
$data = []; // Muito genérico
```

### Database Conventions
```php
// ✅ CORRETO - Convenções de banco
// Tabelas: snake_case, plural
Schema::create('votes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('photo_id')->constrained()->onDelete('cascade');
    $table->timestamps();
    
    $table->unique(['user_id', 'photo_id']);
    $table->index(['user_id', 'created_at']);
    $table->index(['photo_id', 'created_at']);
});

// Colunas: snake_case
$table->string('file_path');
$table->boolean('is_active');
$table->timestamp('voting_deadline');

// Foreign Keys: singular_table_id
$table->foreignId('project_id');
$table->foreignId('user_id');

// ❌ INCORRETO - Convenções inconsistentes
Schema::create('Vote', function (Blueprint $table) { // Deveria ser 'votes'
    $table->id();
    $table->integer('userId'); // Deveria ser 'user_id'
    $table->integer('photoId'); // Deveria ser 'photo_id'
});
```

## Padrões Obrigatórios

### Form Requests
```php
// ✅ CORRETO - Form Request dedicado
class VoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isVoter();
    }
    
    public function rules(): array
    {
        return [
            'photo_id' => [
                'required',
                'exists:photos,id',
                Rule::unique('votes')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
        ];
    }
    
    public function messages(): array
    {
        return [
            'photo_id.required' => 'Selecione uma foto para votar.',
            'photo_id.exists' => 'Foto não encontrada.',
            'photo_id.unique' => 'Você já votou nesta foto.',
        ];
    }
}

// ❌ INCORRETO - Validação no controller
```

### Policies
```php
// ✅ CORRETO - Policy bem definida
class VotingPolicy
{
    public function vote(User $user, Photo $photo): bool
    {
        return $user->isVoter() 
            && $photo->project->is_active
            && !$this->hasUserVotedOnPhoto($user, $photo)
            && $this->canUserStillVote($user);
    }
    
    public function unvote(User $user, Vote $vote): bool
    {
        return $user->id === $vote->user_id
            && $vote->photo->project->is_active;
    }
    
    private function hasUserVotedOnPhoto(User $user, Photo $photo): bool
    {
        return Vote::where('user_id', $user->id)
            ->where('photo_id', $photo->id)
            ->exists();
    }
    
    private function canUserStillVote(User $user): bool
    {
        return $user->votes()->count() < 10;
    }
}

// ❌ INCORRETO - Autorização no controller
```

### Resource Collections
```php
// ✅ CORRETO - API Resources
class PhotoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'file_path' => Storage::url($this->file_path),
            'caption' => $this->caption,
            'project' => new ProjectResource($this->whenLoaded('project')),
            'votes_count' => $this->when($this->votes_count !== null, $this->votes_count),
            'user_voted' => $this->when(
                auth()->check(),
                fn() => $this->votes()->where('user_id', auth()->id())->exists()
            ),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

// ❌ INCORRETO - Arrays manuais no controller
```

## Anti-Patterns Proibidos

### ❌ Fat Controllers
```php
// PROIBIDO - Controller com muita responsabilidade
class VotingController extends Controller
{
    public function vote(Request $request)
    {
        // Validação manual
        if (!$request->photo_id) {
            return back()->withErrors(['photo_id' => 'Required']);
        }
        
        // Lógica de negócio
        $user = auth()->user();
        $photo = Photo::find($request->photo_id);
        $existingVotes = Vote::where('user_id', $user->id)->count();
        
        if ($existingVotes >= 10) {
            return back()->withErrors(['limit' => 'Limite excedido']);
        }
        
        if (!$photo->project->is_active) {
            return back()->withErrors(['project' => 'Projeto inativo']);
        }
        
        // Criação do voto
        Vote::create([
            'user_id' => $user->id,
            'photo_id' => $photo->id,
        ]);
        
        // Atualização de estatísticas
        Cache::forget("project_stats_{$photo->project_id}");
        Cache::forget('dashboard_stats');
        
        // Notificações
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Mail::to($admin)->send(new VoteNotification($vote));
        }
        
        return back()->with('success', 'Voto registrado!');
    }
}
```

### ❌ God Objects
```php
// PROIBIDO - Model com muitas responsabilidades
class User extends Model
{
    // ... propriedades básicas
    
    // Lógica de votação
    public function canVote() {}
    public function hasVotedOnPhoto($photoId) {}
    public function getRemainingVotes() {}
    
    // Lógica de estatísticas
    public function getVotingStatistics() {}
    public function getMostVotedPhotos() {}
    
    // Lógica de relatórios
    public function generateVotingReport() {}
    public function exportVotesToCsv() {}
    
    // Lógica de notificações
    public function sendVoteNotification() {}
    public function notifyAdmins() {}
}
```

### ❌ Magic Numbers/Strings
```php
// PROIBIDO - Números e strings mágicos
if ($user->votes()->count() >= 10) { // 10 é mágico
    return false;
}

if ($user->role === 'admin') { // String mágica
    return true;
}

// ✅ CORRETO - Constantes definidas
class VotingRules
{
    public const MAX_VOTES_PER_USER = 10;
    public const MIN_PHOTOS_PER_PROJECT = 1;
}

enum UserRole: string
{
    case ADMIN = 'admin';
    case VOTER = 'voter';
}
```

### ❌ N+1 Queries
```php
// PROIBIDO - N+1 Problem
$projects = Project::all();
foreach ($projects as $project) {
    echo $project->photos->count(); // Query para cada projeto
}

// ✅ CORRETO - Eager Loading
$projects = Project::withCount('photos')->get();
foreach ($projects as $project) {
    echo $project->photos_count;
}
```

## Guidelines de Testes

### Estrutura de Testes
```php
// ✅ CORRETO - Teste bem estruturado
class VotingTest extends TestCase
{
    use RefreshDatabase;
    
    private User $voter;
    private User $admin;
    private Project $activeProject;
    private Photo $photo;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->voter = User::factory()->create(['role' => UserRole::VOTER]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->activeProject = Project::factory()->create(['is_active' => true]);
        $this->photo = Photo::factory()->create(['project_id' => $this->activeProject->id]);
    }
    
    /** @test */
    public function voter_can_vote_on_active_project_photo(): void
    {
        // Arrange - já feito no setUp()
        
        // Act
        $response = $this->actingAs($this->voter)
            ->post(route('vote'), ['photo_id' => $this->photo->id]);
        
        // Assert
        $response->assertRedirect()
            ->assertSessionHas('success', 'Voto registrado com sucesso!');
            
        $this->assertDatabaseHas('votes', [
            'user_id' => $this->voter->id,
            'photo_id' => $this->photo->id,
        ]);
    }
    
    /** @test */
    public function voter_cannot_exceed_vote_limit(): void
    {
        // Arrange
        Vote::factory()->count(VotingRules::MAX_VOTES_PER_USER)
            ->create(['user_id' => $this->voter->id]);
        
        // Act
        $response = $this->actingAs($this->voter)
            ->post(route('vote'), ['photo_id' => $this->photo->id]);
        
        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['limit']);
    }
}
```

### Cobertura de Testes
```bash
# Configuração de cobertura
php artisan test --coverage --min=80

# Tipos de teste obrigatórios:
# - Unit Tests (Models, Services, Helpers)
# - Feature Tests (Controllers, Routes, Middleware)
# - Browser Tests (Dusk - fluxos críticos)
# - Integration Tests (APIs, External Services)
```

### Factories e Seeders
```php
// ✅ CORRETO - Factory bem definida
class PhotoFactory extends Factory
{
    protected $model = Photo::class;
    
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'file_path' => 'photos/' . $this->faker->uuid() . '.jpg',
            'caption' => $this->faker->sentence(),
        ];
    }
    
    public function withProject(Project $project): static
    {
        return $this->state(fn() => ['project_id' => $project->id]);
    }
    
    public function withoutCaption(): static
    {
        return $this->state(fn() => ['caption' => null]);
    }
}
```

## Versionamento e Git

### Conventional Commits
```bash
# ✅ CORRETO - Commits semânticos
feat(voting): add vote limit validation
fix(auth): resolve SARAM login issue
docs(api): update voting endpoints documentation
refactor(models): extract voting logic to service
test(voting): add integration tests for vote flow
chore(deps): update Laravel to 11.x

# ❌ INCORRETO - Commits vagos
git commit -m "fix bug"
git commit -m "update code"
git commit -m "changes"
```

### Branch Strategy
```bash
# Branches principais
main          # Produção
develop       # Desenvolvimento
release/*     # Preparação para release
hotfix/*      # Correções urgentes

# Branches de feature
feature/voting-system
feature/admin-dashboard
feature/photo-upload
feature/statistics-report

# Branches de bugfix
bugfix/vote-validation
bugfix/auth-redirect
```

### Code Review Checklist
```markdown
## Code Review Checklist

### Funcionalidade
- [ ] O código faz o que deveria fazer?
- [ ] A lógica está correta?
- [ ] Casos extremos foram considerados?

### Arquitetura MALT
- [ ] Modelagem: Relacionamentos corretos?
- [ ] Ação: Controllers enxutos?
- [ ] Lógica: Services bem definidos?
- [ ] Teste: Cobertura adequada?

### Qualidade
- [ ] Segue PSR-12?
- [ ] Nomenclatura consistente?
- [ ] Sem code smells?
- [ ] Performance adequada?

### Segurança
- [ ] Validação de entrada?
- [ ] Autorização implementada?
- [ ] Sem vazamento de dados?
- [ ] CSRF protegido?

### Testes
- [ ] Testes passando?
- [ ] Cobertura > 80%?
- [ ] Casos de erro testados?
```

## Performance Guidelines

### Database Optimization
```php
// ✅ CORRETO - Queries otimizadas
// Eager loading
$projects = Project::with(['photos.votes', 'photos' => function ($query) {
    $query->withCount('votes');
}])->get();

// Chunking para grandes datasets
Vote::with(['user', 'photo.project'])
    ->chunk(1000, function ($votes) {
        foreach ($votes as $vote) {
            // Processar voto
        }
    });

// Índices apropriados
Schema::table('votes', function (Blueprint $table) {
    $table->index(['user_id', 'created_at']);
    $table->index(['photo_id', 'created_at']);
    $table->index(['created_at']); // Para relatórios por data
});

// ❌ INCORRETO - Queries ineficientes
$projects = Project::all();
foreach ($projects as $project) {
    $project->photos; // N+1 query
    foreach ($project->photos as $photo) {
        $photo->votes->count(); // Mais N+1 queries
    }
}
```

### Caching Strategy
```php
// ✅ CORRETO - Cache inteligente
class StatisticsService
{
    public function getDashboardStats(): array
    {
        return Cache::remember('dashboard_stats', 3600, function () {
            return [
                'total_projects' => Project::count(),
                'total_photos' => Photo::count(),
                'total_votes' => Vote::count(),
                'total_users' => User::count(),
                'active_projects' => Project::active()->count(),
            ];
        });
    }
    
    public function getProjectStats(Project $project): array
    {
        return Cache::remember("project_stats_{$project->id}", 1800, function () use ($project) {
            return [
                'photos_count' => $project->photos()->count(),
                'votes_count' => $project->votes()->count(),
                'top_photo' => $project->photos()
                    ->withCount('votes')
                    ->orderByDesc('votes_count')
                    ->first(),
            ];
        });
    }
}
```

Esta documentação representa a **fonte da verdade arquitetural** do projeto e deve ser seguida rigorosamente em todas as implementações futuras.