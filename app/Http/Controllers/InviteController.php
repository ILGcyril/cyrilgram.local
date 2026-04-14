<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInviteRequest;
use App\Models\Invite;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class InviteController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = auth()->user();

        return view('invites.index', compact('user'));
    }

    public function store(StoreInviteRequest $request)
    {
        $room = Room::findOrFail($request->room_id);
        $toUser = User::where('name', $request->name)->firstOrFail();
        $this->authorize('create', [Invite::class, $room, $toUser]);

        $data = $request->validated();

        $invite = Invite::create([
            'room_id' => $data['room_id'],
            'from_user_id' => auth()->id(),
            'to_user_id' => (int) User::where('name', $data['name'])->first()->id,
            'status' => 'pending'
        ]);

        return redirect()->route('rooms.show', $data['room_id']);
    }

    public function accept(Invite $invite)
    {
        $this->authorize('accept', $invite);

        DB::transaction(function() use ($invite) {
            $invite->room->users()->attach(auth()->id(), ['role' => 'member']);
            $invite->delete();
        });

        return redirect()->route('rooms.show', $invite->room_id);
    }

    public function decline(Invite $invite)
    {
        $this->authorize('decline', $invite);

            $invite->delete();

        return redirect()->route('invites.index');
    }
}