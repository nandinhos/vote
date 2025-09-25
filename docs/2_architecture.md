# 2. Arquitetura e Design do Sistema

## Arquitetura em Camadas (Laravel MVC + Inertia.js)

### Camada de Apresentação (Frontend)
```
┌─────────────────────────────────────────┐
│           PRESENTATION LAYER            │
├─────────────────────────────────────────┤
│ Vue.js 3 Components + Inertia.js       │
│ ├── Pages/                             │
│ │   ├── Voting/                        │
│ │   │   ├── Index.vue                  │
│ │   │   ├── Gallery.vue                │
│ │   │   └── Show.vue                   │
│ │   ├── Admin/                         │
│ │   │   ├── Dashboard.vue              │
│ │   │   ├── Projects/                  │
│ │   │   └── Photos/                    │
│ │   └── Auth/                          │
│ ├── Components/                        │
│ │   ├── PhotoGallery.vue               │
│ │   ├── VoteCounter.vue                │
│ │   └── ProjectCard.vue                │
│ └── Layouts/                           │
│     ├── AuthenticatedLayout.vue        │
│     └── GuestLayout.vue                │
├─────────────────────────────────────────┤
│ Tailwind CSS + Responsive Design       │
└─────────────────────────────────────────┘
```

### Camada de Aplicação (Controllers + Middleware)
```
┌─────────────────────────────────────────┐
│          APPLICATION LAYER              │
├─────────────────────────────────────────┤
│ HTTP Controllers                        │
│ ├── VotingController                    │
│ │   ├── index() - Lista projetos       │
│ │   ├── gallery() - Galeria unificada  │
│ │   ├── show() - Projeto específico    │
│ │   ├── vote() - Registra voto         │
│ │   └── unvote() - Remove voto         │
│ ├── Admin/                             │
│ │   ├── DashboardController            │
│ │   ├── ProjectController (CRUD)       │
│ │   └── PhotoController (Upload/CRUD)  │
│ └── Auth/                              │
│     └── AuthenticatedSessionController │
├─────────────────────────────────────────┤
│ Middleware Stack                        │
│ ├── HandleInertiaRequests              │
│ ├── RoleMiddleware (admin/voter)        │
│ ├── Authenticate                       │
│ └── VerifyCsrfToken                    │
├─────────────────────────────────────────┤
│ Form Requests & Validation              │
│ ├── ProjectRequest                     │
│ ├── PhotoRequest                       │
│ └── VoteRequest                        │
└─────────────────────────────────────────┘
```

### Camada de Domínio (Models + Business Logic)
```
┌─────────────────────────────────────────┐
│            DOMAIN LAYER                 │
├─────────────────────────────────────────┤
│ Eloquent Models                         │
│ ├── User                               │
│ │   ├── isAdmin()                      │
│ │   ├── isVoter()                      │
│ │   ├── votes() HasMany               │
│ │   └── getAuthIdentifierName()       │
│ ├── Project                           │
│ │   ├── photos() HasMany              │
│ │   ├── votes() HasManyThrough        │
│ │   └── scopeActive()                 │
│ ├── Photo                             │
│ │   ├── project() BelongsTo           │
│ │   ├── votes() HasMany               │
│ │   └── getVoteCountAttribute()       │
│ └── Vote                              │
│     ├── user() BelongsTo              │
│     └── photo() BelongsTo             │
├─────────────────────────────────────────┤
│ Business Rules & Policies               │
│ ├── VotingPolicy                       │
│ ├── ProjectPolicy                      │
│ └── PhotoPolicy                        │
├─────────────────────────────────────────┤
│ Services (Future Extension)             │
│ ├── VotingService                      │
│ ├── StatisticsService                  │
│ └── FileUploadService                  │
└─────────────────────────────────────────┘
```

### Camada de Persistência (Database)
```
┌─────────────────────────────────────────┐
│          PERSISTENCE LAYER              │
├─────────────────────────────────────────┤
│ Database Schema                         │
│ ├── users                              │
│ │   ├── id (PK)                        │
│ │   ├── name                           │
│ │   ├── saram (UNIQUE)                 │
│ │   ├── password (HASHED)              │
│ │   └── role (ENUM: admin/voter)       │
│ ├── projects                           │
│ │   ├── id (PK)                        │
│ │   ├── name (UNIQUE)                  │
│ │   ├── description                    │
│ │   └── is_active (BOOLEAN)            │
│ ├── photos                             │
│ │   ├── id (PK)                        │
│ │   ├── project_id (FK)                │
│ │   ├── file_path                      │
│ │   └── caption                        │
│ └── votes                              │
│     ├── id (PK)                        │
│     ├── user_id (FK)                   │
│     ├── photo_id (FK)                  │
│     └── UNIQUE(user_id, photo_id)      │
├─────────────────────────────────────────┤
│ Storage Layer                           │
│ ├── database/database.sqlite (SQLite)   │
│ ├── storage/app/public/photos/          │
│ ├── database/migrations/                │
│ └── database/seeders/                   │
├─────────────────────────────────────────┤
│ Docker Configuration                    │
│ ├── docker/init.sh (Permissions)       │
│ ├── docker/nginx.conf                  │
│ ├── docker/supervisord.conf            │
│ └── Dockerfile                         │
└─────────────────────────────────────────┘
```

## Modelo de Entidades e Relacionamentos

