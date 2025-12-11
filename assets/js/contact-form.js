document.addEventListener('DOMContentLoaded', function() {
    setupContactForm();
});

function setupContactForm() {
    const contactForm = document.getElementById('contact-form');
    if (!contactForm) return;
    
    const formMessage = document.getElementById('form-message');
    const submitBtn = document.getElementById('submit-btn');
    
    function validateForm() {
        let isValid = true;
        const name = document.getElementById('name');
        const email = document.getElementById('email');
        const message = document.getElementById('message');
        const privacy = document.getElementById('privacy');
        
        if (!name.value.trim()) {
            name.classList.add('error');
            isValid = false;
            name.classList.add('shake');
            setTimeout(() => { name.classList.remove('shake'); }, 500);
        } else {
            name.classList.remove('error');
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.trim() || !emailRegex.test(email.value.trim())) {
            email.classList.add('error');
            isValid = false;
            email.classList.add('shake');
            setTimeout(() => { email.classList.remove('shake'); }, 500);
        } else {
            email.classList.remove('error');
        }
        
        if (!message.value.trim()) {
            message.classList.add('error');
            isValid = false;
            message.classList.add('shake');
            setTimeout(() => { message.classList.remove('shake'); }, 500);
        } else if (message.value.length > 5000) {
            message.classList.add('error');
            isValid = false;
            showMessage('Die Nachricht ist zu lang (max. 5000 Zeichen).', 'error');
        } else {
            message.classList.remove('error');
        }
        
        if (!privacy.checked) {
            privacy.classList.add('error');
            isValid = false;
            const privacyLabel = privacy.parentElement;
            privacyLabel.classList.add('shake');
            setTimeout(() => { privacyLabel.classList.remove('shake'); }, 500);
        } else {
            privacy.classList.remove('error');
        }
        
        return isValid;
    }
    
    const formFields = contactForm.querySelectorAll('.form-control, .form-check-input');
    formFields.forEach(field => {
        field.addEventListener('input', function() {
            if (field.type === 'checkbox') {
                if (!field.checked) {
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            } 
            else if (field.type === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!field.value.trim() || !emailRegex.test(field.value.trim())) {
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            } 
            else {
                if (!field.value.trim() && field.hasAttribute('required')) {
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            }
        });
    });

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!validateForm()) {
            showMessage('Bitte füllen Sie alle Pflichtfelder korrekt aus.', 'error');
            return;
        }
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Wird gesendet...';
            submitBtn.classList.add('btn-loading');
        }
        
        const formData = new FormData(contactForm);
        fetch(contactForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            return response.json().then(data => {
                if (!response.ok) {
                    throw new Error(data.message || 'Fehler beim Senden der Nachricht');
                }
                return data;
            });
        })
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                contactForm.reset();
                contactForm.style.opacity = '0.7';
                setTimeout(() => {
                    contactForm.style.opacity = '1';
                }, 1000);
                setTimeout(() => {
                    hideMessage();
                }, 8000);
            } else {
                throw new Error(data.message || 'Unbekannter Fehler');
            }
        })
        .catch(error => {
            console.error('Kontaktformular-Fehler:', error);
            showMessage(
                error.message || 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später noch einmal oder kontaktieren Sie uns telefonisch.', 
                'error'
            );
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Nachricht senden';
                submitBtn.classList.remove('btn-loading');
            }
        });
    });
    
    function showMessage(message, type) {
        if (formMessage) {
            formMessage.textContent = message;
            formMessage.className = `form-message ${type}`;
            formMessage.style.display = 'block';
            
            formMessage.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }
    }
    
    function hideMessage() {
        if (formMessage) {
            formMessage.style.opacity = '0';
            setTimeout(() => {
                formMessage.style.display = 'none';
                formMessage.style.opacity = '1';
            }, 500);
        }
    }
    
    if (!document.querySelector('style#contact-form-styles')) {
        const styleElement = document.createElement('style');
        styleElement.id = 'contact-form-styles';
        styleElement.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            .loading-spinner {
                display: inline-block;
                width: 16px;
                height: 16px;
                border: 2px solid rgba(255,255,255,0.3);
                border-radius: 50%;
                border-top-color: white;
                animation: spin 1s infinite linear;
                margin-right: 8px;
            }
            .shake {
                animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
            }
            @keyframes shake {
                10%, 90% { transform: translate3d(-1px, 0, 0); }
                20%, 80% { transform: translate3d(2px, 0, 0); }
                30%, 50%, 70% { transform: translate3d(-3px, 0, 0); }
                40%, 60% { transform: translate3d(3px, 0, 0); }
            }
            .btn-loading {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .form-message {
                margin-bottom: 1.5rem;
                padding: 1rem 1.2rem;
                border-radius: 6px;
                font-weight: 500;
                transition: opacity 0.5s ease;
            }
            .form-message.success {
                background-color: rgba(46, 204, 113, 0.1);
                border: 2px solid #2ecc71;
                color: #27ae60;
            }
            .form-message.error {
                background-color: rgba(231, 76, 60, 0.1);
                border: 2px solid #e74c3c;
                color: #c0392b;
            }
        `;
        document.head.appendChild(styleElement);
    }
}

function validatePhoneNumber(phone) {
    const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
    return phoneRegex.test(phone.replace(/\s/g, ''));
}

function sanitizeInput(input) {
    const div = document.createElement('div');
    div.textContent = input;
    return div.innerHTML;
}