<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('room.{roomId}', function ($user, $roomId) {
    // Убедись что пользователь авторизован
    if (!$user) {
        Log::info("User not authenticated");
        return false;
    }
    
    $exists = $user->rooms()->where('room_id', $roomId)->exists();
    Log::info("User {$user->id} authorized for room {$roomId}: " . ($exists ? 'YES' : 'NO'));
    
    return $exists;
});