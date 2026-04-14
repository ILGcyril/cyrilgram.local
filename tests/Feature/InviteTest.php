<?php

namespace Tests\Feature;

use App\Models\Invite;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InviteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_invite()
    {
        $room = Room::factory()->create();
        $from_user = User::factory()->create();
        $to_user = User::factory()->create();

        $response = $this->actingAsGuest()
            ->post("/invites", [
                'room_id' => $room->id,
                'from_user' => $from_user->id,
                'to_user' => $to_user->id,
                'status' => 'pending'
            ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('invites', [
            'room_id' => $room->id,
            'from_user_id' => $from_user->id,
            'to_user_id' => $to_user->id,
            'status' => 'pending'
        ]);
    }

    public function test_user_cannot_create_invite_if_not_member_of_room()
    {
        $room = Room::factory()->create();
        $from_user = User::factory()->create();
        $to_user = User::factory()->create();

        $response = $this->actingAs($from_user)
            ->post("/invites", [
                'room_id' => $room->id,
                'name' => $to_user->name,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('invites', [
            'room_id' => $room->id,
            'from_user_id' => $from_user->id,
            'to_user_id' => $to_user->id,
            'status' => 'pending'
        ]);
    }

    public function test_member_can_create_invite()
    {
        $from_user = User::factory()->create();
        $room = Room::factory()->create();
        $room->users()->attach($from_user, ['role' => 'owner']);
        $to_user = User::factory()->create();

        $response = $this->actingAs($from_user)
            ->post("/invites", [
                'room_id' => $room->id,
                'name' => $to_user->name,
            ]);

        $response->assertRedirect(route('rooms.show', $room->id));

        $this->assertDatabaseHas('invites', [
            'room_id' => $room->id,
            'from_user_id' => $from_user->id,
            'to_user_id' => $to_user->id,
            'status' => 'pending'
        ]);
    }

    public function test_member_cannot_invite_non_existent_user()
    {
        $from_user = User::factory()->create();
        $room = Room::factory()->create();
        $room->users()->attach($from_user, ['role' => 'owner']);

        $response = $this->actingAs($from_user)
            ->post("/invites", [
                'room_id' => $room->id,
                'name' => 'test_name',
            ]);

        $this->assertDatabaseMissing('invites', [
            'room_id' => $room->id,
            'from_user_id' => $from_user->id,
            'status' => 'pending'
        ]);
    }

    public function test_user_can_accept_invite_and_join_room()
{
    $room = Room::factory()->create();
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $invite = Invite::factory()->create([
        'room_id' => $room->id,
        'to_user_id' => $user->id,
        'from_user_id' => $otherUser->id,
        'status' => 'pending'
    ]);

    $response = $this->actingAs($user)
        ->post(route('invites.accept', $invite));

    $response->assertRedirect(route('rooms.show', $room->id));
    
    $this->assertDatabaseMissing('invites', ['id' => $invite->id]);
    
    $this->assertDatabaseHas('room_user', [
        'room_id' => $room->id,
        'user_id' => $user->id,
        'role' => 'member'
    ]);
}

public function test_user_cannot_accept_invite_if_not_recipient()
{
    $room = Room::factory()->create();
    $user = User::factory()->create();
    $anotherUser = User::factory()->create();
    $invite = Invite::factory()->create([
        'room_id' => $room->id,
        'to_user_id' => $anotherUser->id,
        'status' => 'pending'
    ]);

    $response = $this->actingAs($user)->post(route('invites.accept', $invite));

    $response->assertForbidden();
    
    $this->assertDatabaseHas('invites', ['id' => $invite->id]);
    $this->assertDatabaseMissing('room_user', [
        'room_id' => $room->id,
        'user_id' => $user->id
    ]);
}

public function test_user_cannot_accept_non_existent_invite()
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('invites.accept', 99999));

    $response->assertNotFound();
}
}
