import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Determine which broadcaster to use based on environment variables
// Check for actual non-empty values
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;

// Pusher takes priority if configured
const usePusher = pusherKey && pusherKey.length > 0;
const useReverb = !usePusher && reverbKey && reverbKey.length > 0;

let echoConfig;

if (usePusher) {
    // Pusher configuration (for shared hosting / production)
    const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER || 'ap2';

    console.log('[Echo] Connecting to Pusher:', { key: pusherKey, cluster: pusherCluster });

    echoConfig = {
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: pusherCluster,
        forceTLS: true,
        encrypted: true,
    };
} else if (useReverb) {
    // Reverb configuration (for local development / VPS)
    const reverbHost = import.meta.env.VITE_REVERB_HOST || '127.0.0.1';
    const reverbScheme = (import.meta.env.VITE_REVERB_SCHEME || 'http').toLowerCase();
    const isSecure = reverbScheme === 'https';
    const reverbPort = Number(import.meta.env.VITE_REVERB_PORT || (isSecure ? 443 : 8080));

    console.log('[Echo] Connecting to Reverb:', { host: reverbHost, port: reverbPort, scheme: reverbScheme });

    echoConfig = {
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: reverbHost,
        wsPort: reverbPort,
        wssPort: reverbPort,
        forceTLS: isSecure,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
        cluster: '',
    };
} else {
    // Fallback: Disable Echo if no configuration
    console.warn('[Echo] No Pusher or Reverb configuration found. Real-time features disabled.');
    echoConfig = null;
}

if (echoConfig) {
    window.Echo = new Echo(echoConfig);

    // Debug: Log connection state changes
    if (window.Echo?.connector?.pusher) {
        window.Echo.connector.pusher.connection.bind('state_change', (states) => {
            console.log('[Echo] Connection:', states.previous, '→', states.current);
        });

        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('[Echo] ✅ Connected successfully');
        });

        window.Echo.connector.pusher.connection.bind('error', (err) => {
            console.error('[Echo] ❌ Connection error:', err);
        });
    }

    if (window.Echo?.connector?.pusher && window.axios) {
        const updateSocketHeader = () => {
            const socketId = window.Echo.socketId();
            if (socketId) {
                window.axios.defaults.headers.common['X-Socket-ID'] = socketId;
            }
        };

        window.Echo.connector.pusher.connection.bind('connected', updateSocketHeader);
    }
}
