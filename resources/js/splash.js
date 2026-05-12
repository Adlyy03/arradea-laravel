import { gsap } from 'gsap';

/**
 * Premium Splash Screen for Arradea Marketplace
 * Fast & Beautiful - Only 1 second!
 */

class SplashScreen {
    constructor() {
        this.splashElement = null;
        this.minDuration = 1000; // Cuma 1 detik!
        this.startTime = Date.now();
    }

    create() {
        // Hanya tampilkan splash screen di halaman welcome (root path)
        const isWelcomePage = window.location.pathname === '/' || window.location.pathname === '';
        if (!isWelcomePage) {
            return;
        }

        // Create splash screen HTML
        this.splashElement = document.createElement('div');
        this.splashElement.id = 'arradea-splash';
        this.splashElement.className = 'fixed inset-0 z-[9999] flex items-center justify-center';
        this.splashElement.style.background = 'linear-gradient(135deg, #72bf77 0%, #4db85a 100%)';
        
        this.splashElement.innerHTML = `
            <div class="splash-content text-center relative">
                <!-- Logo Container -->
                <div class="splash-logo mb-4">
                    <div class="w-20 h-20 mx-auto rounded-2xl bg-white shadow-2xl flex items-center justify-center transform scale-0">
                        <img src="/icons/logo-arradea.png" alt="Arradea" class="w-full h-full rounded-2xl object-cover">
                    </div>
                </div>
                
                <!-- Brand Name -->
                <div class="splash-brand opacity-0">
                    <h1 class="text-3xl font-black text-white tracking-tight">
                        Arradea
                    </h1>
                    <p class="text-xs text-white/80 font-semibold uppercase tracking-wider mt-1">
                        Marketplace Warga
                    </p>
                </div>
                
                <!-- Loading Bar -->
                <div class="splash-loader mt-6 opacity-0">
                    <div class="w-32 h-1 bg-white/30 rounded-full mx-auto overflow-hidden">
                        <div class="loading-bar h-full bg-white rounded-full" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(this.splashElement);
        document.body.style.overflow = 'hidden';
    }

    animate() {
        if (!this.splashElement) return;

        const timeline = gsap.timeline({
            onComplete: () => this.hide()
        });

        // Logo scale in with bounce - CEPET!
        timeline.to('.splash-logo > div', {
            scale: 1,
            duration: 0.3,
            ease: 'back.out(1.5)',
        });

        // Brand fade in - CEPET!
        timeline.to('.splash-brand', {
            opacity: 1,
            y: 0,
            duration: 0.2,
            ease: 'power2.out',
        }, '-=0.1');

        // Loader fade in - CEPET!
        timeline.to('.splash-loader', {
            opacity: 1,
            duration: 0.15,
            ease: 'power2.out',
        }, '-=0.1');

        // Loading bar animation - CEPET!
        timeline.to('.loading-bar', {
            width: '100%',
            duration: 0.4,
            ease: 'power2.inOut',
        }, '-=0.1');
    }

    hide() {
        const elapsed = Date.now() - this.startTime;
        const remainingTime = Math.max(0, this.minDuration - elapsed);

        setTimeout(() => {
            if (!this.splashElement) return;

            // Fade out animation - CEPET!
            gsap.to(this.splashElement, {
                opacity: 0,
                scale: 1.05,
                duration: 0.25,
                ease: 'power2.inOut',
                onComplete: () => {
                    this.splashElement?.remove();
                    document.body.style.overflow = '';
                    
                    // Scroll ke paling atas setelah splash selesai!
                    window.scrollTo(0, 0);
                    
                    // Trigger custom event
                    window.dispatchEvent(new CustomEvent('splashComplete'));
                }
            });
        }, remainingTime);
    }

    init() {
        // Hanya tampilkan splash screen di halaman welcome (root path)
        const isWelcomePage = window.location.pathname === '/' || window.location.pathname === '';
        if (!isWelcomePage) {
            // Langsung trigger event tanpa splash
            window.dispatchEvent(new CustomEvent('splashComplete'));
            return;
        }
        
        // Scroll ke atas dulu sebelum splash muncul!
        window.scrollTo(0, 0);
        
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.create();
                this.animate();
            });
        } else {
            this.create();
            this.animate();
        }
    }
}

// Auto-initialize splash screen
const splash = new SplashScreen();
splash.init();

export default SplashScreen;
