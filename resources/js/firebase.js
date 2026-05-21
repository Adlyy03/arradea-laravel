import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';

// Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyDr3GsRZJgSjw6dVSF_dqUXi1osHxIRmQo",
  authDomain: "arradea-marketplace.firebaseapp.com",
  projectId: "arradea-marketplace",
  storageBucket: "arradea-marketplace.firebasestorage.app",
  messagingSenderId: "574490534147",
  appId: "1:574490534147:web:175e9ba85a2e4100b70936",
  measurementId: "G-GFC3C17THY"
};

// Initialize Firebase
let app = null;
let messaging = null;
let initializationError = null;

try {
    // Check if we're in a browser environment
    if (typeof window === 'undefined') {
        throw new Error('Not in browser environment');
    }

    app = initializeApp(firebaseConfig);
    console.log('✅ Firebase initialized successfully');
} catch (error) {
    console.error('❌ Firebase initialization error:', error);
    initializationError = error;
}

// Initialize Firebase Cloud Messaging
try {
    if (app && typeof window !== 'undefined' && 'serviceWorker' in navigator && 'Notification' in window) {
        messaging = getMessaging(app);
        console.log('✅ Firebase Messaging initialized');
    } else {
        const reason = !app ? 'Firebase app not initialized' :
                      typeof window === 'undefined' ? 'Not in browser' :
                      !('serviceWorker' in navigator) ? 'Service Worker not supported' :
                      !('Notification' in window) ? 'Notifications not supported' :
                      'Unknown reason';
        console.warn(`⚠️ FCM disabled: ${reason}`);
    }
} catch (error) {
    console.error('❌ Firebase Messaging initialization error:', error);
    initializationError = error;
}

/**
 * Check if browser supports notifications
 */
export function isNotificationSupported() {
    try {
        return (
            typeof window !== 'undefined' &&
            'Notification' in window &&
            'serviceWorker' in navigator &&
            messaging !== null &&
            initializationError === null
        );
    } catch (error) {
        console.error('❌ Error checking notification support:', error);
        return false;
    }
}

/**
 * Request notification permission and get FCM token
 */
export async function requestPermission() {
    try {
        console.log('🔔 Requesting notification permission...');

        // Check if browser supports notifications
        if (!isNotificationSupported()) {
            console.warn('⚠️ Browser tidak mendukung notifikasi');
            
            // Show user-friendly message
            if (window.Arradea?.toast) {
                window.Arradea.toast.warning('Browser Anda tidak mendukung notifikasi push');
            }
            
            return null;
        }

        // Check if messaging is initialized
        if (!messaging) {
            console.error('❌ Firebase Messaging tidak berhasil diinisialisasi');
            return null;
        }

        // Check current permission status
        const currentPermission = Notification.permission;
        console.log(`📋 Current permission status: ${currentPermission}`);

        if (currentPermission === 'denied') {
            console.warn('❌ Notification permission already denied by user');
            
            if (window.Arradea?.toast) {
                window.Arradea.toast.warning('Notifikasi diblokir. Aktifkan di pengaturan browser Anda.');
            }
            
            return null;
        }

        // Request notification permission
        const permission = await Notification.requestPermission();
        console.log(`📋 Permission result: ${permission}`);
        
        if (permission === 'granted') {
            console.log('✅ Notification permission granted');
            
            // Register service worker first
            await registerServiceWorker();
            
            // Get FCM token
            const vapidKey = 'BIxSwfnWuLkFS5RpKwj-AJKqx6HAHQBLifRuG_1VoMn08Ag9jkqNCYqoHUI02rUMdIwg1U69nFRsTXgTrBE7sCA';
            
            const token = await getToken(messaging, { 
                vapidKey: vapidKey,
                serviceWorkerRegistration: await navigator.serviceWorker.ready
            });
            
            if (token) {
                console.log('🔑 FCM Token obtained successfully');
                console.log('Token preview:', token.substring(0, 20) + '...');
                
                // Send token to backend
                await saveFCMToken(token);
                
                return token;
            } else {
                console.warn('⚠️ Tidak dapat mengambil FCM token');
                return null;
            }
        } else if (permission === 'denied') {
            console.warn('❌ Notification permission denied by user');
            
            if (window.Arradea?.toast) {
                window.Arradea.toast.warning('Notifikasi ditolak. Anda bisa mengaktifkannya nanti di pengaturan.');
            }
            
            return null;
        } else {
            console.log('⏸️ Notification permission dismissed');
            return null;
        }
    } catch (error) {
        console.error('❌ Error requesting notification permission:', error);
        console.error('Error details:', {
            name: error.name,
            message: error.message,
            code: error.code,
            stack: error.stack
        });
        
        // Handle specific errors
        if (error.code === 'messaging/permission-blocked') {
            console.warn('⚠️ Notifikasi diblokir oleh user. Minta user untuk mengaktifkan di browser settings.');
            
            if (window.Arradea?.toast) {
                window.Arradea.toast.warning('Notifikasi diblokir. Aktifkan di pengaturan browser.');
            }
        } else if (error.code === 'messaging/failed-service-worker-registration') {
            console.error('❌ Service worker registration failed');
            
            if (window.Arradea?.toast) {
                window.Arradea.toast.error('Gagal mendaftarkan service worker untuk notifikasi');
            }
        } else if (error.code === 'messaging/token-subscribe-failed') {
            console.error('❌ Failed to subscribe to FCM');
        } else {
            // Generic error
            if (window.Arradea?.toast) {
                window.Arradea.toast.error('Terjadi kesalahan saat mengaktifkan notifikasi');
            }
        }
        
        return null;
    }
}

