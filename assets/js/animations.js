/**
 * Moderne Animationen und Scroll-Effekte für die Fliesen-Runnebaum-Website
 */
document.addEventListener('DOMContentLoaded', function() {
    // Fonts vorladen
    loadFonts();
    
    // Preloader anzeigen und dann ausblenden
    setupPreloader();
    
    // Initialisiere Hero-Animationen
    initHeroAnimations();
    
    // Scroll-Animationen einrichten
    setupScrollAnimations();
    
    // Service-Element-Animationen
    setupServiceItemAnimations();
    
    // Verstärke Galerie-Hover-Effekte
    enhanceGalleryItems();
     
    // Button-Hover-Effekte
    enhanceButtons();
    
    // Back-to-Top-Button einrichten
    setupBackToTop();
    
    // Parallax-Effekt für Hero-Bereich
    setupParallaxEffect();
    
    // Verbesserte Lightbox-Animation
    enhanceLightbox();
    
    // Cookie-Banner Animation
    setupCookieBanner();
    
    // Entferne den Scroll-Indikator, falls vorhanden
    removeScrollIndicator();
});

/**
 * Lädt die benötigten Webfonts
 */
function loadFonts() {
    const fontLink1 = document.createElement('link');
    fontLink1.href = 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap';
    fontLink1.rel = 'stylesheet';
    
    const fontLink2 = document.createElement('link');
    fontLink2.href = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap';
    fontLink2.rel = 'stylesheet';
    
    document.head.appendChild(fontLink1);
    document.head.appendChild(fontLink2);
}

/**
 * Entfernt den Scroll-Indikator aus der Hero-Sektion, falls vorhanden
 */
function removeScrollIndicator() {
    const scrollIndicator = document.querySelector('.scroll-indicator');
    if (scrollIndicator && scrollIndicator.parentNode) {
        scrollIndicator.parentNode.removeChild(scrollIndicator);
    }
}

/**
 * Richtet den Preloader ein, der während des Ladens der Seite angezeigt wird
 */
function setupPreloader() {
    // Preloader erstellen, falls noch nicht vorhanden
    if (!document.querySelector('.preloader')) {
        const preloader = document.createElement('div');
        preloader.className = 'preloader';
        
        const loader = document.createElement('div');
        loader.className = 'loader';
        
        preloader.appendChild(loader);
        document.body.appendChild(preloader);
        
        // Verhindere Scrollen während des Preloaders
        document.body.style.overflow = 'hidden';
    }
    
    // Seiteninhalte laden und dann Preloader ausblenden
    window.addEventListener('load', function() {
        const preloader = document.querySelector('.preloader');
        
        if (preloader) {
            setTimeout(function() {
                preloader.classList.add('fade-out');
                document.body.style.overflow = '';
                
                // Entferne den Preloader aus dem DOM nach dem Ausblenden
                setTimeout(function() {
                    if (preloader.parentNode) {
                        preloader.parentNode.removeChild(preloader);
                    }
                }, 500);
            }, 500);
        }
    });
}

/**
 * Initialisiert die Animationen für den Hero-Bereich
 * WICHTIG: Diese Funktion wurde angepasst, um sicherzustellen, dass die Hero-Elemente immer sichtbar sind
 */
function initHeroAnimations() {
    const hero = document.querySelector('.hero');
    if (!hero) return;
    
    // Hero-Elemente animieren
    const heroElements = hero.querySelectorAll('h1, p, .btn');
    
    // WICHTIGER FIX: Stelle sicher, dass die Hero-Elemente sichtbar sind
    heroElements.forEach(element => {
        // Direktes Setzen von Inline-Stilen, um sicherzustellen, dass sie sichtbar sind
        element.style.opacity = '1';
        element.style.transform = 'none';
    });
    
    // Optional können wir noch sanfte Animationen hinzufügen, aber sicherstellen,
    // dass die Elemente am Ende sichtbar sind
    setTimeout(() => {
        heroElements.forEach((element, index) => {
            // Nur Animation hinzufügen, wenn keine direkte Klasse schon gesetzt ist
            if (!element.classList.contains('animate-fadeIn') && 
                !element.classList.contains('animate-slideInFromLeft') && 
                !element.classList.contains('animate-slideInFromRight') && 
                !element.classList.contains('animate-zoomIn')) {
                
                element.style.animation = `fadeIn 0.8s ease-out forwards ${0.2 * index}s`;
            }
        });
    }, 100);
}

