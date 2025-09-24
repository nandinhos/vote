# 5. Padrões de Desenvolvimento - Sistema de Votação

## Metodologia MALT Aplicada

### M - Modelagem (Models & Database)

#### Padrões de Models
```php
// ✅ PADRÃO CORRETO - Model com relacionamentos explícitos
class Photo extends Model
{
    protected $fillable = ['project_id', 'file_path', 'caption'];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Relacionamentos sempre tipados
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
    
    // Accessors para lógica de apresentação
    public function getVotesCountAttribute(): int
    {
        return $this->votes()->count();
    }
}
```

#### Convenções de Migrations
```php
// ✅ PADRÃO CORRETO - Migration com constraints e índices
Schema::create('votes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('photo_id')->constrained()->onDelete('cascade');
    $table->timestamps();
    
    // Constraint única para evitar votos duplicados
    $table->unique(['user_id', 'photo_id']);
    
    // Índices para performance
    $table->index(['user_id', 'created_at']);
    $table->index(['photo_id', 'created_at']);
});
```

### A - Ação (Controllers & Routes)

#### Padrões de Controllers
```php
// ✅ PADRÃO CORRETO - Controller com responsabilidade única
class VotingController extends Controller
{
    public function __construct(
        private VotingService $votingService
    ) {}
    
    public function vote(VoteRequest $request): RedirectResponse
    {
        try {
            $this->votingService->castVote(
                auth()->user(),
                $request->validated()
            );
            
            return redirect()->back()->with('success', 'Voto registrado com sucesso!');
        } catch (VotingException $e) {
            return redirect()->back()->withErrors(['voting' => $e->getMessage()]);
        }
    }
}
```

#### Convenções de Rotas
```php
// ✅ PADRÃO CORRETO - Rotas organizadas por contexto
Route::middleware(['auth', 'verified'])->group(function () {
    // Rotas de votação para eleitores
    Route::middleware('role:voter')->prefix('voting')->name('voting.')->group(function () {
        Route::get('/', [VotingController::class, 'index'])->name('index');
        Route::post('/vote', [VotingController::class, 'vote'])->name('vote');
    });
    
    // Rotas administrativas
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('projects', ProjectController::class);
        Route::resource('photos', PhotoController::class);
    });
});
```

### L - Lógica (Services & Business Rules)

#### Service Layer Pattern
```php
// ✅ PADRÃO CORRETO - Service com regras de negócio isoladas
class VotingService
{
    public function castVote(User $user, array $data): void
    {
        $this->validateVotingEligibility($user);
        $this->validateVotingRules($user, $data['photo_ids']);
        
        DB::transaction(function () use ($user, $data) {
            foreach ($data['photo_ids'] as $photoId) {
                Vote::create([
                    'user_id' => $user->id,
                    'photo_id' => $photoId,
                ]);
            }
        });
        
        event(new VotingCompleted($user, $data['photo_ids']));
    }
    
    private function validateVotingEligibility(User $user): void
    {
        if ($user->hasVoted()) {
            throw new VotingException('Usuário já votou no sistema.');
        }
    }
    
    private function validateVotingRules(User $user, array $photoIds): void
    {
        if (count($photoIds) !== 10) {
            throw new VotingException('Deve selecionar exatamente 10 fotos.');
        }
        
        $this->validateProjectDistribution($photoIds);
    }
}
```

### T - Teste (Testing Strategy)

#### Padrões de Testes
```php
// ✅ PADRÃO CORRETO - Testes abrangentes com cenários reais
class VotingServiceTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_cast_valid_vote(): void
    {
        // Arrange
        $user = User::factory()->voter()->create();
        $projects = Project::factory()->count(3)->active()->create();
        $photos = $projects->flatMap(fn($p) => Photo::factory()->count(4)->create(['project_id' => $p->id]));
        $selectedPhotos = $photos->random(10)->pluck('id')->toArray();
        
        // Act
        $this->votingService->castVote($user, ['photo_ids' => $selectedPhotos]);
        
        // Assert
        $this->assertDatabaseCount('votes', 10);
        $this->assertTrue($user->fresh()->hasVoted());
    }
    
    public function test_user_cannot_vote_without_project_distribution(): void
    {
        // Arrange
        $user = User::factory()->voter()->create();
        $project = Project::factory()->active()->create();
        $photos = Photo::factory()->count(10)->create(['project_id' => $project->id]);
        
        // Act & Assert
        $this->expectException(VotingException::class);
        $this->expectExceptionMessage('Deve votar em pelo menos uma foto de cada projeto');
        
        $this->votingService->castVote($user, ['photo_ids' => $photos->pluck('id')->toArray()]);
    }
}
```

