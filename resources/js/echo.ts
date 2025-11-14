import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios';

// Make Pusher available globally for Laravel Echo
(window as any).Pusher = Pusher;

// Function to get CSRF token
function getCsrfToken(): string {
    const metaToken = document.head.querySelector('meta[name="csrf-token"]');
    return metaToken?.getAttribute('content') || '';
}

const echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY || '',
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
    forceTLS: true,
    encrypted: true,
});

export default echo;

