/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import Utils from '/js/utils.js';
import {UsersModel} from '/js/models.js';
import {AjaxQueue} from '/js/requests.js';

/**
 * The main application class
 *
 */
class EpiApp {

    constructor() {

        // Constants
        this.settings = {
            'timeout': 800,
            'colwidth': 150
        };

        // Hold data passed from PHP
        this.data = {};
        this.user = new UsersModel();

        // Queue for ajax requests
        this.ajaxQueue = new AjaxQueue();
        this.loaderCount = 0;

        // Model classes (e.g. property chooser)
        this.models = {};
        this.widgets = {};

        // XHR for Ajax calls
        this.xhr = null;

        this.initEvents();
    }

    initEvents() {
        // Catch errors
        window.addEventListener('error', (ev) => this.logError(ev));
        // window.setTimeout(() => this.makeAnError(),1000 );

        // Hide messages
        Utils.listenEvent(document, 'click', (ev) => this.hideMessage(ev), '.message');

        // Popup links
        // TODO: create a widget
        Utils.listenEvent(document, 'click', (ev) => this.openPopupLink(ev), 'a.popup, a.popup *');
        Utils.listenEvent(document, 'click', (ev) => this.openDetailLink(ev), 'a.frame, a.frame *');

        // Listen to messages
        Utils.listenEvent(document,'app:show:message', (ev) => this.showMessage(ev));
        Utils.listenEvent(document,'app:hide:message', (ev) => this.hideMessage());
        Utils.listenEvent(document,'app:show:loader', (ev) => this.showLoader());
        Utils.listenEvent(document,'app:hide:loader', (ev) => this.hideLoader());
        Utils.listenEvent(document,'app:open:dialog', (ev) => this.showDialog(ev));
        Utils.listenEvent(document,'app:close:dialog', (ev) => this.hideDialog(ev));


        // Focus first input
        // TODO: think about focus control
        //this.focusContent();

    }


