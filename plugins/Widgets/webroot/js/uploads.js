/*
 * Handle file uploads - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {BaseWidget} from '/js/base.js';

import Utils from '/js/utils.js';
import {i18n}  from '/js/lingui.js';

/**
 * Handles file uploads
 *
 * Generate a form element with class 'widget-upload' in the backend using the Files->dropzone() function.
 * This will also add the data attribute 'data-target-list-name' for directly importing files into an article.
 *
 * TODO: Implement as document satellite widget
 */
export class UploadWidget extends BaseWidget {

    constructor(element, name, parent) {
        super(element, name, parent);

        this.widgetElement = element;
        this.widgetElement.widgetFiles = this;

        // The data list to get files for import
        this.dataList = null;

        if (element.tagName !== 'FORM') {
            return;
        }

        const dropzoneparams = {
            paramName: "FileData",
            uploadMultiple: true,
            maxFilesize: 250, // MB
            queuecomplete: () => this.onComplete(),
            success: (file) => this.onSuccess(file),
            //totaluploadprogress:function(uploadProgress,totalBytes,totalBytesSent) {console.log(uploadProgress);}
        };

        if ($.fn.dropzone) {
            this.widgetElement.classList.add('dropzone');
            $(element).dropzone(dropzoneparams);
        }

        this.listenEvent(document,'click',(event) => this.onClicked(event), '[data-role=import]');
    }

    initWidget() {
        const listName = this.widgetElement.dataset.targetListName;
        this.dataList = document.querySelector([`[data-list-name="${listName}"]`]);
    }

    onSuccess(file) {
        if (file && file.xhr) {
            const response = JSON.parse(file.xhr.responseText);
            const filenames = Utils.getValue(response,'files.filenames');
            const files = Array.from(filenames, item => ({
                fileName: item
            }));

            this.importFiles(files);
        }
    }

    onComplete() {
        // Reload page in popup or in window
        if (this.isInFrame())  {
            const event = new Event('epi:reload:page', {bubbles: true, cancelable: false});
            this.widgetElement.dispatchEvent(event);
        } else {
            location.reload();
        }
    }

    /**
     * Handles clicks on the import button
     *
     * @param {Event} event
     */
    onClicked(event) {
        const paneElement = this.getFrame(false);
        if (!paneElement.contains(event.target)) {
            return;
        }

        // TODO: Pass section instead of document
        this.importListedFiles();
        event.preventDefault();
        return false;
    }

    importFiles(fileItems) {
        if (!this.dataList) {
            return;
        }
        this.emitEvent('epi:upload:files', {items: fileItems, listName : this.dataList.dataset.listName });
    }

    importListedFiles(section) {
        this.importFiles(this.getListedFiles());
    }

    /**
     * Returns the files to import from a data list
     *
     * @return An array of objects, each containing the file name in the 'fileName' key.
     */
    getListedFiles() {
        if (!this.dataList) {
            return;
        }

        const items = this.dataList.querySelectorAll('[data-list-itemtype=file]');
        const files = Array.from(items, item => ({
            fileName: item.dataset.value
        }));
        return files;
    }
}

window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['upload'] = UploadWidget;
