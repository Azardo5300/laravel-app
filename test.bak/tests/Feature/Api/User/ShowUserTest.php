<?php

namespace Tests\Feature\Api\User;

use App\Jwt;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ShowUserTest extends TestCase
{
    public function testShowUser(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/user');

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('user', fn (AssertableJson $item) =>
                    $item->whereAll([
                            'username' => $user->username,
                            'email' => $user->email,
                            'bio' => $user->bio,
                            'avatar' => $user->avatar,
                        ])->etc()
                )
            );
        $this->assertAuthenticatedAs($user);
    }

    public function testShowUserWithoutAuth(): void
    {
        $this->getJson('/api/user')
            ->assertUnauthorized();
    }
}
