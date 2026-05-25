import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';

// Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyDr3GsRZJgSjw6dVSF_dqUXi1osHxIRmQo",
    authDomain: "arradea-marketplace.firebaseapp.com",
    projectId: "arradea-marketplace",
    storageBucket: "arradea-marketplace.firebasestorage.app",
    messagingSenderId: "574490534147",
    appId: "1:574490534147:web:175e9ba85a2e4100b70936"
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
        console.log('='.repeat(80));
        console.log('🔔 [FCM] Requesting notification permission...');
        console.log('='.repeat(80));

        // Check if browser supports notifications
        if (!isNotificationSupported()) {
            console.warn('⚠️ [FCM] Browser tidak mendukung notifikasi');
            console.log('  - Notification in window:', 'Notification' in window);
            console.log('  - ServiceWorker in navigator:', 'serviceWorker' in navigator);
            console.log('  - Messaging initialized:', messaging !== null);
            
            // Show user-friendly message
            if (window.Arradea?.toast) {
                window.Arradea.toast.warning('Browser Anda tidak mendukung notifikasi push');
            }
            
            return null;
        }

        // Check if messaging is initialized
        if (!messaging) {
            console.error('❌ [FCM] Firebase Messaging tidak berhasil diinisialisasi');
            return null;
        }

        // Check current permission status
        const currentPermission = Notification.permission;
        console.log(`📋 [FCM] Current permission status: ${currentPermission}`);

        if (currentPermission === 'denied') {
            console.warn('❌ [FCM] Notification permission already denied by user');
            console.log('   User needs to manually enable in browser settings');
            
            if (window.Arradea?.toast) {
                window.Arradea.toast.warning('Notifikasi diblokir. Aktifkan di pengaturan browser Anda.');
            }
            
            return null;
        }

        // Request notification permission
        console.log('🔔 [FCM] Requesting permission from browser...');
        const permission = await Notification.requestPermission();
        console.log(`📋 [FCM] Permission result: ${permission}`);
        
        if (permission === 'granted') {
            console.log('✅ [FCM] Notification permission granted');
            
            // Register service worker first
            console.log('📝 [FCM] Registering service worker...');
            await registerServiceWorker();
            console.log('✅ [FCM] Service worker registered');
            
            // Get FCM token
            const vapidKey = 'BIxSwfnWuLkFS5RpKwj-AJKqx6HAHQBLifRuG_1VoMn08Ag9jkqNCYqoHUI02rUMdIwg1U69nFRsTXgTrBE7sCA';
            
            console.log('🔑 [FCM] Getting FCM token...');
            console.log('   VAPID Key:', vapidKey.substring(0, 20) + '...');
            
            const token = await getToken(messaging, { 
                vapidKey: vapidKey,
                serviceWorkerRegistration: await navigator.serviceWorker.ready
            });
            
            if (token) {
                console.log('='.repeat(80));
                console.log('🔑 [FCM] FCM Token obtained successfully!');
                console.log('='.repeat(80));
                console.log('Token length:', token.length);
                console.log('Token preview:', token.substring(0, 30) + '...');
                console.log('Full token:', token);
                console.log('='.repeat(80));
                
                // Send token to backend
                console.log('💾 [FCM] Saving token to backend...');
                await saveFCMToken(token);
                
                return token;
            } else {
                console.warn('⚠️ [FCM] Tidak dapat mengambil FCM token');
                console.log('   Check Firebase configuration');
                return null;
            }
        } else if (permission === 'denied') {
            console.warn('❌ [FCM] Notification permission denied by user');
            
            if (window.Arradea?.toast) {
                window.Arradea.toast.warning('Notifikasi ditolak. Anda bisa mengaktifkannya nanti di pengaturan.');
            }
            
            return null;
        } else {
            console.log('⏸️ [FCM] Notification permission dismissed');
            return null;
        }
    } catch (error) {
        console.error('='.repeat(80));
        console.error('❌ [FCM] Error requesting notification permission');
        console.error('='.repeat(80));
        console.error('Error name:', error.name);
        console.error('Error message:', error.message);
        console.error('Error code:', error.code);
        console.error('Error stack:', error.stack);
        console.error('='.repeat(80));
        
        // Handle specific errors
        if (error.code === 'messaging/permission-blocked') {
            console.warn('⚠️ [FCM] Notifikasi diblokir oleh user. Minta user untuk mengaktifkan di browser settings.');
            
            if (window.Arradea?.toast) {
                window.Arradea.toast.warning('Notifikasi diblokir. Aktifkan di pengaturan browser.');
            }
        } else if (error.code === 'messaging/failed-service-worker-registration') {
            console.error('❌ [FCM] Service worker registration failed');
            
            if (window.Arradea?.toast) {
                window.Arradea.toast.error('Gagal mendaftarkan service worker untuk notifikasi');
            }
        } else if (error.code === 'messaging/token-subscribe-failed') {
            console.error('❌ [FCM] Failed to subscribe to FCM');
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
            console.log('   Script URL:', existingRegistration.active?.scriptURL);
            console.log('   State:', existingRegistration.active?.state);
            await navigator.serviceWorker.ready;
            return existingRegistration;
        }

        // Register new service worker
        console.log('📝 Registering new service worker at: /firebase-messaging-sw.js');
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
        
        // Check if page is controlled
        if (navigator.serviceWorker.controller) {
            console.log('✅ Page is controlled by service worker');
        } else {
            console.warn('⚠️ Page is NOT controlled by service worker yet');
            console.warn('   You may need to refresh the page for full functionality');
        }
        
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
        console.log('='.repeat(80));
        console.log('💾 [FCM] Saving FCM token to backend...');
        console.log('='.repeat(80));
        console.log('🔍 [Token Save] Token length:', token.length);
        console.log('🔍 [Token Save] Token preview:', token.substring(0, 30) + '...');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        if (!csrfToken) {
            console.error('❌ [Token Save] CSRF token not found in page');
            console.error('🔍 [Token Save] Available meta tags:', 
                Array.from(document.querySelectorAll('meta[name]')).map(m => m.getAttribute('name'))
            );
            return;
        }

        console.log('✅ [Token Save] CSRF token found');
        console.log('🔍 [Token Save] CSRF token preview:', csrfToken.substring(0, 20) + '...');
        console.log('🔍 [Token Save] Sending POST to: /save-fcm-token');
        
        const requestBody = { fcm_token: token };
        console.log('🔍 [Token Save] Request body:', requestBody);
        
        const response = await fetch('/save-fcm-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(requestBody)
        });

        console.log('🔍 [Token Save] Response status:', response.status);
        console.log('🔍 [Token Save] Response ok:', response.ok);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ [Token Save] HTTP error:', response.status);
            console.error('❌ [Token Save] Error response:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        console.log('='.repeat(80));
        console.log('✅ [FCM] FCM token berhasil disimpan ke backend!');
        console.log('='.repeat(80));
        console.log('🔍 [Token Save] Response data:', data);
        console.log('='.repeat(80));
        
        // Show success toast if available and not shown in this session
        if (window.Arradea?.toast && !sessionStorage.getItem('fcm_toast_shown')) {
            window.Arradea.toast.success('Notifikasi browser berhasil diaktifkan!');
            sessionStorage.setItem('fcm_toast_shown', 'true');
        }
    } catch (error) {
        console.error('='.repeat(80));
        console.error('❌ [FCM] Error saving FCM token');
        console.error('='.repeat(80));
        console.error('Error name:', error.name);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        console.error('='.repeat(80));
        
        // Don't show error toast to user, just log it
        // The notification will still work locally
    }
}

