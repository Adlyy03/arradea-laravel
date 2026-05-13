/**
 * Mobile Debug Helper for Arradea
 * Copy-paste this script to browser console on mobile device
 */

(function() {
    console.log('='.repeat(50));
    console.log('🔍 ARRADEA MOBILE DEBUG HELPER');
    console.log('='.repeat(50));
    
    const results = {
        timestamp: new Date().toISOString(),
        device: {},
        styles: {},
        scripts: {},
        elements: {},
        errors: []
    };
    
    // 1. Device Info
    console.log('\n📱 DEVICE INFO:');
    results.device = {
        userAgent: navigator.userAgent,
        screenWidth: window.innerWidth,
        screenHeight: window.innerHeight,
        devicePixelRatio: window.devicePixelRatio,
        platform: navigator.platform,
        language: navigator.language
    };
    console.table(results.device);
    
    // 2. Check Body Styles
    console.log('\n🎨 BODY STYLES:');
    const bodyStyles = getComputedStyle(document.body);
    results.styles.body = {
        display: bodyStyles.display,
        visibility: bodyStyles.visibility,
        opacity: bodyStyles.opacity,
        backgroundColor: bodyStyles.backgroundColor,
        position: bodyStyles.position,
        overflow: bodyStyles.overflow,
        width: bodyStyles.width,
        height: bodyStyles.height
    };
    console.table(results.styles.body);
    
    // 3. Check if Tailwind is loaded
    console.log('\n🎨 TAILWIND CHECK:');
    const hasTailwind = bodyStyles.backgroundColor !== 'rgba(0, 0, 0, 0)' && 
                        bodyStyles.backgroundColor !== 'rgb(255, 255, 255)';
    results.styles.tailwindLoaded = hasTailwind;
    console.log('Tailwind loaded:', hasTailwind ? '✅ YES' : '❌ NO');
    console.log('Expected bg:', '#f7faf7 or rgb(247, 250, 247)');
    console.log('Actual bg:', bodyStyles.backgroundColor);
    
    // 4. Check Alpine.js
    console.log('\n⚡ ALPINE.JS CHECK:');
    results.scripts.alpine = {
        loaded: typeof window.Alpine !== 'undefined',
        version: window.Alpine?.version || 'N/A'
    };
    console.log('Alpine loaded:', results.scripts.alpine.loaded ? '✅ YES' : '❌ NO');
    if (results.scripts.alpine.loaded) {
        console.log('Alpine version:', results.scripts.alpine.version);
    }
    
    // 5. Check GSAP
    console.log('\n🎬 GSAP CHECK:');
    results.scripts.gsap = {
        loaded: typeof window.gsap !== 'undefined',
        version: window.gsap?.version || 'N/A'
    };
    console.log('GSAP loaded:', results.scripts.gsap.loaded ? '✅ YES' : '❌ NO');
    
    // 6. Check Arradea global object
    console.log('\n🏪 ARRADEA OBJECT CHECK:');
    results.scripts.arradea = {
        loaded: typeof window.Arradea !== 'undefined',
        hasToast: typeof window.Arradea?.toast !== 'undefined',
        hasLoading: typeof window.Arradea?.loading !== 'undefined'
    };
    console.log('Arradea loaded:', results.scripts.arradea.loaded ? '✅ YES' : '❌ NO');
    
    // 7. Check critical elements
    console.log('\n🔍 CRITICAL ELEMENTS:');
    results.elements = {
        nav: document.querySelector('nav') !== null,
        main: document.querySelector('main') !== null,
        footer: document.querySelector('footer') !== null,
        body: document.body !== null
    };
    console.table(results.elements);
    
    // 8. Check for white overlays
    console.log('\n⚠️ CHECKING FOR WHITE OVERLAYS:');
    const suspiciousElements = [];
    document.querySelectorAll('*').forEach(el => {
        const styles = getComputedStyle(el);
        const bg = styles.backgroundColor;
        const pos = styles.position;
        const zIndex = parseInt(styles.zIndex) || 0;
        
        if (bg === 'rgb(255, 255, 255)' && 
            (pos === 'fixed' || pos === 'absolute') && 
            zIndex > 100) {
            suspiciousElements.push({
                tag: el.tagName,
                id: el.id || 'N/A',
                class: el.className || 'N/A',
                position: pos,
                zIndex: zIndex,
                width: styles.width,
                height: styles.height
            });
        }
    });
    
    if (suspiciousElements.length > 0) {
        console.warn('⚠️ Found suspicious white overlays:');
        console.table(suspiciousElements);
        results.errors.push('Found white overlays that might hide content');
    } else {
        console.log('✅ No suspicious white overlays found');
    }
    
    // 9. Check for CSS files
    console.log('\n📄 CSS FILES:');
    const cssFiles = Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map(link => ({
        href: link.href,
        loaded: link.sheet !== null
    }));
    results.styles.cssFiles = cssFiles;
    console.table(cssFiles);
    
    // 10. Check for JS files
    console.log('\n📄 JS FILES:');
    const jsFiles = Array.from(document.querySelectorAll('script[src]')).map(script => ({
        src: script.src,
        async: script.async,
        defer: script.defer
    }));
    results.scripts.jsFiles = jsFiles;
    console.table(jsFiles);
    
    // 11. Check for console errors
    console.log('\n❌ CONSOLE ERRORS:');
    console.log('Check the Console tab for any red error messages');
    
    // 12. Network check
    console.log('\n🌐 NETWORK CHECK:');
    console.log('Check the Network tab to see if all assets loaded (status 200)');
    console.log('Look for any 404 or 500 errors');
    
    // 13. Summary
    console.log('\n' + '='.repeat(50));
    console.log('📊 SUMMARY:');
    console.log('='.repeat(50));
    
    const issues = [];
    if (!results.styles.tailwindLoaded) issues.push('❌ Tailwind CSS not loaded');
    if (!results.scripts.alpine.loaded) issues.push('❌ Alpine.js not loaded');
    if (!results.scripts.arradea.loaded) issues.push('❌ Arradea object not loaded');
    if (!results.elements.nav) issues.push('❌ Navbar not found');
    if (!results.elements.main) issues.push('❌ Main content not found');
    if (suspiciousElements.length > 0) issues.push('⚠️ White overlays detected');
    
    if (issues.length === 0) {
        console.log('✅ ALL CHECKS PASSED!');
        console.log('If you still see a blank screen, try:');
        console.log('1. Hard refresh (Ctrl+Shift+R or Cmd+Shift+R)');
        console.log('2. Clear browser cache');
        console.log('3. Check Network tab for failed requests');
    } else {
        console.log('⚠️ ISSUES FOUND:');
        issues.forEach(issue => console.log(issue));
    }
    
    console.log('\n' + '='.repeat(50));
    console.log('💾 Full results saved to: window.arradeaDebug');
    console.log('='.repeat(50));
    
    // Save results to global object
    window.arradeaDebug = results;
    
    // Return results
    return results;
})();