### Diagrama ER
```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│    User     │       │    Vote     │       │    Photo    │
├─────────────┤       ├─────────────┤       ├─────────────┤
│ id (PK)     │◄─────►│ id (PK)     │◄─────►│ id (PK)     │
│ name        │   1:N │ user_id(FK) │   N:1 │ project_id  │
│ saram       │       │ photo_id(FK)│       │ file_path   │
│ password    │       │ created_at  │       │ caption     │
│ role        │       │ updated_at  │       │ created_at  │
│ created_at  │       └─────────────┘       │ updated_at  │
│ updated_at  │                             └─────────────┘
└─────────────┘                                     ▲
                                                    │ N:1
                                                    │
                                            ┌─────────────┐
                                            │   Project   │
                                            ├─────────────┤
                                            │ id (PK)     │
                                            │ name        │
                                            │ description │
                                            │ is_active   │
                                            │ created_at  │
                                            │ updated_at  │
                                            └─────────────┘
```

### Relacionamentos Eloquent
```php
// User Model
public function votes(): HasMany
public function isAdmin(): bool
public function isVoter(): bool

// Project Model  
public function photos(): HasMany
public function votes(): HasManyThrough
public function scopeActive($query)

// Photo Model
public function project(): BelongsTo
public function votes(): HasMany
public function getVoteCountAttribute(): int

// Vote Model
public function user(): BelongsTo
public function photo(): BelongsTo
```

## Fluxo de Dados

### Fluxo de Votação
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Browser   │    │ Controller  │    │   Models    │    │  Database   │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
       │                   │                   │                   │
   1.  │ GET /voting       │                   │                   │
       ├──────────────────►│                   │                   │
   2.  │                   │ Project::active() │                   │
       │                   ├──────────────────►│                   │
   3.  │                   │                   │ SELECT projects   │
       │                   │                   ├──────────────────►│
   4.  │                   │                   │ ◄─────────────────┤
       │                   │ ◄─────────────────┤                   │
   5.  │ Inertia Response  │                   │                   │
       │ ◄─────────────────┤                   │                   │
   6.  │ POST /vote        │                   │                   │
       ├──────────────────►│                   │                   │
   7.  │                   │ Validate Rules    │                   │
   8.  │                   │ Vote::create()    │                   │
       │                   ├──────────────────►│                   │
   9.  │                   │                   │ INSERT votes      │
       │                   │                   ├──────────────────►│
  10.  │                   │                   │ ◄─────────────────┤
  11.  │                   │ ◄─────────────────┤                   │
  12.  │ Success Response  │                   │                   │
       │ ◄─────────────────┤                   │                   │
```

### Fluxo de Dashboard Admin
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Browser   │    │ Controller  │    │   Models    │    │  Database   │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
       │                   │                   │                   │
   1.  │ GET /admin/dash   │                   │                   │
       ├──────────────────►│                   │                   │
   2.  │                   │ Statistics Query  │                   │
       │                   ├──────────────────►│                   │
   3.  │                   │                   │ Complex Queries   │
       │                   │                   ├──────────────────►│
   4.  │                   │                   │ ◄─────────────────┤
   5.  │                   │ Top Photos Query  │                   │
       │                   ├──────────────────►│                   │
   6.  │                   │                   │ ORDER BY votes    │
       │                   │                   ├──────────────────►│
   7.  │                   │                   │ ◄─────────────────┤
   8.  │                   │ ◄─────────────────┤                   │
   9.  │ Dashboard Data    │                   │                   │
       │ ◄─────────────────┤                   │                   │
```

## Extensibilidade e Pontos de Extensão

### Service Providers
```php
// VotingServiceProvider
public function register()
{
    $this->app->bind(VotingService::class);
    $this->app->bind(StatisticsService::class);
}

// EventServiceProvider  
protected $listen = [
    VoteCast::class => [
        UpdateStatistics::class,
        NotifyAdmins::class,
    ],
];
```

### Event/Listener System
```php
// Events
class VoteCast extends Event
class ProjectCreated extends Event
class VotingCompleted extends Event

// Listeners
class UpdateStatistics implements ShouldQueue
class NotifyAdmins implements ShouldQueue
class GenerateReports implements ShouldQueue
```

### Blade Components
```php
// Reusable Components
<x-photo-card :photo="$photo" :voted="$voted" />
<x-project-stats :project="$project" />
<x-vote-counter :current="$current" :max="$max" />
<x-admin-metric :title="$title" :value="$value" />
```

### Modular Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Módulo Administrativo
│   │   ├── Auth/           # Módulo Autenticação  
│   │   └── VotingController # Módulo Votação
├── Models/                 # Entidades de Domínio
├── Services/              # Lógica de Negócio
├── Events/                # Sistema de Eventos
├── Listeners/             # Handlers de Eventos
└── Policies/              # Autorização
```

## Padrões Arquiteturais Implementados

### Repository Pattern (Futuro)
```php
interface VoteRepositoryInterface
{
    public function findByUser(User $user): Collection;
    public function countByPhoto(Photo $photo): int;
    public function hasUserVoted(User $user): bool;
}
```

### Observer Pattern
```php
class VoteObserver
{
    public function created(Vote $vote): void
    {
        event(new VoteCast($vote));
    }
}
```

### Policy Pattern
```php
class VotingPolicy
{
    public function vote(User $user, Photo $photo): bool
    {
        return !$this->hasUserVoted($user) && 
               $photo->project->is_active;
    }
}
```

### Factory Pattern
```php
class VoteFactory
{
    public static function createFromRequest(Request $request): Vote
    {
        return new Vote([
            'user_id' => auth()->id(),
            'photo_id' => $request->photo_id,
        ]);
    }
}
```