/**
 * Richtet die Scroll-Animationen ein
 */
function setupScrollAnimations() {
    // Elemente, die beim Scrollen animiert werden sollen
    const elementsToAnimate = document.querySelectorAll(
        '.about-content, .service-card, .skill-card, .gallery-item, ' +
        '.contact-form-container, .contact-info, .map-container, ' +
        '.service-item, .animate-on-scroll'
    );
    
    if (elementsToAnimate.length === 0) return;
    
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Das Element ist im Viewport
                    entry.target.classList.add('in-view');
                    
                    // Für Elemente mit eigenen Animationsklassen
                    if (entry.target.classList.contains('animate-on-scroll')) {
                        const animationType = getAnimationType(entry.target);
                        entry.target.classList.add(animationType);
                    }
                    
                    // Nicht mehr beobachten, sobald die Animation ausgelöst wurde
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        });
        
        elementsToAnimate.forEach(element => {
            observer.observe(element);
        });
    } else {
        // Fallback für Browser ohne IntersectionObserver
        elementsToAnimate.forEach(element => {
            element.classList.add('in-view');
            if (element.classList.contains('animate-on-scroll')) {
                const animationType = getAnimationType(element);
                element.classList.add(animationType);
            }
        });
    }
}

/**
 * Ermittelt den Animationstyp für ein Element basierend auf seiner Position
 * @param {HTMLElement} element - Das zu animierende Element
 * @returns {string} - Der Animationsklassenname
 */
function getAnimationType(element) {
    // Position des Elements im Viewport ermitteln
    const rect = element.getBoundingClientRect();
    const centerX = rect.left + rect.width / 2;
    const windowCenterX = window.innerWidth / 2;
    
    // Index des Elements bestimmen, falls es in einem Container ist
    let index = 0;
    if (element.parentElement) {
        const siblings = Array.from(element.parentElement.children);
        index = siblings.indexOf(element);
    }
    
    // Animation basierend auf Position und Index auswählen
    if (centerX < windowCenterX) {
        return 'animate-slideInFromLeft';
    } else if (index % 3 === 0) {
        return 'animate-bounceIn';
    } else if (index % 3 === 1) {
        return 'animate-rotateIn';
    } else {
        return 'animate-slideInFromRight';
    }
}

/**
 * Richtet Animationen für Service-Elemente ein (auf der Leistungsseite)
 */
function setupServiceItemAnimations() {
    const serviceItems = document.querySelectorAll('.service-item');
    if (serviceItems.length === 0) return;
    
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = 0.2; // Verzögerung in Sekunden
                    const item = entry.target;
                    
                    // Verzögerte Animation für das Hauptelement
                    item.style.transitionDelay = `${delay}s`;
                    
                    // Verzögerte Animation für untergeordnete Elemente
                    const image = item.querySelector('.service-image');
                    const details = item.querySelector('.service-details');
                    
                    if (image) {
                        image.style.transitionDelay = `${delay + 0.2}s`;
                    }
                    
                    if (details) {
                        details.style.transitionDelay = `${delay + 0.4}s`;
                    }
                    
                    // Element als "in-view" markieren
                    item.classList.add('in-view');
                    
                    // Verzögerung zurücksetzen nach der Animation
                    setTimeout(() => {
                        item.style.transitionDelay = '';
                        if (image) image.style.transitionDelay = '';
                        if (details) details.style.transitionDelay = '';
                    }, 1500);
                    
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        });
        
        serviceItems.forEach(item => {
            observer.observe(item);
        });
    } else {
        // Fallback für Browser ohne IntersectionObserver
        serviceItems.forEach(item => {
            item.classList.add('in-view');
        });
    }
}

