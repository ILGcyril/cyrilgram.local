<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RoomPolicy
{
    public function view(User $user, Room $room): bool
    {
        return $user->rooms()->where('room_id', $room->id)->exists();
    }

    public function update(User $user, Room $room): bool
    {
        return $user->rooms()->where('room_id', $room->id)->wherePivotIn('role', ['owner', 'admin'])->exists();
    }

    public function delete(User $user, Room $room): bool
    {
        return $user->rooms()->where('room_id', $room->id)->wherePivot('role', 'owner')->exists();
    }
}
