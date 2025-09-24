<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhotoRequest;
use App\Models\Photo;
use App\Models\Project;
use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Photo::with(['project'])
            ->withCount('votes');

        if ($request->has('project') && $request->project) {
            $query->where('project_id', $request->project);
        }

        if ($request->has('search') && $request->search) {
            $query->where('caption', 'like', '%' . $request->search . '%');
        }

        $photos = $query->orderBy('votes_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $projects = Project::active()->get();

        return Inertia::render('Admin/Photos/Index', [
            'photos' => $photos,
            'projects' => $projects,
            'filters' => $request->only(['project', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::active()->get();

        return Inertia::render('Admin/Photos/Create', [
            'projects' => $projects,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PhotoRequest $request, ImageOptimizationService $imageService)
    {
        $validated = $request->validated();

        // Verificar se é upload múltiplo ou único
        $photos = $request->file('photos') ?? [$request->file('photo')];
        $photos = array_filter($photos); // Remove valores nulos
        
        $uploadedCount = 0;
        
        foreach ($photos as $file) {
            if ($file && $file->isValid()) {
                // Otimizar a imagem automaticamente
                $path = $imageService->optimizeUploadedImage($file, 'photos');

                Photo::create([
                    'project_id' => $validated['project_id'],
                    'file_path' => $path,
                    'caption' => $validated['caption'] ?? null,
                ]);
                
                $uploadedCount++;
            }
        }

        $message = $uploadedCount === 1 
            ? 'Foto adicionada e otimizada com sucesso!' 
            : "{$uploadedCount} fotos adicionadas e otimizadas com sucesso!";

        return redirect()->route('admin.photos.index')
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Photo $photo)
    {
        $photo->load(['project']);

        // Get paginated votes for this photo
        $votes = $photo->votes()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/Photos/Show', [
            'photo' => $photo,
            'votes' => $votes,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Photo $photo)
    {
        $photo->load('project');
        $projects = Project::active()->get();

        return Inertia::render('Admin/Photos/Edit', [
            'photo' => $photo,
            'projects' => $projects,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PhotoRequest $request, Photo $photo, ImageOptimizationService $imageService)
    {
        $validated = $request->validated();

        $updateData = [
            'project_id' => $validated['project_id'],
            'caption' => $validated['caption'] ?? null,
        ];

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($photo->file_path) {
                Storage::disk('public')->delete($photo->file_path);
            }

            // Store and optimize new photo
            $file = $request->file('photo');
            $path = $imageService->optimizeUploadedImage($file, 'photos');
            $updateData['file_path'] = $path;
        }

        $photo->update($updateData);

        return redirect()->route('admin.photos.index')
            ->with('success', 'Foto atualizada e otimizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Photo $photo)
    {
        // Delete photo file
        if ($photo->file_path) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $photo->delete();

        // Preserve filters in redirect
        $filters = $request->only(['project', 'search']);
        
        return redirect()->route('admin.photos.index', $filters)
            ->with('success', 'Foto excluída com sucesso!');
    }
}
