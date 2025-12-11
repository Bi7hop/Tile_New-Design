document.addEventListener('DOMContentLoaded', function() {
    setupCookieConsent();
});

function setupCookieConsent() {
    const cookieModal = document.getElementById('cookie-modal');
    const cookieClose = document.getElementById('cookie-close');
    const cookieAcceptAll = document.getElementById('cookie-accept-all');
    const cookieRejectAll = document.getElementById('cookie-reject-all');
    const cookieSave = document.getElementById('cookie-save');
    
    if (!localStorage.getItem('cookieConsent') && cookieModal) {
        // Kurze Verzögerung für bessere UX
        setTimeout(() => {
            cookieModal.classList.add('active');
            toggleBodyScroll(true);
        }, 1000); 
    }
    
    // Modal schließen ohne Einstellungen zu speichern
    cookieClose.addEventListener('click', function() {
        closeCookieModal();
    });
    
    // Alle Cookies akzeptieren
    cookieAcceptAll.addEventListener('click', function() {
        const settings = {
            essential: true,
            analytics: true,
            marketing: true,
            timestamp: new Date().toISOString()
        };
        
        localStorage.setItem('cookieConsent', JSON.stringify(settings));
        closeCookieModal();
        
        // Hier könnten Sie Code einfügen, um die entsprechenden Cookies zu setzen
        // z.B. activateAnalytics(), activateMarketing() etc.
    });
    
    // Alle nicht-essentiellen Cookies ablehnen
    cookieRejectAll.addEventListener('click', function() {
        const settings = {
            essential: true,
            analytics: false,
            marketing: false,
            timestamp: new Date().toISOString()
        };
        
        localStorage.setItem('cookieConsent', JSON.stringify(settings));
        closeCookieModal();
        
        // Hier könnten Sie Code einfügen, um alle nicht-essentiellen Cookies zu entfernen
    });
    
    // Ausgewählte Cookie-Einstellungen speichern
    cookieSave.addEventListener('click', function() {
        const analyticsConsent = document.getElementById('analytics-cookies').checked;
        const marketingConsent = document.getElementById('marketing-cookies').checked;
        
        const settings = {
            essential: true,
            analytics: analyticsConsent,
            marketing: marketingConsent,
            timestamp: new Date().toISOString()
        };
        
        localStorage.setItem('cookieConsent', JSON.stringify(settings));
        closeCookieModal();
        
        // Aktivieren Sie die entsprechenden Cookies basierend auf den Benutzereinstellungen
        if (analyticsConsent) {
            // Analyse-Cookies aktivieren
        }
        
        if (marketingConsent) {
            // Marketing-Cookies aktivieren
        }
    });
    
    // MOBILE FIXES
    setupCookieModalMobile();
}

// NEUE MOBILE FUNKTIONEN

function closeCookieModal() {
    const cookieModal = document.getElementById('cookie-modal');
    cookieModal.classList.remove('active');
    toggleBodyScroll(false); // Body Scroll freigeben
}

function toggleBodyScroll(disable) {
    if (disable) {
        document.body.classList.add('modal-open');
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.width = '100%';
        document.body.style.height = '100%';
        document.body.style.top = `-${window.scrollY}px`;
    } else {
        const scrollY = document.body.style.top;
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';
        document.body.style.height = '';
        document.body.style.top = '';
        
        if (scrollY) {
            window.scrollTo(0, parseInt(scrollY || '0') * -1);
        }
    }
}

