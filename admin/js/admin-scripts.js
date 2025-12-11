/**
 * Admin-Bereich JavaScript für Fliesen Runnebaum
 */
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle
    const sidebarToggle = document.querySelectorAll('.sidebar-toggle');
    const adminContainer = document.querySelector('.admin-container');
    
    sidebarToggle.forEach(button => {
        button.addEventListener('click', function() {
            adminContainer.classList.toggle('sidebar-collapsed');
        });
    });
    
    // Automatisches Berechnen der Ersparnis
    const oldPriceInput = document.getElementById('old_price');
    const newPriceInput = document.getElementById('new_price');
    const savingInput = document.getElementById('saving');
    
    function calculateSaving() {
        if (oldPriceInput && newPriceInput && savingInput) {
            const oldPrice = parseFloat(oldPriceInput.value.replace(',', '.')) || 0;
            const newPrice = parseFloat(newPriceInput.value.replace(',', '.')) || 0;
            const saving = Math.max(0, oldPrice - newPrice).toFixed(2).replace('.', ',');
            
            savingInput.value = saving;
        }
    }
    
    if (oldPriceInput && newPriceInput) {
        oldPriceInput.addEventListener('input', calculateSaving);
        newPriceInput.addEventListener('input', calculateSaving);
    }
    
    // Bildvorschau für Hauptbild
    const mainImageInput = document.getElementById('main_image');
    const mainImagePreview = document.getElementById('main-image-preview');
    const mainImageDropzone = document.getElementById('main-image-dropzone');
    
    if (mainImageInput && mainImagePreview && mainImageDropzone) {
        // Klick auf Dropzone simuliert Klick auf Input
        mainImageDropzone.addEventListener('click', function() {
            mainImageInput.click();
        });
        
        // Drag & Drop Funktionalität
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            mainImageDropzone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            mainImageDropzone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            mainImageDropzone.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            mainImageDropzone.classList.add('dragover');
        }
        
        function unhighlight() {
            mainImageDropzone.classList.remove('dragover');
        }
        
        mainImageDropzone.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                mainImageInput.files = files;
                updateMainImagePreview(files[0]);
            }
        }
        
        // Änderung des Input-Feldes
        mainImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                updateMainImagePreview(this.files[0]);
            }
        });
        
        function updateMainImagePreview(file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                mainImagePreview.src = e.target.result;
                const previewInfo = mainImagePreview.nextElementSibling;
                if (previewInfo) {
                    previewInfo.textContent = 'Neues Bild: ' + file.name;
                }
            };
            
            reader.readAsDataURL(file);
        }
    }
    
    // Bildvorschau für Detailbilder
    const detailImagesInput = document.getElementById('detail_images');
    const detailImagesGallery = document.getElementById('detail-images-gallery');
    const detailImagesDropzone = document.getElementById('detail-images-dropzone');
    
    if (detailImagesInput && detailImagesGallery && detailImagesDropzone) {
        // Klick auf Dropzone simuliert Klick auf Input
        detailImagesDropzone.addEventListener('click', function() {
            detailImagesInput.click();
        });
        
        // Drag & Drop Funktionalität
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            detailImagesDropzone.addEventListener(eventName, preventDefaults, false);
        });
        
        ['dragenter', 'dragover'].forEach(eventName => {
            detailImagesDropzone.addEventListener(eventName, function() {
                detailImagesDropzone.classList.add('dragover');
            }, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            detailImagesDropzone.addEventListener(eventName, function() {
                detailImagesDropzone.classList.remove('dragover');
            }, false);
        });
        
        detailImagesDropzone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                detailImagesInput.files = files;
                updateDetailImagesPreview(files);
            }
        }, false);
        
        // Änderung des Input-Feldes
        detailImagesInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                updateDetailImagesPreview(this.files);
            }
        });
        
        function updateDetailImagesPreview(files) {
            // Leere die Galerie
            detailImagesGallery.innerHTML = '';
            
            // Maximale Anzahl von Bildern begrenzen
            const maxImages = 3;
            const filesToPreview = Array.from(files).slice(0, maxImages);
            
            // Für jedes Bild eine Vorschau erstellen
            filesToPreview.forEach((file, index) => {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const galleryItem = document.createElement('div');
                    galleryItem.className = 'image-gallery-item';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Neues Detailbild ' + (index + 1);
                    
                    const actions = document.createElement('div');
                    actions.className = 'image-gallery-actions';
                    
                    const removeAction = document.createElement('span');
                    removeAction.className = 'image-gallery-action remove';
                    removeAction.title = 'Entfernen';
                    removeAction.innerHTML = '<i class="fas fa-trash-alt"></i>';
                    
                    // Füge einen Event-Listener für die Entfernen-Aktion hinzu
                    // (In diesem Fall nur visuelle Entfernung, da neue Bilder die alten ersetzen)
                    removeAction.addEventListener('click', function() {
                        galleryItem.remove();
                    });
                    
                    actions.appendChild(removeAction);
                    galleryItem.appendChild(img);
                    galleryItem.appendChild(actions);
                    detailImagesGallery.appendChild(galleryItem);
                };
                
                reader.readAsDataURL(file);
            });
        }
    }
    
    // Alerts automatisch ausblenden
    const alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.height = alert.offsetHeight + 'px';
            
            setTimeout(() => {
                alert.style.height = '0';
                alert.style.margin = '0';
                alert.style.padding = '0';
                alert.style.border = 'none';
                
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 300);
            }, 300);
        }, 3000);
    });
});