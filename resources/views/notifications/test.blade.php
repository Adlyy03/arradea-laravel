<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-authenticated" content="true">
    <title>Test Push Notification - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Push Notification Test
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    Test dan kelola push notification untuk aplikasi Anda
                </p>
            </div>

            <!-- Status Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                    Status Notification
                </h2>
                
                <div class="space-y-4">
                    <!-- Browser Support -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Browser Support</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400" id="browser-support">Checking...</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium" id="support-badge">
                            <span class="animate-pulse">●</span> Checking
                        </span>
                    </div>

                    <!-- Permission Status -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Permission Status</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400" id="permission-status">Checking...</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium" id="permission-badge">
                            <span class="animate-pulse">●</span> Checking
                        </span>
                    </div>

                    <!-- FCM Token -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white">FCM Token</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Device registration token</p>
                            </div>
                            <button onclick="copyToken()" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-lg text-sm font-medium transition-colors">
                                Copy
                            </button>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 font-mono text-xs break-all text-gray-700 dark:text-gray-300" id="fcm-token">
                            No token yet
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Request Permission -->
                <button onclick="requestPermission()" class="group relative overflow-hidden bg-gradient-to-br from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white rounded-2xl p-8 shadow-xl transition-all duration-300 hover:shadow-2xl hover:scale-105">
                    <div class="relative z-10">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Request Permission</h3>
                        <p class="text-white/80 text-sm">Minta izin notifikasi dari browser</p>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </button>

                <!-- Send Test Notification -->
                <button onclick="sendTestNotification()" class="group relative overflow-hidden bg-gradient-to-br from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-2xl p-8 shadow-xl transition-all duration-300 hover:shadow-2xl hover:scale-105">
                    <div class="relative z-10">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Send Test</h3>
                        <p class="text-white/80 text-sm">Kirim notifikasi test ke device ini</p>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </button>
            </div>

            <!-- Device Info -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                    Device Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Browser</p>
                        <p class="font-semibold text-gray-900 dark:text-white" id="device-browser">-</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Platform</p>
                        <p class="font-semibold text-gray-900 dark:text-white" id="device-platform">-</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">User Agent</p>
                        <p class="font-mono text-xs text-gray-900 dark:text-white break-all" id="device-ua">-</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Service Worker</p>
                        <p class="font-semibold text-gray-900 dark:text-white" id="sw-status">-</p>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-8 text-center">
                <a href="{{ route('buyer.dashboard') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <script type="module">
        import pushNotification from './resources/js/push-notification.js';
        import notificationUI from './resources/js/notification-ui.js';

        // Update status on page load
        window.addEventListener('load', async () => {
            updateStatus();
            updateDeviceInfo();
            
            // Initialize push notification
            await pushNotification.initialize();
            
            // Setup event listeners
            pushNotification.on('onTokenReceived', (data) => {
                document.getElementById('fcm-token').textContent = data.token;
                updateStatus();
            });
        });

        function updateStatus() {
            const isSupported = pushNotification.isNotificationSupported();
            const permission = pushNotification.getPermissionStatus();
            
            // Browser support
            const supportBadge = document.getElementById('support-badge');
            const browserSupport = document.getElementById('browser-support');
            
            if (isSupported) {
                supportBadge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                supportBadge.innerHTML = '● Supported';
                browserSupport.textContent = 'Your browser supports push notifications';
            } else {
                supportBadge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                supportBadge.innerHTML = '● Not Supported';
                browserSupport.textContent = 'Your browser does not support push notifications';
            }
            
            // Permission status
            const permissionBadge = document.getElementById('permission-badge');
            const permissionStatus = document.getElementById('permission-status');
            
            if (permission === 'granted') {
                permissionBadge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                permissionBadge.innerHTML = '● Granted';
                permissionStatus.textContent = 'Notifications are enabled';
            } else if (permission === 'denied') {
                permissionBadge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                permissionBadge.innerHTML = '● Denied';
                permissionStatus.textContent = 'Notifications are blocked';
            } else {
                permissionBadge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                permissionBadge.innerHTML = '● Not Asked';
                permissionStatus.textContent = 'Permission not requested yet';
            }
        }

        function updateDeviceInfo() {
            const deviceInfo = pushNotification.getDeviceInfo();
            document.getElementById('device-browser').textContent = deviceInfo.browser;
            document.getElementById('device-platform').textContent = deviceInfo.platform;
            document.getElementById('device-ua').textContent = navigator.userAgent;
            
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistration().then(reg => {
                    document.getElementById('sw-status').textContent = reg ? 'Registered' : 'Not Registered';
                });
            } else {
                document.getElementById('sw-status').textContent = 'Not Supported';
            }
        }

        window.requestPermission = async function() {
            const granted = await pushNotification.requestPermission();
            updateStatus();
            
            if (granted) {
                notificationUI.showToast('Success', 'Notification permission granted!', 'success');
            } else {
                notificationUI.showToast('Denied', 'Notification permission was denied', 'error');
            }
        };

        window.sendTestNotification = async function() {
            try {
                const result = await pushNotification.sendTestNotification();
                
                if (result.success) {
                    notificationUI.showToast('Sent!', 'Test notification sent successfully', 'success');
                } else {
                    notificationUI.showToast('Failed', result.message || 'Failed to send notification', 'error');
                }
            } catch (error) {
                notificationUI.showToast('Error', error.message, 'error');
            }
        };

        window.copyToken = function() {
            const token = document.getElementById('fcm-token').textContent;
            if (token && token !== 'No token yet') {
                navigator.clipboard.writeText(token).then(() => {
                    notificationUI.showToast('Copied!', 'Token copied to clipboard', 'success', 2000);
                });
            }
        };
    </script>
</body>
</html>