function setupCookieModalMobile() {
    const cookieModal = document.getElementById('cookie-modal');
    if (!cookieModal) return;
    
    // Touch Events für bessere Mobile Experience
    let startY = 0;
    let currentY = 0;
    
    cookieModal.addEventListener('touchstart', function(e) {
        startY = e.touches[0].clientY;
    }, { passive: true });
    
    cookieModal.addEventListener('touchmove', function(e) {
        currentY = e.touches[0].clientY;
        const modal = cookieModal.querySelector('.modal');
        
        // Verhindere Scrollen wenn am oberen oder unteren Ende
        if (modal.scrollTop === 0 && currentY > startY) {
            e.preventDefault();
        } else if (modal.scrollTop >= modal.scrollHeight - modal.clientHeight && currentY < startY) {
            e.preventDefault();
        }
    }, { passive: false });
    
    cookieModal.addEventListener('touchend', function(e) {
        const diff = currentY - startY;
        
        // Wenn nach unten gewischt wird (> 100px) und außerhalb des Modals, schließen
        if (diff > 100 && e.target === cookieModal) {
            const closeButton = document.getElementById('cookie-close');
            if (closeButton) {
                closeButton.click();
            }
        }
    }, { passive: true });
    
    // Escape Key Handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && cookieModal.classList.contains('active')) {
            const closeButton = document.getElementById('cookie-close');
            if (closeButton) {
                closeButton.click();
            }
        }
    });
    
    // Outside Click Handler (verbessert für Mobile)
    cookieModal.addEventListener('click', function(e) {
        if (e.target === cookieModal) {
            // Auf Mobile nur schließen wenn explizit geklickt (nicht gewischt)
            if (!isTouchDevice() || Math.abs(currentY - startY) < 10) {
                const closeButton = document.getElementById('cookie-close');
                if (closeButton) {
                    closeButton.click();
                }
            }
        }
    });
    
    // Auto-Focus auf erstes Element (nur Desktop)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                const isActive = cookieModal.classList.contains('active');
                if (isActive && window.innerWidth > 768) {
                    // Fokus nur auf Desktop setzen
                    setTimeout(() => {
                        const firstButton = cookieModal.querySelector('button:not(.modal-close), input[type="checkbox"]');
                        if (firstButton) {
                            firstButton.focus();
                        }
                    }, 100);
                }
            }
        });
    });
    
    observer.observe(cookieModal, {
        attributes: true,
        attributeFilter: ['class']
    });
    
    // Verbesserte Button-Interaktion für Touch
    const buttons = cookieModal.querySelectorAll('button');
    buttons.forEach(button => {
        button.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        }, { passive: true });
        
        button.addEventListener('touchend', function() {
            this.style.transform = '';
        }, { passive: true });
        
        button.addEventListener('touchcancel', function() {
            this.style.transform = '';
        }, { passive: true });
    });
    
    // Checkbox Touch Optimization
    const checkboxes = cookieModal.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        const label = checkbox.closest('.cookie-option');
        if (label) {
            label.addEventListener('click', function(e) {
                if (e.target !== checkbox) {
                    e.preventDefault();
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                }
            });
        }
    });
}

// Utility Funktion: Prüft ob Gerät Touch unterstützt
function isTouchDevice() {
    return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
}

// Viewport Height Fix für Mobile
function setVhProperty() {
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', vh + 'px');
}

// Initial setzen und bei Resize/Orientation Change aktualisieren
setVhProperty();
window.addEventListener('resize', setVhProperty);
window.addEventListener('orientationchange', function() {
    setTimeout(setVhProperty, 500);
});

// Performance Optimization: Throttle für Resize Events
function throttle(func, delay) {
    let timeoutId;
    let lastExecTime = 0;
    
    return function (...args) {
        const currentTime = Date.now();
        
        if (currentTime - lastExecTime > delay) {
            func.apply(this, args);
            lastExecTime = currentTime;
        } else {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                func.apply(this, args);
                lastExecTime = Date.now();
            }, delay - (currentTime - lastExecTime));
        }
    };
}

// Throttled Resize Handler
window.addEventListener('resize', throttle(function() {
    setVhProperty();
}, 100));

// Debug Mode (nur in Entwicklung)
if (localStorage.getItem('debug') === 'true') {
    console.log('Cookie Modal Mobile Fixes loaded');
    
    // Debug Funktion für Modal State
    window.debugCookieModal = function() {
        const modal = document.getElementById('cookie-modal');
        console.log({
            modalVisible: modal?.classList.contains('active'),
            bodyScrollLocked: document.body.classList.contains('modal-open'),
            viewportWidth: window.innerWidth,
            viewportHeight: window.innerHeight,
            isMobile: window.innerWidth <= 768,
            isTouchDevice: isTouchDevice()
        });
    };
}
function applyCookieSettings() {
    const consent = localStorage.getItem('cookieConsent');
    if (!consent) return;
    
    const settings = JSON.parse(consent);
    
    if (settings.analytics) {
        console.log('Analytics-Cookies aktiviert');
        // Hier würden Sie Google Analytics oder ähnliches laden:
        // loadGoogleAnalytics();
    } else {
        console.log('Analytics-Cookies deaktiviert');
        // Hier würden Sie Analytics deaktivieren
    }
    
    // Marketing-Cookies
    if (settings.marketing) {
        console.log('Marketing-Cookies aktiviert');
        // Hier würden Sie Marketing-Tools laden:
        // loadFacebookPixel(), loadGoogleAds(), etc.
    } else {
        console.log('Marketing-Cookies deaktiviert');
    }
}

// Einstellungen beim Laden der Seite anwenden
document.addEventListener('DOMContentLoaded', function() {
    applyCookieSettings();
    setupCookieConsent();
});