<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_get_messages()
    {
        $message = Message::factory()->create();

        $response = $this->actingAsGuest()
            ->get("/rooms/$message->room_id/chat");

        $response->assertRedirect(route('login'));
    }
    
    public function test_user_cannot_get_messages_if_not_member()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $message = Message::factory()->create(['room_id' => $room->id]);

        $response = $this->actingAs($user)
            ->get("/rooms/$message->room_id/chat");

        $response->assertForbidden();
    }
    
    public function test_member_can_get_messages()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $room->users()->attach($user->id);
        $message = Message::factory()->create(['room_id' => $room->id]);

        $response = $this->actingAs($user)
            ->get("/rooms/$message->room_id/chat");

        $response->assertOk();
    }

    public function test_gueset_cannot_create_messages()
    {
        $room = Room::factory()->create();

        $response = $this->actingAsGuest()
            ->post("/rooms/$room->id/chat", [
                'content' => 'test_content',
                'room_id' => $room->id,
                'user_id' => 1
            ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('messages', [
            'content' => 'test_content',
            'room_id' => $room->id
        ]);
    }

    public function test_user_cannot_create_messages_if_not_member()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('messages.store', $room), [
                'message' => 'test_content'
            ]
        );
        
        $response->assertForbidden();
        
        $this->assertDatabaseMissing('messages', [
            'room_id' => $room->id,
            'content' => 'test_content'
        ]);
    }

    public function test_member_can_create_message()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $room->users()->attach($user->id);

        $response = $this->actingAs($user)
            ->post(route('messages.store', $room), [
                'message' => 'test_content'
            ]
        );

        $response->assertOk();

        $this->assertDatabaseHas('messages', [
            'user_id' => $user->id,
            'content' => 'test_content'
        ]);
    }

    public function test_guest_cannot_delete_message()
    {
        $message = Message::factory()->create();

        $response = $this->actingAsGuest()
            ->delete("/rooms/$message->room_id/chat/$message");

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'content' => $message->content,
            'room_id' => $message->room_id,
            'user_id' => $message->user_id,
        ]);
    }

    public function test_user_cannot_delete_other_users_message()
    {
        $message = Message::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->delete("/rooms/$message->room_id/chat/$message->id");

        $response->assertForbidden();

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'content' => $message->content,
            'room_id' => $message->room_id,
            'user_id' => $message->user_id,
        ]);
    }

    public function test_user_can_delete_own_message()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $room->users()->attach($user->id);
        $message = Message::factory()->create(['room_id' => $room->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->delete("/rooms/$room->id/chat/$message->id");

        $response->assertRedirectBack();

        $this->assertDatabaseMissing('messages', [
            'id' => $message->id,
            'content' => $message->content,
            'user_id' => $message->user_id,
            'room_id' => $message->room_id
        ]);
    }
}
