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
        $query = User::query()->withCount('votes');

        // Filter by role if specified
        if ($request->has('role') && in_array($request->role, ['admin', 'voter'])) {
            $query->where('role', $request->role);
        }

        // Filter by voting status if specified
        if ($request->has('status') && in_array($request->status, ['voted', 'pending'])) {
            if ($request->status === 'voted') {
                $query->has('votes');
            } else {
                $query->doesntHave('votes');
            }
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

        // Add voting status to each user
        $users->getCollection()->transform(function ($user) {
            $user->voting_status = $user->votes_count > 0 ? 'voted' : 'pending';
            return $user;
        });

        // Calculate statistics
        $totalUsers = User::count();
        $totalVoters = User::where('role', 'voter')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $usersWhoVoted = User::has('votes')->count();
        $usersPending = User::doesntHave('votes')->count();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'status']),
            'stats' => [
                'total_users' => $totalUsers,
                'total_voters' => $totalVoters,
                'total_admins' => $totalAdmins,
                'users_voted' => $usersWhoVoted,
                'users_pending' => $usersPending,
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