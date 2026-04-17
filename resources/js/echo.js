import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Инициализируем Echo ТОЛЬКО если есть все переменные
const appKey = import.meta.env.VITE_REVERB_APP_KEY;
const wsHost = import.meta.env.VITE_REVERB_HOST;

if (appKey && appKey !== 'test' && wsHost) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: appKey,
        wsHost: wsHost,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        cluster: 'us-east-1',
    });
} else {
    // Заглушка чтобы не было ошибок
    window.Echo = {
        private: () => ({ 
            listen: () => ({}) 
        }),
        socketId: () => null,
    };
}