// Firebase Cloud Messaging Service Worker
// Handles BACKGROUND messages (when browser tab is NOT in focus)
// Version: 2.0 - Production Ready

console.log('[firebase-messaging-sw.js] Loading...');

// Import Firebase compat scripts (required for service worker)
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

// Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyDr3GsRZJgSjw6dVSF_dqUXi1osHxIRmQo",
    authDomain: "arradea-marketplace.firebaseapp.com",
    projectId: "arradea-marketplace",
    storageBucket: "arradea-marketplace.firebasestorage.app",
    messagingSenderId: "574490534147",
    appId: "1:574490534147:web:175e9ba85a2e4100b70936"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

console.log('[firebase-messaging-sw.js] Firebase initialized');

// Handle BACKGROUND messages
// This fires when the browser tab is NOT focused / minimized / closed
messaging.onBackgroundMessage(function(payload) {
    console.log('🔥 BACKGROUND MESSAGE:', payload);
    console.log('[firebase-messaging-sw.js] Full payload:', JSON.stringify(payload));

    // PENTING: Jika payload mengandung object 'notification', FCM SDK akan
    // otomatis menampilkan notifikasinya. Kita tidak boleh memanggil 
    // showNotification() secara manual di sini karena akan menyebabkan duplikat 
    // atau conflict.
    if (payload.notification) {
        console.log('[firebase-messaging-sw.js] Payload contains notification object. FCM SDK will handle it automatically.');
        return;
    }

    // Jika ini adalah DATA-ONLY payload (tidak ada object notification),
    // barulah kita render manual:
    const title = payload.data?.title || 'Arradea Notification';
    const body  = payload.data?.body || '';
    const icon  = payload.data?.icon || '/icons/logo-arradea.png';
    const clickUrl = payload.data?.url || payload.data?.click_action || '/';

    console.log('[firebase-messaging-sw.js] Data-only payload detected. Showing notification manually:', { title, body, icon });

    const notificationOptions = {
        body: body,
        icon: icon,
        badge: '/icons/logo-arradea.png',
        tag: 'arradea-notification',
        data: {
            url: clickUrl,
            ...payload.data
        },
        requireInteraction: false,
        vibrate: [200, 100, 200],
    };

    return self.registration.showNotification(title, notificationOptions);
});

// Handle notification click
self.addEventListener('notificationclick', function(event) {
    console.log('[firebase-messaging-sw.js] Notification clicked:', event.notification.data);
    event.notification.close();

    const urlToOpen = event.notification.data?.url || event.notification.data?.click_action || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function(clientList) {
                // Focus existing window if available
                for (const client of clientList) {
                    if ('focus' in client) {
                        return client.focus();
                    }
                }
                // Open new window
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

console.log('[firebase-messaging-sw.js] Ready - Background message handler registered');
