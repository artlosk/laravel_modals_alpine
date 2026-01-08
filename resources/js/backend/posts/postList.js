document.addEventListener('alpine:init', () => {
    Alpine.data('postList', () => ({
        init() {
            // Any specific post list initialization
        }
    }));
});