    /**
     * Send an error log entry to the server
     *
     * @param {ErrorEvent} event
     */
    logError(event) {

        if (!App.baseUrl || !App.user || !App.user.role || App.user.role === 'guest') {
            return;
        }

        const errorMessage = event.message || 'Unknown error';
        const errorDetails = {
            name : event.error ? (event.error.name || 'Unknown error type') : 'Unknown error type',
            filename: event.filename || 'Unknown file',
            line: event.lineno || 0,
            column: event.colno || 0,
            stack: event.error ? event.error.stack : 'No stack trace available',
            timestamp: new Date().toISOString(),
            'Request URL': window.location.href,
            'Referer URL': document.referrer
        };

        const url = new URL('/users/track/error/' + encodeURIComponent(errorMessage), App.baseUrl);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(errorDetails)
        })
            .then(response => {
                if (response.ok) {
                    console.log('Sent error log to server');
                } else {
                    console.error('Failed to log error to server', response.statusText);
                }
            })
            .catch(error => {
                console.error('Error while logging error to server:', error);
            });
    }

    addWidget(widgetId, widget) {
        if (widgetId !== undefined) {
            this.widgets[widgetId] = widget;
        }
        return widget;
    }

    getWidget(widgetId) {
        if (widgetId !== undefined) {
            return this.widgets[widgetId] || undefined;
        }
    }

    clearWidget(widgetId) {
        let widget;
        if (widgetId !== undefined) {
            widget = this.getWidget(widgetId);
            delete this.widgets[widgetId];
        }
        return widget;
    }

    focusContent() {
        let contentElements = document.querySelectorAll(
            '.content-wrapper input, .content-wrapper a, .content-wrapper select, .content-wrapper button'
        );
        if (contentElements.length > 0) {
            contentElements[0].focus();
        }
    }

    showLoader() {
        this.loaderCount += 1;
        $('#loader').show();
    }

    hideLoader() {
        this.loaderCount -= 1;
        if (this.loaderCount <= 0) {
            this.loaderCount = 0;
            $('#loader').hide();
        }
    }

    /**
     * Show a flash message
     *
     * @param {string|Event} message The message or an event containing a message in the details
     * @param {string} status
     * @param {BaseFrame} containerWidget The container where the flash message should be shown
     * @param {String} msgId An ID that can be used to hide the message later
     */
    showMessage(message, status='error', containerWidget, msgId) {
        if (message instanceof Event) {
            status = message.detail.data.status || status;
            let errors = message.detail.data.errors || {};

            message = message.detail.data.msg;
            if (errors) {
              message = message + '<br>' + Object.entries(errors).flatMap(
                  ([key, values]) => values.map(value => `${key}: ${value}`)
              );
            }
        }

        if (containerWidget) {
            containerWidget.showMessage(message, status);
        } else {
            const flash = document.querySelector('.content-flash');
            if (flash) {
                let div = Utils.spawnFromString(`<div class="message ${status}">${message}</div>`);
                flash.replaceChildren(div);
            }
        }

        const loader = document.querySelector('#loader');
        if (loader && (status === 'error')) {
            loader.classList.add('error');
        }
    }

    /**
     * Event handler for flash message click. Hides the flash message.
     *
     * @param {Event} event
     */
    hideMessage(event) {
        if ((!event) || (event.target.tagName !== 'A')) {
            const flash = event ? event.target.closest('.message') : document.querySelector('.message');
            if (flash) {
                flash.classList.add('hidden');
            }

            const loader = document.querySelector('#loader');
            if (loader) {
                loader.classList.remove('error');
            }
        }
    }

    /**
     * Open a modal popup and wait for confirmation
     *
     * Example:
     * const areYouSure = await App.confirmAction('Are you sure you want to delete the section?');
     *
     * @param {string} message The prompt that needs confirmation
     * @return {Promise}
     */
    confirmAction(message) {
        return new Promise((resolve, reject) => {
            const options =
                {
                    message : message,
                    onConfirm: value =>  resolve(value)
                };

            App.confirmWindow.showData(options);
        });
    }

    /**
     * Return the content pane which contains the element.
     *
     * //TODO: remove redundancy with base.js: getContentPane()
     *
     * Used to determine whether the scope of the widget should be constrained to AJAX content.
     * See popups.js.
     * For document level widgets, this is the document,
     * for widgets within a popup, this is the popup element,
     * for widgets within the sidebar, this is the sidebar element.
     *
     * @param {HTMLElement}  element The contained element
     * @param {boolean} widget Return the widget or the HTML element?
     * @return {*|Document|HTMLElement|BaseWidget}
     */
    getContentPane(element, widget=true) {
        if (!element) {
            return document;
        }

        const frame = element.closest('.widget-content-pane');
        if (!widget) {
            return (frame && !frame.classList.contains('widget-content-pane-main')) ? frame : document;
        }
        else {
            const frameWidget = frame ? frame.widgets['content-pane'] : document;
            return frameWidget ? frameWidget : document;
        }
    }

    /**
     * Emit a custom event
     *
     * @param {HTMLElement} element
     * @param {string} name
     * @param {Object} data
     * @param {boolean} cancelable
     * @return {boolean}
     */
    emitEvent(element, name, data, cancelable=false) {
        let event = new CustomEvent(
            name,
            {
                bubbles: true,
                cancelable: cancelable,
                detail: {
                    data: data,
                    sender: this
                }
            }
        );

        return element.dispatchEvent(event);
    }

    /**
     * Get data from the API
     *
     * @param {string} url
     * @param {function} callback Callback with a data parameter
     *
     */
    fetch(url, callback) {
        App.ajaxQueue.add('api',
            {
                type: 'GET',
                url: url,
                dataType: 'json',
                beforeSend: function (xhr) {
                    App.showLoader();
                },
                success: function (data, textStatus, xhr) {
                    data.status = 'success';
                    callback(data);
                },
                error : function (xhr,  textStatus, errorThrown) {
                    // if aborted (see https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/abort)
                    if ((xhr.readyState === XMLHttpRequest.UNSENT) && (xhr.status === 0)) {
                        callback({'status':'abort'});
                    } else {
                        callback({'status':'error'});
                        App.showMessage(errorThrown, textStatus);
                    }
                },
                complete : function(xhr, textStatus) {
                    App.hideLoader();
                }
            }
        );
    }

    /**
     * Load HTML
     *
     * If the endpoint's HTML response contains an element
     * with the attribute data-snippet="message", the message will be extracted and displayed.
     *
     * @param {string} url
     * @param {function} callback Callback with a data parameter
     */
    fetchHtml(url, callback) {

        //TODO: use ajaxqueue
        if (this.xhr !== null) {
            this.xhr.abort();
        }

        App.showLoader();
        this.xhr = $.ajax({
            type: 'GET',
            url: url,
            dataType: 'html',
            success: (data, textStatus, xhr) => {
                callback(data);

                const message = Utils.extractSnippetText(data,'message');
                if (message) {
                    this.showMessage(message);
                }
            },
            error: (xhr, textStatus, errorThrown) => {
                // If aborted (see above, https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/abort)
                if ((xhr.readyState === XMLHttpRequest.UNSENT) && (xhr.status === 0)) {
                    return;
                }

                let errorMessage = Utils.extractSnippetText(xhr.responseText, 'error');
                if (!errorMessage) {
                    errorMessage = errorThrown;
                }

                this.showMessage(errorMessage, 'error');
            },
            complete: (xhr, textStatus)=> {
                this.hideLoader();
            }
        });
    }

    /**
     * Load URL and replace data snippets
     *
     * The endpoint should deliver HTML content with elements that carry a data-snippet attribute.
     * All such elements (e.g. with data-snippet="mysnippetname") are extracted and matching elements
     * in the container are replaced by the new data.
     *
     * In addition, messages can be triggered. If the endpoint's HTML response contains an element
     * with the attribute data-snippet="message", the message will be extracted and displayed.
     *
     * @param {string} url
     * @param {HTMLElement} container
     * @param {boolean} history Push the URL to history (true|false)
     * @param {boolean} updateNavigation Whether to update the navigation in the frame
     */
    loadDataSnippets(url, container, history=false, updateNavigation=false) {

        //TODO: use ajaxqueue
        if (this.xhr !== null) {
            this.xhr.abort();
        }

        App.showLoader();
        this.xhr = $.ajax({
            type: 'GET',
            url: url,
            dataType: 'html',
            success: (data, textStatus, xhr) => {
                if (history && window.history.pushState) {
                    window.history.pushState(url, "Epigraf", url);
                }

                const frame = this.getContentPane(container);
                this.replaceDataSnippets(data, container);

                // Messages
                const message = Utils.extractSnippetText(data,'message');
                if (message) {
                    this.showMessage(message);
                }

                // Popup and frame navigation elements
                if (updateNavigation && frame && frame.updateNavigation) {
                    frame.updateNavigation(data)
                }

                if (container) {
                    this.emitEvent(container,'epi:load:content');
                }

            },
            error: (xhr, textStatus, errorThrown) => {
                // If aborted (see above, https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/abort)
                if ((xhr.readyState === XMLHttpRequest.UNSENT) && (xhr.status === 0)) {
                    return;
                }

                let errorMessage = Utils.extractSnippetText(xhr.responseText, 'error');
                if (!errorMessage) {
                    errorMessage = errorThrown;
                }

                this.showMessage(errorMessage, 'error');
            },
            complete: (xhr, textStatus) => {
                this.hideLoader();
            }
        });
    }

    /**
     * Update snippets from AJAX calls
     *
     * Add data-snippet="mysnippetname" to html elements that should be replaced.
     *
     * TODO: remove / finish widgets in replaced elements. JJ: Already done?
     * TODO: replace jquery by vanilla js
     *
     * @param {string} data
     * @param {HTMLElement|document} container Only replace snippets within this element.
     *                                      If the container is the document, only snippets within
     *                                      widget-content-pane-main and the footer element are replaced.
     */
    replaceDataSnippets(data, container) {
        const snippets = Utils.querySelectorAllAndSelf(
            Utils.spawnFromString(data,undefined, false),
            '[data-snippet]'
        );

        if (!snippets) {
            return;
        }

        if ((typeof container === "undefined")  || (container === document) ) {
            container = [
                document.querySelector('.widget-content-pane-main'),
                document.querySelector('.page-wrapper > footer')
            ];
        }

        if (!Array.isArray(container)) {
            container = [container];
        }

        snippets.forEach( (elm) => {
            const selector = '[data-snippet="' +elm.dataset.snippet + '"]';
            const snip_old = Utils.querySelectorAndSelf(container, selector);

            if (snip_old) {
                if (typeof this.finishWidgets === 'function') {
                    this.finishWidgets(snip_old);
                }
                snip_old.replaceWith(elm);
                if (typeof this.initWidgets === 'function') {
                    this.initWidgets(elm);
                }
                this.emitEvent(snip_old, 'epi:replace:content', {newTarget: elm});
            }
        });
    }

    // Update snippets from AJAX calls: add below
    appendDataSnippets(data) {
        var snippets = $(data).find('[data-snippet]').addBack('[data-snippet]');

        snippets.each(function (idx, elm) {
            const snip_old = $('[data-snippet="' + $(this).data('snippet') + '"]');
            snip_old.after(this);
        });
    }

    /**
     * Popup link handler
     *
     * @param {Event} event
     * @returns {boolean}
     */
    openPopupLink(event) {
        // Single clicks only
        if (event.detail > 1) {
            return;
        }

        const a = event.target.closest('a');
        let url = a.href;
        if (url && !event.ctrlKey) {

            const size = a.dataset.popupSize;
            let modal = a.dataset.popupModal;
            modal = (modal === 'true') || (modal === '1');

            App.openPopup(url,{external:true, modal:modal, focus: modal, size: size});

            event.preventDefault();
            return false;
        }
    }

    /**
     * Frame link handler
     *
     * @param event
     * @returns {boolean}
     */
    openDetailLink(event) {
        // Single clicks only
        if ((event.detail > 1) || event.ctrlKey ) {
            return;
        }

        // Perform default action on ctrl click
        if (event.ctrlKey) {
            return;
        }

        const container = event.target.closest('.ui-dialog, .sidebar');
        const a = event.target.closest('a');

        // Prevent popups in select list (e.g. when selecting an external article in an annotation)
        if (container && a.dataset.linkwrapper) {
            event.preventDefault();
            return false;
        }

        // Redirect clicks in sidebars or popups to popups
        if (container) {
            App.openPopupLink(event);
            event.preventDefault();
            return false;
        }

        let url = a.href;

        // Get the view and tab link from tables
        if (a.dataset.linkwrapper) {
            const tableWidget = App.findWidget(a, 'table');
            if (tableWidget) {
                const node = a.closest('.node');
                url = tableWidget.getFirstAction(node) || url;
            }
        }

        if (url) {
            const target = a.dataset.frameTarget || 'details';
            const caption = a.dataset.frameCaption || 'Details';
            App.openDetails(url, {external:true, frameTarget: target, frameCaption: caption, force: false});
            event.preventDefault();
            return false;
        }
    }

   /**
     * Show data loaded from an URL in a frame (instead of a popup)
     *
     * @param data URL to be loaded. Can be skipped
     * @param options Object with the following keys
     *                - url
     *                - actions
     *
     * @returns {TabFrame}
     */
    openDetails(data, options) {

        // Merge options
        if (typeof data === 'string') {
            options = options || {};
            options.url = data;
        }
        else if (typeof data === 'object') {
            options = options || {};
            options.element = data;
        }
        else {
            options = data || options || {};
        }

        // Get widget
        const tabsheetsWidget = App.findWidget(App.sidebarright.widgetElement,'tabsheets');
        if (!tabsheetsWidget) {
            return;
        }

        options.frameTarget = options.frameTarget || 'details';
        options.frameCaption = options.frameCaption || 'Details';
        const tabsheet = tabsheetsWidget.createTab(options.frameTarget, options.frameCaption);
        const detailWidget = App.createWidget(tabsheet,'frame');
        detailWidget.showData(options);

        return detailWidget;
    }

    /**
     * Open sidebar and tabsheet
     *
     * @param frame
     * @return {HTMLElement} The tabsheet element
     */
    activateTabsheet(frame) {
        App.sidebarright.showSidebar();
        const tabsheetsWidget = App.findWidget(App.sidebarright.widgetElement,'tabsheets');
        return tabsheetsWidget.showTab(frame);
    }
}

window.App = new EpiApp();
