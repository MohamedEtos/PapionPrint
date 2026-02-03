/**
 * Bootstrap 5 Compatibility Script for Bootstrap 4 Markup
 * Handles `data-dismiss="modal"` which was removed in Bootstrap 5 in favor of `data-bs-dismiss="modal"`.
 */

import { Modal } from 'bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    // Delegate click event for better performance and dynamic content support
    document.body.addEventListener('click', (event) => {
        const dismissBtn = event.target.closest('[data-dismiss="modal"]');

        if (dismissBtn) {
            event.preventDefault();

            // Find the modal element
            // 1. Try to find the closest modal ancestor
            let modalEl = dismissBtn.closest('.modal');

            // 2. If not inside a modal (unlikely for a dismiss button, but possible if it targets via ID), 
            //    we could check for data-target, but standard BS4 dismiss is usually inside.

            if (modalEl) {
                // Get existing instance or create new one
                const modal = Modal.getInstance(modalEl) || new Modal(modalEl);
                modal.hide();
            }
        }
    });
});
