document.addEventListener('alpine:init', () => {
    Alpine.data('userList', () => ({
        init() {
            // User list specific init
        }
    }));
});
