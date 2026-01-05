/*
 * Job widget - EpiWidJs framework
 *
 * Starts jobs by polling and updating a progress bar
 *  TODO: replace jQueryUI progress bar by vanilla JS
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {BaseWidget} from '/js/base.js';
import Utils from '/js/utils.js';
import {i18n}  from '/js/lingui.js';

export class JobWidget extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        this.delay = 0;
        this.lastRefresh = 0;

        this.isPolling = false;
        this.isCancelled = false;
        this.isFinished = false;
        this.proceedUrl = null;

        this.progressBar = this.widgetElement.querySelector('.widget-job-bar');
        $(this.progressBar).progressbar({
            disabled: false,
            value: false
        });

        this.messageBar = this.widgetElement.querySelector('.widget-job-message');
        this.resultTable = this.widgetElement.querySelector('.widget-job-results');
        this.frameWidget = this.getFrame();
    }

    initWidget() {
        this.frameWidget = this.getFrame();

        if (this.frameWidget) {
            this.listenEvent(this.frameWidget.buttonPane,'click', (event) => this.onButtonClick(event));
            this.listenEvent(this.frameWidget.widgetElement,'epi:click:button', (event) => this.onButtonClick(event))
        }

        this.startPolling();
    }

    /**
     * Extract the URL from the button
     *
     * @return {string}
     */
    getNextUrl() {
        return this.widgetElement.dataset.jobNexturl;
    }

    /**
     * Extract the redirect URL from the widget
     *
     * @return {string|undefined}
     */
    getRedirectUrl() {
        if (this.widgetElement.dataset.jobRedirect) {
            const url = new URL(this.widgetElement.dataset.jobRedirect, App.baseUrl);
            return url.toString();
        }
    }

    /**
     * Extract the download URL from the widget
     *
     * @return {string|undefined}
     */
    getDownloadUrl() {
        if (this.widgetElement.dataset.jobDownload) {
            const url = new URL(this.widgetElement.dataset.jobDownload, App.baseUrl);
            return url.toString();
        }
    }

    /**
     * Extract the cancel URL from the widget
     *
     * @return {string}
     */
    getCancelUrl() {
        return this.widgetElement.dataset.jobCancelurl;
    }

    /**
     *  Start polling
     *
     *  @return {boolean} Whether a next action is available (download or polling)
     */
    startPolling() {

        // If a redirect or download URL is present, the job is already finished
        const data = {
            downloadUrl : this.getDownloadUrl(),
            redirectUrl : this.getRedirectUrl()
        };
        if (data.downloadUrl || data.redirectUrl) {
            this.showProceed(data);
            return true;
        }

        this.url = this.getNextUrl();
        if (!this.url) {
            return false;
        }

        if (!this.frameWidget) {
            return false;
        }

        this.isCancelled = false;
        this.frameWidget.hideButton('proceed');
        this.frameWidget.showButton('cancel','Stop');

        this.continuePolling(this.url);
        return true;
    }

    /**
     * Stop polling
     */
    stopPolling(data) {
        if (this.isCancelled) {
            this.showError('Cancelled');
        } else {
            this.showResult(data);
        }
    }

    /**
     * Cancel polling
     *
     */
    onButtonClick(event) {
        const role = Utils.getValue(event,'detail.data.role', event.target.dataset.role);

         if (role === 'cancel') {
             if (this.isPolling) {
                this.cancel();
                event.preventDefault();
             } else if (this.isInFrame()) {
                this.frameWidget.closeWindow();
                event.preventDefault();
             }
        }

        else if (this.isFinished && role === 'proceed') {
            window.location = this.proceedUrl;
            event.preventDefault();
        }
    }

    /**
     * Show an error message, hide progress bar and the stop button
     *
     * @param msg
     */
    showError(msg){
        this.isPolling = false;

        msg = msg === undefined ? "An error occured. See the logs for details." : msg;

        this.messageBar.innerHTML = msg;
        this.messageBar.classList.add('error');
        Utils.hide(this.progressBar);
        this.frameWidget.hideButton('proceed');
        this.frameWidget.showButton('cancel', 'Close');
    }

    /**
     * Show a success message and hide progress bar
     *
     * @param msg
     */
    showSuccess(msg){
        this.messageBar.innerHTML = msg;
        this.messageBar.classList.add('success');
        this.frameWidget.hideButton('cancel');
        Utils.hide(this.progressBar);
    }

    /**
     * Open the proceed page
     *
     * If downloadURL is present in the data, it will be opened in a new tab.
     * If redirectURL is present, it will trigger a redirect.
     *
     * @param {object} data
     */
    showProceed(data) {
        this.showResult(data);

        let url;
        if (data.downloadUrl) {
            url = data.downloadUrl;
        } else {
            url = data.redirectUrl;
        }
        url = new URL(url, App.baseUrl);
        this.proceedUrl = url.toString();

        const caption = data.downloadUrl ? 'Download' : 'Proceed';
        this.frameWidget.showButton('proceed', caption, this.proceedUrl);
        if (this.isInFrame()) {
            this.frameWidget.showButton('cancel', 'Close');
            this.frameWidget.closeWindow();
        }

        if (data.downloadUrl) {
            window.open(this.proceedUrl);
        } else {
            window.location = this.proceedUrl;
        }
    }

    /**
     * Show a success message and show download URLs in the result table
     *
     * @param {object} data
     */
    showResult(data) {

        this.isPolling = false;
        this.isFinished = true;

        const msg = data && data.message ? data.message : 'Finished';
        this.showSuccess(msg);

        const downloads = Utils.getValue(data,'result.downloads');
        const td = this.resultTable ? this.resultTable.querySelector('td') : undefined;

        if (!downloads || !td) {
            return;
        }

        let downloadHtml = '';
        downloads.forEach(download => {
            downloadHtml += `<a href="${download.url}" target="_blank">${download.caption}</a><br>`;
        })

        td.innerHTML = downloadHtml;
        this.resultTable.classList.remove('empty');
    }

    /**
     *
     *  Issues an AJAX request
     *
     *  Each response should be in JSON format with the following keys:
     *  - progressmax
     *  - progress
     *  - message
     *  - error
     *  - redirect Redirect after the job was finished
     *  - nexturl Continue polling with this URL
     *
     */
    continuePolling(url, form) {
        const self = this;
        this.isPolling = true;

        // Slow down
        const currentTime = Date.now();
        if (this.lastRefresh) {
            if ((this.lastRefresh + this.delay) > currentTime) {
                setTimeout(() => {
                    this.continuePolling(url, form);
                }, this.delay);
                return;
            }
        }
        this.lastRefresh = currentTime;

        let data = {};
        if (form !== undefined) {
            data = $(form).serialize();
        }

        App.showLoader();
        $.ajax({
            type: "post",
            url: url,
            data: data,
            dataType: "json",

            success: function (data, textStatus) {
                data = data.job;

                // Progress
                if(self.progressBar) {

                    if (data.progressmax) {
                        $(self.progressBar).progressbar("option", "max", parseInt(data.progressmax));
                    }

                    if (data.progress) {
                        $(self.progressBar).progressbar("option", "value", parseInt(data.progress));
                        self.progressBar.querySelector('.ui-progressbar-value').innerHTML = data.progress + ' / ' + data.progressmax;
                    }
                }

                // Update status message
                self.messageBar.innerHTML = data.message ? data.message : '';

                // Error
                if (data.error || (data.status === 'error')) {
                    self.showError(data.error || Utils.getValue(data, 'result.error'));
                }

                // Download or redirect
                else if (data.downloadUrl || data.redirectUrl) {
                    self.showProceed(data);
                }

                // Continue
                else if (data.nexturl && !self.isCancelled) {

                    // For delayed jobs, request status every second
                    if (data.delay === 1) {
                        self.delay = 1000;
                    }

                    const target = new URL(data.nexturl, App.baseUrl);
                    self.continuePolling(target);
                }

                // Stop
                else {
                    self.stopPolling(data);
                }
            },

            error: function (jqXHR, textStatus, errorThrown) {
                self.showError(errorThrown);
            },

            complete: function (jqXHR, textStatus) {
                App.hideLoader();
            }

        });
    }

    cancel() {
        this.isCancelled = true;
        this.progressBar.querySelector('.ui-progressbar-value').innerHtml = '';
        $(this.progressBar).progressbar("option", "value", false);
        $(this.progressBar).progressbar("option", "max", false);

        // Send a delete request to the server to cancel the job
        const url = this.getCancelUrl();
        if (url) {
            return fetch(url, {
                method: 'DELETE',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
            });
        }
    }

}


/**
 * Register widget classes in the app
 */
window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['job'] = JobWidget;
