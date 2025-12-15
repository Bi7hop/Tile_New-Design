document.addEventListener('DOMContentLoaded', function() {
    showLagerBanner();
});

function showLagerBanner() {
    if (sessionStorage.getItem('lagerBannerClosed')) {
        return;
    }

    const banner = document.createElement('div');
    banner.className = 'lager-banner';
    banner.innerHTML = `
        <div class="lager-banner-content">
            <div class="lager-banner-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div class="lager-banner-text">
                <strong>Jeden Freitag Lagerverkauf!</strong>
                <span>8:00 - 16:00 Uhr · Rouen Kamp 1, Steinfeld</span>
            </div>
            <button class="lager-banner-close" aria-label="Banner schließen">&times;</button>
        </div>
    `;
    
    document.body.prepend(banner);
    
    setTimeout(() => {
        banner.classList.add('active');
        document.body.classList.add('has-banner');
    }, 100);
    
    banner.querySelector('.lager-banner-close').addEventListener('click', function() {
        banner.classList.remove('active');
        document.body.classList.remove('has-banner');
        setTimeout(() => {
            banner.remove();
        }, 400);
        sessionStorage.setItem('lagerBannerClosed', 'true');
    });
}