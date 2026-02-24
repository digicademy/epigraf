/*
 * Image viewer widget - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {BaseWidget} from '/js/base.js';
import {Accordion, ResizableSidebar} from './layout.js';
import Utils from "/js/utils.js";

/**
 * Class for displaying images of articles
 *
 * Each img element needs the following attributes:
 *
 * - title: Optional, a title for the image
 * - alt: A description of the image content
 * - data-display: An URL to get the full image
 * - data-copyright: The copyright holder
 * - data-meta: Metadata in JSON format
 *
 * Each img element should be wrapped in an item element. This can be a span, div or a element.
 * Additionally, to find the full image list, all item elements need a common ancestor.
 *
 * Set config.imageSelector to a CSS selector that finds all those items.
 * Set config.containerSelector to a CSS selector that finds the container element.
 *
 * You can use the static attachImages() method to init the widget with the appropriate config.
 *
 */
export class ImagesWidget extends BaseWidget{
    // TODO: Rename variables in camelCase?
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
            inlineArrows: false
        }

        this.overlay = undefined;
        this.overlay_header = undefined;
        this.image_container = undefined;
        this.image_viewport = undefined;
        this.image_image = undefined;
        this.loader = undefined;
        this.items = undefined;
        this.currentPosition = undefined;
        this.sideBarRight = undefined;
        this.sideBarLeft = undefined;

        this.items = undefined;
        this.options = {'title': "Image viewer"};

        // Positions
        this.rotationAngle = 0;
        this.dragX = undefined;
        this.dragY = undefined;

        // Attach event handler
        this.listenEvent(this.widgetElement, 'click', event => this.clickOnImage(event));

        // Load image from hash fragment
        this.selectImageByHash();
    }

    static attachImages(selContainer, selImages, config = {}) {
        const elmContainer = document.querySelector(selContainer);
        if (!elmContainer) {
            return;
        }
        const item = elmContainer.querySelector(selImages);
        if (!item) {
            return;
        }

        const widget = new ImagesWidget(container);
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
            '        <nav class="sidebar sidebar-left sidebar-size-2 accordion-item ' + leftsidebar + '" data-accordion-item="overlay-sidebar-left">\n' +
            '            <div class="sidebar-content"></div>\n' +
            '        </nav>\n' +
            '\n' +
            '        <div class="image-container accordion-item accordion-main" data-accordion-item="overlay-main">\n' +
            '            <div class="overlay-image-prev fontawesome">\uf053</div>\n' +
            '            <div class="image-viewport" style="transform: rotate(0deg);">\n' +
            '                <img alt="" src="" class="">\n' +
            '                <div class="loader"></div>\n' +
            '            </div>\n' +
            '            <div class="overlay-image-next fontawesome">\uf054</div>"\n' +
            '        </div>\n' +
            '        <div class="metadata-container sidebar sidebar-right accordion-item" data-accordion-item="overlay-sidebar-right">\n' +
            '            <div class="metadata-content"></div>\n' +
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
        this.listenEvent(overlay.querySelector('.overlay-image-prev') ,'click', event => this.previousImage());
        this.listenEvent(overlay.querySelector('.overlay-image-next') ,'click', event => this.nextImage());
        Utils.listenEvent(document,'keyup', event => this.onKeyUp(event));
        return overlay;
    }

    /**
     * Copy images from a page into the image viewer's sidebar, if it is still empty
     *
     * @param imageList
     */
    loadItems(imageList) {
        if (this.items) {
            return;
        }

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
     * Called on image click. Updates the loaded image.
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
     * Prev/ next image by keyboard
     *
     * @param {Event} event
     */
    onKeyUp(event) {
        if (!this.isActive() || !this.items || (this.items.length < 2)) {
            return;
        }

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
        }

        else {

            // Prepare overlay
            this.showOverlay();

            // Construct image list
            const imageList = selected.closest(this.config.containerSelector);

            if (imageList) {
                this.loadItems(imageList);
                const siblings = imageList.querySelectorAll(this.config.imageSelector);
                this.currentPosition = Array.from(siblings).findIndex(elm => selected === elm);
            } else {
                this.currentPosition = 0;
                this.items = [selected];
            }
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
            selectedId ?
                document.querySelector('.doc-image[data-row-id="' + selectedId[1] + '"]') :
                null;

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

    /**
     * Show the selected image.
     */
    showCurrentImage() {
        const selected = this.getCurrentImage();

        this.items.forEach(element => {
            element.classList.remove('selected');
        });

        selected.classList.add('selected');
        Utils.scrollIntoViewIfNeeded(selected);

        // Load image
        this.image_image.classList.add('hidden');
        this.loader.classList.remove('hidden');

        const downloadingImage = new Image();
        downloadingImage.onload = () => {
            downloadingImage.onload = null;
            this.loader.classList.add('hidden');
            this.image_image.setAttribute('src', downloadingImage.src);
            this.image_image.classList.remove('hidden');
        };

        const img = selected.querySelector('img');
        downloadingImage.src = img ? img.dataset.display : '';

        // Fit
        this.fitSize();

        // Show metadata
        this.showMetadata(selected);
    }

    getMetadata(selected) {
        let data = {};

        // Use metadata element
        const metadataElm = selected.querySelector('.doc-image-metadata');
        if (metadataElm) {
            data['title'] = metadataElm.querySelector('[data-row-field="file"]').textContent;
            data['element'] = metadataElm.cloneNode(true);
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
        header += '. ' + img.getAttribute('title') || '';
        data['title'] = header;

        if (img.dataset.copyright) {
            data['footer'] = img.dataset.copyright || '';
        }

        // Metadata
        if (img.dataset.metadata) {
            try {
                const metadata = JSON.parse(img.dataset.metadata);
                data['element'] = this.createMetaTable(metadata);
            } catch (e) {
                console.error('Invalid JSON:', e);
            }
        }

        return data;
    }

    /**
     * Create a table from an object
     *
     * @param {Object} data An object with keys/value pairs to be displayed in a table
     * @returns {HTMLTableElement} The generated table
     */
    createMetaTable(data) {
        const table = document.createElement('table');
        table.classList.add('doc-image-metadata');
        const tbody = document.createElement('tbody');
        table.appendChild(tbody);

        // Iterate over own enumerable properties only
        for (const key in data) {
            if (Object.prototype.hasOwnProperty.call(data, key)) {
                const row = document.createElement('tr');

                const keyCell = document.createElement('td');
                keyCell.textContent = key;
                row.appendChild(keyCell);

                const valueCell = document.createElement('td');

                // Convert value to string
                let value = data[key];
                if (value === null || value === undefined) {
                    valueCell.textContent = '';
                } else if (typeof value === 'object') {
                    // For objects/arrays, stringify prettily
                    valueCell.textContent = JSON.stringify(value, null, 2);
                } else {
                    valueCell.textContent = value.toString();
                }

                row.appendChild(valueCell);

                tbody.appendChild(row);
            }
        }

        return table;
    }

    showMetadata(selected) {
        const metadata = this.getMetadata(selected);

        // Change title
        const titleElement = this.overlay.querySelector('.overlay-header-title');
        let titleText = '';
        if (titleElement && metadata['title']) {
            titleText = metadata['title'] || '';
        }
        if (this.config.counter && this.items && (this.items.length > 1)) {
            const counterText =  `[${this.currentPosition + 1}/${this.items.length}] `;
            titleText = counterText + titleText;
        }
        titleElement.innerText = titleText;

        const footerElement = this.overlay.querySelector('.overlay-footer-title');
        if (footerElement && metadata['footer']) {
            footerElement.innerText = metadata['footer'] || '';
        }

        // Change detail view
        const metadataElement = this.overlay.querySelector('.metadata-content');
        if (metadataElement && metadata['element']) {
            metadataElement.replaceChildren(metadata['element']);
        } else {
            this.sideBarRight.hideSidebar(true);
        }

        // Change open button
        const openElement = this.overlay.querySelector('.btn-open');
        if (openElement) {
            Utils.toggle(openElement, selected.dataset.itemUrl || false);
        }

    }

    updateNavigation() {
        if (!this.items) {
            return;
        }

        const showThumbs = Utils.isWideScreen() && ((this.items.length > 1) &&  (this.config.thumbs));
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
        if (this.overlay) {
            this.overlay.classList.add('hidden');
            document.body.classList.remove('no-scroll');
        }
    }

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

        // Add markup
        this.overlay = this.createOverlay()
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

        // Sidebars
        const rightSidebarElement = this.overlay.querySelector('.metadata-container');
        this.sideBarRight = new ResizableSidebar(rightSidebarElement, 'right', 10, true);

        const leftSidebarElement = this.overlay.querySelector('.sidebar-left');
        this.sideBarLeft = new ResizableSidebar(leftSidebarElement, 'left', 10, this.config.thumbs);

        // Footer
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

        // Move to body
        document.querySelector('body').append(this.overlay);

        // Init mouse interaction and event handlers
        this.image_container.addEventListener('mousedown', event => this.dragMouseDown(event));
        this.image_container.addEventListener('wheel', event => this.mouseWheel(event));
        this.image_image.addEventListener('load', event => this.fitSize(event));

        this.listenEvent(this.overlay,'epi:hide:sidebar', (event) => this.onHideSidebar(event));
    }

    createButton(value) {
        const button = document.createElement('button');
        button.className = value.class;

        button.textContent = value.content || '';
        button.ariaLabel = value.areaLabel || '';
        button.title = value.title || '';

        if (value.symbol) {
            const icon = document.createElement('span');
            icon.className = 'icon fontawesome';
            icon.textContent = value.symbol;
            button.prepend(icon);
        }

        button.addEventListener('click', value.click);
        return button;
    }

    onHideSidebar(event) {
        this.fitSize(event);
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

    /**
     * Get buttons for image view dialog.
     *
     * @returns {{Object}} Button configuration object
     */
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
                click: event => this.previousImage()
            },
            'next': {
                class: 'button-svg button-next',
                position: 'topleft',
                title: 'Next image',
                ariaLabel: 'Next image',
                click: event => this.nextImage()
            },

            'open': {
                class: 'btn-open',
                title: 'Open in new tab',
                ariaLabel: 'Open in new tab',
                symbol: '\uf35d',
                position: 'topright',
                click: event => this.openInNewTab()
            },
            'download': {
                class: 'btn-download',
                position: 'topright',
                title: 'Download',
                symbol: '\uf0c7',
                ariaLabel: 'Download',
                click: event => this.downloadImage()
            },
            'close': {
                class: 'btn',
                position: 'topright',
                title: 'Close',
                symbol: '\uf00d',
                ariaLabel: 'Close',
                click: event => this.closeOverlay()
            },
            'meta': {
                class: 'btn-meta',
                position: 'bottomright',
                title: 'Metadata',
                symbol: '\uf05a',
                ariaLabel: 'Metadata',
                click: event => this.sideBarRight.toggleSidebar()
            },
            'rotateleft': {
                class: 'button-svg button-rotate-left',
                title: 'Rotate left',
                ariaLabel: 'Rotate left',
                position: 'bottom',
                click: event => this.rotateLeft()
            },
            'rotateright': {
                class: 'button-svg button-rotate-right',
                title: 'Rotate right',
                ariaLabel: 'Rotate right',
                position: 'bottom',
                click: event => this.rotateRight()
            },

            'zoomin': {
                class: 'button-svg button-zoom-in',
                title: 'Zoom in',
                ariaLabel: 'Zoom in',
                position: 'bottom',
                click: event => this.zoomIn()
            },

            'zoomout': {
                class: 'button-svg button-zoom-out',
                title: 'Zoom out',
                ariaLabel: 'Zoom out',
                position: 'bottom',
                click: event => this.zoomOut()
            },

            'fitsize': {
                class: 'button-svg button-size-fit',
                title: 'Fit image size to frame',
                ariaLabel: 'Fit image size to frame',
                position: 'bottom',
                click: event => this.fitSize(event)
            },
        };


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
     * Show previous image.
     */
    previousImage() {
        this.currentPosition -= 1;

        const maxPosition = typeof items === 'string' ? 0 : this.items.length;
        this.currentPosition = Utils.mod(this.currentPosition, maxPosition);

        this.showCurrentImage();
    }

    /**
     * Show next image.
     */
    nextImage() {
        this.currentPosition += 1;

        const maxPosition = typeof items === 'string' ? 0 : this.items.length;
        this.currentPosition = Utils.mod(this.currentPosition, maxPosition);

        this.showCurrentImage();
    }

    /**
     * Rotate image by -45 degree.
     */
    rotateLeft() {
        this.rotateBy(-45);
    }

    /**
     * Rotate image by 45 degree.
     */
    rotateRight() {
        this.rotateBy(+45);
    }

    /**
     * Rotate image by given angle.
     *
     * @param angle Angle to rotate image by
     */
    rotateBy(angle) {
        this.rotationAngle = (this.rotationAngle + angle) % 360;
        this.image_viewport.style.transform = `rotate(${this.rotationAngle}deg)`;
    }

    /**
     * Recalculate image coordinates after mouse drag.
     *
     * @param cx
     * @param cy
     * @param x
     * @param y
     * @param angle
     * @returns {array}
     */
    rotateCoordinates(cx, cy, x, y, angle) {
        const radians = (Math.PI / 180) * angle,
            cos = Math.cos(radians),
            sin = Math.sin(radians),
            nx = (cos * (x - cx)) + (sin * (y - cy)) + cx,
            ny = (cos * (y - cy)) - (sin * (x - cx)) + cy;
        return [nx, ny];
    }

    /**
     * Called when try to drag image.
     *
     * @param event Mousedown
     */
    dragMouseDown(event) {
        if (!event.target.closest('img')) {
            return;
        }
        event.preventDefault();

        // Get the mouse cursor position at startup
        this.dragX = event.clientX;
        this.dragY = event.clientY;

        // TODO: Auf normale Event listener umstellen? Klappt erstmal noch so...
        document.onmouseup = event => this.dragMouseUp(event);
        document.onmousemove = event => this.dragMouseMove(event);
    }

    /**
     * Called on every mouse move after drag has started.
     *
     * @param event Mousemove
     */
    dragMouseMove(event) {
        event.preventDefault();

        // Calculate the shift
        let shiftX = this.dragX - event.clientX;
        let shiftY = this.dragY - event.clientY;

        this.dragX = event.clientX;
        this.dragY = event.clientY;

        // Rotate shift to image coordinate system
        const shiftRotated = this.rotateCoordinates(0, 0, shiftX, shiftY, this.rotationAngle);
        shiftX = shiftRotated[0];
        shiftY = shiftRotated[1];

        // set the element's new position
        const img = this.image_image;
        img.style.left = `${(img.offsetLeft - shiftX)}px`;
        img.style.top = `${(img.offsetTop - shiftY)}px`;
    }

    /**
     * Called after drag has ended.
     *
     * @param event Mouseup
     */
    dragMouseUp(event) {
        document.onmouseup = null;
        document.onmousemove = null;
    }

    /**
     * Zoom in on image.
     */
    zoomIn() {
        this.zoom(0.3, true);
    }

    /**
     * Zoom out on image.
     */
    zoomOut() {
        this.zoom(-0.3, true);
    }

    /**
     * Zoom image by given number.
     *
     * @param scale Number to zoom by.
     * @param animate If zoom should be animated (with ease)
     * @param centerX x coordinate of image center
     * @param centerY y coordinate of image center
     */
    zoom(scale, animate, centerX, centerY) {
        const img = this.image_image;
        const cont = this.image_container;

        // Calculate size
        const height = img.offsetHeight;
        const width = img.offsetWidth;
        const newHeight = height + scale * height;
        const newWidth = width * (newHeight / height);

        // Calculate shift
        centerX = centerX === undefined ? (cont.offsetWidth / 2) - img.offsetLeft : centerX;
        centerY = centerY === undefined ? (cont.offsetHeight / 2) - img.offsetTop : centerY;
        const newLeft = img.offsetLeft - ((centerX / width) * (newWidth - width));
        const newTop = img.offsetTop - ((centerY / height) * (newHeight - height));


        if (animate === true) {
            this.image_image.classList.add("animate-zoom");
            setTimeout(()=> this.image_image.classList.remove("animate-zoom"), 150);
        }

        this.image_image.style.left = `${newLeft}px`;
        this.image_image.style.top = `${newTop}px`;
        this.image_image.style.height = `${newHeight}px`;

    }

    /**
     * Called on image zoom with mousewheel.
     *
     * @param {WheelEvent} event Wheel event
     * @returns {void}
     */
    mouseWheel(event) {
        if (!event.target.closest('img')) {
            return;
        }

        const img = this.image_image;
        const cont = this.image_container;

        // Calculate center
        const rect = cont.getBoundingClientRect();
        let centerX = (event.clientX - rect.left);
        let centerY = (event.clientY - rect.top);
        const centerRot = this.rotateCoordinates(cont.offsetWidth / 2, cont.offsetHeight / 2, centerX, centerY, this.rotationAngle);

        centerX = centerRot[0] - img.offsetLeft;
        centerY = centerRot[1] - img.offsetTop;

        // Zoom
        const scale = event.deltaY > 0 ? -0.1 : 0.1;
        this.zoom(scale, false, centerX, centerY);

        event.preventDefault();
    }

    /**
     * Fit image to viewport size.
     *
     * @param event Click or load
     */
    fitSize(event) {
        const viewport = this.image_viewport;
        viewport.style.transform = 'rotate(0deg)';

        this.rotationAngle = 0;

        const target_height = viewport.offsetHeight;
        const target_width = viewport.offsetWidth;

        const img = this.image_image;
        let height = img.offsetHeight;
        let width = img.offsetWidth;

        let newHeight;
        if (((target_height / height) * width) > target_width)
            newHeight = (target_width / width) * height;
        else
            newHeight = target_height;

        img.style.height = `${newHeight}px`;

        height = img.offsetHeight;
        width = img.offsetWidth;

        const newLeft = (0.5 * (target_width - width));
        const newTop = (0.5 * (target_height - height));

        img.style.left = `${newLeft}px`;
        img.style.top = `${newTop}px`;
        img.style.position = 'absolute';
    }

}


window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['image-viewer'] = ImagesWidget;
