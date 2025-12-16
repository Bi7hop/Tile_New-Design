document.addEventListener('DOMContentLoaded', function() {
    const cookieModal = document.getElementById('cookie-modal');
    const cookieAcceptAll = document.getElementById('cookie-accept-all');
    const cookieClose = document.getElementById('cookie-close');
    
    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
        document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/;SameSite=Lax';
    }
    
    function getCookie(name) {
        const nameEQ = name + '=';
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    
    function showCookieModal() {
        if (cookieModal) {
            cookieModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function hideCookieModal() {
        if (cookieModal) {
            cookieModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    
    function acceptCookies() {
        setCookie('cookie_consent', 'accepted', 365);
        hideCookieModal();
    }
    
    if (!getCookie('cookie_consent')) {
        setTimeout(showCookieModal, 500);
    }
    
    if (cookieAcceptAll) {
        cookieAcceptAll.addEventListener('click', acceptCookies);
    }
    
    if (cookieClose) {
        cookieClose.addEventListener('click', acceptCookies);
    }
    
    if (cookieModal) {
        cookieModal.addEventListener('click', function(e) {
            if (e.target === cookieModal) {
                acceptCookies();
            }
        });
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && cookieModal && cookieModal.classList.contains('active')) {
            acceptCookies();
        }
    });
});