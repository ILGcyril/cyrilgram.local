<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_search_for_other_users()
    {
        $currentUser = User::factory()->create(['name' => 'Ivan']);
        $targetUser = User::factory()->create(['name' => 'Ivan Petrov']);
        $otherUser = User::factory()->create(['name' => 'Maria']);

        $response = $this->actingAs($currentUser)
            ->get(route('users.search', ['username' => 'Ivan']));

        $response->assertStatus(200);
        $response->assertViewIs('users.search');
        
        $response->assertSeeText('Ivan Petrov');
        
        $response->assertDontSeeText('Maria');
    }

    public function test_search_excludes_current_user()
    {
        $currentUser = User::factory()->create(['name' => 'Alex']);
        
        $response = $this->actingAs($currentUser)
            ->get(route('users.search', ['username' => 'Alex']));

        $response->assertStatus(200);
        
        $response->assertSeeText('Пользователи не найдены');
    }

    public function test_search_returns_empty_if_no_query()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get(route('users.search'));

        $response->assertStatus(200);
        $response->assertViewIs('users.search');
    }

    public function test_user_can_view_existing_user_profile()
    {
        $viewer = User::factory()->create();
        $profileUser = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);

        $response = $this->actingAs($viewer)
            ->get(route('users.show', $profileUser));

        $response->assertStatus(200);
        $response->assertViewIs('users.show');
        $response->assertSeeText('John Doe');
    }

    public function test_viewing_non_existent_user_returns_404()
    {
        $viewer = User::factory()->create();

        $response = $this->actingAs($viewer)
            ->get(route('users.show', 99999));

        $response->assertNotFound();
    }

    public function test_guest_cannot_view_user_profile()
    {
        $profileUser = User::factory()->create(['name' => 'Jane Doe']);

        $response = $this->get(route('users.show', $profileUser));

        $response->assertRedirect(route('login'));
    }
}
