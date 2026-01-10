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

            document.body.style.overflow = 'hidden';

            if (!window.axios) {
                console.error('Axios is not available');
                this.showToast('error', 'Ошибка: Axios не загружен');
                this.closeModal();
                return;
            }

            try {

                const response = await window.axios.get(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.modalContent = response.data.html || response.data;
                this.isLoading = false;

                this.$nextTick(() => {
                    if (typeof window.initMediaGalleryWidget === 'function') {
                        const contentContainer = this.$refs.modalContent;
                        if (contentContainer) {
                            const galleryWidgets = contentContainer.querySelectorAll('.media-gallery-widget');
                            galleryWidgets.forEach((container) => {
                                window.initMediaGalleryWidget(container);
                            });
                        }
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

            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            if (!window.axios) {
                console.error('Axios is not available');
                this.showToast('error', 'Ошибка: Axios не загружен');
                this.isLoading = false;
                return;
            }

            try {
                const response = await window.axios({
                    method: method,
                    url: action,
                    data: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.data.success) {
                    if (response.data.redirect) {
                        window.location.href = response.data.redirect;
                        return;
                    }

                    if (response.data.message) {
                        this.showToast('success', response.data.message);
                    }

                    this.closeModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);

                } else {
                    this.showToast('error', response.data.message || 'Ошибка выполнения операции');
                }

            } catch (error) {
                if (error.response && error.response.status === 422) {
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
