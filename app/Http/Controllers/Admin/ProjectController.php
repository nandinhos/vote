<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::withCount('photos', 'votes');

        // Filtro de busca
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('description', 'like', '%'.$request->search.'%');
        }

        // Filtro de status
        if ($request->filled('status')) {
            if ($request->status === '1' || $request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === '0' || $request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $projects = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Projects/Index', [
            'projects' => $projects,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Projects/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectRequest $request)
    {
        $validated = $request->validated();

        Project::create($validated);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Projeto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        // Carregar o projeto com as contagens necessárias
        $project->loadCount(['photos', 'votes']);
        $project->load(['photos.votes', 'votes']);

        // Calcular estatísticas das fotos
        $photos = $project->photos->map(function ($photo) {
            return [
                'id' => $photo->id,
                'caption' => $photo->caption,
                'file_path' => $photo->file_path,
                'vote_count' => $photo->votes->count(),
                'created_at' => $photo->created_at,
            ];
        });

        // Calcular contagem de votantes únicos
        $project->unique_voters_count = $project->votes->unique('user_id')->count();

        return Inertia::render('Admin/Projects/Show', [
            'project' => $project,
            'photos' => $photos,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return Inertia::render('Admin/Projects/Edit', [
            'project' => $project,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectRequest $request, Project $project)
    {
        $validated = $request->validated();

        $project->update($validated);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Projeto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Projeto excluído com sucesso!');
    }
}
