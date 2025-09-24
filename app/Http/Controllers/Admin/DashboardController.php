<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Project;
use App\Models\User;
use App\Models\Vote;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'total_projects' => Project::count(),
            'active_projects' => Project::active()->count(),
            'total_photos' => Photo::count(),
            'total_votes' => Vote::count(),
            'total_users' => User::count(),
            'voters' => User::where('role', 'voter')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ];

        // Get recent projects
        $recentProjects = Project::with('photos')
            ->withCount(['photos', 'votes'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get most voted photos (Hall das 10 mais votadas - apenas fotos que receberam votos)
        $topPhotos = Photo::with(['project', 'votes'])
            ->withCount('votes')
            ->whereHas('votes')
            ->orderBy('votes_count', 'desc')
            ->limit(10)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentProjects' => $recentProjects,
            'topPhotos' => $topPhotos,
        ]);
    }
}
