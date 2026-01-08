import '/resources/js/bootstrap.js';
import $ from 'jquery';
import toastr from 'toastr';
import * as bootstrap from 'bootstrap';
import * as FilePond from 'filepond/dist/filepond.esm.js';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.esm.js';
import FilePondPluginFileValidateType
    from 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.esm.js';
import FilePondPluginFileValidateSize
    from 'filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.esm.js';
import Sortable from 'sortablejs';
import 'toastr/build/toastr.css';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';
import {
    initializeFilePond,
    loadMediaLibraryModal,
    attachSelectedMedia,
    updateSelectedMediaPreview,
    initMainSortable,
    initModalSortable
} from '/resources/js/mediaUtils.js';

window.$ = window.jQuery = $;
window.bootstrap = bootstrap;
window.FilePond = FilePond;
window.FilePondPluginImagePreview = FilePondPluginImagePreview;
window.FilePondPluginFileValidateType = FilePondPluginFileValidateType;
window.FilePondPluginFileValidateSize = FilePondPluginFileValidateSize;
window.Sortable = Sortable;

toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "5000"
};

// Export the initialization function so it can be called from other scripts (e.g. modals)
export function initMediaGalleryWidgets(root = document) {
    root.querySelectorAll('.media-gallery-widget').forEach((container, index) => {
        const name = container.dataset.name;
        if (!name) {
            return;
        }

        const modalSelector = `#${name}-mediaLibraryModal`;
        const contentSelector = `#${name}-mediaItemsList`;
        const attachButtonSelector = `#${name}-attachSelectedMedia`;
        const previewSelector = `#${name}-selectedMediaPreview`;
        const selectedMediaIdsSelector = `#${name}-selectedMediaIds`;

        const fileInput = container.querySelector('input[type="file"].filepond');
        if (!fileInput) {
            return;
        }

        const previewContainer = document.querySelector(previewSelector);
        if (!previewContainer) {
            return;
        }

        // Search for modal in the whole document as it might be moved to body end
        // BUT for initialization inside a modal container, it should be there.
        // Let's use document for modal search if not found in root, or just document.
        // Actually, if using `root`, we should look inside `root` first?
        // Modals usually get moved, but here the HTML is fresh.
        // Let's stick to `document.querySelector` for modalSelector as it is ID based.
        const modal = document.querySelector(modalSelector);
        if (!modal) {
            return;
        }

        // Initialize modal explicitly to ensure Bootstrap tracks it
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modal);
        } else if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modal);
        }

        initializeFilePond(fileInput);

        loadMediaLibraryModal(modalSelector, contentSelector);

        attachSelectedMedia(attachButtonSelector, modalSelector);

        initMainSortable(previewSelector);

        initModalSortable(contentSelector);

        const selectedMediaIds = document.querySelector(selectedMediaIdsSelector)?.value;
        if (selectedMediaIds) {
            const initialMediaIds = selectedMediaIds.split(',').map(id => parseInt(id)).filter(id => !isNaN(id));
            if (initialMediaIds.length > 0) {
                updateSelectedMediaPreview(initialMediaIds, previewSelector);
            }
        }
    });
}

// Make it available globally for modals
window.initMediaGalleryWidgets = initMediaGalleryWidgets;

document.addEventListener('DOMContentLoaded', () => {
    initMediaGalleryWidgets();
});
