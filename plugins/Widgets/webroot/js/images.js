/*
 * Image viewer widget - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {BaseWidget} from '/js/base.js';
import {ResizableSidebar} from './layout.js';
import Utils from "/js/utils.js";

/**
 * Class for displaying images of articles
 */
export class ImagesWidget extends BaseWidget{
    // TODO: Rename variables in camelCase?
    constructor(element, name, parent) {
        super(element, name, parent);

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

    /**
     * Create the markup
     *
     * @return {HTMLElement}
     */
    createOverlay() {
        const template = '<div class="overlay hidden">\n' +
            '    <div class="overlay-header">\n' +
            '        <div class="overlay-header-buttons overlay-header-buttons-left"></div>\n' +
            '        <div class="overlay-header-title"></div>\n' +
            '        <div class="overlay-header-buttons overlay-header-buttons-right"></div>\n' +
            '    </div>\n' +
            '    <div class="overlay-content">\n' +
            '        <nav class="sidebar sidebar-left sidebar-size-2 sidebar-expanded">\n' +
            '            <div class="sidebar-content"></div>\n' +
            '        </nav>\n' +
            '\n' +
            '        <div class="image-container">\n' +
            '            <div class="image-viewport" style="transform: rotate(0deg);">\n' +
            '                <img alt="" src="" class="">\n' +
            '                <div class="loader"></div>\n' +
            '            </div>\n' +
            '        </div>\n' +
            '        <div class="metadata-container">\n' +
            '            <div class="metadata-content"></div>\n' +
            '        </div>\n' +
            '    </div>\n' +
            '    <footer class="overlay-footer">\n' +
            '        <div class="overlay-footer-left">\n' +
            '            <nav></nav>\n' +
            '        </div>\n' +
            '    </footer>\n' +
            '</div>';

        const overlay = Utils.spawnFromString(template);
        this.listenEvent(overlay, 'click', event => this.clickOnImage(event));
        return overlay;
    }


    loadItems(imageList) {
        if (this.items) {
            return;
        }

        const sidebar = this.overlay.querySelector('.sidebar-left .sidebar-content');
        if (sidebar && imageList) {
            sidebar.replaceChildren(imageList.cloneNode(true));
        }

        this.items = sidebar.querySelectorAll('.doc-image');
    }

    /**
     * Called on image click. Updates the loaded image.
     *
     * @param event Click
     */
    clickOnImage(event) {
        if (!event.target.closest('a.link-image') || event.ctrlKey) {
            return;
        }

        const selected = event.target.closest('.doc-image');

        if (selected) {
            event.preventDefault();
            this.selectImageByElement(selected);
        }
    }

    /**
     * Show image
     *
     * @param selected An image item, either from the article or from the overlay
     */
    selectImageByElement(selected) {
        const imageList = selected.closest('.doc-imagelist');

        // Prepare overlay
        this.showOverlay(imageList);

        if (imageList) {
            this.loadItems(imageList);
            const siblings = imageList.querySelectorAll('.doc-image');
            this.currentPosition = Array.from(siblings).findIndex(elm => selected === elm);
        } else {
            this.currentPosition = 0;
            this.items = [selected];
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
            this.image_image.classList.remove('hidden');
            this.image_image.setAttribute('src', downloadingImage.src);
        };

        const img = selected.querySelector('img');
        downloadingImage.src = img ? img.dataset.display : '';

        // Show metadata
        this.showMetadata(selected);

        // Fit
        this.fitSize();
    }

    getMetadata(selected) {
        let data = {};
        const metadata = selected.querySelector('.doc-image-metadata');
        if (metadata) {
            data['title'] = metadata.querySelector('[data-row-field="file"]').textContent,
                data['element'] = metadata.cloneNode(true)
        }
        return data;
    }

    showMetadata(selected) {
        const metadata = this.getMetadata(selected);

        // Change title
        const titleElement = this.overlay.querySelector('.overlay-header-title');
        if (titleElement && metadata['title']) {
            titleElement.innerText = metadata['title'] || '';
        }

        // Change detail view
        const metadataElement = this.overlay.querySelector('.metadata-content');
        if (metadataElement && metadata['element']) {
            metadataElement.replaceChildren(metadata['element']);
        }

    }

    /**
     * Close overlay window.
     */
    closeOverlay() {
        if (this.overlay) {
            this.overlay.classList.add('hidden');
        }
    }

    /**
     * Create and show overlay
     */
    showOverlay() {
        if (this.overlay) {
            this.overlay.classList.remove('hidden');
            return;
        }

        // Add markup
        this.overlay = this.createOverlay()
        this.overlay.classList.remove('hidden');

        // Header bar
        this.overlay_header = this.overlay.querySelector('.overlay-header');

        const topButtons = this.getDialogButtons();
        for (const [key, value] of Object.entries(topButtons)) {
            if ((value.position === 'topleft') || (value.position === 'topright')) {
                const button = document.createElement('button');
                button.className = value.class;
                button.addEventListener('click', value.click);

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
        this.sideBarLeft = new ResizableSidebar(leftSidebarElement, 'left', 10, false);

        // Footer
        const footerButtons = this.getDialogButtons();
        for (const [key, value] of Object.entries(footerButtons)) {
            if (value.position === 'bottom') {
                const button = document.createElement('button');
                button.className = value.class;
                button.addEventListener('click', value.click);

                this.overlay.querySelector('.overlay-footer-left nav').append(button);
            }
        }

        // Move to body
        document.querySelector('body').append(this.overlay);

        // Init mouse interaction and event handlers
        this.image_container.addEventListener('mousedown', event => this.dragMouseDown(event));
        this.image_container.addEventListener('wheel', event => this.mouseWheel(event));
        this.image_image.addEventListener('load', event => this.fitSize(event));
    }


    /**
     * Open the image viewer in a new tab
     */
    openInNewTab() {
        const selected = this.getCurrentImage();
        if (selected && selected.dataset.itemUrl) {
            window.open(selected.dataset.itemUrl, '_blank');
            this.closeOverlay();
        }
    }

    /**
     * Get buttons for image view dialog.
     *
     * @returns {{Object}} Button configuration object
     */
    getDialogButtons() {
        return {
            // 'Manage file': function () {
            //     window.open(self.url_manage, '_blank').focus();
            //     self.overlay.dialog("close");
            // },
            'Previous Image': {
                class: 'button-svg button-previous',
                title: 'Previous image',
                ariaLabel: 'Previous image',
                position: 'topleft',
                click: event => this.previousImage()
            },
            'Next Image': {
                class: 'button-svg button-next',
                position: 'topleft',
                title: 'Next image',
                ariaLabel: 'Next image',
                click: event => this.nextImage()
            },

            'Open in new tab': {
                class: 'btn-open',
                title: 'Open in new tab',
                ariaLabel: 'Open in new tab',
                position: 'topright',
                click: event => this.openInNewTab()
            },
            'Close': {
                class: 'btn-close',
                position: 'topright',
                title: 'Close',
                ariaLabel: 'Close',
                click: event => this.closeOverlay()
            },

            'Rotate to left': {
                class: 'button-svg button-rotate-left',
                title: 'Rotate left',
                ariaLabel: 'Rotate left',
                position: 'bottom',
                click: event => this.rotateLeft()
            },
            'Rotate to right': {
                class: 'button-svg button-rotate-right',
                title: 'Rotate right',
                ariaLabel: 'Rotate right',
                position: 'bottom',
                click: event => this.rotateRight()
            },

            'Zoom in': {
                class: 'button-svg button-zoom-in',
                title: 'Zoom in',
                ariaLabel: 'Zoom in',
                position: 'bottom',
                click: event => this.zoomIn()
            },

            'Zoom out': {
                class: 'button-svg button-zoom-out',
                title: 'Zoom out',
                ariaLabel: 'Zoom out',
                position: 'bottom',
                click: event => this.zoomOut()
            },

            'Fit size': {
                class: 'button-svg button-size-fit',
                title: 'Fit image size to frame',
                ariaLabel: 'Fit image size to frame',
                position: 'bottom',
                click: event => this.fitSize(event)
            },
        };
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
        this.loader.classList.add('hidden');

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
