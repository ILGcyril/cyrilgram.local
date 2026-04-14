<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>      

        <div class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <h2>Мои комнаты</h2>    
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-white ">Комнаты:</h2>
                    <hr><br>
                    @forelse($rooms as $room)
                        <div class="bg-white dark:bg-gray-700 px-4 py-2 rounded-lg w-fit shadow-sm border border-gray-100 dark:border-gray-600">
                            <div class="text-blue-300">{{ $room->name }}</div><br>
                            <a href="{{ route('messages.index', $room) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Чат</a>
                        </div><br>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-gray-400">

                            <p>Пустота.. Создайте или войдите в свою первую комнату</p>
                        </div>
                    @endforelse
                    <hr><br><br><br>

                    <a href="{{ route('rooms.create') }}" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded">Создать комнату</a><br><br>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>