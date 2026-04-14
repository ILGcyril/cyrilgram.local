<?php

namespace App\Policies;

use App\Models\Invite;
use App\Models\Room;
use App\Models\User;

class InvitePolicy
{
    public function accept(User $user, Invite $invite): bool
    {
        return $invite->to_user_id === $user->id;
    }

    public function decline(User $user, Invite $invite): bool
    {
        return $invite->to_user_id === $user->id;
    }

    public function create(User $user, Room $room, $toUser):bool
    {
        return !($room->users()->wherePivot('user_id', $toUser->id)->exists()) && $room->users()->where('user_id', $user->id)->exists();
    }
}
