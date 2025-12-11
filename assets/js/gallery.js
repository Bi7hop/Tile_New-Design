/**
 * Galerie-Funktionalität mit Lightbox und Filterung
 */
document.addEventListener('DOMContentLoaded', function() {
    initGallery();
});

function initGallery() {
    // Hauptelemente abrufen
    const gallery = document.getElementById('gallery');
    const lightbox = document.getElementById('lightbox');
    
    // Wenn keine Galerie vorhanden ist, abbrechen
    if (!gallery || !lightbox) return;
    
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxCaption = document.getElementById('lightbox-caption');
    const lightboxClose = document.getElementById('lightbox-close');
    const lightboxPrev = document.getElementById('lightbox-prev');
    const lightboxNext = document.getElementById('lightbox-next');
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    let currentIndex = 0;
    let visibleItems = []; // Nur sichtbare Elemente speichern
    
    // Initial sichtbare Elemente sammeln
    updateVisibleItems();
    
    // Klick auf Galerie-Items
    gallery.addEventListener('click', function(e) {
        // Das nächste .gallery-item Element zum geklickten Element finden
        const galleryItem = e.target.closest('.gallery-item');
        if (!galleryItem) return; // Wenn kein Gallery-Item, abbrechen
        
        // Aktuellen Index ermitteln
        updateVisibleItems();
        currentIndex = visibleItems.indexOf(galleryItem);
        
        // Wenn das Element nicht in der Liste ist oder die Liste leer ist, abbrechen
        if (currentIndex === -1 || visibleItems.length === 0) return;
        
        // Bild und Caption aktualisieren
        updateLightboxContent();
        
        // Lightbox anzeigen
        lightbox.classList.add('active');
    });
    
    // Filter-Buttons
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Aktiven Button markieren
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter anwenden
            const filter = this.getAttribute('data-filter');
            filterGalleryItems(filter);
            
            // Sichtbare Items aktualisieren
            updateVisibleItems();
        });
    });
    
    // Schließen-Button
    if (lightboxClose) {
        lightboxClose.addEventListener('click', closeLightbox);
    }
    
    // Außerhalb klicken, um zu schließen
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });
    
    // Vorheriges Bild
    if (lightboxPrev) {
        lightboxPrev.addEventListener('click', showPreviousImage);
    }
    
    // Nächstes Bild
    if (lightboxNext) {
        lightboxNext.addEventListener('click', showNextImage);
    }
    
    // Tastatur-Navigation
    document.addEventListener('keydown', function(e) {
        if (!lightbox.classList.contains('active')) return;
        
        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === 'ArrowLeft') {
            showPreviousImage();
        } else if (e.key === 'ArrowRight') {
            showNextImage();
        }
    });
    
    /**
     * Aktualisiert die Liste der sichtbaren Galerie-Elemente
     */
    function updateVisibleItems() {
        visibleItems = Array.from(gallery.querySelectorAll('.gallery-item')).filter(item => {
            // Nur Elemente, die sichtbar sind
            return getComputedStyle(item).display !== 'none';
        });
    }
    
    /**
     * Filtert die Galerie-Elemente basierend auf der Kategorie
     */
    function filterGalleryItems(filter) {
        const items = gallery.querySelectorAll('.gallery-item');
        
        items.forEach(item => {
            const category = item.getAttribute('data-category');
            
            if (filter === 'all' || category === filter) {
                item.style.display = 'block';
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, 50);
            } else {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    item.style.display = 'none';
                }, 300);
            }
        });
    }
    
    /**
     * Schließt die Lightbox
     */
    function closeLightbox() {
        lightbox.classList.remove('active');
    }
    
    /**
     * Zeigt das vorherige Bild in der Lightbox
     */
    function showPreviousImage() {
        if (visibleItems.length === 0) return;
        
        currentIndex = (currentIndex - 1 + visibleItems.length) % visibleItems.length;
        updateLightboxContent();
    }
    
    /**
     * Zeigt das nächste Bild in der Lightbox
     */
    function showNextImage() {
        if (visibleItems.length === 0) return;
        
        currentIndex = (currentIndex + 1) % visibleItems.length;
        updateLightboxContent();
    }
    
    /**
     * Aktualisiert den Inhalt der Lightbox basierend auf dem aktuellen Index
     */
    function updateLightboxContent() {
        // Sicherheitscheck
        if (visibleItems.length === 0 || !visibleItems[currentIndex]) {
            console.warn('Kein gültiges Galerie-Element für den Index:', currentIndex);
            return;
        }
        
        const item = visibleItems[currentIndex];
        
        // Bild aktualisieren
        const galleryImg = item.querySelector('img');
        if (galleryImg && lightboxImg) {
            lightboxImg.src = galleryImg.src;
            lightboxImg.alt = galleryImg.alt || '';
        }
        
        // Caption aktualisieren
        if (lightboxCaption) {
            lightboxCaption.innerHTML = '';
            
            const captionH3 = item.querySelector('.gallery-item-caption h3');
            if (captionH3) {
                const h3 = document.createElement('h3');
                h3.textContent = captionH3.textContent;
                lightboxCaption.appendChild(h3);
            }
            
            const captionP = item.querySelector('.gallery-item-caption p');
            if (captionP) {
                const p = document.createElement('p');
                p.textContent = captionP.textContent;
                lightboxCaption.appendChild(p);
            }
        }
    }
}