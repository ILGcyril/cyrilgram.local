<?php

use App\Http\Controllers\InviteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    //auth
    Route::get('/', function() { return view('home'); })->name('home');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //rooms
    Route::resource('/rooms', RoomController::class);
    Route::get('/public-rooms', [RoomController::class, 'publicRooms'])->name('rooms.public');
    Route::post('/rooms/{room}/join', [RoomController::class, 'join'])->name('rooms.join');
    Route::post('/rooms/{room}/leave', [RoomController::class, 'leave'])->name('rooms.leave');

    //messages
    Route::get('/rooms/{room}/chat', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/rooms/{room}/chat', [MessageController::class, 'store'])->name('messages.store');
    Route::delete('/rooms/{room}/chat/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

    //users search
    Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    //invites
    Route::get('/invites', [InviteController::class, 'index'])->name('invites.index');
    Route::post('/invites', [InviteController::class, 'store'])->name('invites.store'); 
    Route::post('/invites/{invite}/accept', [InviteController::class, 'accept'])->name('invites.accept');
    Route::post('/invites/{invite}/decline', [InviteController::class, 'decline'])->name('invites.decline');
});

require __DIR__.'/auth.php';