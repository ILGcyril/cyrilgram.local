<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('rooms.show', $room) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
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
                    <form action="{{ route('rooms.update', $room->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <p>Новое название комнаты: <input type="text" name="name" id="name" value="{{ $room->name }}" class="text-gray-900 dark:text-black"></p>
                        <p>Тип: <select id="type" name="type" class="text-gray-900 dark:text-black">
                                    <option value="public" {{ $room->type === 'public' ? 'selected' : '' }}>Публичная</option>
                                    <option value="private" {{ $room->type === 'private' ? 'selected' : '' }}>Приватная</option>
                                </select><br>
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded">Обновить</button>
                        <br><br>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>