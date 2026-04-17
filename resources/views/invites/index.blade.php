<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            
            <div class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <h2>Приглашения</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div>
                        @forelse($user->receivedInvites()->get() as $invite)
                            <div class="bg-white dark:bg-gray-700 px-4 py-2 rounded-lg w-fit shadow-sm border border-gray-100 dark:border-gray-600 flex items-center gap-4">
                                <div class="text-blue-300">Инвайт в {{ App\Models\Room::where('id', $invite->room_id)->first()->name }}</div>
                                
                                <form action="{{ route('invites.accept', $invite) }}" method="POST" class="mb-4">
                                    @csrf
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white px-2 py-1 rounded">Принять</button>
                                </form>

                                <form action="{{ route('invites.decline', $invite) }}" method="POST" class="mb-4">
                                    @csrf
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 rounded">Отклонить</button>
                                </form>
                            </div><br>
                        @empty
                            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                                <p>Пустота.. Похоже, вам еще не приходили приглашения</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>