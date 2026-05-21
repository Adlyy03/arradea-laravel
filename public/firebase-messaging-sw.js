// Firebase Cloud Messaging Service Worker

// Import Firebase scripts
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

// Initialize Firebase in service worker
// Note: You need to replace these with your actual Firebase config
// These values should match your .env file
const firebaseConfig = {
    apiKey: "YOUR_API_KEY",
    authDomain: "YOUR_AUTH_DOMAIN",
    projectId: "YOUR_PROJECT_ID",
    storageBucket: "YOUR_STORAGE_BUCKET",
    messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
    appId: "YOUR_APP_ID",
    measurementId: "YOUR_MEASUREMENT_ID"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Retrieve an instance of Firebase Messaging
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message:', payload);

    const notificationTitle = payload.notification?.title || payload.data?.title || 'New Notification';
    const notificationOptions = {
        body: payload.notification?.body || payload.data?.body || '',
        icon: payload.notification?.icon || payload.data?.icon || '/images/logo.png',
        badge: '/images/badge.png',
        tag: payload.data?.tag || 'notification-' + Date.now(),
        requireInteraction: false,
        vibrate: [200, 100, 200],
        data: {
            url: payload.data?.click_action || payload.fcmOptions?.link || '/',
            ...payload.data
        },
        actions: [
            {
                action: 'open',
                title: 'Open',
                icon: '/images/open-icon.png'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/images/close-icon.png'
            }
        ]
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('[firebase-messaging-sw.js] Notification clicked:', event);

    event.notification.close();

    const urlToOpen = event.notification.data?.url || '/';

    // Handle action buttons
    if (event.action === 'close') {
        return;
    }

    // Open or focus the URL
    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then((clientList) => {
            // Check if there's already a window open with this URL
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url === urlToOpen && 'focus' in client) {
                    return client.focus();
                }
            }

            // If no window is open, open a new one
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

// Handle push event (alternative to onBackgroundMessage)
self.addEventListener('push', (event) => {
    console.log('[firebase-messaging-sw.js] Push event received:', event);

    if (event.data) {
        try {
            const payload = event.data.json();
            console.log('[firebase-messaging-sw.js] Push payload:', payload);

            const notificationTitle = payload.notification?.title || payload.data?.title || 'New Notification';
            const notificationOptions = {
                body: payload.notification?.body || payload.data?.body || '',
                icon: payload.notification?.icon || payload.data?.icon || '/images/logo.png',
                badge: '/images/badge.png',
                tag: payload.data?.tag || 'notification-' + Date.now(),
                requireInteraction: false,
                vibrate: [200, 100, 200],
                data: {
                    url: payload.data?.click_action || payload.fcmOptions?.link || '/',
                    ...payload.data
                }
            };

            event.waitUntil(
                self.registration.showNotification(notificationTitle, notificationOptions)
            );
        } catch (error) {
            console.error('[firebase-messaging-sw.js] Error parsing push data:', error);
        }
    }
});

// Service worker activation
self.addEventListener('activate', (event) => {
    console.log('[firebase-messaging-sw.js] Service worker activated');
    event.waitUntil(clients.claim());
});

// Service worker installation
self.addEventListener('install', (event) => {
    console.log('[firebase-messaging-sw.js] Service worker installed');
    self.skipWaiting();
});

// Handle message from client
self.addEventListener('message', (event) => {
    console.log('[firebase-messaging-sw.js] Message from client:', event.data);

    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