/**
 * Verbessert die Hover-Effekte für Galerie-Elemente
 */
function enhanceGalleryItems() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    if (galleryItems.length === 0) return;
    
    galleryItems.forEach(item => {
        // Mouseenter-Effekt
        item.addEventListener('mouseenter', function() {
            // Zusätzlicher Zoom-Effekt
            const image = this.querySelector('img');
            if (image) {
                image.style.transform = 'scale(1.12)';
                image.style.transition = 'transform 0.6s cubic-bezier(0.25, 0.8, 0.25, 1)';
            }
            
            // Caption-Animation verbessern
            const caption = this.querySelector('.gallery-item-caption');
            if (caption) {
                caption.style.transform = 'translateY(0)';
                caption.style.opacity = '1';
                caption.style.transition = 'transform 0.6s cubic-bezier(0.25, 0.8, 0.25, 1), opacity 0.6s cubic-bezier(0.25, 0.8, 0.25, 1)';
            }
        });
        
        // Mouseleave-Effekt
        item.addEventListener('mouseleave', function() {
            const image = this.querySelector('img');
            if (image) {
                image.style.transform = '';
            }
            
            const caption = this.querySelector('.gallery-item-caption');
            if (caption) {
                caption.style.transform = '';
                caption.style.opacity = '';
            }
        });
    });
}

/**
 * Verbessert die Hover-Effekte für Buttons
 */
function enhanceButtons() {
    const buttons = document.querySelectorAll('.btn, .filter-btn');
    if (buttons.length === 0) return;
    
    buttons.forEach(button => {
        // Hover-Effekt verstärken
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.15)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
        
        // Klick-Effekt
        button.addEventListener('mousedown', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 5px 10px rgba(0, 0, 0, 0.1)';
        });
        
        button.addEventListener('mouseup', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.15)';
        });
        
        // Ripple-Effekt für primary buttons
        if (button.classList.contains('btn') && 
            !button.classList.contains('filter-btn') && 
            !button.classList.contains('btn-secondary')) {
            
            button.classList.add('btn-ripple');
            
            // Ripple-Animation stoppen beim Hover
            button.addEventListener('mouseenter', function() {
                this.style.animationPlayState = 'paused';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.animationPlayState = '';
            });
        }
    });
}

/**
 * Richtet einen "Back to Top"-Button ein
 */
function setupBackToTop() {
    // Erstelle den Button, falls noch nicht vorhanden
    if (!document.querySelector('.back-to-top')) {
        const backToTopButton = document.createElement('a');
        backToTopButton.className = 'back-to-top';
        backToTopButton.href = '#';
        backToTopButton.innerHTML = '&#8679;';
        document.body.appendChild(backToTopButton);
        
        // Scroll-Event-Listener
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('active');
            } else {
                backToTopButton.classList.remove('active');
            }
        });
        
        // Klick-Event für glattes Scrollen nach oben
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

/**
 * Fügt einen Parallax-Effekt zum Hero-Bereich hinzu
 */
function setupParallaxEffect() {
    const hero = document.querySelector('.hero');
    if (!hero) return;
    
    window.addEventListener('scroll', function() {
        const scrollPosition = window.scrollY;
        if (scrollPosition <= window.innerHeight) {
            // Parallax-Effekt: Hintergrund bewegt sich langsamer als der Scroll
            hero.style.backgroundPositionY = `${scrollPosition * 0.4}px`;
        }
    });
}

/**
 * Verbesserte Lightbox-Animation für die Galerie
 * Diese Funktion sollte nach der bestehenden Lightbox-Initialisierung aufgerufen werden
 */
