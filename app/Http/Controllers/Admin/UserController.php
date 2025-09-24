<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role if specified
        if ($request->has('role') && in_array($request->role, ['admin', 'voter'])) {
            $query->where('role', $request->role);
        }

        // Search by name or saram
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('saram', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('name')
                      ->paginate(15)
                      ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role']),
            'stats' => [
                'total_users' => User::count(),
                'total_voters' => User::where('role', 'voter')->count(),
                'total_admins' => User::where('role', 'admin')->count(),
            ]
        ]);
    }



    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return Inertia::render('Admin/Users/Create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(UserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'saram' => $validated['saram'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('votes.photo.project');

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'votesCount' => $user->votes()->count(),
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validated();

        $updateData = [
            'name' => $validated['name'],
            'saram' => $validated['saram'],
            'role' => $validated['role'],
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deletion of the current authenticated user
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                            ->with('error', 'Você não pode excluir sua própria conta!');
        }

        // Check if user has votes before deletion
        if ($user->votes()->count() > 0) {
            return redirect()->route('admin.users.index')
                            ->with('error', 'Não é possível excluir um usuário que já votou!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuário excluído com sucesso!');
    }
}