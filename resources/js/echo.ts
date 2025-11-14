import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make Pusher available globally for Laravel Echo
(window as any).Pusher = Pusher;

function getCsrfToken(): string {
    const metaToken = document.head.querySelector('meta[name="csrf-token"]');
    return metaToken?.getAttribute('content') || '';
}

const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;

const echo = new Echo({
    broadcaster: 'pusher',
    key: pusherKey || '',
    cluster: pusherCluster || 'mt1',
    forceTLS: true,
    encrypted: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
    },
});

export default echo;

