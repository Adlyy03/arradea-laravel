<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-authenticated" content="true">
    <title>Notification Settings - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Notification Settings
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    Manage your notification preferences
                </p>
            </div>

            <!-- Settings Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                    Push Notifications
                </h2>

                <div class="space-y-6">
                    <!-- Enable/Disable Notifications -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                                Enable Notifications
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Receive push notifications for important updates
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="enable-notifications" class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600"></div>
                        </label>
                    </div>

                    <!-- Sound -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                                Notification Sound
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Play sound when receiving notifications
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="enable-sound" class="sr-only peer" checked>
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Registered Devices -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                    Registered Devices
                </h2>
                
                <div id="devices-list" class="space-y-4">
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto"></div>
                        <p class="text-gray-600 dark:text-gray-400 mt-4">Loading devices...</p>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center">
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

        // Load devices on page load
        window.addEventListener('load', async () => {
            await loadDevices();
            updateToggleStates();
        });

        async function loadDevices() {
            try {
                const response = await fetch('/notifications/tokens', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success && result.data.length > 0) {
                    renderDevices(result.data);
                } else {
                    document.getElementById('devices-list').innerHTML = `
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-gray-600 dark:text-gray-400">No devices registered</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading devices:', error);
                notificationUI.showToast('Error', 'Failed to load devices', 'error');
            }
        }

        function renderDevices(devices) {
            const devicesList = document.getElementById('devices-list');
            devicesList.innerHTML = devices.map(device => `
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                ${device.device_name || 'Unknown Device'}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                ${device.browser || 'Unknown'} on ${device.platform || 'Unknown'}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                Last used: ${new Date(device.last_used_at).toLocaleString()}
                            </p>
                        </div>
                    </div>
                    <button onclick="removeDevice('${device.token}')" class="px-4 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:hover:bg-red-800 text-red-700 dark:text-red-200 rounded-lg text-sm font-medium transition-colors">
                        Remove
                    </button>
                </div>
            `).join('');
        }

        function updateToggleStates() {
            const permission = pushNotification.getPermissionStatus();
            const enableNotifications = document.getElementById('enable-notifications');
            
            if (permission === 'granted') {
                enableNotifications.checked = true;
            } else {
                enableNotifications.checked = false;
            }
        }

        // Enable/Disable notifications
        document.getElementById('enable-notifications').addEventListener('change', async function() {
            if (this.checked) {
                const granted = await pushNotification.requestPermission();
                if (!granted) {
                    this.checked = false;
                    notificationUI.showToast('Denied', 'Notification permission was denied', 'error');
                } else {
                    notificationUI.showToast('Enabled', 'Notifications have been enabled', 'success');
                    await loadDevices();
                }
            } else {
                notificationUI.showToast('Disabled', 'Notifications have been disabled', 'info');
            }
        });

        // Sound toggle
        document.getElementById('enable-sound').addEventListener('change', function() {
            localStorage.setItem('notification-sound', this.checked ? 'enabled' : 'disabled');
            notificationUI.showToast(
                this.checked ? 'Sound Enabled' : 'Sound Disabled',
                this.checked ? 'Notification sound is now enabled' : 'Notification sound is now disabled',
                'info',
                3000
            );
        });

        // Remove device
        window.removeDevice = async function(token) {
            if (!confirm('Are you sure you want to remove this device?')) {
                return;
            }

            try {
                const response = await fetch('/notifications/token/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ token })
                });

                const result = await response.json();

                if (result.success) {
                    notificationUI.showToast('Removed', 'Device has been removed', 'success');
                    await loadDevices();
                } else {
                    notificationUI.showToast('Error', result.message || 'Failed to remove device', 'error');
                }
            } catch (error) {
                console.error('Error removing device:', error);
                notificationUI.showToast('Error', 'Failed to remove device', 'error');
            }
        };
    </script>
</body>
</html>