/**
 * Setup foreground message handler
 * Handle notifications when app is in foreground (tab is active)
 *
 * NOTE: FCM SDK suppresses the system notification when the tab is in foreground.
 * We MUST manually show a Notification via the Web Notifications API.
 * onMessage fires for:
 *   - data-only payloads
 *   - payloads with BOTH notification + data fields
 * It does NOT fire for notification-only payloads (handled by SW).
 */
export function setupForegroundMessageHandler() {
    if (!messaging) {
        console.warn('⚠️ Cannot setup foreground handler: messaging not initialized');
        return;
    }

    try {
        console.log('📬 Setting up foreground message handler...');

        onMessage(messaging, (payload) => {
            console.log('🔥 MESSAGE RECEIVED:', payload);
            console.log('='.repeat(80));
            console.log('📬 FOREGROUND MESSAGE RECEIVED');
            console.log('='.repeat(80));
            console.log('Full payload:', JSON.stringify(payload, null, 2));
            console.log('Notification object:', payload.notification);
            console.log('Data object:', payload.data);
            console.log('Notification.permission:', Notification.permission);
            console.log('='.repeat(80));

            // Debug: service worker registrations
            navigator.serviceWorker.getRegistrations().then(regs => {
                console.log('SW Registrations:', regs.map(r => ({ scope: r.scope, script: r.active?.scriptURL })));
            });

            try {
                // Extract title & body - support both notification object and data object
                const title =
                    payload.notification?.title ||
                    payload.data?.title ||
                    'Test Notification';

                const body =
                    payload.notification?.body ||
                    payload.data?.body ||
                    'Notification body';

                const icon =
                    payload.notification?.icon ||
                    payload.data?.icon ||
                    '/icons/logo-arradea.png';

                console.log('🔔 Processing foreground notification:');
                console.log('  Title:', title);
                console.log('  Body:', body);
                console.log('  Icon:', icon);

                // Show Arradea toast (in-app notification)
                if (window.Arradea?.toast) {
                    console.log('✅ Showing Arradea toast notification');
                    window.Arradea.toast.info(`${title}: ${body}`, 6000);
                } else {
                    console.warn('⚠️ window.Arradea.toast not available');
                }

                // CRITICAL: Show browser system notification
                // FCM suppresses this automatically in foreground, so we create it manually
                if ('Notification' in window && Notification.permission === 'granted') {
                    try {
                        console.log('🔔 Creating browser system notification...');

                        const notificationOptions = {
                            body: body,
                            icon: icon,
                            badge: '/icons/logo-arradea.png',
                            tag: payload.data?.tag || 'arradea-fg-notification',
                            requireInteraction: false,
                            data: payload.data || {},
                            vibrate: [200, 100, 200],
                        };

                        console.log('Notification options:', notificationOptions);

                        const browserNotification = new Notification(title, notificationOptions);
                        console.log('✅ Browser notification created successfully');

                        // Handle click
                        browserNotification.onclick = (event) => {
                            console.log('🖱️ Notification clicked');
                            event.preventDefault();
                            const url = payload.data?.url || payload.data?.click_action || '/';
                            window.open(url, '_self');
                            browserNotification.close();
                        };

                        // Auto close after 10 seconds
                        setTimeout(() => browserNotification.close(), 10000);

                    } catch (notifError) {
                        console.error('❌ Error creating browser notification:', notifError);
                    }
                } else {
                    console.warn('⚠️ Cannot show browser notification:');
                    console.warn('  Notification in window:', 'Notification' in window);
                    console.warn('  Permission:', Notification.permission);
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

/**
 * Debug: Check all service worker registrations
 */
export async function debugServiceWorkers() {
    try {
        console.log('='.repeat(80));
        console.log('🔍 SERVICE WORKER DEBUG INFO');
        console.log('='.repeat(80));
        
        if (!('serviceWorker' in navigator)) {
            console.error('❌ Service Worker not supported');
            return;
        }
        
        const registrations = await navigator.serviceWorker.getRegistrations();
        console.log(`📝 Total registrations: ${registrations.length}`);
        
        registrations.forEach((reg, index) => {
            console.log(`\n📝 Registration ${index + 1}:`);
            console.log('   Scope:', reg.scope);
            console.log('   Active:', reg.active ? 'Yes' : 'No');
            console.log('   Installing:', reg.installing ? 'Yes' : 'No');
            console.log('   Waiting:', reg.waiting ? 'Yes' : 'No');
            
            if (reg.active) {
                console.log('   State:', reg.active.state);
                console.log('   Script URL:', reg.active.scriptURL);
            }
        });
        
        if (navigator.serviceWorker.controller) {
            console.log('\n✅ Page is controlled by service worker');
            console.log('   Controller script:', navigator.serviceWorker.controller.scriptURL);
        } else {
            console.warn('\n⚠️ Page is NOT controlled by service worker');
            console.warn('   Refresh the page to activate service worker control');
        }
        
        console.log('='.repeat(80));
    } catch (error) {
        console.error('❌ Error checking service workers:', error);
    }
}

// Make debug function available globally
if (typeof window !== 'undefined') {
    window.debugServiceWorkers = debugServiceWorkers;
}

// Export messaging for advanced usage
export { messaging };
