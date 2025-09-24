# Guia de Desenvolvimento - Sistema de Votação

## Bem-vindo ao Projeto!

Este guia foi criado para ajudar novos desenvolvedores a entender rapidamente o projeto e começar a contribuir de forma efetiva.

## 1. Visão Geral do Projeto

### 1.1 Objetivo
Sistema de votação para fotos organizadas em projetos, com interface administrativa e sistema de autenticação SARAM.

### 1.2 Stack Tecnológica
- **Backend**: Laravel 11.x
- **Frontend**: Vue.js 3 + Inertia.js
- **Styling**: Tailwind CSS
- **Database**: SQLite (desenvolvimento) / MySQL (produção)
- **Authentication**: Laravel Breeze

### 1.3 Arquitetura
- **Padrão MALT**: Modeling, Action, Logic, Testing
- **Arquitetura em Camadas**: Presentation → Application → Domain → Persistence

## 2. Setup do Ambiente

### 2.1 Pré-requisitos
```bash
# Verificar versões
php --version    # >= 8.2
node --version   # >= 18
composer --version
```

### 2.2 Instalação
```bash
# Clonar repositório
git clone [repository-url]
cd vote

# Instalar dependências
composer install
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Configurar banco de dados
php artisan migrate --seed

# Compilar assets
npm run dev
```

### 2.3 Executar Projeto
```bash
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Vite (desenvolvimento)
npm run dev
```

## 3. Estrutura do Projeto

### 3.1 Diretórios Principais
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/              # Área administrativa
│   │   ├── Auth/               # Autenticação
│   │   └── VotingController.php # Votação principal
│   ├── Requests/               # Validações
│   └── Middleware/             # Middleware customizado
├── Models/                     # Eloquent Models
├── Services/                   # Lógica de negócio
├── Exceptions/                 # Exceções customizadas
└── Policies/                   # Autorização

resources/
├── js/
│   ├── Components/             # Componentes Vue reutilizáveis
│   ├── Pages/                  # Páginas Inertia
│   └── app.js                  # Entry point
└── css/
    └── app.css                 # Tailwind CSS

