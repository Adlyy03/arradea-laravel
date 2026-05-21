// Notification UI Manager
import pushNotification from './push-notification';

class NotificationUI {
    constructor() {
        this.modal = null;
        this.toast = null;
        this.isDarkMode = this.detectDarkMode();
        this.initializeStyles();
    }

    /**
     * Detect dark mode
     */
    detectDarkMode() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    /**
     * Initialize styles
     */
    initializeStyles() {
        if (!document.getElementById('notification-ui-styles')) {
            const style = document.createElement('style');
            style.id = 'notification-ui-styles';
            style.textContent = `
                .notification-modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.5);
                    backdrop-filter: blur(4px);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                    animation: fadeIn 0.3s ease;
                }

                .notification-modal {
                    background: white;
                    border-radius: 16px;
                    padding: 32px;
                    max-width: 480px;
                    width: 90%;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                    animation: slideUp 0.3s ease;
                }

                .dark .notification-modal {
                    background: #1f2937;
                    color: white;
                }

                .notification-modal-icon {
                    width: 64px;
                    height: 64px;
                    margin: 0 auto 24px;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 32px;
                }

                .notification-modal-title {
                    font-size: 24px;
                    font-weight: 700;
                    text-align: center;
                    margin-bottom: 12px;
                    color: #111827;
                }

                .dark .notification-modal-title {
                    color: white;
                }

                .notification-modal-description {
                    font-size: 16px;
                    text-align: center;
                    color: #6b7280;
                    margin-bottom: 24px;
                    line-height: 1.6;
                }

                .dark .notification-modal-description {
                    color: #9ca3af;
                }

                .notification-modal-buttons {
                    display: flex;
                    gap: 12px;
                }

                .notification-btn {
                    flex: 1;
                    padding: 14px 24px;
                    border-radius: 12px;
                    font-size: 16px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    border: none;
                    outline: none;
                }

                .notification-btn-primary {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                }

                .notification-btn-primary:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
                }

                .notification-btn-secondary {
                    background: #f3f4f6;
                    color: #374151;
                }

                .dark .notification-btn-secondary {
                    background: #374151;
                    color: #f3f4f6;
                }

                .notification-btn-secondary:hover {
                    background: #e5e7eb;
                }

                .dark .notification-btn-secondary:hover {
                    background: #4b5563;
                }

                .notification-toast {
                    position: fixed;
                    bottom: 24px;
                    right: 24px;
                    background: white;
                    border-radius: 12px;
                    padding: 16px 20px;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    z-index: 10000;
                    animation: slideInRight 0.3s ease;
                    max-width: 400px;
                }

                .dark .notification-toast {
                    background: #1f2937;
                    color: white;
                }

                .notification-toast-icon {
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 20px;
                    flex-shrink: 0;
                }

                .notification-toast-success {
                    background: #10b981;
                    color: white;
                }

                .notification-toast-error {
                    background: #ef4444;
                    color: white;
                }

                .notification-toast-warning {
                    background: #f59e0b;
                    color: white;
                }

                .notification-toast-info {
                    background: #3b82f6;
                    color: white;
                }

                .notification-toast-content {
                    flex: 1;
                }

                .notification-toast-title {
                    font-weight: 600;
                    font-size: 14px;
                    margin-bottom: 4px;
                    color: #111827;
                }

                .dark .notification-toast-title {
                    color: white;
                }

                .notification-toast-message {
                    font-size: 13px;
                    color: #6b7280;
                    line-height: 1.4;
                }

                .dark .notification-toast-message {
                    color: #9ca3af;
                }

                .notification-toast-close {
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    background: #f3f4f6;
                    border: none;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 16px;
                    color: #6b7280;
                    flex-shrink: 0;
                    transition: all 0.2s ease;
                }

                .dark .notification-toast-close {
                    background: #374151;
                    color: #9ca3af;
                }

                .notification-toast-close:hover {
                    background: #e5e7eb;
                    color: #111827;
                }

                .dark .notification-toast-close:hover {
                    background: #4b5563;
                    color: white;
                }

                @keyframes fadeIn {
                    from {
                        opacity: 0;
                    }
                    to {
                        opacity: 1;
                    }
                }

                @keyframes slideUp {
                    from {
                        transform: translateY(20px);
                        opacity: 0;
                    }
                    to {
                        transform: translateY(0);
                        opacity: 1;
                    }
                }

                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }

                @keyframes slideOutRight {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }

    /**
     * Show permission request modal
     */
    showPermissionModal() {
        return new Promise((resolve) => {
            const overlay = document.createElement('div');
            overlay.className = 'notification-modal-overlay';
            overlay.innerHTML = `
                <div class="notification-modal">
                    <div class="notification-modal-icon">🔔</div>
                    <h2 class="notification-modal-title">Enable Notifications</h2>
                    <p class="notification-modal-description">
                        Stay updated with real-time notifications about your orders, messages, and important updates.
                    </p>
                    <div class="notification-modal-buttons">
                        <button class="notification-btn notification-btn-secondary" data-action="deny">
                            Not Now
                        </button>
                        <button class="notification-btn notification-btn-primary" data-action="allow">
                            Enable Notifications
                        </button>
                    </div>
                </div>
            `;

            overlay.querySelector('[data-action="allow"]').addEventListener('click', () => {
                document.body.removeChild(overlay);
                resolve(true);
            });

            overlay.querySelector('[data-action="deny"]').addEventListener('click', () => {
                document.body.removeChild(overlay);
                resolve(false);
            });

            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    document.body.removeChild(overlay);
                    resolve(false);
                }
            });

            document.body.appendChild(overlay);
            this.modal = overlay;
        });
    }

    /**
     * Show toast notification
     */
    showToast(title, message, type = 'info', duration = 5000) {
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };

        const toast = document.createElement('div');
        toast.className = 'notification-toast';
        toast.innerHTML = `
            <div class="notification-toast-icon notification-toast-${type}">
                ${icons[type] || icons.info}
            </div>
            <div class="notification-toast-content">
                <div class="notification-toast-title">${title}</div>
                <div class="notification-toast-message">${message}</div>
            </div>
            <button class="notification-toast-close">×</button>
        `;

        const closeBtn = toast.querySelector('.notification-toast-close');
        closeBtn.addEventListener('click', () => {
            this.hideToast(toast);
        });

        document.body.appendChild(toast);
        this.toast = toast;

        if (duration > 0) {
            setTimeout(() => {
                this.hideToast(toast);
            }, duration);
        }

        return toast;
    }

    /**
     * Hide toast
     */
    hideToast(toast) {
        if (toast && toast.parentNode) {
            toast.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }
    }

    /**
     * Show permission denied warning
     */
    showPermissionDeniedWarning() {
        this.showToast(
            'Notifications Blocked',
            'You have blocked notifications. To enable them, please update your browser settings.',
            'warning',
            8000
        );
    }

    /**
     * Show permission granted success
     */
    showPermissionGrantedSuccess() {
        this.showToast(
            'Notifications Enabled',
            'You will now receive push notifications for important updates.',
            'success',
            5000
        );
    }

    /**
     * Show error message
     */
    showError(message) {
        this.showToast(
            'Error',
            message,
            'error',
            6000
        );
    }

    /**
     * Initialize notification UI
     */
    async initialize() {
        // Check if notifications are supported
        if (!pushNotification.isNotificationSupported()) {
            this.showError('Push notifications are not supported in your browser.');
            return;
        }

        // Check current permission status
        const permission = pushNotification.getPermissionStatus();

        if (permission === 'default') {
            // Show permission modal
            const userAccepted = await this.showPermissionModal();
            
            if (userAccepted) {
                const granted = await pushNotification.requestPermission();
                
                if (granted) {
                    this.showPermissionGrantedSuccess();
                } else {
                    this.showPermissionDeniedWarning();
                }
            }
        } else if (permission === 'granted') {
            // Already granted, just initialize
            await pushNotification.initialize();
        } else if (permission === 'denied') {
            // Show warning
            this.showPermissionDeniedWarning();
        }

        // Setup event listeners
        this.setupEventListeners();
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        pushNotification
            .on('onPermissionGranted', () => {
                this.showPermissionGrantedSuccess();
            })
            .on('onPermissionDenied', () => {
                this.showPermissionDeniedWarning();
            })
            .on('onTokenReceived', (data) => {
                console.log('FCM Token received:', data.token);
            })
            .on('onMessageReceived', (payload) => {
                console.log('Message received:', payload);
                this.showToast(
                    payload.notification?.title || 'New Notification',
                    payload.notification?.body || '',
                    'info',
                    6000
                );
            })
            .on('onError', (error) => {
                console.error('Notification error:', error);
                this.showError(error.message || 'An error occurred with notifications');
            });
    }
}

// Export singleton instance
const notificationUI = new NotificationUI();

export default notificationUI;
