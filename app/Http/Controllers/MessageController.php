<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Events\MessageSent;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use App\Models\Room;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MessageController extends Controller
{
    use AuthorizesRequests;

    public function index(Room $room)
    {
        $this->authorize('view', $room);

        $messages = $room->messages()->with('user')->get();

        return view('messages.index', compact('messages', 'room'));
    }

    public function store(StoreMessageRequest $request, Room $room)
    {
        $this->authorize('view', $room);
        
        $data = $request->validated();

        $message = Message::create([
            'room_id' => $room->id,
            'user_id' => auth()->user()->id,
            'content' => $data['message']
        ]);

        $socketId = request('socket_id');
        $broadcast = broadcast(new MessageSent($message->load('user')))->toOthers($socketId);
    
        Log::info("Broadcasting MessageSent for room {$room->id}");
        
        Log::info("Broadcast result: " . json_encode($broadcast));

        return response()->json(['ok' => true]);
    }

    public function destroy(Room $room, Message $message)
    {
        $this->authorize('delete', $message);

        $message->delete();

        return back();
    }
}