/**
 * Register service worker for FCM
 */
async function registerServiceWorker() {
    try {
        console.log('📝 Registering service worker...');

        if (!('serviceWorker' in navigator)) {
            throw new Error('Service Worker not supported in this browser');
        }

        // Check if service worker is already registered
        const existingRegistration = await navigator.serviceWorker.getRegistration('/');
        
        if (existingRegistration) {
            console.log('✅ Service Worker already registered:', existingRegistration.scope);
            await navigator.serviceWorker.ready;
            return existingRegistration;
        }

        // Register new service worker
        const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js', {
            scope: '/',
            updateViaCache: 'none'
        });
        
        console.log('✅ Service Worker registered successfully');
        console.log('   Scope:', registration.scope);
        console.log('   State:', registration.active?.state || 'installing');
        
        // Wait for service worker to be ready
        await navigator.serviceWorker.ready;
        console.log('✅ Service Worker is ready');
        
        return registration;
    } catch (error) {
        console.error('❌ Service Worker registration failed:', error);
        console.error('Error details:', {
            name: error.name,
            message: error.message,
            stack: error.stack
        });
        throw error;
    }
}

/**
 * Save FCM token to backend
 */
async function saveFCMToken(token) {
    try {
        console.log('💾 Saving FCM token to backend...');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        if (!csrfToken) {
            console.error('❌ CSRF token not found in page');
            return;
        }
        
        const response = await fetch('/save-fcm-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ fcm_token: token })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        console.log('✅ FCM token berhasil disimpan ke backend');
        console.log('Response:', data);
        
        // Show success toast if available
        if (window.Arradea?.toast) {
            window.Arradea.toast.success('Notifikasi browser berhasil diaktifkan!');
        }
    } catch (error) {
        console.error('❌ Error saving FCM token:', error);
        console.error('Error details:', {
            name: error.name,
            message: error.message
        });
        
        // Don't show error toast to user, just log it
        // The notification will still work locally
    }
}

/**
 * Setup foreground message handler
 * Handle notifications when app is in foreground
 */
export function setupForegroundMessageHandler() {
    if (!messaging) {
        console.warn('⚠️ Cannot setup foreground handler: messaging not initialized');
        return;
    }

    try {
        console.log('📬 Setting up foreground message handler...');

        onMessage(messaging, (payload) => {
            console.log('📬 Foreground message received:', payload);

            try {
                const { notification, data } = payload;

                if (notification) {
                    const { title, body, icon, image } = notification;

                    // Show custom notification using Arradea toast
                    if (window.Arradea?.toast) {
                        window.Arradea.toast.info(`${title}: ${body}`, 6000);
                    }

                    // Also show browser notification
                    if ('Notification' in window && Notification.permission === 'granted') {
                        try {
                            const notificationOptions = {
                                body: body,
                                icon: icon || '/images/logo.png',
                                image: image,
                                badge: '/images/badge.png',
                                tag: data?.tag || 'arradea-notification',
                                requireInteraction: false,
                                data: data || {}
                            };

                            const browserNotification = new Notification(title, notificationOptions);

                            // Handle notification click
                            browserNotification.onclick = (event) => {
                                event.preventDefault();
                                
                                // Open URL if provided
                                if (data?.url) {
                                    window.open(data.url, '_blank');
                                }
                                
                                browserNotification.close();
                            };

                            // Auto close after 5 seconds
                            setTimeout(() => {
                                browserNotification.close();
                            }, 5000);
                        } catch (error) {
                            console.error('❌ Error showing browser notification:', error);
                        }
                    }
                }
            } catch (error) {
                console.error('❌ Error processing foreground message:', error);
            }
        });
        
        console.log('✅ Foreground message handler setup complete');
    } catch (error) {
        console.error('❌ Error setting up foreground message handler:', error);
    }
}

/**
 * Delete FCM token (for logout)
 */
export async function deleteFCMToken() {
    try {
        if (!messaging) {
            console.warn('⚠️ Messaging not initialized, cannot delete token');
            return false;
        }

        console.log('🗑️ Deleting FCM token...');

        const { deleteToken } = await import('firebase/messaging');
        const result = await deleteToken(messaging);
        
        console.log('✅ FCM token deleted successfully');
        return result;
    } catch (error) {
        console.error('❌ Error deleting FCM token:', error);
        return false;
    }
}

// Export messaging for advanced usage
export { messaging };
