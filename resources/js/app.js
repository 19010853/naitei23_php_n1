import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Flash Message Handler for Customer
(function() {
    function initFlashMessages() {
        const messages = document.querySelectorAll('[data-flash-message]');
        if (!messages.length) {
            return;
        }

        messages.forEach((message) => {
            // Auto hide after 1 second
            const hideTimeout = setTimeout(() => {
                message.classList.add('is-hiding');

                // Remove from DOM after fade-out transition
                const removeTimeout = setTimeout(() => {
                    if (message.parentElement) {
                        message.parentElement.removeChild(message);
                    }
                }, 450);

                // In case component is destroyed early
                message.addEventListener('transitionend', () => {
                    clearTimeout(removeTimeout);
                    if (message.parentElement) {
                        message.parentElement.removeChild(message);
                    }
                }, { once: true });
            }, 1000);

            // Allow user to dismiss earlier by click
            message.addEventListener('click', () => {
                clearTimeout(hideTimeout);
                message.classList.add('is-hiding');
            });
        });
    }

    /**
     * Show a flash message programmatically (for AJAX responses)
     * @param {string} message - The message to display
     */
    window.showFlashMessage = function(message) {
        // Get or create flash message container
        let container = document.querySelector('.flash-message-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'flash-message-container';
            document.body.appendChild(container);
        }

        // Create flash message element
        const flashMessage = document.createElement('div');
        flashMessage.className = 'flash-message flash-message--success';
        flashMessage.setAttribute('data-flash-message', '');
        flashMessage.innerHTML = `
            <div class="flash-message__icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="flash-message__content">
                ${message}
            </div>
        `;

        // Add to container
        container.appendChild(flashMessage);

        // Initialize the flash message behavior
        initFlashMessages();
    };

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', initFlashMessages);
})();