docs/                           # Documentação do projeto
```

### 3.2 Modelos Principais
- **User**: Usuários do sistema
- **Project**: Projetos de votação
- **Photo**: Fotos dos projetos
- **Vote**: Votos dos usuários

## 4. Fluxos Principais

### 4.1 Fluxo de Votação
1. Usuário acessa galeria unificada
2. Visualiza fotos de projetos ativos
3. Clica para votar (máximo 10 votos)
4. Sistema valida regras de negócio
5. Voto é registrado com feedback

### 4.2 Fluxo Administrativo
1. Admin faz login
2. Acessa dashboard com estatísticas
3. Gerencia projetos (CRUD)
4. Gerencia fotos (CRUD)
5. Visualiza relatórios de votação

## 5. Padrões de Desenvolvimento

### 5.1 Controllers
```php
// ✅ Padrão correto
class VotingController extends Controller
{
    public function __construct(
        private VotingService $votingService
    ) {}

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

### 5.2 Services
```php
// ✅ Padrão correto
class VotingService
{
    public function vote(Photo $photo): Vote
    {
        return DB::transaction(function () use ($photo) {
            $this->validateBusinessRules($photo);
            return Vote::create([
                'user_id' => Auth::id(),
                'photo_id' => $photo->id
            ]);
        });
    }
}
```

### 5.3 Form Requests
```php
// ✅ Padrão correto
class VoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'photo_id' => [
                'required',
                'exists:photos,id',
                // Validações customizadas
            ]
        ];
    }
}
```

## 6. Comandos Úteis

### 6.1 Artisan Commands
```bash
# Migrations
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh --seed

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Testes
php artisan test
php artisan test --filter VotingTest
```

### 6.2 NPM Scripts
```bash
npm run dev          # Desenvolvimento
npm run build        # Produção
npm run lint         # Verificar código
```

## 7. Debugging

### 7.1 Laravel Debugging
```php
// Debug queries
DB::enableQueryLog();
// ... código ...
dd(DB::getQueryLog());

// Debug variáveis
dump($variable);
dd($variable);

// Log personalizado
Log::info('Debug info', ['data' => $data]);
```

### 7.2 Vue.js Debugging
```javascript
// Console log
console.log('Debug:', data)

// Vue DevTools (recomendado)
// Instalar extensão do navegador
```

## 8. Testes

### 8.1 Executar Testes
```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --filter VotingTest
php artisan test tests/Feature/VotingTest.php
```

### 8.2 Criar Testes
```php
// Feature Test
class VotingTest extends TestCase
{
    public function test_user_can_vote()
    {
        $user = User::factory()->create();
        $photo = Photo::factory()->create();
        
        $response = $this->actingAs($user)
            ->post("/vote/{$photo->id}");
            
        $response->assertRedirect();
        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'photo_id' => $photo->id
        ]);
    }
}
```

## 9. Contribuindo

### 9.1 Workflow Git
```bash
# Criar branch para feature
git checkout -b feature/nova-funcionalidade

# Fazer commits pequenos e descritivos
git commit -m "feat: adicionar validação de voto duplicado"

# Push e criar Pull Request
git push origin feature/nova-funcionalidade
```

### 9.2 Padrões de Commit
- `feat:` Nova funcionalidade
- `fix:` Correção de bug
- `docs:` Documentação
- `style:` Formatação
- `refactor:` Refatoração
- `test:` Testes

### 9.3 Code Review Checklist
- [ ] Código segue padrões estabelecidos
- [ ] Testes incluídos e passando
- [ ] Documentação atualizada
- [ ] Performance considerada
- [ ] Segurança verificada

## 10. Troubleshooting

### 10.1 Problemas Comuns

**Erro de permissão:**
```bash
sudo chown -R $USER:$USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

**Cache issues:**
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**NPM issues:**
```bash
rm -rf node_modules package-lock.json
npm install
```

### 10.2 Logs
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Nginx/Apache logs
tail -f /var/log/nginx/error.log
```

## 11. Recursos Úteis

### 11.1 Documentação
- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Guide](https://vuejs.org/guide/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [Tailwind CSS](https://tailwindcss.com/docs)

### 11.2 Ferramentas Recomendadas
- **IDE**: VS Code com extensões Laravel e Vue
- **Database**: TablePlus ou phpMyAdmin
- **API Testing**: Postman ou Insomnia
- **Git GUI**: GitKraken ou SourceTree

## 12. Contatos e Suporte

### 12.1 Equipe
- **Tech Lead**: [Nome] - [email]
- **Backend**: [Nome] - [email]
- **Frontend**: [Nome] - [email]

### 12.2 Canais de Comunicação
- **Slack**: #desenvolvimento
- **Email**: dev-team@empresa.com
- **Issues**: GitHub Issues

## 13. Próximos Passos

Após configurar o ambiente:

1. **Explorar o código**: Comece pelos Controllers principais
2. **Executar testes**: Entenda como o sistema funciona
3. **Fazer pequenas mudanças**: Comece com bugs simples
4. **Estudar documentação**: Leia todos os docs/ 
5. **Participar de code reviews**: Aprenda com o time

## 14. Dicas de Produtividade

### 14.1 Aliases Úteis
```bash
# Adicionar ao ~/.bashrc ou ~/.zshrc
alias art="php artisan"
alias tinker="php artisan tinker"
alias migrate="php artisan migrate"
alias test="php artisan test"
```

### 14.2 VS Code Extensions
- Laravel Extension Pack
- Vue Language Features (Volar)
- Tailwind CSS IntelliSense
- GitLens
- PHP Intelephense

### 14.3 Snippets Úteis
```php
// Model factory
User::factory()->create(['role' => 'admin']);

// Quick test data
$project = Project::factory()->active()->create();
$photos = Photo::factory(5)->for($project)->create();
```

---

**Bem-vindo à equipe! 🚀**

Lembre-se: não hesite em fazer perguntas. É melhor perguntar do que assumir!