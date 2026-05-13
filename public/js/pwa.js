(() => {
  const serviceWorkerPath = '/sw.js';
  const installButtonId = 'pwa-install-btn';
  const buttonLabel = 'Add to Home Screen';

  function ensureInstallButton() {
    let button = document.getElementById(installButtonId);

    if (!button) {
      button = document.createElement('button');
      button.id = installButtonId;
      button.type = 'button';
      button.textContent = buttonLabel;
      button.style.cssText = [
        'position:fixed',
        'right:16px',
        'bottom:calc(16px + env(safe-area-inset-bottom))',
        'z-index:9999',
        'display:none',
        'align-items:center',
        'gap:8px',
        'padding:12px 16px',
        'border:0',
        'border-radius:999px',
        'background:#000000',
        'color:#ffffff',
        'font:600 14px/1.2 system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
        'box-shadow:0 12px 30px rgba(0,0,0,.24)'
      ].join(';');
      document.body.appendChild(button);
    }

    return button;
  }

  function registerServiceWorker() {
    if (!('serviceWorker' in navigator)) {
      return;
    }

    window.addEventListener('load', () => {
      navigator.serviceWorker.register(serviceWorkerPath, { scope: '/' }).catch(() => {});
    });
  }

  function setupInstallPrompt() {
    let deferredPrompt = null;
    const installButton = ensureInstallButton();

    window.addEventListener('beforeinstallprompt', (event) => {
      event.preventDefault();
      deferredPrompt = event;
      installButton.style.display = 'inline-flex';
    });

    installButton.addEventListener('click', async () => {
      if (!deferredPrompt) {
        return;
      }

      deferredPrompt.prompt();
      try {
        await deferredPrompt.userChoice;
      } catch (error) {
        void error;
      }
      deferredPrompt = null;
      installButton.style.display = 'none';
    });

    window.addEventListener('appinstalled', () => {
      deferredPrompt = null;
      installButton.style.display = 'none';
    });
  }

  function markStandaloneMode() {
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

    if (isStandalone) {
      document.body.classList.add('pwa-mode');
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      registerServiceWorker();
      setupInstallPrompt();
      markStandaloneMode();
    });
  } else {
    registerServiceWorker();
    setupInstallPrompt();
    markStandaloneMode();
  }

})();