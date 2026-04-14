<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Room;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $rooms = (auth()->user())->rooms()->latest()->get();

        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(StoreRoomRequest $request)
    {
        $data = $request->validated();

        $room = DB::transaction(function() use ($data) {
            $room = Room::create($data);

            $room->users()->attach(auth()->id(), ['role' => 'owner']);

            return $room;
        });

        return redirect()->route('rooms.show', $room->id);
    }

    public function show(Room $room)
    {
        $this->authorize('view', $room);
        
        $role = auth()->user()->rooms()
            ->where('room_id', $room->id)
            ->first()->pivot->role;

        return view('rooms.show', compact('room', 'role'));
    }

    public function edit(Room $room)
    {
        $this->authorize('update', $room);
        
        return view('rooms.edit', compact('room'));
    }

    public function update(UpdateRoomRequest $request, Room $room)
    {
        $this->authorize('update', $room);

        $data = $request->validated();

        $room->update($data);

        return redirect()->route('rooms.show', $room->id);
    }

    public function destroy(Room $room)
    {
        $this->authorize('delete', $room);

        $room->delete();

        return redirect()->route('rooms.index');
    }

    public function publicRooms()
    {
        $rooms = Room::where('type', 'public')->latest()->paginate(10);
        $userRoomIds = auth()->user()->rooms->pluck('id');
    
        return view('rooms.public-rooms', compact('rooms', 'userRoomIds'));
    }

    public function join(Room $room)
    {
        if($room->type === 'private') {
            return redirect()->route('rooms.index');
        }        

        $user = auth()->user();

        if($user->rooms()->wherePivot('room_id', $room->id)->exists()) {
            return redirect()->route('rooms.show', $room);
        }

        $user->rooms()->attach($room->id, ['role' => 'member']);

        return redirect()->route('messages.index', $room);
    }

    public function leave(Room $room) 
    {
        $user = auth()->user();

        if ($user->rooms()->wherePivot('room_id', $room->id)->value('role') == 'owner') {
            abort(403, 'Owner cannot leave the room');
        }

        if(!$user->rooms()->wherePivot('room_id', $room->id)->exists()) {
            return redirect()->route('rooms.index');
        }
        
        $user->rooms()->detach($room->id);

        return redirect()->route('rooms.index');
    }
}
