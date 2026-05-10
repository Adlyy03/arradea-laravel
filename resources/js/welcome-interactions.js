/**
 * Premium Interactive Features for Welcome Page
 * Modern, Minimalist, Expensive Feel
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    initParallaxEffects();
    initMagneticButtons();
    initCounterAnimations();
    initProductCardInteractions();
    initSmoothScrollEffects();
    initCursorFollower();
});

/**
 * Parallax scroll effects for hero section
 */
function initParallaxEffects() {
    const heroBlobs = document.querySelectorAll('.hero-blob');
    
    heroBlobs.forEach((blob, index) => {
        gsap.to(blob, {
            y: () => (index % 2 === 0 ? 100 : -100),
            scrollTrigger: {
                trigger: blob,
                start: 'top bottom',
                end: 'bottom top',
                scrub: 1.5,
            }
        });
    });

    // Parallax for floating cards
    const floatingCards = document.querySelectorAll('.floating-card');
    floatingCards.forEach((card, index) => {
        gsap.to(card, {
            y: () => (index % 2 === 0 ? -50 : 50),
            scrollTrigger: {
                trigger: card,
                start: 'top bottom',
                end: 'bottom top',
                scrub: 2,
            }
        });
    });
}

/**
 * Magnetic effect for premium buttons
 */
function initMagneticButtons() {
    const buttons = document.querySelectorAll('.btn-premium');
    
    buttons.forEach(button => {
        button.addEventListener('mousemove', (e) => {
            const rect = button.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            gsap.to(button, {
                x: x * 0.3,
                y: y * 0.3,
                duration: 0.3,
                ease: 'power2.out'
            });
        });
        
        button.addEventListener('mouseleave', () => {
            gsap.to(button, {
                x: 0,
                y: 0,
                duration: 0.5,
                ease: 'elastic.out(1, 0.5)'
            });
        });
    });
}

/**
 * Animated counter for stats section
 */
function initCounterAnimations() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    statNumbers.forEach(stat => {
        const text = stat.textContent;
        const hasPlus = text.includes('+');
        const hasPercent = text.includes('%');
        const numericValue = parseInt(text.replace(/[^0-9]/g, ''));
        
        if (!isNaN(numericValue)) {
            ScrollTrigger.create({
                trigger: stat,
                start: 'top 80%',
                once: true,
                onEnter: () => {
                    const obj = { value: 0 };
                    gsap.to(obj, {
                        value: numericValue,
                        duration: 2,
                        ease: 'power2.out',
                        onUpdate: () => {
                            let displayValue = Math.round(obj.value);
                            if (hasPlus) displayValue += '+';
                            if (hasPercent) displayValue += '%';
                            stat.textContent = displayValue;
                        }
                    });
                }
            });
        }
    });
}

/**
 * Enhanced product card interactions
 */
function initProductCardInteractions() {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        // 3D tilt effect on hover
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;
            
            gsap.to(card, {
                rotateX: rotateX,
                rotateY: rotateY,
                duration: 0.3,
                ease: 'power2.out',
                transformPerspective: 1000,
            });
        });
        
        card.addEventListener('mouseleave', () => {
            gsap.to(card, {
                rotateX: 0,
                rotateY: 0,
                duration: 0.5,
                ease: 'elastic.out(1, 0.5)'
            });
        });
    });
}

/**
 * Smooth reveal animations on scroll
 */
function initSmoothScrollEffects() {
    // Feature cards stagger animation
    const featureCards = document.querySelectorAll('.feature-card');
    
    ScrollTrigger.batch(featureCards, {
        start: 'top 85%',
        onEnter: (batch) => {
            gsap.from(batch, {
                y: 60,
                opacity: 0,
                stagger: 0.15,
                duration: 0.8,
                ease: 'power3.out'
            });
        },
        once: true
    });

    // Testimonial cards
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    
    ScrollTrigger.batch(testimonialCards, {
        start: 'top 85%',
        onEnter: (batch) => {
            gsap.from(batch, {
                scale: 0.9,
                opacity: 0,
                stagger: 0.2,
                duration: 0.7,
                ease: 'back.out(1.4)'
            });
        },
        once: true
    });
}

/**
 * Custom cursor follower for premium feel
 */
function initCursorFollower() {
    // Only on desktop
    if (window.innerWidth < 1024) return;
    
    const cursor = document.createElement('div');
    cursor.className = 'custom-cursor';
    cursor.style.cssText = `
        position: fixed;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: rgba(114, 191, 119, 0.3);
        pointer-events: none;
        z-index: 9999;
        mix-blend-mode: difference;
        transition: transform 0.2s ease;
    `;
    document.body.appendChild(cursor);
    
    let mouseX = 0, mouseY = 0;
    let cursorX = 0, cursorY = 0;
    
    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
    });
    
    function animateCursor() {
        cursorX += (mouseX - cursorX) * 0.1;
        cursorY += (mouseY - cursorY) * 0.1;
        
        cursor.style.left = cursorX - 10 + 'px';
        cursor.style.top = cursorY - 10 + 'px';
        
        requestAnimationFrame(animateCursor);
    }
    
    animateCursor();
    
    // Scale up on interactive elements
    const interactiveElements = document.querySelectorAll('a, button, .product-card, .feature-card, .testimonial-card');
    
    interactiveElements.forEach(el => {
        el.addEventListener('mouseenter', () => {
            cursor.style.transform = 'scale(2)';
            cursor.style.background = 'rgba(114, 191, 119, 0.5)';
        });
        
        el.addEventListener('mouseleave', () => {
            cursor.style.transform = 'scale(1)';
            cursor.style.background = 'rgba(114, 191, 119, 0.3)';
        });
    });
}

// Export for use in other modules
export {
    initParallaxEffects,
    initMagneticButtons,
    initCounterAnimations,
    initProductCardInteractions,
    initSmoothScrollEffects,
    initCursorFollower
};
