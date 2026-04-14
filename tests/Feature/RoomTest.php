<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Concerns\ResolvesDumpSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Termwind\Components\Raw;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_rooms()
    {
        Room::factory()->create();

        $response = $this->actingAsGuest()
            ->get('/rooms');

        $response->assertRedirect('/login');
    }

    public function test_user_can_create_room()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/rooms', [
                'name' => 'test_name',
                'type' => 'public'
            ]);

        $room = Room::first();
        $response->assertRedirect(route('rooms.show', $room->id));

        $this->assertDatabaseHas('rooms', [
            'name' => 'test_name',
            'type' => 'public'
        ]);
    }

    public function test_guest_cannot_create_room()
    {
        $response = $this->actingAsGuest()
            ->post('/rooms', [
                'name' => 'test_name',
                'type' => 'public',
            ]);

        $response->assertRedirect('/login');
    }

    public function test_user_cannot_view_room_without_membership()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        $response = $this->actingAs($user)
            ->get("/rooms/$room->id");

        $response->assertForbidden();
    }

    public function test_user_can_join_in_public_room()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        $response = $this->actingAs($user)
            ->post("/rooms/$room->id/join");

        $response->assertRedirect(route('messages.index', $room->id));

        $this->assertDatabaseHas('room_user', [
            'room_id' => $room->id,
            'user_id' => $user->id
        ]);
    }

    public function test_user_cannot_join_in_private_room()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create(['type' => 'private']);

        $response = $this->actingAs($user)
            ->post("/rooms/$room->id/join");

        $response->assertRedirect(route('rooms.index'));

        $this->assertDatabaseMissing('room_user', [
            'room_id' => $room->id,
            'user_id' => $user->id
        ]);
    }

    public function test_user_can_leave_from_room_if_exists()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $room->users()->attach($user->id, ['role' => 'admin']);

        $response = $this->actingAs($user)
            ->post("/rooms/$room->id/leave");

        $response->assertRedirect(route('rooms.index'));

        $this->assertDatabaseMissing('room_user', [
            'room_id' => $room->id,
            'user_id' => $user->id,
        ]);
    }
}
