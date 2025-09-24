<?php

namespace App\Http\Controllers;

use App\Exceptions\VotingException;
use App\Http\Requests\UnvoteRequest;
use App\Http\Requests\VoteRequest;
use App\Models\Photo;
use App\Models\Project;
use App\Models\Vote;
use App\Services\VotingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class VotingController extends Controller
{
    /**
     * The voting service instance.
     */
    protected VotingService $votingService;

    /**
     * Create a new controller instance.
     */
    public function __construct(VotingService $votingService)
    {
        $this->votingService = $votingService;
    }

    /**
     * Display active projects for voting - redirects to unified gallery.
     */
    public function index()
    {
        // Redirect directly to the unified gallery as requested
        return redirect()->route('voting.gallery');
    }

    /**
     * Display all photos from active projects for unified voting.
     */
    public function gallery()
    {
        // Get all active projects with their photos
        $projects = Project::active()
            ->with(['photos' => function ($query) {
                $query->withCount('votes');
            }])
            ->get();

        // Flatten all photos from all projects
        $allPhotos = collect();
        foreach ($projects as $project) {
            foreach ($project->photos as $photo) {
                $photo->project_name = $project->name;
                $photo->project_id = $project->id;
                $allPhotos->push($photo);
            }
        }

        // Get user's votes for all photos
        $user = Auth::user();
        $userVotes = Vote::where('user_id', $user ? $user->id : null)
            ->whereIn('photo_id', $allPhotos->pluck('id'))
            ->pluck('photo_id')
            ->toArray();

        return Inertia::render('Voting/Gallery', [
            'photos' => $allPhotos,
            'userVotes' => $userVotes,
            'projects' => $projects,
        ]);
    }

    /**
     * Display photos for a specific project.
     */
    public function show(Project $project)
    {
        if (! $project->is_active) {
            abort(404, 'Projeto não encontrado ou inativo.');
        }

        $photos = $project->photos()
            ->withCount('votes')
            ->get();

        // Get user's votes for this project
        $userVotes = Vote::where('user_id', Auth::id())
            ->whereIn('photo_id', $photos->pluck('id'))
            ->get();

        // Add project statistics
        $project->loadCount('votes as vote_count');
        $project->unique_voters = Vote::whereIn('photo_id', $photos->pluck('id'))
            ->distinct('user_id')
            ->count('user_id');

        return Inertia::render('Voting/Show', [
            'project' => $project,
            'photos' => $photos,
            'userVotes' => $userVotes,
        ]);
    }

    /**
     * Vote for a photo.
     */
    public function vote(VoteRequest $request, $photo)
    {
        // Manually resolve the Photo model for debugging
        if (is_numeric($photo)) {
            $photo = Photo::findOrFail($photo);
        }
        
        Log::info("VotingController::vote called with Photo ID: " . ($photo->id ?? 'NULL') . ", Photo exists: " . ($photo->exists ? 'true' : 'false'));
        Log::info("Photo attributes: " . json_encode($photo->getAttributes()));
        Log::info("Request route parameters: " . json_encode($request->route()->parameters()));
        
        try {
            $this->votingService->vote($photo);

            return back()->with('success', 'Voto registrado com sucesso!');
        } catch (VotingException $e) {
            Log::error("VotingException in vote: " . $e->getMessage());
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error("Exception in vote: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            return back()->withErrors(['error' => 'Erro interno do servidor.']);
        }
    }

    /**
     * Remove vote from a photo.
     */
    public function unvote(UnvoteRequest $request, $photo)
    {
        // Manually resolve the Photo model for debugging
        if (is_numeric($photo)) {
            $photo = Photo::findOrFail($photo);
        }
        
        try {
            $this->votingService->unvote($photo);

            $userVotes = $this->votingService->getUserVotes((int) Auth::id());

            return back()->with([
                'success' => 'Voto removido com sucesso!',
                'userVotes' => $userVotes,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
