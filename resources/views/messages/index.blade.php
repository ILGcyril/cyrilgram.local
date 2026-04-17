<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('rooms.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition" title="К спискам комнат">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Чат: {{ $room->name }}
                </h2>
            </div>

            {{-- Правая часть: Кнопка управления комнатой --}}
            <a href="{{ route('rooms.show', $room) }}" class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-3 py-1.5 rounded-lg transition text-sm font-medium border border-gray-300 dark:border-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Инфо</span>
            </a>
        </div>
    </x-slot>

    <div class="p-6 text-gray-900 dark:text-gray-100">
        <div class="space-y-4 mb-6 h-[500px] overflow-y-auto p-4 bg-gray-50/50 dark:bg-gray-900/20 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
        @forelse($messages as $message)
            <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }} group mb-4">
                
                <div class="relative max-w-[80%] px-4 py-2 rounded-xl border 
                    {{ $message->user_id === auth()->id() 
                        ? 'bg-indigo-500 text-white border-indigo-600 rounded-tr-none' 
                        : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-200 dark:border-gray-600 rounded-tl-none' 
                    }}">
                    
                    <div class="text-xs font-bold opacity-90 mb-1">{{ $message->user->name }}</div>
                    <div class="text-sm">{{ $message->content }}</div>

                    @if($message->user_id == auth()->id())
                        <div class="absolute -top-3 {{ $message->user_id === auth()->id() ? '-left-6' : '-right-6' }} flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <form action="{{ route('messages.destroy', [$room, $message]) }}" method="POST" onsubmit="return confirm('Удалить?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 p-1.5 rounded-full text-white hover:bg-red-600 shadow-md">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p>Пустота.. Начните разговор своим сообщением</p>
            </div>
        @endforelse
        </div>

        <form id="chat-form" action="{{ str_replace('http://', 'https://', route('messages.store', $room))" method="POST" class="flex gap-2 border-t dark:border-gray-700 pt-4" >
            @csrf
            <input type="hidden" name="socket_id" id="socket_id">
            <div class="relative flex-grow">
                <input 
                    type="text" 
                    name="message" 
                    id="message" 
                    placeholder="Написать сообщение..." 
                    class="w-full rounded-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2"
                    required
                >
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full font-semibold transition duration-150 ease-in-out">
                Отправить
            </button>
        </form>
    </div>
</x-app-layout>
<script>
window.addEventListener('load', function() {
    const chat = document.querySelector('.space-y-4');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('message');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const messageText = input.value;
        if (!messageText.trim()) return;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: messageText })
        })
        .then(response => {
            if (!response.ok) throw new Error('Ошибка сервера');
            return response.json();
        })
        .then(() => {
            const div = document.createElement('div');
            div.classList.add('flex', 'justify-end', 'group', 'mb-4');
            div.innerHTML = `
                <div class="relative max-w-[80%] px-4 py-2 rounded-xl border bg-indigo-500 text-white border-indigo-600 rounded-tr-none">
                    <div class="text-xs font-bold opacity-90 mb-1 text-right">{{ auth()->user()->name }}</div>
                    <div class="text-sm">${messageText}</div>
                </div>
            `;
            chat.appendChild(div);
            chat.scrollTop = chat.scrollHeight;
            input.value = '';
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Не удалось отправить сообщение');
        });
    });

    // Безопасная подписка на канал
    if (window.Echo && typeof window.Echo.private === 'function') {
        window.Echo.private(`room.{{ $room->id }}`)
            .listen('MessageSent', (e) => {
                const div = document.createElement('div');
                div.classList.add('flex', 'justify-start', 'group', 'mb-4');
                div.innerHTML = `
                    <div class="relative max-w-[80%] px-4 py-2 rounded-xl border bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-gray-200 dark:border-gray-600 rounded-tl-none">
                        <div class="text-xs font-bold opacity-90 mb-1">${e.message.user.name}</div>
                        <div class="text-sm">${e.message.content}</div>
                    </div>
                `;
                chat.appendChild(div);
                chat.scrollTop = chat.scrollHeight;
            });
    }
});
</script>