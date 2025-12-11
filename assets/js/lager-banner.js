document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        setupLagerBanner();
    }, 100);
});

function setupLagerBanner() {
    if (localStorage.getItem('lagerBannerClosed') === 'true' || 
        localStorage.getItem('lagerBannerClosed') === 'permanent') {
        return; 
    }
    
    if (document.getElementById('lager-banner')) {
        return; 
    }
    
    createLagerBanner();
    showLagerBanner();
    setupBannerEvents();
}

function createLagerBanner() {
    if (document.getElementById('lager-banner')) {
        return;
    }
    
    const banner = document.createElement('div');
    banner.id = 'lager-banner';
    banner.className = 'lager-banner';
    
    banner.innerHTML = `
        <div class="lager-banner-content">
            <span class="lager-banner-icon">🛍️</span>
            <span class="lager-banner-text">
                LAGERVERKAUF FREITAG 13-18 UHR | Sonderposten & Auslaufserien
            </span>
            <span class="lager-banner-icon">🔥</span>
        </div>
        <button class="lager-banner-close" id="lager-banner-close" aria-label="Banner schließen">
            ×
        </button>
    `;
    
    document.body.appendChild(banner);
}

function showLagerBanner() {
    const banner = document.getElementById('lager-banner');
    const body = document.body;
    
    if (banner) {
        body.classList.add('lager-banner-active');
        
        setTimeout(() => {
            banner.style.display = 'flex';
        }, 100);
        adjustBodyPaddingOnly();
    }
}

function hideLagerBanner() {
    const banner = document.getElementById('lager-banner');
    const body = document.body;
    
    if (banner) {
        banner.classList.add('hidden');
        body.classList.remove('lager-banner-active');
        
        resetBodyPaddingOnly();
        setTimeout(() => {
            if (banner.parentNode) {
                banner.parentNode.removeChild(banner);
            }
        }, 300);
        
        localStorage.setItem('lagerBannerClosed', 'true');
        
        setTimeout(() => {
            localStorage.removeItem('lagerBannerClosed');
        }, 24 * 60 * 60 * 1000);
    }
}

function setupBannerEvents() {
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'lager-banner-close') {
            hideLagerBanner();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const banner = document.getElementById('lager-banner');
            if (banner && !banner.classList.contains('hidden')) {
                hideLagerBanner();
            }
        }
    });
    
    setupTouchEvents();
}


function adjustBodyPaddingOnly() {
    const banner = document.getElementById('lager-banner');
    
    if (banner && !banner.classList.contains('hidden')) {
        const bannerHeight = banner.offsetHeight;
        
        if (window.innerWidth <= 768) {
            document.body.style.paddingTop = bannerHeight + 'px';
        } else {
            document.body.style.paddingTop = bannerHeight + 'px';
        }
    }
}

function resetBodyPaddingOnly() {
    document.body.style.paddingTop = '';
}

function setupTouchEvents() {
    const banner = document.getElementById('lager-banner');
    if (!banner) return;
    
    let startY = 0;
    let currentY = 0;
    
    banner.addEventListener('touchstart', function(e) {
        startY = e.touches[0].clientY;
    }, { passive: true });
    
    banner.addEventListener('touchmove', function(e) {
        currentY = e.touches[0].clientY;
    }, { passive: true });
    
    banner.addEventListener('touchend', function(e) {
        const diff = startY - currentY;
        
        if (diff > 50) {
            const closeButton = document.getElementById('lager-banner-close');
            if (closeButton) {
                closeButton.click();
            }
        }
    }, { passive: true });
}

window.addEventListener('resize', function() {
    const banner = document.getElementById('lager-banner');
    if (banner && !banner.classList.contains('hidden')) {
        setTimeout(() => {
            adjustBodyPaddingOnly();
        }, 100);
    }
});

window.addEventListener('orientationchange', function() {
    setTimeout(() => {
        const banner = document.getElementById('lager-banner');
        if (banner && !banner.classList.contains('hidden')) {
            adjustBodyPaddingOnly();
        }
    }, 500);
});

function showLagerBannerManual() {
    localStorage.removeItem('lagerBannerClosed');
    
    const existingBanner = document.getElementById('lager-banner');
    if (existingBanner) {
        existingBanner.remove();
        document.body.classList.remove('lager-banner-active');
        resetBodyPaddingOnly();
    }
    
    createLagerBanner();
    showLagerBanner();
    setupBannerEvents();
}

function hideLagerBannerPermanent() {
    const banner = document.getElementById('lager-banner');
    if (banner) {
        hideLagerBanner();
    }
    localStorage.setItem('lagerBannerClosed', 'permanent');
}

window.showLagerBannerManual = showLagerBannerManual;
window.hideLagerBannerPermanent = hideLagerBannerPermanent;

function debugBannerPosition() {
    if (localStorage.getItem('debug') === 'true') {
        const banner = document.getElementById('lager-banner');
        const header = document.querySelector('.header');
        
        console.log('=== BANNER DEBUG ===');
        console.log('Banner Height:', banner?.offsetHeight);
        console.log('Banner Z-Index:', getComputedStyle(banner).zIndex);
        console.log('Header Position:', getComputedStyle(header).position);
        console.log('Header Top:', getComputedStyle(header).top);
        console.log('Header Z-Index:', getComputedStyle(header).zIndex);
        console.log('Body Padding Top:', getComputedStyle(document.body).paddingTop);
        console.log('Viewport Width:', window.innerWidth);
        console.log('Is Mobile:', window.innerWidth <= 768);
        console.log('Banner Active Class:', document.body.classList.contains('lager-banner-active'));
        console.log('==================');
    }
}

window.addEventListener('resize', debugBannerPosition);