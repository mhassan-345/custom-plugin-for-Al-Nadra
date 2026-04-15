document.addEventListener('DOMContentLoaded', function() {
    // 1. Handle original Calculator Forms (Iterate over all instances)
    document.querySelectorAll('.js-dubai-calc-form').forEach(calcForm => {
        // Handle Pill Groups (Office Requirements)
        const pillGroups = calcForm.querySelectorAll('.pill-group');
        pillGroups.forEach(group => {
            const btns = group.querySelectorAll('.pill-btn');
            const hiddenInput = group.querySelector('input[type="hidden"]');
            
            btns.forEach(btn => {
                btn.addEventListener('click', () => {
                    btns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    hiddenInput.value = btn.getAttribute('data-value');
                });
            });
        });

        // Handle Circle/Pill Combo Groups (Shareholders & Employees)
        const circlePillGroups = calcForm.querySelectorAll('.circle-pill-group');
        circlePillGroups.forEach(group => {
            const btns = group.querySelectorAll('.circle-btn, .pill-btn');
            const hiddenInput = group.querySelector('input[type="hidden"]');
            
            btns.forEach(btn => {
                btn.addEventListener('click', () => {
                    btns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    hiddenInput.value = btn.getAttribute('data-value');
                });
            });
        });

        // Handle Contact Toggle
        const contactToggleBtns = calcForm.querySelectorAll('.contact-toggle .toggle-btn');
        const contactInput = calcForm.querySelector('.js-contact-input');
        const contactTypeHidden = calcForm.querySelector('.js-contact_type');

        if (contactToggleBtns && contactInput && contactTypeHidden) {
            contactToggleBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    contactToggleBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    
                    const type = btn.getAttribute('data-type');
                    contactTypeHidden.value = type;

                    if (type === 'email') {
                        contactInput.type = 'email';
                        contactInput.placeholder = 'Email';
                    } else if (type === 'whatsapp') {
                        contactInput.type = 'tel';
                        contactInput.placeholder = 'Phone Number (WhatsApp)';
                    }
                });
            });
        }

        // Real-time input restrictions
        if (contactInput) {
            contactInput.addEventListener('input', function(e) {
                if (this.type === 'tel') {
                    this.value = this.value.replace(/[^\d\s+\-()]/g, '');
                }
            });
        }

        // Handle Form Submission via AJAX
        calcForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = calcForm.querySelector('.js-dubai-calc-submit');
            const messageDiv = calcForm.querySelector('.dubai_calc_message');
            
            if (!submitBtn) return;
            const originalBtnText = submitBtn.innerText;
            
            submitBtn.innerText = 'PROCESSING...';
            submitBtn.disabled = true;
            if(messageDiv) messageDiv.style.display = 'none';

            const formData = new FormData(calcForm);
            formData.append('action', 'submit_custom_calculator');
            formData.append('nonce', customPluginObj.nonce);

            fetch(customPluginObj.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(messageDiv) messageDiv.style.display = 'block';
                if (data.success) {
                    if(messageDiv) {
                        messageDiv.style.color = '#28a745';
                        messageDiv.innerText = data.data.message;
                    }
                    calcForm.reset();
                    
                    // Clear active states
                    calcForm.querySelectorAll('.pill-btn, .circle-btn').forEach(btn => btn.classList.remove('active'));
                    
                    // Handle Contact toggle reset visually
                    if (contactToggleBtns) {
                        contactToggleBtns.forEach(b => b.classList.remove('active'));
                        const emailBtn = calcForm.querySelector('.contact-toggle .toggle-btn[data-type="email"]');
                        if (emailBtn) emailBtn.classList.add('active');
                    }
                    if (contactTypeHidden) contactTypeHidden.value = 'email';
                    if (contactInput) {
                        contactInput.type = 'email';
                        contactInput.placeholder = 'Email';
                    }

                    calcForm.querySelectorAll('input[type="hidden"]').forEach(input => {
                        // Only clear the hidden inputs we added for groups, not the contact_type which we just reset
                        if(input.name !== 'contact_type' && input.name !== 'form_source') {
                            input.value = '';
                        }
                    });
                    
                } else {
                    if(messageDiv) {
                        messageDiv.style.color = '#dc3545';
                        messageDiv.innerText = data.data.message || 'Something went wrong. Please try again.';
                    }
                }
            })
            .catch(error => {
                if(messageDiv) {
                    messageDiv.style.display = 'block';
                    messageDiv.style.color = '#dc3545';
                    messageDiv.innerText = 'Server error. Please try again later.';
                }
            })
            .finally(() => {
                submitBtn.innerText = originalBtnText;
                submitBtn.disabled = false;
            });
        });
    });

    // 2. Handle Expert Support Forms (Iterate over all instances)
    document.querySelectorAll('.js-expert-support-form').forEach(expertForm => {
        const toggleBtns = expertForm.querySelectorAll('.expert-toggle-btn');
        const contactTypeHidden = expertForm.querySelector('.expert_contact_type');
        const phoneContainer = expertForm.querySelector('.expert_phone_container');
        const emailContainer = expertForm.querySelector('.expert_email_container');
        const phoneInput = expertForm.querySelector('.expert_phone');
        const emailInput = expertForm.querySelector('.expert_email');

        toggleBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                toggleBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const type = btn.getAttribute('data-type');
                if (contactTypeHidden) contactTypeHidden.value = type;

                if (type === 'phone') {
                    if(phoneContainer) phoneContainer.style.display = 'flex';
                    if(emailContainer) emailContainer.style.display = 'none';
                    if(phoneInput) phoneInput.setAttribute('required', 'required');
                    if(emailInput) emailInput.removeAttribute('required');
                } else {
                    if(phoneContainer) phoneContainer.style.display = 'none';
                    if(emailContainer) emailContainer.style.display = 'flex';
                    if(emailInput) emailInput.setAttribute('required', 'required');
                    if(phoneInput) phoneInput.removeAttribute('required');
                }
            });
        });

        // Real-time input restrictions
        const nameInput = expertForm.querySelector('.expert-name-input');
        if (nameInput) {
            nameInput.addEventListener('input', function(e) {
                // Restrict to alphabets, spaces, and hyphens
                this.value = this.value.replace(/[^a-zA-Z\s\-]/g, '');
            });
        }

        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                // Restrict to numbers and phone symbols
                this.value = this.value.replace(/[^\d\s+\-()]/g, '');
            });
        }

        // Submission
        expertForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = expertForm.querySelector('.js-expert-submit-btn');
            const messageDiv = expertForm.querySelector('.expert_msg');
            
            if (!submitBtn) return;
            const originalBtnText = submitBtn.innerText;
            
            submitBtn.innerText = 'PROCESSING...';
            submitBtn.disabled = true;
            if(messageDiv) messageDiv.style.display = 'none';

            const formData = new FormData(expertForm);
            formData.append('action', 'submit_custom_calculator');
            formData.append('nonce', customPluginObj.nonce);

            fetch(customPluginObj.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(messageDiv) messageDiv.style.display = 'block';
                if (data.success) {
                    if(messageDiv) {
                        messageDiv.style.color = '#28a745';
                        messageDiv.innerText = data.data.message;
                    }
                    expertForm.reset();
                    
                    // Reset toggles visually
                    toggleBtns.forEach(b => b.classList.remove('active'));
                    const phoneBtn = expertForm.querySelector('.expert-toggle-btn[data-type="phone"]');
                    if(phoneBtn) phoneBtn.classList.add('active');
                    if(contactTypeHidden) contactTypeHidden.value = 'phone';
                    
                    if(phoneContainer) phoneContainer.style.display = 'flex';
                    if(emailContainer) emailContainer.style.display = 'none';
                    if(phoneInput) phoneInput.setAttribute('required', 'required');
                    if(emailInput) emailInput.removeAttribute('required');
                    
                } else {
                    if(messageDiv) {
                        messageDiv.style.color = '#dc3545';
                        messageDiv.innerText = data.data.message || 'Something went wrong. Please try again.';
                    }
                }
            })
            .catch(error => {
                if(messageDiv) {
                    messageDiv.style.display = 'block';
                    messageDiv.style.color = '#dc3545';
                    messageDiv.innerText = 'Server error. Please try again later.';
                }
            })
            .finally(() => {
                submitBtn.innerText = originalBtnText;
                submitBtn.disabled = false;
            });
        });
    });

    // 3. Handle Pricing Grid and Modal
    const pricingModal = document.getElementById('consultationModal');
    const closeBtn = document.getElementById('closeModal');
    
    if (pricingModal) {
        // Move modal to body to prevent stacking context overlap issues from Avada builder wrappers
        document.body.appendChild(pricingModal);

        // Setup triggers
        document.querySelectorAll('.trigger-popup').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const card = btn.closest('.pricing-card');
                const pkgName = card.getAttribute('data-package');
                const pkgPrice = card.getAttribute('data-price');

                const packageInput = document.getElementById('selectedPackageName');
                const priceInput = document.getElementById('selectedPackagePrice');
                const displayTargetPackage = document.getElementById('displayTargetPackage');
                const displayTargetPrice = document.getElementById('displayTargetPrice');
                const packageInfoDisplay = document.getElementById('packageInfoDisplay');

                if(packageInput) packageInput.value = pkgName;
                if(priceInput) priceInput.value = pkgPrice;
                if(displayTargetPackage) displayTargetPackage.textContent = pkgName;
                if(displayTargetPrice) displayTargetPrice.textContent = pkgPrice;
                if(packageInfoDisplay) packageInfoDisplay.style.display = 'block';

                pricingModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });

        // Close logic
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                pricingModal.classList.remove('active');
                document.body.style.overflow = '';
            });
        }
        pricingModal.addEventListener('click', (e) => {
            if (e.target === pricingModal) {
                pricingModal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Tabs
        const tabBtns = pricingModal.querySelectorAll('.tab-btn');
        const phoneArea = document.getElementById('phoneInputArea');
        const emailArea = document.getElementById('emailInputArea');
        const phoneInput = pricingModal.querySelector('.pricing_phone');
        const emailInput = pricingModal.querySelector('.pricing_email');
        const contactTypeHidden = pricingModal.querySelector('.pricing_contact_type');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                tabBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const target = btn.getAttribute('data-target');
                if (contactTypeHidden) contactTypeHidden.value = target;

                if (target === 'phone') {
                    if(phoneArea) phoneArea.style.display = 'block';
                    if(emailArea) emailArea.style.display = 'none';
                    if(phoneInput) phoneInput.setAttribute('required', 'required');
                    if(emailInput) emailInput.removeAttribute('required');
                } else {
                    if(phoneArea) phoneArea.style.display = 'none';
                    if(emailArea) emailArea.style.display = 'block';
                    if(emailInput) emailInput.setAttribute('required', 'required');
                    if(phoneInput) phoneInput.removeAttribute('required');
                }
            });
        });

        // Real-time input restrictions
        const nameInput = pricingModal.querySelector('.pricing_name');
        if (nameInput) {
            nameInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^a-zA-Z\s\-]/g, '');
            });
        }

        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^\d\s+\-()]/g, '');
            });
        }

        // Form submission
        document.querySelectorAll('.js-pricing-popup-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = form.querySelector('.js-pricing-submit');
                const messageDiv = form.querySelector('.pricing_msg');
                
                if (!submitBtn) return;
                const originalBtnText = submitBtn.innerText;
                
                submitBtn.innerText = 'PROCESSING...';
                submitBtn.disabled = true;
                if(messageDiv) messageDiv.style.display = 'none';

                const formData = new FormData(form);
                formData.append('action', 'submit_custom_calculator');
                formData.append('nonce', customPluginObj.nonce);

                fetch(customPluginObj.ajax_url, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(messageDiv) messageDiv.style.display = 'block';
                    if (data.success) {
                        if(messageDiv) {
                            messageDiv.style.color = '#28a745';
                            messageDiv.innerText = data.data.message;
                        }
                        
                        setTimeout(() => {
                            pricingModal.classList.remove('active');
                            document.body.style.overflow = '';
                            form.reset();
                            if(messageDiv) messageDiv.style.display = 'none';
                        }, 3000);
                        
                    } else {
                        if(messageDiv) {
                            messageDiv.style.color = '#dc3545';
                            messageDiv.innerText = data.data.message || 'Something went wrong.';
                        }
                    }
                })
                .catch(error => {
                    if(messageDiv) {
                        messageDiv.style.display = 'block';
                        messageDiv.style.color = '#dc3545';
                        messageDiv.innerText = 'Server error. Please try again later.';
                    }
                })
                .finally(() => {
                    submitBtn.innerText = originalBtnText;
                    submitBtn.disabled = false;
                });
            });
        });
    }
});
