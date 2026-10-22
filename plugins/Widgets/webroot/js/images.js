/*
 * Image viewer widget - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {createImageAnnotator, mountAnnotoriousTools} from './annotorious/annotorious.bundle.js';

import {BaseWidget} from '/js/base.js';
import {Accordion, ResizableSidebar} from './layout.js';
import Utils from "/js/utils.js";

/**
 * Image viewer widget — EpiWidJs framework
 *
 * Manages the overlay, image list, metadata, and coordinates
 * the ImageNavigator (zoom/pan/rotate) and ImageAnnotator (Annotorious).
 *
 * Each img element needs the following attributes:
 *
 * - title: Optional, a title for the image
 * - alt: A description of the image content
 * - data-display: An URL to get the full image
 * - data-copyright: The copyright holder
 * - data-meta: Metadata in JSON format
 *
 * Each img element should be wrapped in an item element. This can be a `span`, `div` or `a` element.
 * The item element needs the following data attributes:
 *
 * - data-anno-type: (optional) If set, annotations are enabled for this image.
 *                     The value should contain the itemtype name used to store annotations.
 *                     Updated annotations are signaled by emitting the event 'epi:patch:item',
 *                     with the new annotation data in the event detail.
 *
 * Additionally, to find the full image list, all item elements need a common ancestor.
 *
 * Set config.imageSelector to a CSS selector that finds all those items.
 * Set config.containerSelector to a CSS selector that finds the container element.
 *
 * You can use the static attachImages() method to init the widget with the appropriate config.
 * Alternatively, the widget will be attached automatically if you add the following classes to the container:
 *
 * - widget-image-viewer
 * - widget-image-viewer-edit (optional, if image annotations should be editable)
 *
 */
export class ImagesWidget extends BaseWidget {

    constructor(element, name, parent) {
        super(element, name, parent);

        this.config = {
            closeOnOpen: false,
            recreateContainer: true,
            buttons: {},
            containerSelector: '.doc-imagelist',
            imageSelector: '.doc-image',
            thumbs: true,
            counter: true,
            inlineArrows: false,
            edit: element.classList.contains('widget-image-viewer-edit')
        };

        // DOM references
        this.overlay = undefined;
        this.overlay_header = undefined;
        this.image_container = undefined;
        this.image_viewport = undefined;
        this.image_image = undefined;
        this.loader = undefined;

        // State
        this.sourceImageList = undefined;
        this.items = undefined;
        this.currentPosition = undefined;
        this.options = {title: "Image viewer"};
        this.annoType = undefined;
        this.activeImage = undefined;

        // Sidebars
        this.sideBarRight = undefined;
        this.sideBarLeft = undefined;

        // Sub-components
        this.navigator = new ImageNavigator();
        this.annotator = new ImageAnnotator();
        this.annotator.editable = this.config.edit;

        // Attach event handler
        this.listenEvent(this.widgetElement, 'click', event => this.clickOnImage(event));

        // Load image from hash fragment
        this.selectImageByHash();
    }

    static attachImages(selContainer, selImages, config = {}) {
        const elmContainer = document.querySelector(selContainer);
        if (!elmContainer) return;

        const item = elmContainer.querySelector(selImages);
        if (!item) return;

        const widget = new ImagesWidget(elmContainer);
        widget.config.containerSelector = selContainer;
        widget.config.imageSelector = selImages;
        widget.config = Object.assign(widget.config, config);
        widget.config.thumbs = Utils.isTrue(item.dataset.epiImageThumbs, true);
        widget.config.counter = Utils.isTrue(item.dataset.epiImageCounter, true);
    }

    /**
     * Create the markup
     *
     * @return {HTMLElement}
     */
    createOverlay() {

        const leftsidebar = 'sidebar-init-collapsed'; // sidebar-init-expanded;

        const template = '<div class="overlay hidden">\n' +
            '    <div class="overlay-header">\n' +
            '        <div class="overlay-header-buttons overlay-header-buttons-left">' +
            '          <button class="accordion-toggle" data-toggle-accordion="overlay-sidebar-left"></button>' +
            '</div>\n' +
            '        <div class="overlay-header-title"></div>\n' +
            '        <div class="overlay-header-buttons overlay-header-buttons-right"></div>\n' +
            '    </div>\n' +
            '    <div class="overlay-content widget-accordion">\n' +
            '        <nav class="sidebar sidebar-left sidebar-size-2 accordion-item ' + leftsidebar + '" data-accordion-item="overlay-sidebar-left" data-ui-key="images-sidebar-left">\n' +
            '            <div class="sidebar-content"></div>\n' +
            '        </nav>\n' +
            '\n' +
            '        <div class="image-container accordion-item accordion-main" data-accordion-item="overlay-main">\n' +
            '            <div class="overlay-image-prev fontawesome">\uf053</div>\n' +
            '            <div class="image-viewport" style="transform: rotate(0deg);">\n' +
            '                <img alt="" src="" class="">\n' +
            '                <div class="loader"></div>\n' +
            '            </div>\n' +
            '            <div class="overlay-image-next fontawesome">\uf054</div>\n' +
            '        </div>\n' +
            '        <div class="metadata-container sidebar sidebar-right accordion-item" data-accordion-item="overlay-sidebar-right" data-ui-key="images-sidebar-right">\n' +
            '            <div class="sidebar-content"></div>\n' +
            '        </div>\n' +
            '    </div>\n' +
            '    <footer class="overlay-footer">\n' +
            '        <div class="overlay-footer-left">\n' +
            '            <nav></nav>\n' +
            '        </div>\n' +
            '        <div class="overlay-footer-title"></div>\n' +
            '        <div class="overlay-footer-buttons overlay-footer-buttons-right"></div>\n' +
            '    </footer>\n' +
            '</div>';

        const overlay = Utils.spawnFromString(template);
        document.querySelector('body').append(overlay);
        App.initWidgets(overlay);

        this.listenEvent(overlay, 'click', event => this.clickOnImage(event));
        this.listenEvent(overlay.querySelector('.overlay-image-prev'), 'click', () => this.previousImage());
        this.listenEvent(overlay.querySelector('.overlay-image-next'), 'click', () => this.nextImage());
        Utils.listenEvent(document, 'keyup', event => this.onKeyUp(event));

        return overlay;
    }


    /**
     * Get the container for image annotations in the metadata sidebar.
     *
     * @return {*}
     */
    getMetadataContainer(purpose) {
        let elm = this.overlay.querySelector('.metadata-container .sidebar-content .metadata-content-' + purpose);
        if (!elm) {
            const container = this.overlay.querySelector('.metadata-container .sidebar-content');
            if (!container) {
                return;
            }
            elm = container.appendChild(Utils.spawnFromString('<div class="metadata-content-' + purpose + '"></div>'));
        }
        return elm;
    }

    /**
     * Copy images from a page into the image viewer's sidebar, if it is still empty
     *
     * @param imageList
     */
    loadItems(imageList) {
        if (this.items) return;

        const sidebar = this.overlay.querySelector('.sidebar-left .sidebar-content');
        if (sidebar && imageList) {

            // Either clone the source list directly...
            if (!this.config.recreateContainer) {
                imageList = imageList.cloneNode(true);
            }
            // ... or create a new container and clone each item into the container
            else {
                const items = imageList.querySelectorAll(this.config.imageSelector);
                imageList = document.createElement('div');
                imageList.classList.add('doc-imagelist');
                imageList.classList.add('doc-imagelist-medium');
                items.forEach(elm => {
                    const newElm = elm.cloneNode(true);
                    newElm.classList.remove('hidden');
                    imageList.append(newElm);
                });
            }
            sidebar.replaceChildren(imageList);
        }

        this.items = sidebar.querySelectorAll(this.config.imageSelector);

        // Show / hide navigation elements
        this.updateNavigation();
    }

    /**
     * Called on image click, updates the loaded image
     *
     * @param event Click
     */
    clickOnImage(event) {
        if (!event.target.closest('a') || event.ctrlKey || event.metaKey) {
            return;
        }

        const selected = event.target.closest(this.config.imageSelector);
        if (selected) {
            event.preventDefault();
            this.selectImageByElement(selected);
        }
    }

    /**
     * Keyboard interaction
     *
     * Handles prev/ next image by arrow keys,
     * cycles annotation mode by "a",
     * deletes annotation by backspace or delete key.
     *
     * @param {Event} event
     */
    onKeyUp(event) {
        // Don't handle keys if typing in a form field
        if (event.target.closest('textarea, input, select, [contenteditable]')) return;

        // "A" key: cycle annotation mode ──
        if (event.key === 'a' || event.key === 'A') {
            if (!this.isActive()) return;
            this.cycleAnnotationMode();
            event.preventDefault();
            return;
        }

        // Delete / backspace / ESC in annotation mode
        if (this.annotator.isActive()) {

            // TODO: Not a good idea, because we need this key for deleting points in a polygon.
            // if (event.key === 'Delete' || event.key === 'Backspace') {
            //     if (this.annotator.deleteSelected()) {
            //         event.preventDefault();
            //     }
            //     return;
            // }
            if (event.key === 'Escape') {
                this.stopAnnotationMode();
                event.preventDefault();
                return;
            }
        }

        else if (this.isActive()) {
            if (event.key === 'Escape') {
                this.closeOverlay();
                event.preventDefault();
                return;
            }
        }

        // Arrow keys for image navigation
        if (!this.isActive() || !this.items || (this.items.length < 2)) return;

        if (event.key === 'ArrowLeft') {
            this.previousImage();
            event.preventDefault();
        } else if (event.key === 'ArrowRight') {
            this.nextImage();
            event.preventDefault();
        }
    }

