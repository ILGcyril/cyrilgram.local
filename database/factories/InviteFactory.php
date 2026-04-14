<?php

namespace Database\Factories;

use App\Models\Invite;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invite>
 */
class InviteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'to_user_id' => User::factory(),
            'from_user_id' =>  User::factory(),
            'status' => 'pending'
        ];
    }
}
