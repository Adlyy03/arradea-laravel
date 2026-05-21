// Firebase Cloud Messaging Service Worker
// This file handles background notifications when the app is not in focus

// Import Firebase scripts with error handling
try {
    importScripts('https://www.gstatic.com/firebasejs/10.13.0/firebase-app-compat.js');
    importScripts('https://www.gstatic.com/firebasejs/10.13.0/firebase-messaging-compat.js');
    console.log('✅ Firebase scripts loaded in service worker');
} catch (error) {
    console.error('❌ Error loading Firebase scripts:', error);
}

// Firebase configuration (same as in firebase.js)
const firebaseConfig = {
    apiKey: "AIzaSyDr3GsRZJgSjw6dVSF_dqUXi1osHxIRmQo",
    authDomain: "arradea-marketplace.firebaseapp.com",
    projectId: "arradea-marketplace",
    storageBucket: "arradea-marketplace.firebasestorage.app",
    messagingSenderId: "574490534147",
    appId: "1:574490534147:web:175e9ba85a2e4100b70936"
};

// Initialize Firebase in service worker
let messaging = null;

try {
    if (typeof firebase !== 'undefined') {
        firebase.initializeApp(firebaseConfig);
        messaging = firebase.messaging();
        console.log('✅ Firebase Service Worker initialized');
    } else {
        console.error('❌ Firebase is not defined in service worker');
    }
} catch (error) {
    console.error('❌ Error initializing Firebase in service worker:', error);
}

// Handle background messages
if (messaging) {
    try {
        messaging.onBackgroundMessage((payload) => {
            console.log('[firebase-messaging-sw.js] Background message received:', payload);

            try {
                const { notification, data } = payload;

                if (notification) {
                    const notificationTitle = notification.title || 'Arradea Marketplace';
                    const notificationOptions = {
                        body: notification.body || 'Anda memiliki notifikasi baru',
                        icon: notification.icon || '/images/logo.png',
                        image: notification.image,
                        badge: '/images/badge.png',
                        tag: data?.tag || 'arradea-notification',
                        requireInteraction: false,
                        data: data || {},
                        actions: data?.actions ? JSON.parse(data.actions) : []
                    };

                    // Show notification
                    return self.registration.showNotification(notificationTitle, notificationOptions);
                }
            } catch (error) {
                console.error('[firebase-messaging-sw.js] Error handling background message:', error);
            }
        });
    } catch (error) {
        console.error('[firebase-messaging-sw.js] Error setting up background message handler:', error);
    }
} else {
    console.warn('⚠️ Messaging not initialized, background notifications disabled');
}

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('[firebase-messaging-sw.js] Notification clicked:', event);

    try {
        event.notification.close();

        // Handle action button clicks
        if (event.action) {
            console.log('Action clicked:', event.action);
            // Handle specific actions here
        }

        // Open URL if provided
        const urlToOpen = event.notification.data?.url || '/';

        event.waitUntil(
            clients.matchAll({ type: 'window', includeUncontrolled: true })
                .then((clientList) => {
                    // Check if there's already a window open
                    for (let i = 0; i < clientList.length; i++) {
                        const client = clientList[i];
                        if (client.url.includes(urlToOpen) && 'focus' in client) {
                            return client.focus();
                        }
                    }
                    
                    // Open new window if no matching window found
                    if (clients.openWindow) {
                        return clients.openWindow(urlToOpen);
                    }
                })
                .catch((error) => {
                    console.error('[firebase-messaging-sw.js] Error handling notification click:', error);
                })
        );
    } catch (error) {
        console.error('[firebase-messaging-sw.js] Error in notification click handler:', error);
    }
});

// IMPORTANT: Let all fetch requests pass through
// Don't intercept any network requests - let browser handle them normally
self.addEventListener('fetch', (event) => {
    // Pass through - don't intercept
    // This ensures images, CSS, JS, and all other assets load normally
    return;
});

// Service worker activation
self.addEventListener('activate', (event) => {
    console.log('[firebase-messaging-sw.js] Service worker activated');
    event.waitUntil(self.clients.claim());
});

// Service worker installation
self.addEventListener('install', (event) => {
    console.log('[firebase-messaging-sw.js] Service worker installed');
    self.skipWaiting();
});
