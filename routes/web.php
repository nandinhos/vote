<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PhotoController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VotingController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Página inicial - redireciona baseado no role se autenticado, senão vai para login
Route::get('/', function () {
    if (Auth::check()) {
        /** @var User $user */
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('voting.index');
    }

    return redirect()->route('login');
});

// Dashboard padrão do Laravel Breeze - redireciona baseado no role
Route::get('/dashboard', function () {
    /** @var User $user */
    $user = Auth::user();
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('voting.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rotas de Votação (usuários autenticados)
Route::middleware(['auth', 'verified', 'role:voter'])->group(function () {
    Route::get('/voting', [VotingController::class, 'index'])->name('voting.index');
    Route::get('/voting/gallery', [VotingController::class, 'gallery'])->name('voting.gallery');
    Route::get('/voting/{project}', [VotingController::class, 'show'])->name('voting.show');
    Route::post('/voting/{photo}/vote', [VotingController::class, 'vote'])->name('voting.vote');
    Route::delete('/voting/{photo}/unvote', [VotingController::class, 'unvote'])->name('voting.unvote');
});

// Rotas Administrativas
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Administrativo
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gerenciamento de Projetos
    Route::resource('projects', ProjectController::class);

    // Gerenciamento de Fotos
    Route::resource('photos', PhotoController::class);
    
    // Gerenciamento de Usuários
    Route::resource('users', UserController::class);
});

// Rotas de Perfil (usuários autenticados)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
