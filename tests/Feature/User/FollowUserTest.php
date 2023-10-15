<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowUserTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var User $user */
        $user = User::factory()->create();
        $this->user = $user;
    }

    public function testFollowProfile(): void
    {
        /** @var User $follower */
        $follower = User::factory()->create();

        $response = $this->actingAs($follower)
            ->postJson("/api/profiles/{$this->user->username}/follow");
        $response->assertOk()
            ->assertJsonPath('profile.following', true);

        $this->actingAs($follower)
            ->postJson("/api/profiles/{$this->user->username}/follow")
            ->assertOk();
        $this->assertDatabaseCount('user_followers', 1);
    }

    public function testFollowProfileWithoutAuth(): void
    {
        $this->postJson("/api/profiles/{$this->user->username}/follow")
            ->assertUnauthorized();
    }

    public function testFollowNonExistentProfile(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/profiles/non-existent/follow')
            ->assertNotFound();
    }
}
