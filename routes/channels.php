<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('room.{roomId}', function ($user, $roomId) {
    Log::info("Authorizing user {$user->id} for room {$roomId}");
    return $user->rooms()->where('room_id', $roomId)->exists();
});