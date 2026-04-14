<x-app-layout>
    <x-slot name="header">
        <div class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <h2>Главная страница</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <a href="{{ route('rooms.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">Мои комнаты</a><br><br>
                    <a href="{{ route('rooms.public') }}" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">Публичные комнаты</a><br><br>
                    <a href="{{ route('users.search') }}" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">Найти пользователя</a><br><br>
                    <a href="{{ route('invites.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">Приглашения в комнаты</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>