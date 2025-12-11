document.addEventListener('DOMContentLoaded', function() {
    setupPreloader();
});

function setupPreloader() {
    if (!document.querySelector('.preloader')) {
        const preloader = document.createElement('div');
        preloader.className = 'preloader';
        
        const loader = document.createElement('div');
        loader.className = 'loader';
        
        preloader.appendChild(loader);
        document.body.appendChild(preloader);
        
        document.body.style.overflow = 'hidden';
    }
    
    window.addEventListener('load', function() {
        const preloader = document.querySelector('.preloader');
        
        if (preloader) {
            setTimeout(function() {
                preloader.classList.add('fade-out');
                document.body.style.overflow = '';
                setTimeout(function() {
                    if (preloader.parentNode) {
                        preloader.parentNode.removeChild(preloader);
                    }
                }, 500);
            }, 500);
        }
    });
}