    /**
     * Show image
     *
     * @param {Element} selected An image item, either from the article or from the overlay
     */
    selectImageByElement(selected) {
        if (this.isActive() && this.items) {
            this.currentPosition = Array.from(this.items).findIndex(elm => selected === elm);
        } else {

            // Prepare overlay
            this.showOverlay();

            // Construct image list
            this.sourceImageList = selected.closest(this.config.containerSelector);
            if (this.sourceImageList) {
                this.loadItems(this.sourceImageList);
                const siblings = this.sourceImageList.querySelectorAll(this.config.imageSelector);
                this.currentPosition = Array.from(siblings).findIndex(elm => selected === elm);
            } else {
                this.currentPosition = 0;
                this.items = [selected];
            }
            this.updateNavigation();
        }

        // Load image
        this.showCurrentImage();
    }

    /**
     * Extract the item ID from the hash fragment and
     * open the image if it matches an image.
     *
     */
    selectImageByHash() {
        const selectedId = location.hash.match(/^#items-(\d+)$/);
        const selectedItem =
            selectedId
                ? document.querySelector('.doc-image[data-row-id="' + selectedId[1] + '"]')
                : null;

        if (selectedItem) {
            this.selectImageByElement(selectedItem);
        }
    }

    /**
     * Get the selected image
     *
     * @returns {HTMLDivElement|null} Image container.
     */
    getCurrentImage() {
        if (!this.items) {
            return;
        }

        if ((this.currentPosition > -1) && (this.currentPosition < this.items.length)) {
            return this.items[this.currentPosition];
        }
        return null;
    }

    previousImage() {
        this.currentPosition -= 1;
        const maxPosition = this.items ? this.items.length : 0;
        this.currentPosition = Utils.mod(this.currentPosition, maxPosition);
        this.showCurrentImage();
    }

    nextImage() {
        this.currentPosition += 1;
        const maxPosition = this.items ? this.items.length : 0;
        this.currentPosition = Utils.mod(this.currentPosition, maxPosition);
        this.showCurrentImage();
    }

    /**
     * Show the selected image.
     */
    showCurrentImage() {
        const selected = this.getCurrentImage();

        // Notify annotator before switching
        this.stopAnnotationMode();

        // Highlight in sidebar
        this.items.forEach(element => element.classList.remove('selected'));
        selected.classList.add('selected');
        Utils.scrollIntoViewIfNeeded(selected);

        // ── Prepare image element ──
        this.image_image.classList.add('hidden');
        this.image_image.classList.remove('fitted');
        this.loader.classList.remove('hidden');

        // Reset inline styles so CSS height:0 takes effect
        // (prevents flash of old image at old size)
        this.image_image.style.height = '0';
        this.image_image.style.width = '0';

        // Pre-fetch, then display
        const downloadingImage = new Image();
        downloadingImage.onload = () => {
            downloadingImage.onload = null;

            // Set src (data is already cached, no network wait)
            this.image_image.setAttribute('src', downloadingImage.src);

            // Fit BEFORE showing — no jump
            this.navigator.fitSize();

            // Now reveal
            this.loader.classList.add('hidden');
            this.image_image.classList.remove('hidden');
        };

        const img = selected.querySelector('img');
        downloadingImage.src = img ? img.dataset.display : '';

        // Show metadata
        this.showMetadata(selected);

        // Update current image
        this.loadAnnotations(selected);
        this.activeImage = selected;
    }

    /**
     * Called when the <img> element finishes loading a new src.
     */
    onImageLoad() {
        // Re-fit if dimensions changed (e.g. browser decoded async)
        if (!this.image_image.classList.contains('fitted')) {
            this.navigator.fitSize();
            this.image_image.classList.remove('hidden');
        }

        // Re-init annotation layer if we're in annotation mode
        // if (this.active && !this.annotator) {
        //     requestAnimationFrame(() => this._createAnnotorious());
        // }
    }

    /**
     * Load annotations for the current image
     *
     * @param {HTMLElement} selected
     */
    loadAnnotations(selected) {

        this.annoType = selected.dataset.annoType;

        if (selected && this.annoType) {
            let data = {};
            const annoElm = selected.querySelector('.doc-image-annotation');
            if (annoElm) {
                try {
                    data = JSON.parse(annoElm.textContent);
                }
                catch (e) {
                    console.error('Invalid annotation JSON:', e);
                }
            }
            this.annotator.loadAnnotations(data);
            if (this.config.edit && Utils.isWideScreen()) {
                this.sideBarRight.showSidebar();
            }
        }

        this.overlay.classList.toggle('annotation-allowed', this.annoType !== undefined);
    }

    /**
     * Save annotations for the current image
     *
     * @param {Object} data
     * @fires epi:patch:item
     */
    saveAnnotations(data) {
        if (this.activeImage) {
            data = JSON.stringify(data);

            const annoContainer = this.activeImage.querySelector('.doc-image-annotations');
            if (!annoContainer) {
                return;
            }
            let annoElm = annoContainer.querySelector('.doc-image-annotation');
            if (!annoElm) {
                annoElm = document.createElement('script');
                annoElm.type = 'application/json';
                annoElm.classList.add('doc-image-annotation');
                annoContainer.appendChild(annoElm);
            }

            if (annoElm) {
                annoElm.textContent = data;

                const fileName = this.activeImage.querySelector('[data-row-field="file"] .doc-field-content')?.textContent;
                const eventData = {
                    handler: {itemtype: this.annoType},
                    selector: {file: fileName},
                    content: {content: data}
                };
                this.emitEvent('epi:patch:item', eventData);
            }
        }
    }

    /**
     * Start annotation mode.
     */
    startAnnotationMode() {
        this.navigator.disable();
        this.annotator.enable();
        if (this.config.edit) {
            this.overlay.classList.add('annotation-mode');
        } else {
            this.overlay.classList.add('annotation-show');
        }
        this.updateToolButtons();
    }

    stopAnnotationMode() {
        this.annotator.disable();
        this.navigator.enable();
        this.overlay.classList.remove('annotation-mode');
        this.overlay.classList.remove('annotation-show');
        this.updateToolButtons();
    }

    /**
     * Switch between navigation mode and annotation mode.
     */
    toggleAnnotationMode() {
        if (this.annotator.isActive()) {
            this.stopAnnotationMode();
        } else {
            this.startAnnotationMode();
        }
    }

    /**
     * Press "a" to enter annotation mode (rect), cycle tools, then exit
     *
     * Cycle: off → rectangle → polygon → off
     */
    cycleAnnotationMode() {
        if (this.annoType === undefined) {
            return;
        }

        if (!this.annotator.isActive()) {
          this.startAnnotationMode();
        } else {
            this.annotator.nextTool();
            this.updateToolButtons();
        }
    }

    /**
     * Highlight the active tool button, dim the others.
     */
    updateToolButtons() {
        if (!this.overlay) return;

        const currentTool = this.annotator.getCurrentTool();

        this.overlay.querySelectorAll('[data-tool]').forEach(btn => {
            btn.classList.toggle('tool-active', btn.dataset.tool === currentTool);
        });

        // Toggle the main annotate button
        const annotateBtn = this.overlay.querySelector('.btn-annotate');
        if (annotateBtn) {
            annotateBtn.classList.toggle('tool-active', this.annotator.isActive());
        }

        // Label visibility
        const labelBtn = this.overlay.querySelector('.btn-toggle-labels');
        if (labelBtn) {
            labelBtn.classList.toggle('tool-active', this.annotator.showLabels);
        }
    }

    /**
     * Extract metadata from the selected image item
     *
     * First tries to find a .doc-image-metadata element and use its content.
     * If not found, falls back to using the img attributes and data-metadata.
     *
     * The returned object can contain:
     * - title: A string to be shown as the image title
     * - footer: A string to be shown in the footer (e.g. copyright)
     * - element: An HTMLElement to be shown in the metadata sidebar
     * - article: An HTMLElement with article data to be shown in the metadata sidebar
     *
     * @param {HTMLElement} selected
     * @return {{}}
     */
    getMetadata(selected) {
        let data = {};

        // Use metadata element
        const metadataElm = selected.querySelector('.doc-image-metadata');
        if (metadataElm) {
            data['title'] = metadataElm.querySelector('[data-row-field="file"]').textContent;
            data['element'] = metadataElm.cloneNode(true);
            data['article'] = this.createMetatableForArticle(selected);
            return data;
        }

        // Use image attributes
        const img = selected.querySelector('img');
        if (!img) {
            return data;
        }

        // Title and footer
        let header = '';
        header += img.getAttribute('alt') || '';
        const title = img.getAttribute('title');
        if (title) {
            header += '. ' + title;
        }
        data['title'] = header;

        if (img.dataset.copyright) {
            data['footer'] = img.dataset.copyright || '';
        }

        // Image metadata from the data attribute
        if (img.dataset.metadata) {
            try {
                const metadata = JSON.parse(img.dataset.metadata);
                data['element'] = this.createMetatableForImage(metadata);
            } catch (e) {
                console.error('Invalid JSON:', e);
            }
        }

        return data;
    }

    /**
     * Create a table for image metadata
     *
     * @param {Object} data An object with keys/value pairs to be displayed in a table
     * @returns {HTMLElement} The generated table
     */
    createMetatableForImage(data) {

        const editor = new MetadataEditor(
            {},
            {
                inputs: false,
                autoFields: true,
                hidden: false,
                editable: false,
                tableClass: 'doc-image-metadata'
            }
        );

        editor.setValues(data);
        return editor.getElement();
    }

    /**
     * Create a table with article information
     *
     * @param {HTMLElement} selected The selected image
     * @return {HTMLElement} The generated table
     */
    createMetatableForArticle(selected) {
        const editor = new MetadataEditor(
            {},
            {
                inputs: false,
                autoFields: true,
                hidden: false,
                editable: false,
                tableClass: 'doc-image-metadata'
            }
        );

        let data = {};
        if (this.sourceImageList) {
            /** @type {DocumentWidget} */
            const docWidget = this.getWidget(this.sourceImageList, 'document');
            if (docWidget) {
                // TODO: get keys from config, use PEL keys
                const extractKeys = {
                    'Transcriptions' : '[data-row-table="items"][data-row-type="dio-inscriptions-raw"] [data-row-field="content"] .doc-field-content'
                }

                for (const [label, key]  of Object.entries(extractKeys)) {
                    const valuesList = docWidget.extractValues(key);
                    data[label] = valuesList.join("<br>") || "";

                }
            }
        }

        editor.setValues(data);
        return editor.getElement();
    }


    /**
     * Show metadata in the overlay
     *
     * @param {HTMLElement} selected The selected image
     */
    showMetadata(selected) {
        const metadata = this.getMetadata(selected);

        // Change title
        const titleElement = this.overlay.querySelector('.overlay-header-title');
        let titleText = '';
        if (titleElement && metadata['title']) {
            titleText = metadata['title'] || '';
        }
        if (this.config.counter && this.items && (this.items.length > 1)) {
            const counterText = `[${this.currentPosition + 1}/${this.items.length}] `;
            titleText = counterText + titleText;
        }
        titleElement.innerText = titleText;

        const footerElement = this.overlay.querySelector('.overlay-footer-title');
        if (footerElement && metadata['footer']) {
            footerElement.innerText = metadata['footer'] || '';
        }

        // Change detail view for image metadata
        let hasMetadata = false;
        if (this.metadataImageContainer && metadata['element']) {
            hasMetadata = true;
            this.metadataImageContainer.replaceChildren(metadata['element']);
        }
        if (this.metadataArticleContainer  && metadata['article']) {
            hasMetadata = true;
            this.metadataArticleContainer.replaceChildren(metadata['article']);
        }

        if (!hasMetadata) {
            this.sideBarRight.hideSidebar(true);
        }

        // Change open button
        const openElement = this.overlay.querySelector('.btn-open');
        if (openElement) {
            Utils.toggle(openElement, selected.dataset.itemUrl || false);
        }
    }

    /**
     * Navigation UI (thumbs, arrows)
     */
    updateNavigation() {
        if (!this.items) {
            return;
        }

        const showThumbs = Utils.isWideScreen() && ((this.items.length > 1) && this.config.thumbs);
        const showInlineArrows = (this.items.length > 1) && (this.config.inlineArrows || !Utils.isWideScreen());
        const showTitleArrows = (this.items.length > 1) && !showThumbs && !showInlineArrows;

        if (!showThumbs) {
            this.sideBarLeft.hideSidebar(true);
        } else {
            this.sideBarLeft.showSidebar();
        }

        this.overlay.classList.toggle('overlay-inline-arrows', showInlineArrows);
        this.overlay.classList.toggle('overlay-title-arrows', showTitleArrows);
    }

    /**
     * Close overlay window.
     */
    closeOverlay() {
        // Tear down annotation mode
        this.stopAnnotationMode();

        if (this.overlay) {
            this.overlay.classList.add('hidden');
            document.body.classList.remove('no-scroll');
        }
    }

    isActive() {
        return this.overlay && !this.overlay.classList.contains('hidden');
    }

    /**
     * Create and show overlay
     */
    showOverlay() {
        if (this.overlay) {
            this.overlay.classList.remove('hidden');
            document.body.classList.add('no-scroll');
            return;
        }

        // Buttons
        const buttons = this.getDialogButtons();

        // Build markup
        this.overlay = this.createOverlay();
        this.overlay.classList.remove('hidden');
        document.body.classList.add('no-scroll');

        // Header bar
        this.overlay_header = this.overlay.querySelector('.overlay-header');
        for (const [key, value] of Object.entries(buttons)) {
            if ((value.position === 'topleft') || (value.position === 'topright')) {
                const button = this.createButton(value);
                if (value.position === 'topleft') {
                    this.overlay_header.querySelector('.overlay-header-buttons-left').append(button);
                } else if (value.position === 'topright') {
                    this.overlay_header.querySelector('.overlay-header-buttons-right').append(button);
                }
            }
        }

        // Content section
        this.image_container = this.overlay.querySelector('.image-container');
        this.image_viewport = this.overlay.querySelector('.image-viewport');
        this.image_image = this.overlay.querySelector('img');
        this.loader = this.overlay.querySelector('.loader');
        this.metadataImageContainer = this.getMetadataContainer('image');
        this.metadataAnnoContainer = this.getMetadataContainer('annos');
        this.metadataArticleContainer = this.getMetadataContainer('article');

        // Sidebars
        const rightSidebarElement = this.overlay.querySelector('.metadata-container');
        this.sideBarRight = new ResizableSidebar(rightSidebarElement, 'right', 10, true);

        const leftSidebarElement = this.overlay.querySelector('.sidebar-left');
        this.sideBarLeft = new ResizableSidebar(leftSidebarElement, 'left', 10, this.config.thumbs);

        // Footer buttons
        for (const [key, value] of Object.entries(buttons)) {
            if (value.position === 'bottom') {
                const button = this.createButton(value);
                this.overlay.querySelector('.overlay-footer-left nav').append(button);
            }
            if (value.position === 'bottomright') {
                const button = this.createButton(value);
                this.overlay.querySelector('.overlay-footer-buttons-right').append(button);
            }
        }

        // Move overlay to body
        document.querySelector('body').append(this.overlay);

        // Attach sub-components
        this.navigator.attach(this.image_container, this.image_viewport, this.image_image);
        this.annotator.attach(
            this.image_viewport, this.image_image, this.metadataAnnoContainer,
            (data) => this.saveAnnotations(data)
        );

        // Image load → fit + re-init annotation layer
        this.image_image.addEventListener('load', () => this.onImageLoad());

        // Sidebar hide → refit
        this.listenEvent(this.overlay, 'epi:hide:sidebar', (event) => this.onHideSidebar(event));
    }

    onHideSidebar(event) {
        this.navigator.fitSize();
        this.annotator.refresh();
    }

    /**
     * Create buttons
     *
     * @param {Object} value
     * @return {HTMLButtonElement}
     */
    createButton(value) {
        const button = document.createElement('button');
        button.className = value.class;
        button.textContent = value.content || '';
        button.ariaLabel = value.ariaLabel || '';
        button.title = value.title || '';

        // Data attributes
        if (value.data) {
            for (const [key, val] of Object.entries(value.data)) {
                button.dataset[key] = val;
            }
        }

        if (value.symbol) {
            const icon = document.createElement('span');
            icon.className = 'icon fontawesome';
            icon.textContent = value.symbol;
            button.prepend(icon);
        }

        button.addEventListener('click', value.click);
        return button;
    }

    getDialogButtons() {
        const defaultConfig = {
            // 'Manage file': function () {
            //     window.open(self.url_manage, '_blank').focus();
            //     self.overlay.dialog("close");
            // },
            'prev': {
                class: 'button-svg button-previous',
                title: 'Previous image',
                ariaLabel: 'Previous image',
                position: 'topleft',
                click: () => this.previousImage()
            },
            'next': {
                class: 'button-svg button-next',
                title: 'Next image',
                ariaLabel: 'Next image',
                position: 'topleft',
                click: () => this.nextImage()
            },
            'open': {
                class: 'btn-open',
                title: 'Open in new tab',
                ariaLabel: 'Open in new tab',
                symbol: '\uf35d',
                position: 'topright',
                click: () => this.openInNewTab()
            },
            'download': {
                class: 'btn-download',
                title: 'Download',
                ariaLabel: 'Download',
                symbol: '\uf0c7',
                position: 'topright',
                click: () => this.downloadImage()
            },
            'close': {
                class: 'btn',
                title: 'Close',
                ariaLabel: 'Close',
                symbol: '\uf00d',
                position: 'topright',
                click: () => this.closeOverlay()
            },
            'meta': {
                class: 'btn-meta',
                title: 'Metadata',
                ariaLabel: 'Metadata',
                symbol: '\uf05a',
                position: 'bottomright',
                click: () => this.sideBarRight.toggleSidebar()
            },

            // Annotation tools
            'annotate': {
                class: 'btn-annotate',
                title:  this.config.edit ? 'Annotate image (a to activate, Esc to disable)' : 'Show annotations (a to activate, Esc to disable)',
                ariaLabel: 'Toggle annotation mode',
                symbol: '\uf303',
                position: 'bottom',
                click: () => this.toggleAnnotationMode()
            },
            'drawrect': {
                class: 'btn-draw-rect annotation-tool',
                title: 'Draw rectangle (a to cycle)',
                ariaLabel: 'Rectangle tool',
                symbol: '\uf0c8',
                position: 'bottom',
                data: {tool: 'rectangle'},
                click: () => {
                    this.annotator.setDrawingTool('rectangle');
                    this.updateToolButtons();
                }
            },
            'drawpoly': {
                class: 'btn-draw-poly annotation-tool',
                title: 'Draw polygon (a to cycle)',
                ariaLabel: 'Polygon tool',
                symbol: '\uf5ee',
                position: 'bottom',
                data: {tool: 'polygon'},
                click: () => {
                    this.annotator.setDrawingTool('polygon');
                    this.updateToolButtons();
                }
            },
            'drawpath': {
                class: 'btn-draw-line annotation-tool',
                title: 'Draw path (`a` key to cycle)',
                ariaLabel: 'Path tool',
                symbol: '\uf4d7',
                position: 'bottom',
                data: {tool: 'path'},
                click: () => {
                    this.annotator.setDrawingTool('path');
                    this.updateToolButtons();
                }
            },
            'deleteselected': {
                class: 'btn-delete-annotation annotation-tool',
                title: 'Delete selected annotation (Del or Backspace)',
                ariaLabel: 'Delete selected annotation',
                symbol: '\uf12d', // FontAwesome eraser
                position: 'bottom',
                click: () => this.annotator.deleteSelected()
            },
            // 'deleteall': {
            //     class: 'btn-delete-all-annotations annotation-tool',
            //     title: 'Delete all annotations',
            //     ariaLabel: 'Delete all annotations',
            //     symbol: '\uf1f8',          // FontAwesome trash
            //     position: 'bottom',
            //     click: () => {
            //         if (confirm('Delete all annotations on this image?')) {
            //             this.annotator.deleteAll();
            //         }
            //     }
            // },

            'togglelabels': {
                class: 'btn-toggle-labels annotation-tool',
                title: 'Show/hide annotation labels',
                ariaLabel: 'Toggle annotation labels',
                symbol: '\uf02b',          // FontAwesome tag
                position: 'bottom',
                click: () => {
                    this.annotator.setLabelsVisible();
                    this.updateToolButtons();
                }
            },

            // Navigation tools (delegated to navigator) ──
            'rotateleft': {
                class: 'button-svg button-rotate-left navigation-tool',
                title: 'Rotate left',
                ariaLabel: 'Rotate left',
                position: 'bottom',
                click: () => this.navigator.rotateLeft()
            },
            'rotateright': {
                class: 'button-svg button-rotate-right navigation-tool',
                title: 'Rotate right',
                ariaLabel: 'Rotate right',
                position: 'bottom',
                click: () => this.navigator.rotateRight()
            },
            'zoomin': {
                class: 'button-svg button-zoom-in navigation-tool',
                title: 'Zoom in',
                ariaLabel: 'Zoom in',
                position: 'bottom',
                click: () => this.navigator.zoomIn()
            },
            'zoomout': {
                class: 'button-svg button-zoom-out navigation-tool',
                title: 'Zoom out',
                ariaLabel: 'Zoom out',
                position: 'bottom',
                click: () => this.navigator.zoomOut()
            },
            'fitsize': {
                class: 'button-svg button-size-fit navigation-tool',
                title: 'Fit image size to frame',
                ariaLabel: 'Fit image size to frame',
                position: 'bottom',
                click: () => this.navigator.fitSize()
            }
        };

        // Apply user overrides
        for (const [key, value] of Object.entries(this.config.buttons)) {
            if (key in defaultConfig) {
                if (value === false) {
                    delete defaultConfig[key];
                } else {
                    defaultConfig[key] = Object.assign(defaultConfig[key], value);
                }
            }
        }

        return defaultConfig;
    }


    /**
     * Download the current image
     */
    downloadImage() {
        if (this.image_image) {
            const src = this.image_image.src;

            const link = document.createElement('a');
            link.href = src;
            link.download = src.split('/').pop().split('?')[0].split('#')[0];

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    /**
     * Open the image viewer in a new tab
     */
    openInNewTab() {
        const selected = this.getCurrentImage();
        if (selected && selected.dataset.itemUrl) {
            window.open(selected.dataset.itemUrl, '_blank');
            if (this.config.closeOnOpen) {
                this.closeOverlay();
            }
        }
    }
}

/**
 * Image navigation — zoom, pan, rotate
 *
 * Operates on an image element inside a viewport/container.
 * Can be enabled/disabled so it doesn't conflict with annotation mode.
 */
export class ImageNavigator {

    constructor() {
        this.container = null;
        this.viewport = null;
        this.image = null;

        this.enabled = true;
        this.rotationAngle = 0;
        this.dragX = 0;
        this.dragY = 0;

        // Stable handler references for add/removeEventListener
        this._handleMouseDown = (e) => this.onMouseDown(e);
        this._handleMouseMove = (e) => this.onMouseMove(e);
        this._handleMouseUp = (e) => this.onMouseUp(e);
        this._handleWheel = (e) => this.onWheel(e);
    }

    /**
     * Attach to DOM elements and bind interaction listeners
     *
     * @param {HTMLElement} container  The outer container (.image-container)
     * @param {HTMLElement} viewport   The rotatable viewport (.image-viewport)
     * @param {HTMLImageElement} image The <img> element
     */
    attach(container, viewport, image) {
        this.detach();

        this.container = container;
        this.viewport = viewport;
        this.image = image;

        this.container.addEventListener('mousedown', this._handleMouseDown);
        this.container.addEventListener('wheel', this._handleWheel, {passive: false});
    }

    /**
     * Remove all listeners
     */
    detach() {
        if (this.container) {
            this.container.removeEventListener('mousedown', this._handleMouseDown);
            this.container.removeEventListener('wheel', this._handleWheel);
        }
        document.removeEventListener('mousemove', this._handleMouseMove);
        document.removeEventListener('mouseup', this._handleMouseUp);
    }

    enable() {
        this.enabled = true;
    }

    disable() {
        this.enabled = false;
    }

    isEnabled() {
        return this.enabled;
    }

    /**
     * Clean up references and listeners (called when destroying the whole widget)
     */
    destroy() {
        this.detach();
        this.container = null;
        this.viewport = null;
        this.image = null;
    }

    /**
     * Reset rotation and fit the image to the viewport
     */
    fitSize() {
        const viewport = this.viewport;
        const img = this.image;
        if (!viewport || !img) return;

        const naturalW = img.naturalWidth;
        const naturalH = img.naturalHeight;
        if (!naturalW || !naturalH) return;

        // Reset rotation
        this.rotationAngle = 0;
        viewport.style.transform = 'rotate(0deg)';

        const targetWidth  = viewport.offsetWidth;
        const targetHeight = viewport.offsetHeight;
        if (!targetWidth || !targetHeight) return;

        // Calculate fitted size from natural dimensions (not from DOM readback)
        const scaleW = targetWidth  / naturalW;
        const scaleH = targetHeight / naturalH;
        const scale  = Math.min(scaleW, scaleH);

        const fitWidth  = naturalW * scale;
        const fitHeight = naturalH * scale;

        // Apply size and centre
        img.style.height   = `${fitHeight}px`;
        img.style.width    =  'auto';
        img.style.position = 'absolute';
        img.style.left     = `${0.5 * (targetWidth  - fitWidth)}px`;
        img.style.top      = `${0.5 * (targetHeight - fitHeight)}px`;

        img.classList.add('fitted');
    }

    /**
     * Zoom in
     */
    zoomIn() {
        this.zoom(0.3, true);
    }

    /**
     * Zoom out
     */
    zoomOut() {
        this.zoom(-0.3, true);
    }

    /**
     * Zoom the image by a relative scale factor
     *
     * @param {number}  scale    Relative zoom amount (positive = in)
     * @param {boolean} animate  Whether to ease the transition
     * @param {number}  [centerX] Zoom centre X in image-local coords
     * @param {number}  [centerY] Zoom centre Y in image-local coords
     */
    zoom(scale, animate, centerX, centerY) {
        const img = this.image;
        const cont = this.container;
        if (!img || !cont) return;

        const height = img.offsetHeight;
        const width = img.offsetWidth;
        const newHeight = height + scale * height;
        const newWidth = width * (newHeight / height);

        if (centerX === undefined) centerX = (cont.offsetWidth / 2) - img.offsetLeft;
        if (centerY === undefined) centerY = (cont.offsetHeight / 2) - img.offsetTop;

        const newLeft = img.offsetLeft - ((centerX / width) * (newWidth - width));
        const newTop = img.offsetTop - ((centerY / height) * (newHeight - height));

        if (animate) {
            img.classList.add('animate-zoom');
            setTimeout(() => img.classList.remove('animate-zoom'), 150);
        }

        img.style.left = `${newLeft}px`;
        img.style.top = `${newTop}px`;
        img.style.height = `${newHeight}px`;
    }


    /**
     * Rotate 45 degrees left
     */
    rotateLeft() {
        this.rotateBy(-45);
    }

    /**
     * Rotate 45 degrees right
     */
    rotateRight() {
        this.rotateBy(45);
    }

    /**
     * Rotate the viewport by the given angle (cumulative)
     *
     * @param {number} angle Degrees to add
     */
    rotateBy(angle) {
        this.rotationAngle = (this.rotationAngle + angle) % 360;
        if (this.viewport) {
            this.viewport.style.transform = `rotate(${this.rotationAngle}deg)`;
        }
    }

    /**
     * Rotate a point (x,y) around (cx,cy) by the given angle
     *
     * @returns {number[]} [newX, newY]
     */
    rotateCoordinates(cx, cy, x, y, angle) {
        const radians = (Math.PI / 180) * angle;
        const cos = Math.cos(radians);
        const sin = Math.sin(radians);
        return [
            cos * (x - cx) + sin * (y - cy) + cx,
            cos * (y - cy) - sin * (x - cx) + cy
        ];
    }

    /**
     * Start panning on mouse down, but only if clicking on the image (not the background)
     *
     * @param {MouseEvent} event
     */
    onMouseDown(event) {
        if (!this.enabled) return;
        if (!event.target.closest('img')) return;
        event.preventDefault();

        this.dragX = event.clientX;
        this.dragY = event.clientY;

        document.addEventListener('mousemove', this._handleMouseMove);
        document.addEventListener('mouseup', this._handleMouseUp);
    }

    /**
     * Pan the image by the mouse movement delta, rotated into the image coordinate system
     *
     * @param {MouseEvent} event
     */
    onMouseMove(event) {
        event.preventDefault();

        let shiftX = this.dragX - event.clientX;
        let shiftY = this.dragY - event.clientY;
        this.dragX = event.clientX;
        this.dragY = event.clientY;

        // Rotate shift into image coordinate system
        const [rotX, rotY] = this.rotateCoordinates(
            0, 0, shiftX, shiftY, this.rotationAngle
        );

        const img = this.image;
        img.style.left = `${img.offsetLeft - rotX}px`;
        img.style.top = `${img.offsetTop - rotY}px`;
    }

    /**
     * Stop panning on mouse up
     *
     * @param {MouseEvent} event
     */
    onMouseUp(event) {
        document.removeEventListener('mousemove', this._handleMouseMove);
        document.removeEventListener('mouseup', this._handleMouseUp);
    }

    /**
     * Zoom in/out on wheel, centered on the cursor position (rotated into image coordinates)
     *
     * @param {WheelEvent} event
     */
    onWheel(event) {
        if (!this.enabled) return;
        if (!event.target.closest('img')) return;

        const img = this.image;
        const cont = this.container;
        const rect = cont.getBoundingClientRect();

        let centerX = event.clientX - rect.left;
        let centerY = event.clientY - rect.top;

        // Rotate cursor into image coordinate system
        const [rotX, rotY] = this.rotateCoordinates(
            cont.offsetWidth / 2, cont.offsetHeight / 2,
            centerX, centerY, this.rotationAngle
        );

        const scale = event.deltaY > 0 ? -0.1 : 0.1;
        this.zoom(scale, false, rotX - img.offsetLeft, rotY - img.offsetTop);
        event.preventDefault();
    }
}

/**
 * Image annotation layer — wraps Annotorious
 *
 * Manages the Annotorious lifecycle, stores annotations per image key,
 * and handles enable/disable toggling for integration with an image viewer.
 */
export class ImageAnnotator {

    constructor() {
        this.viewport = null;
        this.image = null;
        this.saveCallback = null;
        this.selectCallback = null;
        this.editable = true;

        this.annotator = null;
        this.active = false;
        this.currentData = {};

        this.annoContainer = null;
        this.metadataEditor = this.createMetatableForAnnotation();

        // When true, committed values are not propagated to the save callback
        this._suppressSave = false;

        this._savedImgStyle = null;
        this._savedViewportStyle = null;

        this.selectedAnnotation = null;

        //Tool cycling
        this.tools = ['rectangle', 'polygon', 'path'];
        this.currentTool = null;

        // Annotation labels
        this.labelLayer = null;      // Overlay div holding the tags
        this.showLabels = true;      // Toggled by the toolbar button
        this.labelField = 'text';    // Which metadata field to display
        this.labelMaxLength = 24;    // Truncate longer values
    }

    /**
     * Attach the image annotator to the image viewer
     *
     * @param {HTMLElement} viewport
     * @param {HTMLElement} image
     * @param {HTMLElement} annoContainer Container for the metadata form (right sidebar)
     * @param {Function} saveCallback     Called with the annotation list whenever it changes
     * @param {Function} [selectCallback] Called with the selected annotation (or null)
     */
    attach(viewport, image, annoContainer, saveCallback, selectCallback = null) {
        this.viewport = viewport;
        this.image = image;
        this.annoContainer = annoContainer;
        this.saveCallback = saveCallback;
        this.selectCallback = selectCallback;

        this.metadataEditor.setEditable(this.editable);
        this.metadataEditor.attach(annoContainer);
    }

    isActive() {
        return this.active;
    }

    /**
     * Enable annotation mode and create the Annotorious instance if not already created
     */
    enable() {
        if (this.active) return;
        this.active = true;

        if (!this.currentTool) {
            this.currentTool = this.tools[0];
        }

        requestAnimationFrame(() => this._createAnnotorious());
    }

    /**
     * Disable annotation mode and destroy the Annotorious instance
     */
    disable() {
        if (!this.active) return;

        this.flushMetadata(false);
        this.saveAnnotations();
        this._destroyAnnotorious();
        this.active = false;
    }

    /**
     * Set drawing tool and track it
     *
     * @param {string} tool
     */
    setDrawingTool(tool) {
        this.currentTool = tool;
        if (this.annotator) this.annotator.setDrawingTool(tool);
    }

    /**
     * Cycle to the next tool
     *
     * @returns {string} The new tool name
     */
    nextTool() {
        const idx = this.currentTool ? this.tools.indexOf(this.currentTool) : -1;
        const next = (idx + 1) % this.tools.length;
        this.setDrawingTool(this.tools[next]);
        return this.currentTool;
    }

    /**
     * Get current tool name
     *
     * @returns {string|null}
     */
    getCurrentTool() {
        return this.currentTool;
    }

    /**
     * Toggle annotation mode on/off
     *
     * @return {boolean}
     */
    toggle() {
        this.active ? this.disable() : this.enable();
        return this.active;
    }

    /**
     * Re-create the Annotorious instance (e.g. after an image change) while keeping the same active state
     */
    refresh() {
        if (!this.active || !this.annotator) return;
        this._destroyAnnotorious();
        this.active = true;
        requestAnimationFrame(() => this._createAnnotorious());
    }

    /**
     * Clean up annotator instance and restore DOM to pre-annotation state (called when disabling or destroying)
     */
    destroy() {
        this._destroyAnnotorious();
        this.active = false;
        this.viewport = null;
        this.image = null;
        this.metadataEditor.destroy();
    }


    /**
     * Delete the currently selected annotation
     *
     * @returns {boolean} Whether an annotation was deleted
     */
    deleteSelected() {
        if (!this.annotator || !this.selectedAnnotation) return false;

        this.metadataEditor.cancelPending();
        this.annotator.removeAnnotation(this.selectedAnnotation);
        this.selectedAnnotation = null;
        this.closeMetadata();
        this._downcastAnnotorious();
        this.saveAnnotations();
        return true;
    }

    /**
     * Delete all annotations for the current image
     */
    deleteAll() {
        if (!this.annotator) return;

        this.metadataEditor.cancelPending();
        this.annotator.clearAnnotations();
        this.selectedAnnotation = null;
        this.closeMetadata();
        this._downcastAnnotorious();
        this.saveAnnotations();
    }

    /**
     * Check if there is a currently selected annotation
     *
     * @returns {boolean} Whether an annotation is currently selected
     */
    hasSelection() {
        return this.selectedAnnotation !== null;
    }


    /**
     * Create the metadata editor for the annotation fields
     *
     * @return {MetadataEditor}
     */
    createMetatableForAnnotation() {

        // The available annotation fields, rendered by the MetadataEditor
        // TODO: Get from types config
        this.annoFields = {
            type : {
                caption: "Annotation type",
                input: "select",
                options: {"region" : "Region", "line": "Line", "baseline": "Baseline", "glyph": "Glyph"}
            },
            order: {
                caption : "Reading order",
                input : "input"
            },
            text: {
                caption : "Recognized text",
                input : "textarea",
                rows: 3
            },
            comment: {
                caption : "Comments",
                input : "textarea",
                rows: 3
            }

        };

        // The purpose used to store the field values in the annotation body
        this.metadataPurpose = 'metadata';

        const editor = new MetadataEditor(
            this.annoFields,
            {
                tableClass: 'doc-image-metadata',
                editable: this.editable,
                autoCommitDelay: 1000,
            }
        );

        editor.onChange = (values) => this.commitMetadata(values);
        return editor;
    }

    //
    // ── Metadata ───────────────────────────────────────────────────────
    //

    /**
     * Show the metadata form and load the values of the selected annotation
     *
     * @return {boolean} Whether the form is shown
     */
    editMetadata() {
        if (!this.annotator || !this.selectedAnnotation) {
            this.closeMetadata();
            return false;
        }

        const editor = this.metadataEditor;
        if (!editor.isAttached()) {
            editor.attach(this.annoContainer);
        }
        if (!editor.isAttached()) {
            return false;
        }

        editor.setEditable(this.editable);
        editor.setValues(
            this.getMetadata(this.selectedAnnotation, this.metadataPurpose),
            this.selectedAnnotation.id
        );
        editor.show();

        if (this.annoContainer) {
            this.annoContainer.classList.add('has-annotation');
        }
        return true;
    }

    /**
     * Hide the metadata form
     */
    closeMetadata() {
        this.metadataEditor.hide();
        this.metadataEditor.clear();

        if (this.annoContainer) {
            this.annoContainer.classList.remove('has-annotation');
        }
    }

    /**
     * Write the values of the metadata form into the selected annotation
     *
     * Called by the editor whenever the values changed.
     *
     * @param {Object} values
     * @return {boolean} Whether the annotation was changed
     */
    commitMetadata(values) {
        if (!this.annotator || !this.selectedAnnotation || !this.editable) return false;

        if (!this.updateMetadata(values, this.metadataPurpose)) {
            return false;
        }

        this._downcastAnnotorious();
        if (!this._suppressSave) {
            this.saveAnnotations();
        }
        return true;
    }

    /**
     * Commit pending edits, e.g. before the selection or the image changes
     *
     * @param {boolean} [save] Whether to propagate the change to the save callback
     */
    flushMetadata(save = true) {
        this._suppressSave = !save;
        this.metadataEditor.flush();
        this._suppressSave = false;
    }

    /**
     * Add or replace metadata on the selected annotation
     *
     * @param {Object} fields e.g. {type: 'glyph', text: 'ABC'}
     * @param {string} purpose
     * @return {boolean} Whether the annotation was changed
     */
    updateMetadata(fields, purpose = 'metadata') {
        if (!this.annotator || !this.selectedAnnotation) return false;

        const anno = this.selectedAnnotation;

        // Remove old metadata for the given purpose
        const annoBody = (anno.body || []).filter(b => b.purpose !== purpose);

        if (fields && Object.keys(fields).length) {
            annoBody.push({
                type: 'TextualBody',
                purpose: purpose,
                value: JSON.stringify(fields)
            });
        }

        // Nothing changed? Don't touch Annotorious (avoids re-render / deselect)
        if (JSON.stringify(anno.body || []) === JSON.stringify(annoBody)) {
            return false;
        }

        const updated = {...anno, body: annoBody};

        this.annotator.updateAnnotation(updated);
        this.selectedAnnotation = updated;
        return true;
    }

    /**
     * Read metadata back from an annotation
     *
     * @param {Object} annotation
     * @param {string} purpose
     * @return {Object}
     */
    getMetadata(annotation, purpose = 'metadata') {
        const body = (annotation?.body || []).find(b => b.purpose === purpose);
        if (!body) return {};
        try {
            const data = JSON.parse(body.value);
            return (data && (typeof data === 'object')) ? data : {};
        } catch {
            return {};
        }
    }

    //
    // ── Selection ──────────────────────────────────────────────────────
    //

    /**
     * Track the selection and update the metadata form
     *
     * @param {Object|null} annotation
     * @private
     */
    _selectAnnotation(annotation) {
        // Commit edits belonging to the previously selected annotation
        if (!this.selectedAnnotation || !annotation || (this.selectedAnnotation.id !== annotation.id)) {
            this.flushMetadata();
        }

        this.selectedAnnotation = annotation || null;
        this._renderLabels();

        if (this.selectCallback) {
            this.selectCallback(this.selectedAnnotation);
        }

        this.editMetadata();
    }

    /**
     * Export annotations of the current image
     *
     * @return {Object}
     */
    saveAnnotations() {
        if (this.annotator) this._downcastAnnotorious();

        if (this.saveCallback) {
            this.saveCallback(this.currentData);
        }
    }

    /**
     * Import annotations into the internal store object
     *
     * Saves the current annotations and loads the new ones.
     *
     * @param {Object} data
     */
    loadAnnotations(data) {
        if (this.annotator) {
            this.flushMetadata(false);
            this.saveAnnotations();
            this._destroyAnnotorious();
        }

        this.currentData = data;
        this.selectedAnnotation = null;
        this.closeMetadata();
    }

    /**
     * Set the editable state of the image
     *
     * @param {boolean} editable
     */
    setEditable(editable) {
        if (!this.annotator) return;

        this.editable = editable;
        this.metadataEditor.setEditable(editable);
        this.annotator.setDrawingEnabled(editable);
        this.annotator.setUserSelectAction(editable ? 'EDIT' : 'NONE');
    }

    /**
     * Show or hide the annotation labels
     *
     * @param {boolean} [visible] Omit to toggle
     * @return {boolean} The new state
     */
    setLabelsVisible(visible) {
        this.showLabels = (visible === undefined) ? !this.showLabels : !!visible;
        this._renderLabels();
        return this.showLabels;
    }

    /**
     * Create a new Annotorious instance on the current image,
     * and set up event listeners for selection tracking and auto-saving
     *
     * @private
     */
    _createAnnotorious() {
        if (this.annotator) this._destroyAnnotorious();

        const img = this.image;
        const vp = this.viewport;
        if (!img || !vp || !img.naturalWidth || !img.naturalHeight) return;

        // Read current position/size from navigator
        const imgLeft = img.offsetLeft;
        const imgTop = img.offsetTop;
        const imgWidth = img.offsetWidth;
        const imgHeight = img.offsetHeight;

        // Save full inline styles for restore
        this._savedImgStyle = img.getAttribute('style') || '';
        this._savedViewportStyle = vp.getAttribute('style') || '';

        // Make image static with explicit size
        img.style.position = 'static';
        img.style.left = 'auto';
        img.style.top = 'auto';
        img.style.width = imgWidth + 'px';
        img.style.height = imgHeight + 'px';
        img.style.maxWidth = 'none';
        img.style.cursor = 'crosshair';

        // Create Annotorious
        this.annotator = createImageAnnotator(img, {
            drawingEnabled: this.editable,
            userSelectAction: this.editable ? 'EDIT' : 'NONE'
        });
        mountAnnotoriousTools(this.annotator);
        // console.log(this.annotator.listDrawingTools());

        // Position wrapper where the image was
        const wrapper = img.parentElement;
        if (wrapper && wrapper !== vp) {
            wrapper.style.position = 'absolute';
            wrapper.style.left = imgLeft + 'px';
            wrapper.style.top = imgTop + 'px';
            wrapper.style.width = imgWidth + 'px';
            wrapper.style.height = imgHeight + 'px';
            wrapper.style.overflow = 'visible';
        }

        // Label overlay
        this._createLabelLayer();

        // Track selection
        this.selectedAnnotation = null;
        this.closeMetadata();

        this.annotator.on('selectionChanged', (annotations) => {
            this._selectAnnotation((annotations && annotations.length > 0) ? annotations[0] : null);
        });

        this._upcastAnnotorious();
        this._renderLabels();

        this.annotator.on('createAnnotation', (annotation) => {
            this._downcastAnnotorious();
            this._selectAnnotation(annotation || this.selectedAnnotation);
            this.saveAnnotations();
            this.metadataEditor.focus();
        });

        this.annotator.on('updateAnnotation', (annotation) => {
            if (annotation && this.selectedAnnotation && (annotation.id === this.selectedAnnotation.id)) {
                this.selectedAnnotation = annotation;
            }
            this._downcastAnnotorious();
        });

        this.annotator.on('deleteAnnotation', () => {
            this.selectedAnnotation = null;
            this.closeMetadata();
            this._downcastAnnotorious();
        });

        // Set default tool
        if (!this.currentTool) {
            this.currentTool = this.tools[0];
        }
        this.annotator.setDrawingTool(this.currentTool);
    }

    /**
     * Destroy the Annotorious instance and clean up all related DOM changes,
     * restoring the original state of the image and viewport
     *
     * @private
     */
    _destroyAnnotorious() {
        if (!this.annotator) return;

        // Keep unsaved form input
        this.flushMetadata(false);

        this._downcastAnnotorious();
        this._destroyLabelLayer();
        this.selectedAnnotation = null;
        this.closeMetadata();

        const img = this.image;
        const vp = this.viewport;
        const wrapper = img ? img.parentElement : null;
        const isWrapped = wrapper && wrapper !== vp;

        this.annotator.destroy();
        this.annotator = null;

        if (img && img.parentElement !== vp) {
            const loader = vp.querySelector('.loader');
            if (loader) {
                vp.insertBefore(img, loader);
            } else {
                vp.appendChild(img);
            }
        }

        if (isWrapped && wrapper.parentElement) {
            wrapper.remove();
        }

        vp.querySelectorAll('[class*="a9s"]').forEach(el => {
            if (el !== img) el.remove();
        });

        if (img && this._savedImgStyle !== null) {
            img.setAttribute('style', this._savedImgStyle);
            this._savedImgStyle = null;
        }
        if (vp && this._savedViewportStyle !== null) {
            vp.setAttribute('style', this._savedViewportStyle);
            this._savedViewportStyle = null;
        }
    }

    /**
     * Get Annotorious annotations
     *
     * @private
     */
    _downcastAnnotorious() {
        if (!this.annotator) {
            this.currentData = {};
        } else {
            this.currentData = this.annotator.getAnnotations();
        }
        this._renderLabels();
    }

    /**
     * Set Annotorious annotations
     *
     * @private
     */
    _upcastAnnotorious() {
        if (!this.annotator ) return;
        if (this.currentData && this.currentData.length) {
            this.annotator.setAnnotations(this.currentData);
        }
    }

    /**
     * Create the overlay that holds the annotation tags
     *
     * Lives inside the Annotorious wrapper, so it shares the image's
     * coordinate system and is removed together with the annotator.
     *
     * @private
     */
    _createLabelLayer() {
        this._destroyLabelLayer();

        const wrapper = this.image ? this.image.parentElement : null;
        if (!wrapper) return;

        const layer = document.createElement('div');
        layer.className = 'anno-label-layer';

        // Inline essentials so the layer works even without the stylesheet
        layer.style.position = 'absolute';
        layer.style.left = '0';
        layer.style.top = '0';
        layer.style.width = '100%';
        layer.style.height = '100%';
        layer.style.pointerEvents = 'none';
        layer.style.overflow = 'visible';

        wrapper.appendChild(layer);
        this.labelLayer = layer;
    }

    /**
     * Remove the label overlay
     *
     * @private
     */
    _destroyLabelLayer() {
        if (this.labelLayer) {
            this.labelLayer.remove();
            this.labelLayer = null;
        }
    }

    /**
     * Redraw all labels from the current annotation list
     *
     * @private
     */
    _renderLabels() {
        const layer = this.labelLayer;
        const img = this.image;
        if (!layer || !img) return;

        if (!this.showLabels) {
            layer.replaceChildren();
            return;
        }

        const naturalW = img.naturalWidth;
        const naturalH = img.naturalHeight;
        if (!naturalW || !naturalH) return;

        // Annotation coordinates are natural image pixels
        const scaleX = img.offsetWidth / naturalW;
        const scaleY = img.offsetHeight / naturalH;

        const annotations = this.annotator ? (this.annotator.getAnnotations() || []) : [];
        const selectedId = this.selectedAnnotation ? this.selectedAnnotation.id : null;
        const fragment = document.createDocumentFragment();

        annotations.forEach(annotation => {
            const text = this._getLabelText(annotation);
            if (!text) return;

            const bounds = this._getAnnotationBounds(annotation);
            if (!bounds) return;

            const tag = document.createElement('span');
            tag.className = 'anno-label';
            if (annotation.id === selectedId) {
                tag.classList.add('anno-label-selected');
            }

            tag.textContent = (text.length > this.labelMaxLength)
                ? text.slice(0, this.labelMaxLength - 1) + '\u2026'
                : text;
            tag.title = text;

            tag.style.position = 'absolute';
            tag.style.left = (bounds.minX * scaleX) + 'px';
            tag.style.top = (bounds.minY * scaleY) + 'px';

            fragment.append(tag);
        });

        layer.replaceChildren(fragment);
    }

    /**
     * Get the bounding box of an annotation in natural image coordinates
     *
     * Handles the different geometry shapes (rectangle, polygon, path, ellipse).
     *
     * @param {Object} annotation
     * @return {{minX: number, minY: number, maxX: number, maxY: number}|null}
     * @private
     */
    _getAnnotationBounds(annotation) {
        const selector = annotation?.target?.selector;
        const geometry = Array.isArray(selector) ? selector[0]?.geometry : selector?.geometry;
        if (!geometry) return null;

        // Annotorious normally precomputes bounds
        if (geometry.bounds) {
            return geometry.bounds;
        }

        // Polygon / polyline / path
        if (Array.isArray(geometry.points) && geometry.points.length) {
            const xs = geometry.points.map(p => p[0]);
            const ys = geometry.points.map(p => p[1]);
            return {
                minX: Math.min(...xs), minY: Math.min(...ys),
                maxX: Math.max(...xs), maxY: Math.max(...ys)
            };
        }

        // Rectangle
        if (geometry.x !== undefined) {
            return {
                minX: geometry.x,
                minY: geometry.y,
                maxX: geometry.x + (geometry.w || 0),
                maxY: geometry.y + (geometry.h || 0)
            };
        }

        // Ellipse
        if (geometry.cx !== undefined) {
            return {
                minX: geometry.cx - geometry.rx, minY: geometry.cy - geometry.ry,
                maxX: geometry.cx + geometry.rx, maxY: geometry.cy + geometry.ry
            };
        }

        return null;
    }

    /**
     * Get the label text of an annotation
     *
     * @param {Object} annotation
     * @return {string}
     * @private
     */
    _getLabelText(annotation) {
        const metadata = this.getMetadata(annotation, this.metadataPurpose);
        const value = metadata[this.labelField];
        if ((value === undefined) || (value === null)) return '';
        return String(value).replace(/\s+/g, ' ').trim();
    }


}

/**
 * Metadata editor — builds a table from a field definition
 *
 * The editor is decoupled from its data source: it only maps a plain
 * key/value object to table rows and back, and notifies the owner
 * whenever the values changed.
 *
 * The generated markup is a table with one row per field,
 * the caption in the first and the value in the second cell:
 *
 * <form class="metadata-editor">
 *     <table class="metadata-editor-table">
 *         <caption>Title</caption>
 *         <tbody>
 *             <tr><td><label>Caption</label></td><td><input></td></tr>
 *         </tbody>
 *     </table>
 * </form>
 *
 * Set the option `inputs` to false to render plain text cells
 * instead of input elements (read only table).
 *
 * Field definitions are keyed by the field name, e.g.:
 *
 * {
 *     type: {caption: 'Annotation type', input: 'select', options: {region: 'Region'}},
 *     text: {caption: 'Recognized text', input: 'textarea', rows: 4}
 * }
 *
 * Supported field properties:
 *
 * - caption:     Label text (defaults to the field key)
 * - input:       'text' | 'textarea' | 'select' | 'checkbox' | any <input> type
 * - options:     Value/caption pairs for select inputs
 * - empty:       Caption of the empty option for select inputs
 * - rows:        Number of rows for textarea inputs
 * - placeholder: Placeholder text
 * - hint:        Additional description shown below the input
 *
 * Callbacks:
 *
 * - onChange(values, editor)  Committed values (on change, blur or after the debounce delay)
 * - onInput(key, editor)      Every keystroke, before committing
 */
export class MetadataEditor {

    /**
     * @param {Object} fields    Field definitions
     * @param {Object} [options] Editor options
     */
    constructor(fields = {}, options = {}) {
        this.fields = Object.assign({}, fields);

        this.options = Object.assign({
            title: '',                      // Table caption, empty = no caption
            tableClass: 'doc-image-metadata', // Class of the table
            formClass: '',                    // Class of the form element, defaults to tableClass + '-form'
            hiddenClass: 'hidden',
            hidden: true,                   // Whether the form is hidden after rendering
            inputs: true,                   // False = plain text cells
            autoFields: false,              // Create missing fields from the data keys
            editable: true,
            autoCommitDelay: 1000,          // ms, 0 = only commit on change/blur
        }, options);

        if (!this.options.formClass) {
            this.options.formClass = this.options.tableClass + '-editor';
        }

        // DOM
        this.container = null;
        this.element = null;            // <form>
        this.table = null;              // <table>
        this.tbody = null;              // <tbody>
        this.inputs = {};               // Field key => input element
        this.cells = {};                // Field key => value cell
        this.fieldCounter = 0;

        // State
        this.editable = this.options.editable;
        this.values = {};               // Last values passed to setValues()
        this.context = null;            // Identifies the object currently edited
        this._committed = null;         // Serialized values of the last commit
        this._timer = null;

        // Callbacks
        this.onChange = null;
        this.onInput = null;
    }

    //
    // ── Mounting ───────────────────────────────────────────────────────
    //

    /**
     * Render the form and append it to the given container
     *
     * @param {HTMLElement} container
     * @return {HTMLElement|undefined} The form element
     */
    attach(container) {
        if (!container) return;

        this.container = container;
        const element = this.getElement();
        if (element.parentElement !== container) {
            container.append(element);
        }
        return element;
    }

    /**
     * Remove the form from the DOM, keeping the instance reusable
     */
    detach() {
        this.cancelPending();
        if (this.element) {
            this.element.remove();
        }
        this.container = null;
    }

    /**
     * Remove the form and drop all references
     */
    destroy() {
        this.detach();
        this.element = null;
        this.table = null;
        this.tbody = null;
        this.inputs = {};
        this.cells = {};
        this.onChange = null;
        this.onInput = null;
    }

    /**
     * Whether the form is mounted in the document
     *
     * @return {boolean}
     */
    isAttached() {
        return !!(this.element && this.element.isConnected);
    }

    /**
     * Get the form element, rendering it on first access
     *
     * @return {HTMLFormElement}
     */
    getElement() {
        if (!this.element) {
            this.render();
        }
        return this.element;
    }

    //
    // ── Markup ─────────────────────────────────────────────────────────
    //

    /**
     * Build the form and the table from the field definitions
     *
     * @return {HTMLFormElement}
     */
    render() {
        const form = document.createElement('form');
        form.className = this.options.formClass;
        if (this.options.hidden) {
            form.classList.add(this.options.hiddenClass);
        }
        if (!this.editable) {
            form.classList.add('readonly');
        }
        form.addEventListener('submit', event => event.preventDefault());

        // Table
        const table = document.createElement('table');
        table.className = this.options.tableClass;

        // Caption
        if (this.options.title) {
            const caption = document.createElement('caption');
            caption.textContent = this.options.title;
            table.append(caption);
        }

        const tbody = document.createElement('tbody');
        table.append(tbody);
        form.append(table);

        this.element = form;
        this.table = table;
        this.tbody = tbody;

        // Rows
        this.inputs = {};
        this.cells = {};
        for (const [key, field] of Object.entries(this.fields)) {
            tbody.append(this.createField(key, field));
        }

        return form;
    }

    /**
     * Replace the field definitions and rebuild the table
     *
     * @param {Object} fields
     */
    setFields(fields) {
        this.fields = Object.assign({}, fields);

        if (!this.element) return;

        this.inputs = {};
        this.cells = {};
        this.tbody.replaceChildren();
        for (const [key, field] of Object.entries(this.fields)) {
            this.tbody.append(this.createField(key, field));
        }
    }

    /**
     * Add a single field definition and append its row
     *
     * @param {string} key
     * @param {Object} [field]
     */
    addField(key, field = {}) {
        this.fields[key] = field;
        if (this.tbody) {
            this.tbody.append(this.createField(key, field));
        }
    }

    /**
     * Create a table row for a single field
     *
     * The first cell contains the caption, the second cell either
     * an input element or the plain value.
     *
     * @param {string} key
     * @param {Object} field
     * @return {HTMLTableRowElement}
     */
    createField(key, field) {
        const row = document.createElement('tr');
        row.className = this.options.formClass + '-field ' + this.options.formClass + '-field-' + key;

        const keyCell = document.createElement('td');
        keyCell.className = this.options.formClass + '-caption';

        const valueCell = document.createElement('td');
        valueCell.className = this.options.formClass + '-value';

        const caption = field.caption || key;

        if (this.options.inputs) {
            const input = this.createInput(key, field);

            const label = document.createElement('label');
            label.textContent = caption;
            label.htmlFor = input.id;

            keyCell.append(label);
            valueCell.append(input);

            if (field.hint) {
                const hint = document.createElement('p');
                hint.className = this.options.formClass + '-hint';
                hint.textContent = field.hint;
                valueCell.append(hint);
            }

            this.inputs[key] = input;
        } else {
            keyCell.textContent = caption;
            valueCell.textContent = this.formatValue(this.values[key], field);
        }

        this.cells[key] = valueCell;

        row.append(keyCell, valueCell);
        return row;
    }

    /**
     * Create the input element for a single field
     *
     * @param {string} key
     * @param {Object} field
     * @return {HTMLElement}
     */
    createInput(key, field) {
        let input;

        switch (field.input) {
            case 'select':
                input = document.createElement('select');
                input.append(this.createOption('', field.empty || ''));
                for (const [value, caption] of Object.entries(field.options || {})) {
                    input.append(this.createOption(value, caption));
                }
                break;

            case 'textarea':
                input = document.createElement('textarea');
                input.rows = field.rows || 3;
                break;

            case 'checkbox':
                input = document.createElement('input');
                input.type = 'checkbox';
                break;

            default:
                input = document.createElement('input');
                input.type = field.input || 'text';
        }

        input.id = this.options.formClass + '-' + key + '-' + (++this.fieldCounter);
        input.name = key;
        input.dataset.field = key;
        input.disabled = !this.editable;

        if (field.placeholder) {
            input.placeholder = field.placeholder;
        }

        // Commit on change/blur, debounce while typing
        input.addEventListener('change', () => this.commit());
        input.addEventListener('input', () => {
            if (this.onInput) this.onInput(key, this);
            this.scheduleCommit();
        });

        // Don't let the viewer shortcuts (a, arrows, Esc) fire while editing
        input.addEventListener('keyup', event => event.stopPropagation());
        input.addEventListener('keydown', event => event.stopPropagation());

        return input;
    }

    /**
     * Create an option element
     *
     * @param {string} value
     * @param {string} caption
     * @return {HTMLOptionElement}
     */
    createOption(value, caption) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = caption;
        return option;
    }

    //
    // ── Visibility ─────────────────────────────────────────────────────
    //

    show() {
        if (this.element) {
            this.element.classList.remove(this.options.hiddenClass);
        }
    }

    hide() {
        this.cancelPending();
        if (this.element) {
            this.element.classList.add(this.options.hiddenClass);
        }
    }

    isVisible() {
        return !!(this.element && !this.element.classList.contains(this.options.hiddenClass));
    }

    /**
     * Whether the focus is currently inside the form
     *
     * @return {boolean}
     */
    isFocused() {
        return !!(this.element && this.element.contains(document.activeElement));
    }

    /**
     * Focus the first enabled input
     */
    focus() {
        const input = Object.values(this.inputs).find(elm => !elm.disabled);
        if (input) input.focus();
    }

    //
    // ── Values ─────────────────────────────────────────────────────────
    //

    /**
     * Fill the table
     *
     * If the values belong to the object that is already being edited and the
     * user is typing, the form is left untouched to avoid losing input.
     *
     * @param {Object} values   Key/value pairs
     * @param {*} [context]     Identifies the edited object (e.g. an annotation id)
     * @param {boolean} [force] Overwrite the form even while it has focus
     * @return {boolean} Whether the table was filled
     */
    setValues(values, context = null, force = false) {
        const sameContext = (context !== null) && (context === this.context);

        if (sameContext && this.isFocused() && !force) {
            return false;
        }

        this.cancelPending();
        this.context = context;
        this.values = Object.assign({}, values);

        this.getElement();

        // Create rows for unknown keys
        if (this.options.autoFields) {
            for (const key of Object.keys(this.values)) {
                if (!Object.prototype.hasOwnProperty.call(this.fields, key)) {
                    this.addField(key);
                }
            }
        }

        for (const [key, field] of Object.entries(this.fields)) {
            const value = this.values[key];
            if (this.options.inputs) {
                this.setInputValue(this.inputs[key], field, value);
            } else if (this.cells[key]) {
                this.cells[key].textContent = this.formatValue(value, field);
            }
        }

        this._committed = JSON.stringify(this.getValues());
        return true;
    }

    /**
     * Read the values
     *
     * @param {boolean} [includeEmpty] Include empty strings and unchecked boxes
     * @return {Object}
     */
    getValues(includeEmpty = false) {
        const values = {};

        for (const [key, field] of Object.entries(this.fields)) {
            const value = this.options.inputs
                ? this.getInputValue(this.inputs[key], field)
                : this.values[key];

            if (includeEmpty || ((value !== '') && (value !== false) && (value !== null) && (value !== undefined))) {
                values[key] = value;
            }
        }
        return values;
    }

    /**
     * Reset the table
     */
    clear() {
        this.cancelPending();
        this.context = null;
        this.values = {};

        for (const [key, field] of Object.entries(this.fields)) {
            if (this.options.inputs) {
                this.setInputValue(this.inputs[key], field, undefined);
            } else if (this.cells[key]) {
                this.cells[key].textContent = '';
            }
        }

        this._committed = JSON.stringify(this.getValues());
    }

    /**
     * Convert a value to its string representation
     *
     * @param {*} value
     * @param {Object} [field]
     * @return {string}
     */
    formatValue(value, field) {
        if ((value === null) || (value === undefined)) {
            return '';
        }
        if (typeof value === 'object') {
            return JSON.stringify(value, null, 2);
        }
        if ((typeof value === 'boolean') && field && field.options) {
            return field.options[String(value)] || String(value);
        }
        if (field && field.options && (field.options[value] !== undefined)) {
            return field.options[value];
        }
        return value.toString();
    }

    /**
     * Write a single value into an input element
     *
     * @param {HTMLElement} input
     * @param {Object} field
     * @param {*} value
     */
    setInputValue(input, field, value) {
        if (!input) return;

        if (field && (field.input === 'checkbox')) {
            input.checked = !!value && (value !== 'false') && (value !== '0');
            return;
        }
        input.value = ((value === undefined) || (value === null)) ? '' : String(value);
    }

    /**
     * Read a single value from an input element
     *
     * @param {HTMLElement} input
     * @param {Object} field
     * @return {*}
     */
    getInputValue(input, field) {
        if (!input) return '';

        if (field && (field.input === 'checkbox')) {
            return input.checked;
        }

        const value = (input.value || '').trim();

        if (field && (field.input === 'number')) {
            return value === '' ? '' : Number(value);
        }
        return value;
    }

    //
    // ── Editable state ─────────────────────────────────────────────────
    //

    /**
     * Enable or disable all inputs
     *
     * @param {boolean} editable
     */
    setEditable(editable) {
        editable = !!editable;
        if (this.editable === editable) return;

        this.editable = editable;
        Object.values(this.inputs).forEach(input => input.disabled = !editable);
        if (this.element) {
            this.element.classList.toggle('readonly', !editable);
        }
    }

    //
    // ── Committing ─────────────────────────────────────────────────────
    //

    /**
     * Notify the owner about changed values
     *
     * @param {boolean} [force] Commit even if nothing changed
     * @return {boolean} Whether onChange was called
     */
    commit(force = false) {
        this.cancelPending();

        if (!this.options.inputs || !this.editable || !this.onChange) return false;

        const values = this.getValues();
        const serialized = JSON.stringify(values);
        if (!force && (serialized === this._committed)) {
            return false;
        }

        this._committed = serialized;
        this.values = values;
        this.onChange(values, this);
        return true;
    }

    /**
     * Commit after the configured delay (while typing)
     */
    scheduleCommit() {
        if (!this.options.autoCommitDelay) return;
        this.cancelPending();
        this._timer = setTimeout(() => {
            this._timer = null;
            this.commit();
        }, this.options.autoCommitDelay);
    }

    /**
     * Commit pending changes immediately, e.g. before switching the context
     *
     * @return {boolean}
     */
    flush() {
        if (!this.isVisible()) return false;
        return this.commit();
    }

    /**
     * Discard a scheduled commit
     */
    cancelPending() {
        if (this._timer) {
            clearTimeout(this._timer);
            this._timer = null;
        }
    }

    /**
     * Whether the values differ from the last committed state
     *
     * @return {boolean}
     */
    isDirty() {
        return JSON.stringify(this.getValues()) !== this._committed;
    }
}

window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['image-viewer'] = ImagesWidget;