function enhanceLightbox() {
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxCaption = document.getElementById('lightbox-caption');
    const lightboxClose = document.getElementById('lightbox-close');
    
    if (!lightbox || !lightboxImg || !lightboxCaption || !lightboxClose) return;
    
    // Verbesserte Öffnen-Animation
    function showLightboxWithAnimation() {
        lightbox.style.display = 'flex';
        lightbox.style.opacity = '0';
        
        setTimeout(() => {
            lightbox.style.opacity = '1';
            lightbox.style.transition = 'opacity 0.4s ease-out';
            
            // Content-Animation
            lightboxImg.style.transform = 'scale(0.9)';
            lightboxImg.style.opacity = '0';
            
            setTimeout(() => {
                lightboxImg.style.transform = 'scale(1)';
                lightboxImg.style.opacity = '1';
                lightboxImg.style.transition = 'transform 0.5s cubic-bezier(0.25, 0.8, 0.25, 1), opacity 0.5s cubic-bezier(0.25, 0.8, 0.25, 1)';
            }, 100);
            
            lightboxCaption.style.opacity = '0';
            lightboxCaption.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                lightboxCaption.style.opacity = '1';
                lightboxCaption.style.transform = 'translateY(0)';
                lightboxCaption.style.transition = 'opacity 0.5s cubic-bezier(0.25, 0.8, 0.25, 1), transform 0.5s cubic-bezier(0.25, 0.8, 0.25, 1)';
            }, 300);
        }, 10);
    }
    
    // Schließen-Animation
    function hideLightboxWithAnimation() {
        lightbox.style.opacity = '0';
        lightboxImg.style.transform = 'scale(0.9)';
        lightboxImg.style.opacity = '0';
        
        setTimeout(() => {
            lightbox.style.display = 'none';
            // Für das nächste Öffnen zurücksetzen
            lightbox.style.opacity = '';
            lightboxImg.style.transform = '';
            lightboxImg.style.opacity = '';
        }, 400);
    }
    
    // Event-Listener für vorhandene Lightbox hinzufügen
    lightboxClose.addEventListener('click', hideLightboxWithAnimation);
    
    // Außerhalb-Klick-Handler
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            hideLightboxWithAnimation();
        }
    });
    
    // Tastatur-Navigation
    document.addEventListener('keydown', function(e) {
        if (!lightbox.classList.contains('active')) return;
        
        if (e.key === 'Escape') {
            hideLightboxWithAnimation();
        }
    });
    
    // Suchen nach allen Galerie-Elementen, die die Lightbox öffnen können
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    galleryItems.forEach(item => {
        item.addEventListener('click', function() {
            // Die vorhandene Lightbox-Funktionalität wird nicht überschrieben,
            // sondern wir fügen nur unsere Animation hinzu
            setTimeout(() => {
                if (lightbox.classList.contains('active')) {
                    showLightboxWithAnimation();
                }
            }, 10);
        });
    });
}

/**
 * Cookie-Banner-Animation einrichten
 * Komplett überarbeitete Version, die sicherstellt, dass der Banner angezeigt wird
 */
function setupCookieBanner() {
    const cookieBanner = document.getElementById('cookie-banner');
    if (!cookieBanner) {
        return;
    }
    
    // Banner anzeigen, falls Cookies noch nicht akzeptiert wurden
    if (localStorage.getItem('cookiesAccepted') !== 'true') {
        // Direktes Anzeigen des Banners ohne Transformationen oder Verzögerungen
        cookieBanner.style.display = 'flex';
        cookieBanner.style.opacity = '1';
        cookieBanner.style.transform = 'none';
    }
    
    // Akzeptieren-Button-Event
    const acceptButton = document.getElementById('cookie-accept');
    if (acceptButton) {
        // Sicherstellen, dass wir nicht mehrere Event-Listener hinzufügen
        acceptButton.removeEventListener('click', cookieAcceptHandler);
        acceptButton.addEventListener('click', cookieAcceptHandler);
    }
    
    // Separate Funktion für den Event-Handler, um ihn später entfernen zu können
    function cookieAcceptHandler() {
        localStorage.setItem('cookiesAccepted', 'true');
        
        // Banner ausblenden
        cookieBanner.style.display = 'none';
    }
}