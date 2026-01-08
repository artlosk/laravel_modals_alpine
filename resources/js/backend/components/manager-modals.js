import axios from 'axios';

document.addEventListener('alpine:init', () => {
    Alpine.data('managerModals', () => ({
        showModal: false,
        modalTitle: '',
        modalContent: '',
        isLoading: false,
        url: '',
        toasts: [],

        init() {
            window.addEventListener('open-modal', (event) => {
                this.openModal(event.detail.url, event.detail.title);
            });
            window.addEventListener('show-toast', (event) => {
                this.showToast(event.detail.type, event.detail.message);
            });
            window.addEventListener('submit-modal-form', (event) => {
                this.handleFormSubmit(event.detail.form);
            });
            window.addEventListener('close-modal', () => {
                this.closeModal();
            });
        },

        async openModal(url, title = 'Modal') {
            this.url = url;
            this.modalTitle = title;
            this.showModal = true;
            this.isLoading = true;
            this.modalContent = '';

            // Lock body scroll
            document.body.style.overflow = 'hidden';

            try {
                const response = await axios.get(url);
                this.modalContent = response.data.html || response.data;
                this.isLoading = false; // Show content before widgets init

                this.$nextTick(() => {
                    if (typeof window.initMediaGalleryWidgets === 'function') {
                        window.initMediaGalleryWidgets();
                    }
                });

            } catch (error) {
                console.error('Error loading modal content:', error);
                this.showToast('error', 'Ошибка загрузки контента');
                this.closeModal();
            }
        },

        closeModal() {
            this.showModal = false;
            this.modalContent = '';
            this.url = '';
            // Unlock body scroll
            document.body.style.overflow = '';
        },

        async submitForm(event) {
            event.preventDefault();
            await this.handleFormSubmit(event.target);
        },

        async handleFormSubmit(form) {
            const formData = new FormData(form);
            const action = form.action;
            const method = form.method;

            this.isLoading = true;

            // Clear previous errors
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            try {
                const response = await axios({
                    method: method,
                    url: action,
                    data: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.data.success) {
                    // Prioritize redirect
                    if (response.data.redirect) {
                        window.location.href = response.data.redirect;
                        return; // Stop further execution in this block
                    }

                    // If no redirect, show toast if message is present
                    if (response.data.message) {
                        this.showToast('success', response.data.message);
                    }

                    this.closeModal(); // Close modal after handling redirect or showing toast

                    // Fallback reload if no redirect was handled
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);

                } else {
                    this.showToast('error', response.data.message || 'Ошибка выполнения операции');
                }

            } catch (error) {
                if (error.response && error.response.status === 422) {
                    // Validation errors
                    const errors = error.response.data.errors;
                    Object.keys(errors).forEach(field => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const errorDiv = document.createElement('div');
                            errorDiv.classList.add('invalid-feedback');
                            errorDiv.innerText = errors[field][0];
                            input.parentNode.appendChild(errorDiv);
                        }
                    });
                    this.showToast('error', 'Пожалуйста, проверьте форму на наличие ошибок.');
                } else {
                    console.error('Submission error:', error);
                    this.showToast('error', 'Произошла непредвиденная ошибка.');
                }
            } finally {
                this.isLoading = false;
            }
        },

        showToast(type, message) {
            const id = Date.now();
            this.toasts.push({ id, type, message });
            setTimeout(() => {
                this.removeToast(id);
            }, 3000);
        },

        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }));
});
