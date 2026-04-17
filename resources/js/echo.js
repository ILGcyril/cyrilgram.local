import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const appKey = import.meta.env.VITE_PUSHER_APP_KEY;
const cluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;

console.log('🔌 Pusher config:', {
    key: appKey,
    host: import.meta.env.VITE_PUSHER_HOST,
    port: import.meta.env.VITE_PUSHER_PORT,
    cluster: cluster,
});

if (!appKey) {
    console.error('❌ VITE_PUSHER_APP_KEY is not set!');
}

if (!cluster) {
    console.error('❌ VITE_PUSHER_APP_CLUSTER is not set!');
}

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: appKey,
    wsHost: import.meta.env.VITE_PUSHER_HOST,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    cluster: cluster,
});

console.log('✅ Echo initialized');