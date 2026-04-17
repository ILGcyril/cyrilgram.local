import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Читаем конфиг из meta-тегов
const key = document.querySelector('meta[name="pusher-key"]')?.content;
const cluster = document.querySelector('meta[name="pusher-cluster"]')?.content;
const host = document.querySelector('meta[name="pusher-host"]')?.content;
const port = document.querySelector('meta[name="pusher-port"]')?.content;
const scheme = document.querySelector('meta[name="pusher-scheme"]')?.content;

console.log('🔌 Pusher config from meta:', { key, cluster, host });

if (key) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: key,
        wsHost: host,
        wsPort: port ?? 80,
        wssPort: port ?? 443,
        forceTLS: scheme === 'https',
        enabledTransports: ['ws', 'wss'],
        cluster: cluster,
    });
    
    console.log('✅ Echo initialized');
} else {
    console.error('❌ Pusher key not found in meta tags!');
}