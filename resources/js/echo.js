import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Читаем конфиг из meta-тегов
const key = document.querySelector('meta[name="pusher-key"]')?.content;
const cluster = document.querySelector('meta[name="pusher-cluster"]')?.content;

console.log('🔌 Pusher config:', { key, cluster });

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: key,
    cluster: cluster,  // Echo сам построит правильный wsHost
    forceTLS: true,    // Только HTTPS/WSS
    encrypted: true,
    // НЕ указываем wsHost - пусть Echo сам определит!
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
});

console.log('✅ Echo initialized');