## Regras de Validação e Tratamento de Erros

### Form Requests Padronizados
```php
// ✅ PADRÃO CORRETO - Request com validações específicas
class VoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isVoter() && !auth()->user()->hasVoted();
    }
    
    public function rules(): array
    {
        return [
            'photo_ids' => ['required', 'array', 'size:10'],
            'photo_ids.*' => ['required', 'integer', 'exists:photos,id'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'photo_ids.size' => 'Deve selecionar exatamente 10 fotos.',
            'photo_ids.*.exists' => 'Uma ou mais fotos selecionadas são inválidas.',
        ];
    }
    
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasProjectDistributionError()) {
                $validator->errors()->add('photo_ids', 'Deve votar em pelo menos uma foto de cada projeto ativo.');
            }
        });
    }
}
```

### Exception Handling
```php
// ✅ PADRÃO CORRETO - Exceptions específicas do domínio
class VotingException extends Exception
{
    public static function userAlreadyVoted(): self
    {
        return new self('Usuário já realizou sua votação.');
    }
    
    public static function invalidPhotoCount(int $count): self
    {
        return new self("Número inválido de fotos selecionadas: {$count}. Deve selecionar exatamente 10.");
    }
    
    public static function missingProjectDistribution(): self
    {
        return new self('Deve votar em pelo menos uma foto de cada projeto ativo.');
    }
}
```

## Convenções de Frontend (Vue.js + Inertia)

### Componentes Vue.js
```vue
<!-- ✅ PADRÃO CORRETO - Componente bem estruturado -->
<template>
    <div class="photo-gallery">
        <div v-for="photo in photos" :key="photo.id" class="photo-card">
            <img :src="photo.file_path" :alt="photo.caption" />
            <button 
                @click="toggleVote(photo.id)"
                :class="voteButtonClass(photo.id)"
                :disabled="!canVote(photo.id)"
            >
                {{ isSelected(photo.id) ? 'Remover Voto' : 'Votar' }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    photos: Array,
    maxVotes: { type: Number, default: 10 }
})

const selectedPhotos = ref([])

const canVote = (photoId) => {
    return selectedPhotos.value.length < props.maxVotes || isSelected(photoId)
}

const toggleVote = (photoId) => {
    if (isSelected(photoId)) {
        selectedPhotos.value = selectedPhotos.value.filter(id => id !== photoId)
    } else if (canVote(photoId)) {
        selectedPhotos.value.push(photoId)
    }
}
</script>
```

## Segurança e Performance

### Middleware de Segurança
```php
// ✅ PADRÃO CORRETO - Middleware com validações específicas
class EnsureUserCanVote
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user->isVoter()) {
            abort(403, 'Apenas eleitores podem acessar a votação.');
        }
        
        if ($user->hasVoted()) {
            return redirect()->route('voting.completed')
                ->with('info', 'Você já realizou sua votação.');
        }
        
        return $next($request);
    }
}
```

### Otimizações de Query
```php
// ✅ PADRÃO CORRETO - Queries otimizadas
class DashboardController extends Controller
{
    public function index()
    {
        $topPhotos = Photo::with(['project:id,name', 'votes:photo_id'])
            ->whereHas('votes')
            ->withCount('votes')
            ->orderByDesc('votes_count')
            ->limit(10)
            ->get();
            
        $recentProjects = Project::with(['photos' => fn($q) => $q->limit(3)])
            ->withCount('photos', 'votes')
            ->latest()
            ->limit(5)
            ->get();
            
        return Inertia::render('Admin/Dashboard', compact('topPhotos', 'recentProjects'));
    }
}
```