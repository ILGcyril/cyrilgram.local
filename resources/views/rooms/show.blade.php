<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('messages.index', $room) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            
            <div class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <h2>Комната "{{ $room->name }}"</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="text-blue-300">Тип: {{ $room->type }}</p><br>
                    <p class="text-blue-300">Участники:</p>
                    @foreach($room->users as $user)
                        <ul>
                            <li>{{ $user->name }}</li>
                        </ul>
                    @endforeach
                    <br><br><br>

                        @if($role !== 'member')
                            <form action="{{ route('invites.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="room_id" id="room_id" value="{{ $room->id }}">
                                <input type="text" name="name" class="text-black" placeholder="Юзернейм">
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded">Отправить инвайт</button>
                            </form>
                            @error('name')
                                <p class="text-red-500">Такого пользователя не существует</p>
                            @enderror<br><br><br>

                            <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('Удалить?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-700 hover:bg-red-900 text-white px-4 py-2 rounded">Удалить эту комнату</button>
                            </form><br>
                            
                            <a href="{{ route('rooms.edit', $room->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white px-4 py-2 rounded">Обновить данные комнаты</a><br><br><br><br>
                        @endif
        
                        @if($role !== 'owner')
                            <form action="{{ route('rooms.leave', $room) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded">Выйти из комнаты</button>
                            </form><br>
                        @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>