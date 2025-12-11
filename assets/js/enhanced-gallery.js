document.addEventListener('DOMContentLoaded', function() {
    initEnhancedGallery();
});

function initEnhancedGallery() {
    const gallery = document.getElementById('gallery');
    const lightbox = document.getElementById('lightbox');
    
    if (!gallery || !lightbox) return;
    
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxCaption = document.getElementById('lightbox-caption');
    const lightboxClose = document.getElementById('lightbox-close');
    const lightboxPrev = document.getElementById('lightbox-prev');
    const lightboxNext = document.getElementById('lightbox-next');
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    let currentIndex = 0;
    let visibleItems = [];
    
    updateVisibleItems();
    
    gallery.addEventListener('click', function(e) {
        const galleryItem = e.target.closest('.gallery-item');
        if (!galleryItem) return;
        
        updateVisibleItems();
        currentIndex = visibleItems.indexOf(galleryItem);
        
        if (currentIndex === -1 || visibleItems.length === 0) return;
        
        updateLightboxContent();
        lightbox.classList.add('active');
    });
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            filterGalleryItems(filter);
            updateVisibleItems();
        });
    });
    
    if (lightboxClose) {
        lightboxClose.addEventListener('click', closeLightbox);
    }
    
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });
    
    if (lightboxPrev) {
        lightboxPrev.addEventListener('click', showPreviousImage);
    }
    
    if (lightboxNext) {
        lightboxNext.addEventListener('click', showNextImage);
    }
    
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
    
    function updateVisibleItems() {
        visibleItems = Array.from(gallery.querySelectorAll('.gallery-item')).filter(item => {
            return getComputedStyle(item).display !== 'none';
        });
    }
    
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
    
    function closeLightbox() {
        lightbox.classList.remove('active');
    }
    
    function showPreviousImage() {
        if (visibleItems.length === 0) return;
        currentIndex = (currentIndex - 1 + visibleItems.length) % visibleItems.length;
        updateLightboxContent();
    }
    
    function showNextImage() {
        if (visibleItems.length === 0) return;
        currentIndex = (currentIndex + 1) % visibleItems.length;
        updateLightboxContent();
    }
    
    function updateLightboxContent() {
        if (visibleItems.length === 0 || !visibleItems[currentIndex]) {
            return;
        }
        
        const item = visibleItems[currentIndex];
        const projectType = item.getAttribute('data-project-type');
        const galleryImg = item.querySelector('img');
        if (galleryImg && lightboxImg) {
            lightboxImg.src = galleryImg.src;
            lightboxImg.alt = galleryImg.alt || '';
        }
        
        if (lightboxCaption) {
            lightboxCaption.innerHTML = '';
            
            const projectBadge = document.createElement('div');
            projectBadge.className = 'lightbox-project-badge';
            if (projectType === 'real') {
                projectBadge.className += ' real';
                projectBadge.innerHTML = '<span class="badge-icon">✓</span> Eigenes Projekt von Fliesen Runnebaum';
            } else {
                projectBadge.className += ' example';
                projectBadge.innerHTML = '<span class="badge-icon">ℹ</span> Referenz-Beispiel zur Inspiration';
            }
            lightboxCaption.appendChild(projectBadge);
            
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