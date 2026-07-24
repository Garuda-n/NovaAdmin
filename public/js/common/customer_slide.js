/**
 * NovaAdmin - Global Reusable Customer Creation Slide JS API & Alpine Component
 */

// Global JS Helper Function
window.openCustomerCreateSlide = function (callback) {
    if (typeof callback === 'function') {
        window._customerSlideCallback = callback;
    } else {
        window._customerSlideCallback = null;
    }
    window.dispatchEvent(new CustomEvent('open-customer-slide', { detail: { callback: callback } }));
};

// Alpine JS Component for Customer Slide
function customerSlideComponent() {
    return {
        isOpen: false,
        submitting: false,
        callback: null,

        openSlide(detail) {
            this.clearErrors();
            this.isOpen = true;
            this.callback = (detail && typeof detail.callback === 'function') ? detail.callback : window._customerSlideCallback;

            // Focus on first input field after slide transitions in
            this.$nextTick(() => {
                const firstInput = document.querySelector('#global-customer-slide-form input[name="customer_name"]');
                if (firstInput) {
                    firstInput.focus();
                }
            });
        },

        closeSlide() {
            this.isOpen = false;
            this.clearErrors();
        },

        clearErrors() {
            const form = document.getElementById('global-customer-slide-form');
            if (!form) return;
            form.querySelectorAll('[data-error-field]').forEach(el => {
                el.innerText = '';
                el.classList.add('hidden');
            });
            form.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500', 'dark:border-red-500');
            });
        },

        submitForm(formEl) {
            this.clearErrors();
            this.submitting = true;

            const formData = new FormData(formEl);

            fetch('/customers', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    if (res.status === 422 && data.errors) {
                        // Display inline validation errors under fields
                        Object.keys(data.errors).forEach(field => {
                            const errEl = formEl.querySelector(`[data-error-field="${field}"]`);
                            if (errEl) {
                                errEl.innerText = data.errors[field][0];
                                errEl.classList.remove('hidden');
                            }
                            const inputEl = formEl.querySelector(`[name="${field}"]`);
                            if (inputEl) {
                                inputEl.classList.add('border-red-500', 'dark:border-red-500');
                            }
                        });
                        if (window.showToast) {
                            window.showToast(data.message || 'Please fix the errors in the form.', 'error');
                        }
                    } else {
                        if (window.showToast) {
                            window.showToast(data.message || 'Failed to save customer.', 'error');
                        }
                    }
                } else {
                    // Success!
                    if (window.showToast) {
                        window.showToast(data.message || 'Customer created successfully.', 'success');
                    }

                    const newCustomer = data.customer || data.data;

                    // Reset form fields
                    formEl.reset();
                    this.closeSlide();

                    // Execute registered callback if present
                    const targetCallback = this.callback || window._customerSlideCallback;
                    if (typeof targetCallback === 'function') {
                        targetCallback(newCustomer);
                    }

                    window._customerSlideCallback = null;
                    this.callback = null;
                }
            })
            .catch(err => {
                console.error('Error submitting customer creation form:', err);
                if (window.showToast) {
                    window.showToast('An unexpected error occurred.', 'error');
                }
            })
            .finally(() => {
                this.submitting = false;
            });
        }
    };
}
