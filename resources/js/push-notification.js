// Push Notification Manager
import { messaging, getToken, onMessage } from './firebase-config';

class PushNotificationManager {
    constructor() {
        this.vapidKey = import.meta.env.VITE_FIREBASE_VAPID_KEY;
        this.isSupported = 'Notification' in window && 'serviceWorker' in navigator;
        this.permission = this.isSupported ? Notification.permission : 'denied';
        this.token = null;
        this.callbacks = {
            onPermissionGranted: [],
            onPermissionDenied: [],
            onTokenReceived: [],
            onMessageReceived: [],
            onError: []
        };
    }

    /**
     * Check if push notifications are supported
     */
    isNotificationSupported() {
        return this.isSupported;
    }

    /**
     * Get current permission status
     */
    getPermissionStatus() {
        return this.permission;
    }

    /**
     * Register event callbacks
     */
    on(event, callback) {
        if (this.callbacks[event]) {
            this.callbacks[event].push(callback);
        }
        return this;
    }

    /**
     * Trigger callbacks
     */
    trigger(event, data) {
        if (this.callbacks[event]) {
            this.callbacks[event].forEach(callback => callback(data));
        }
    }

    /**
     * Request notification permission
     */
    async requestPermission() {
        if (!this.isSupported) {
            this.trigger('onError', { message: 'Push notifications are not supported in this browser' });
            return false;
        }

        try {
            const permission = await Notification.requestPermission();
            this.permission = permission;

            if (permission === 'granted') {
                this.trigger('onPermissionGranted', { permission });
                await this.getDeviceToken();
                return true;
            } else {
                this.trigger('onPermissionDenied', { permission });
                return false;
            }
        } catch (error) {
            console.error('Error requesting permission:', error);
            this.trigger('onError', { message: error.message, error });
            return false;
        }
    }

    /**
     * Get FCM device token
     */
    async getDeviceToken() {
        if (!this.isSupported || this.permission !== 'granted') {
            return null;
        }

        try {
            // Register service worker
            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js', {
                scope: '/'
            });

            // Wait for service worker to be ready
            await navigator.serviceWorker.ready;

            // Get FCM token
            const currentToken = await getToken(messaging, {
                vapidKey: this.vapidKey,
                serviceWorkerRegistration: registration
            });

            if (currentToken) {
                this.token = currentToken;
                this.trigger('onTokenReceived', { token: currentToken });
                
                // Save token to server
                await this.saveTokenToServer(currentToken);
                
                return currentToken;
            } else {
                console.warn('No registration token available.');
                return null;
            }
        } catch (error) {
            console.error('Error getting device token:', error);
            this.trigger('onError', { message: error.message, error });
            return null;
        }
    }

    /**
     * Save token to server
     */
    async saveTokenToServer(token) {
        try {
            // Get device information
            const deviceInfo = this.getDeviceInfo();

            const response = await fetch('/notifications/token/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    token: token,
                    device_type: 'web',
                    device_name: deviceInfo.deviceName,
                    browser: deviceInfo.browser,
                    platform: deviceInfo.platform
                })
            });

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Failed to save token');
            }

            console.log('Token saved successfully:', data);
            return data;
        } catch (error) {
            console.error('Error saving token to server:', error);
            this.trigger('onError', { message: error.message, error });
            throw error;
        }
    }

    /**
     * Delete token from server
     */
    async deleteTokenFromServer(token) {
        try {
            const response = await fetch('/notifications/token/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ token })
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error deleting token from server:', error);
            throw error;
        }
    }

    /**
     * Listen for foreground messages
     */
    listenForMessages() {
        if (!this.isSupported) {
            return;
        }

        onMessage(messaging, (payload) => {
            console.log('Foreground message received:', payload);
            
            this.trigger('onMessageReceived', payload);
            
            // Show notification if page is visible
            if (document.visibilityState === 'visible') {
                this.showNotification(payload);
            }
        });
    }

    /**
     * Show notification
     */
    async showNotification(payload) {
        const { notification, data } = payload;
        
        if (!notification) return;

        const notificationTitle = notification.title || 'New Notification';
        const notificationOptions = {
            body: notification.body || '',
            icon: notification.icon || notification.image || '/images/logo.png',
            badge: '/images/badge.png',
            tag: data?.tag || 'default',
            requireInteraction: false,
            vibrate: [200, 100, 200],
            data: {
                url: data?.click_action || '/',
                ...data
            }
        };

        // Play notification sound
        this.playNotificationSound();

        // Show notification
        if (this.permission === 'granted') {
            const registration = await navigator.serviceWorker.ready;
            await registration.showNotification(notificationTitle, notificationOptions);
        }
    }

    /**
     * Play notification sound
     */
    playNotificationSound() {
        try {
            const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(err => console.warn('Could not play notification sound:', err));
        } catch (error) {
            console.warn('Notification sound error:', error);
        }
    }

    /**
     * Get device information
     */
    getDeviceInfo() {
        const ua = navigator.userAgent;
        let browser = 'Unknown';
        let platform = 'Unknown';

        // Detect browser
        if (ua.indexOf('Firefox') > -1) {
            browser = 'Firefox';
        } else if (ua.indexOf('Chrome') > -1) {
            browser = 'Chrome';
        } else if (ua.indexOf('Safari') > -1) {
            browser = 'Safari';
        } else if (ua.indexOf('Edge') > -1) {
            browser = 'Edge';
        } else if (ua.indexOf('Opera') > -1 || ua.indexOf('OPR') > -1) {
            browser = 'Opera';
        }

        // Detect platform
        if (ua.indexOf('Win') > -1) {
            platform = 'Windows';
        } else if (ua.indexOf('Mac') > -1) {
            platform = 'macOS';
        } else if (ua.indexOf('Linux') > -1) {
            platform = 'Linux';
        } else if (ua.indexOf('Android') > -1) {
            platform = 'Android';
        } else if (ua.indexOf('iOS') > -1 || ua.indexOf('iPhone') > -1 || ua.indexOf('iPad') > -1) {
            platform = 'iOS';
        }

        return {
            browser,
            platform,
            deviceName: `${browser} on ${platform}`
        };
    }

    /**
     * Initialize push notifications
     */
    async initialize() {
        if (!this.isSupported) {
            console.warn('Push notifications are not supported');
            return false;
        }

        // Listen for messages
        this.listenForMessages();

        // If already granted, get token
        if (this.permission === 'granted') {
            await this.getDeviceToken();
        }

        return true;
    }

    /**
     * Send test notification
     */
    async sendTestNotification() {
        try {
            const response = await fetch('/notifications/test', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error sending test notification:', error);
            throw error;
        }
    }
}

// Export singleton instance
const pushNotification = new PushNotificationManager();

export default pushNotification;
