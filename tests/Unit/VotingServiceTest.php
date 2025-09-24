<?php

namespace Tests\Unit;

use App\Exceptions\VotingException;
use App\Models\Photo;
use App\Models\Project;
use App\Models\User;
use App\Models\Vote;
use App\Services\VotingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VotingServiceTest extends TestCase
{
    use RefreshDatabase;

    private VotingService $votingService;
    private User $user;
    private Project $activeProject;
    private Project $inactiveProject;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->votingService = new VotingService();
        
        // Create test user
        $this->user = User::factory()->create();
        
        // Create active and inactive projects
        $this->activeProject = Project::factory()->create(['is_active' => true]);
        $this->inactiveProject = Project::factory()->create(['is_active' => false]);
    }

    /** @test */
    public function user_can_vote_for_photo_in_active_project(): void
    {
        $photo = Photo::factory()->create(['project_id' => $this->activeProject->id]);
        
        $this->actingAs($this->user);
        $result = $this->votingService->vote($photo);

        $this->assertInstanceOf(Vote::class, $result);
        $this->assertDatabaseHas('votes', [
            'user_id' => $this->user->id,
            'photo_id' => $photo->id,
        ]);
    }

    /** @test */
    public function user_cannot_vote_for_photo_in_inactive_project(): void
    {
        $photo = Photo::factory()->create(['project_id' => $this->inactiveProject->id]);

        $this->actingAs($this->user);
        $this->expectException(VotingException::class);
        $this->expectExceptionMessage('Este projeto não está mais ativo para votação.');

        $this->votingService->vote($photo);
    }

    /** @test */
    public function user_cannot_vote_twice_for_same_photo(): void
    {
        $photo = Photo::factory()->create(['project_id' => $this->activeProject->id]);
        
        $this->actingAs($this->user);
        
        // First vote should succeed
        $this->votingService->vote($photo);
        
        // Second vote should fail
        $this->expectException(VotingException::class);
        $this->expectExceptionMessage('Você já votou nesta foto.');

        $this->votingService->vote($photo);
    }

    /** @test */
    public function user_can_vote_up_to_10_photos(): void
    {
        $photos = Photo::factory()->count(10)->create(['project_id' => $this->activeProject->id]);

        $this->actingAs($this->user);
        
        foreach ($photos as $photo) {
            $result = $this->votingService->vote($photo);
            $this->assertInstanceOf(Vote::class, $result);
        }

        $this->assertEquals(10, $this->votingService->getUserVoteCount($this->user->id));
    }

    /** @test */
    public function user_cannot_vote_more_than_10_photos(): void
    {
        $photos = Photo::factory()->count(11)->create(['project_id' => $this->activeProject->id]);

        $this->actingAs($this->user);
        
        // Vote for first 10 photos
        for ($i = 0; $i < 10; $i++) {
            $this->votingService->vote($photos[$i]);
        }

        // 11th vote should fail
        $this->expectException(VotingException::class);
        $this->expectExceptionMessage('Você já atingiu o limite máximo de 10 votos.');

        $this->votingService->vote($photos[10]);
    }

    /** @test */
    public function user_can_vote_freely_across_different_projects(): void
    {
        $project1 = Project::factory()->create(['is_active' => true]);
        $project2 = Project::factory()->create(['is_active' => true]);
        $project3 = Project::factory()->create(['is_active' => true]);

        $photos1 = Photo::factory()->count(3)->create(['project_id' => $project1->id]);
        $photos2 = Photo::factory()->count(4)->create(['project_id' => $project2->id]);
        $photos3 = Photo::factory()->count(3)->create(['project_id' => $project3->id]);

        $this->actingAs($this->user);
        
        // Vote for 3 photos from project1, 4 from project2, 3 from project3
        foreach ($photos1 as $photo) {
            $this->votingService->vote($photo);
        }
        foreach ($photos2 as $photo) {
            $this->votingService->vote($photo);
        }
        foreach ($photos3 as $photo) {
            $this->votingService->vote($photo);
        }

        $this->assertEquals(10, $this->votingService->getUserVoteCount($this->user->id));
    }

    /** @test */
    public function user_can_vote_all_10_photos_from_single_project(): void
    {
        $photos = Photo::factory()->count(10)->create(['project_id' => $this->activeProject->id]);

        $this->actingAs($this->user);
        
        foreach ($photos as $photo) {
            $this->votingService->vote($photo);
        }

        $this->assertEquals(10, $this->votingService->getUserVoteCount($this->user->id));
        
        // Verify all votes are from the same project
        $userVotes = $this->votingService->getUserVotes($this->user->id);
        foreach ($userVotes as $vote) {
            $this->assertEquals($this->activeProject->id, $vote->photo->project_id);
        }
    }

    /** @test */
    public function user_can_unvote_and_vote_again(): void
    {
        $photo1 = Photo::factory()->create(['project_id' => $this->activeProject->id]);
        $photo2 = Photo::factory()->create(['project_id' => $this->activeProject->id]);

        $this->actingAs($this->user);
        
        // Vote for photo1
        $this->votingService->vote($photo1);
        $this->assertEquals(1, $this->votingService->getUserVoteCount($this->user->id));

        // Unvote photo1
        $result = $this->votingService->unvote($photo1);
        $this->assertTrue($result);
        $this->assertEquals(0, $this->votingService->getUserVoteCount($this->user->id));

        // Vote for photo2
        $this->votingService->vote($photo2);
        $this->assertEquals(1, $this->votingService->getUserVoteCount($this->user->id));
    }

    /** @test */
    public function get_user_votes_returns_correct_votes(): void
    {
        $photos = Photo::factory()->count(3)->create(['project_id' => $this->activeProject->id]);

        $this->actingAs($this->user);
        
        foreach ($photos as $photo) {
            $this->votingService->vote($photo);
        }

        $userVotes = $this->votingService->getUserVotes($this->user->id);
        
        $this->assertCount(3, $userVotes);
        foreach ($userVotes as $vote) {
            $this->assertEquals($this->user->id, $vote->user_id);
            $this->assertContains($vote->photo_id, $photos->pluck('id')->toArray());
        }
    }

    /** @test */
    public function has_user_voted_returns_correct_status(): void
    {
        $photo = Photo::factory()->create(['project_id' => $this->activeProject->id]);

        $this->assertFalse($this->votingService->hasUserVoted($this->user->id, $photo->id));

        $this->actingAs($this->user);
        $this->votingService->vote($photo);

        $this->assertTrue($this->votingService->hasUserVoted($this->user->id, $photo->id));
    }

    /** @test */
    public function get_user_vote_count_returns_correct_count(): void
    {
        $this->assertEquals(0, $this->votingService->getUserVoteCount($this->user->id));

        $photos = Photo::factory()->count(5)->create(['project_id' => $this->activeProject->id]);

        $this->actingAs($this->user);
        
        foreach ($photos as $photo) {
            $this->votingService->vote($photo);
        }

        $this->assertEquals(5, $this->votingService->getUserVoteCount($this->user->id));
    }
}