<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>      

        <div class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <h2>Публичные комнаты</h2>    
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <hr><br>
                    @foreach($rooms as $room)
                            <form action="{{ route('rooms.join', $room) }}" method="POST" class="mb-4">
                                @csrf    
                                <div class="bg-white dark:bg-gray-700 px-4 py-2 rounded-lg w-fit shadow-sm border border-gray-100 dark:border-gray-600">
                                    <div class="text-blue-300">{{ $room->name }}</div>
                                    @if($userRoomIds->contains($room->id))
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">Вы уже состоите в этой комнате*</p><br><br>
                                    @else
                                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white px-2 py-1 rounded">Вступить</button>
                                    @endif
                                </div>
                            </form>
                    @endforeach
                    <br><hr>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>