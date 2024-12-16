/*
 * Frames - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import Utils from '/js/utils.js';
import {i18n}  from '/js/lingui.js';
import {BaseForm} from '/js/base.js';

/**
 * Base class for the main frame, sidebar frame and popups
 *
 * Handles request workflow. The workflow depends on the starting point:
 *
 * 1) View entity in the main frame
 *    - add -> on success, redirected to view by the controller
 *    - edit -> on success, redirected to view by the controller
 *    - delete -> on success, no redirect, empty page
 * 2) Open entity in the sidebar
 *    - add -> on success, redirected to view by the controller
 *    - edit -> on success, redirected to view by the controller
 *    - delete -> on success, no redirect, empty page
 * 3) Open entity in a popup
 *    - add -> on success, close the popup (redirected to view by the controller)
 *    - edit -> on success, close the popup (redirected to view by the controller)
 *    - delete -> on success, close the popup (no redirect, empty page)
 *
 * Each frame has the following elements:
 *
 * - this.titlePane
 * - this.contentPane
 * - this.buttonPane
 * - this.footerPane
 * (- this.dialog @deprecated)
 *
 * The widget element holds this.contentPane. The other elements
 * may be placed elsewhere on the page.
 *
 * Use the following methods to interact with the frame:
 *
 * - showButton()
 * - hideButton()
 * - updateNavigation()
 *
 */
export class BaseFrame extends BaseForm {

    constructor(element, name, parent) {
        super(element, name, parent);

        this.titlePane = undefined;
        this.contentPane = undefined;
        this.buttonPane = undefined;
        this.footerPane = undefined;
        this.actionButtons = undefined;
        this.options = {};
        this.formOptions = {};
        this.currentUrl = undefined;
        this.isSaving = false;
    }


    /**
     * Widget initialization
     *
     * The method is called once after all widgets were constructed and are ready to be used.
     */
    initWidget() {
        this.listenEvent(this.widgetElement,'app:show:message', event => this.showMessage(event));
        this.listenEvent(this.widgetElement,'app:hide:message', event => this.hideMessage(event));
    }

    /**
     * Show content
     *
     * ### Options
     *  - width The width of the popup window or the sidebar in pixels, defaults to 800
     *  - height The window height in pixels, defaults to 600 (only for popups)
     *  - url The page to load. Either provide a URL or an element.
     *  - element The element to show. Either provide a URL or an element.
     *  - clone true|false In case you provide an element,
     *          determine whether it will be cloned before it is inserted
     *          into the popup or frame window. Defaults to false.
     *  - modal true|false (only for popups)
     *  - ajaxButtons true|false Whether to extract action buttons from AJAX responses.
     *                           Defaults to true.
     *  - dialogButtons array. Standard buttons to create.
     *           Allowed values: "cancel", "remove", "apply"
     *  - external true|false Create a button to open the current URL in a new tab
     *  - selected The selected value
     *  - input A callback with parameters id and caption,or an input element for choosing values.
     *    Clicks onto tr.choose.value trigger the input callback.
     *    Clicks onto the remove button trigger the input callback with a null value.
     *  - frameTarget
     * - focus true|false Whether to focus the first input element
     * - force true|false By default, the window is forced to open, regardless of the user settings. Set to false to respect the user settings.
     * - title string The title of the frame or window
     *
     * @param options Options object
     * @returns this
     */
    showData(options) {

        // Overwrite default options
        this.options = {...(this.defaults), ...options};

        // Init and open window
        this.openWindow(options.frameTarget || 'details', options.force);

        // Load URL or show element
        if (options.url !== undefined) {
            this.loadUrl(options.url, options);
        } else if (options.element !== undefined) {
            this.loadElement(options.element, options);
        }

        return this;
    }

    /**
     * Show or hide the loader
     *
     * @param {boolean} show
     */
    toggleLoader(show = true) {
        if (!this.widgetElement) {
            return;
        }

        if (show) {
            let loader = this.widgetElement.querySelector('.frame-loader, .loader');
            if (!loader) {
                loader = Utils.spawnFromString('<div class="loader frame-loader"></div>');
                if (this.contentPane) {
                    this.contentPane.appendChild(loader);
                } else {
                    this.widgetElement.appendChild(loader);
                }
            }

        } else {
            const loader = this.widgetElement.querySelector('.frame-loader, .loader');
            if (loader) {
                loader.remove();
            }
        }
    }

    /**
     * Show or hide the loader and clear data
     * Override in child classes
     *
     * @param {boolean} showLoader
     */
    clearData(showLoader) {
        this.unlock();
        this.showLoader(showLoader);
    }

    clearWindow() {
        this.clearData();
        this.updateTitle();
        this.updateTitleButtons();
        this.updateToolbar();
        this.updateActionButtons();
    }

    /**
     * Abstract method, override in child classes
     */
    openWindow() {

    }

    /**
     * Abstract method, override in child classes
     *
     * @param {boolean} canceled Whether closing was triggerd by a cancel button
     * @return {boolean} Whether the window was closed
     */
    closeWindow(canceled = false) {
        return false;
    }

    showLoader() {
        this.toggleLoader(true);
    }

    hideLoader() {
        this.toggleLoader(false);
    }

    /**
     * Show a flash message in the pane
     *
     * @param {string|CustomEvent} message
     * @param {string} status
     */
    showMessage(message, status) {
        let event;
        if (message instanceof CustomEvent) {
            event = message;
            status = event.detail.data.status || status;
            message = event.detail.data.msg;
        }

        // TODO: rename content-flash to frame-message
        let flash = this.widgetElement.querySelector('.content-flash');
        if (!flash && !event) {
            flash = document.querySelector('.content-flash');
        }

        if (flash) {
            let div = Utils.spawnFromString(`<div class="message ${status}">${message}</div>`);
            flash.replaceChildren(div);
        }

        if (flash && event) {
            event.stopPropagation();
        }
    }


    hideMessage(event) {

    }

    /**
     * Create button array from ajax content with corresponding event handlers
     *
     * ### Options
     * - ajaxButtons Array of roles to filter buttons
     *
     * @param {Array} actions Array of elements
     * @param {Object} options
     * @returns {Array}
     */
    attachButtons(actions, options) {
        const self = this;
        if (actions === undefined) {
            return [];
        }

        // Default options
        options = options || {};

        const filterRoles = options.ajaxButtons;
        if (filterRoles instanceof Array) {
            actions = actions.filter((button) => filterRoles.includes(button.dataset.role));
        }

        return actions.map(
            function (button) {
                const role = button.dataset.role;
                let classes = 'role-' + role;
                const shortcuts = button.dataset.shortcuts;
                if (shortcuts) {
                    classes += ' widget-shortcut';
                }

                return {
                    text: button.textContent,
                    title: button.getAttribute('title', button.textContent),
                    ariaLabel: button.getAttribute('aria-label', button.textContent),
                    class: classes,
                    role: role,
                    shortcuts: shortcuts,
                    ariaKeyshortcuts: button.dataset.shortcuts,
                    click: function (event) {
                        if (!self.emitEvent('epi:click:button', {button: button, role: role}, true)) {
                            event.preventDefault();
                            return;
                        }

                        // Submit form
                        if (
                            (role === 'submit') || (role === 'save') ||
                            (role === 'cancel') || (role === 'delete')
                        ) {

                            let form;
                            if (button.hasAttribute('form')) {
                                form = document.getElementById(button.getAttribute('form'));
                            } else {
                                form = button.form;
                            }

                            if (form) {
                                button.setAttribute('disabled', 'disabled');

                                // Save & stay vs. save & close
                                options.focus = false;
                                options.keepopen = (role === 'save');
                                self.formOptions = options;

                                if ((role === 'submit') || (role === 'save')) {
                                    self.submitForm(event);
                                } else if (role === 'delete') {
                                    self.unlockForm(form.dataset.deleteUrl, true);
                                } else if (role === 'cancel') {
                                    self.unlockForm(form.dataset.cancelUrl, true);
                                }
                            } else if (role === 'cancel') {
                                self.closeWindow(true);
                            }
                            return;
                        }

                        // If not submitted or closed...
                        if (button.tagName === 'A') {
                            let url = event.target.dataset.href || button.getAttribute('href');
                            let options = {};
                            if (button.dataset.target) {
                                options.target = button.dataset.target;
                            }
                            self.loadUrl(url, options);
                        }
                    }
                };
            }
        );
    }

    /**
     * Create button element inside a container
     *
     * @param buttons The result of attachButtons()
     * @param {HTMLElement} container
     * @param {boolean} prepend
     */
    addButtons(buttons, container, prepend = false) {
        buttons.forEach((button) => {
            const elm = document.createElement('button');
            elm.textContent = button.text || '';
            elm.classList.add(...(button.class || '').split(' '));
            elm.onclick = button.click;
            elm.dataset.role = button.role;
            // elm.form = button.form;
            elm.setAttribute('title', button.title || '');
            elm.setAttribute('aria-label', button.ariaLabel || '');

            if (button.shortcuts) {
                elm.setAttribute('aria-keyshortcuts', button.ariaKeyshortcuts || '');
                elm.dataset.shortcuts = button.shortcuts;
                elm.classList.add('widget-shortcut');
            }

            if (prepend) {
                container.prepend(elm);
            } else {
                container.appendChild(elm);
            }

        });
        App.initWidgets(container);
    }

    showButton(role, caption, url) {
        if (this.buttonPane) {
            const button = this.buttonPane.querySelector('[data-role="' + role + '"],.role-' + role);
            Utils.show(button, caption);
            if (url) {
                if (button.tagName === 'A') {
                    button.setAttribute('href', url);
                } else {
                    button.dataset.href = url;
                }

            }
        }
    }

    hideButton(role) {
        if (this.buttonPane) {
            const button = this.buttonPane.querySelector('[data-role="' + role + '"], .role-' + role);
            Utils.hide(button);
        }
    }

    clickButton(role) {
        if (!this.buttonPane) {
            return false;
        }
        const button = this.buttonPane.querySelector('[data-role="' + role + '"], .role-' + role);

        if (button) {
            button.click();
            return true;
        }
        return false;
    }

    setButtonUrl(role, url) {
        if (this.buttonPane) {
            const button = this.buttonPane.querySelector('[data-role="' + role + '"], .role-' + role);
            if (button) {
                if (button.tagName === 'A') {
                    button.setAttribute('href', url);
                } else {
                    button.dataset.href = url;
                }
            }

        }
    }

    /**
     * Abstract method, override in child classes
     *
     * @param title
     */
    updateTitle(title) {

    }

    /**
     * Abstract method, override in child classes
     *
     * @param actions
     */
    updateTitleButtons(actions) {

    }

    /**
     * Abstract method, override in child classes
     *
     * @param {HTMLElement} toolbarElement
     */
    updateToolbar(toolbarElement) {

    }

    /**
     * Create action buttons for popups and frames
     *
     * ### Options
     * - ajaxButtons: Whether to create buttons from the AJAX content:
     *                true: Create all AJAX buttons.
     *                false: Create no AJAX buttons.
     *                'exclusive': Create only buttons from the AJAX content and skip default buttons
     *                             (dialogButtons option). The default buttons are always created
     *                             if the AJAX content does not contain any buttons.
     *                array of roles: Create only buttons with the given roles from the AJAX content.
     * - dialogButtons An object with button names as keys and button properties as objects,
     *                 each with the properties 'text', 'title', 'ariaLabel' and 'handler'.
     *
     * @param {Array} actions An array of a-elements and button-elements
     * @param {Object} options
     */
    updateActionButtons(actions, options) {
        let buttons = [];

        // Add action buttons retrieved by AJAX requests
        if (this.options.ajaxButtons) {
            buttons = this.attachButtons(actions, options);
        }

        // Add action buttons passed in the options
        if (this.options.dialogButtons) {
            if ((this.options.ajaxButtons !== 'exclusive') || !buttons.length) {
                for (const [key, value] of Object.entries(this.options.dialogButtons)) {
                    buttons.push({
                        text: value.text,
                        title: value.title,
                        ariaLabel: value.ariaLabel,
                        class: 'role-' + key,
                        click: event => value.handler(this)
                    });
                }
            }
        }
        this.actionButtons = buttons;

        const form = this.contentPane.querySelector('form');
        this.attachForm(form);
    }

    /**
     * After loading new data, scroll to the selected element
     */
    updateSelection() {
        // Scroll to selected value in list
        // TODO: inherit an own class for select popups?
        const selected = this.widgetElement.querySelector('.row-selected');
        // TODO: emit select event and listen in the table widget so that tables can focus the selected row
        if (selected) {
            Utils.scrollIntoViewIfNeeded(selected, selected.parentElement, true, 'y');
        } else {
            $(this.widgetElement).scrollTop(0);
        }
    }
    /**
     * After loading new data, update title and buttons
     *
     * @param {string|HTMLElement} data Data containing title and button elements
     * @param {Object} options
     */
    updateNavigation(data, options) {
        // Convert to DOM elements
        if (Utils.isString(data)) {
            data = Utils.spawnFromString(data, undefined, false);
        }

        // Title
        let breadcrumbs = data.querySelector('nav.breadcrumbs');
        if (breadcrumbs) {
            this.updateTitle(breadcrumbs.textContent);
            breadcrumbs.remove();
        } else if (options.title) {
            this.updateTitle(options.title);
        }

        // Title buttons
        const titleActions = Utils.extractElements(
            data,
            '[data-snippet="actions-top"]',
            'a, button'
        );
        this.updateTitleButtons(titleActions);

        // Toolbar
        const toolbarElement =  data.querySelector('div.content-toolbar');
        this.updateToolbar(toolbarElement);

        // Action buttons
        const bottomActions = Utils.extractElements(
            data,
            '[data-snippet="actions-bottom"], [data-snippet="actions-content"]',
            'a, button'
        );
        this.updateActionButtons(bottomActions, options);

        // Remove footer if empty
        const footer = data.querySelector('footer');
        if (footer && footer.innerHTML.trim() === '') {
            footer.remove();
        }
    }

    /**
     * Shows the result of an ajax request in the frame or popup.
     *
     * If the result of the request is an object with a response value "close",
     * the frame will be closed.
     *
     * All listening widgets will be informed about the new data. If the request changed data,
     * (post request within a frame), this can be used to update other widgets, for example
     * a table that shows a record that was edited within the frame.
     *
     * Some content is extracted from the response:
     * - Breadcrumbs in the response will be used as header in the frame or window.
     * - Help buttons will be added to the frame or window
     * - Buttons inside of bottom and content action snippets will be added to the frame or window.
     *   [data-snippet="actions-bottom"], [data-snippet="actions-content"]
     * - The updateActions
     *
     * ### Options
     * - focus true|false Whether to focus the first input element
     * - title string The title of the frame or window
     *
     * @param {Element|String} data
     * @param {Object} options
     * @returns {boolean}
     */
    updatePage(data, options) {
        // Default options
        options = options || {focus: true, openDropdown: false, title: ''};

        // Content
        this.clearData();
        if (data instanceof Element) {
            this.contentPane.replaceChildren(data);
        } else {
            this.contentPane.innerHTML = data;
        }

        this.updateSelection();
        this.updateNavigation(this.widgetElement, options);

        if (typeof App.initWidgets === 'function') {
            App.initWidgets(this.widgetElement);
        }

        // Fixed sections
        if (this.footerPane) {
            this.footerPane.innerHTML = '';
            const fixedSections = Array.from(this.widgetElement.querySelectorAll('[data-position=fixed]'));
            fixedSections.forEach((elm, idx) => {
                let form = elm.closest('form');
                if (form) {
                    Array.from(elm.querySelectorAll('input')).forEach(
                        (input) => {
                            input.setAttribute('form', form.getAttribute('id'));
                        }
                    );
                }
                this.footerPane.appendChild(elm);
            });
        }

        // Load callback
        if (options.onLoad) {
            options.onLoad(this, data, options);
        }

        // Focus element
        if (options.focus) {
            // Find focus element
            let input = this.widgetElement.querySelector('[autofocus]');
            input = !input ? this.widgetElement.querySelector('select, input[type=text], .widget-xmleditor') : input;

            if (input) {
                // Set focus
                input.removeAttribute('autofocus');
                input.focus();

                // Select input text
                if (typeof input.select === 'function') {
                    input.select();
                }
            }
        }

    }

    /**
     * Show element in frame
     *
     * @param {HTMLElement} element
     * @param {Object} options
     */
    loadElement(element, options={}) {
        options.element = element;
        if (options.clone || false) {
            options.element = options.element.cloneNode(true);
        }
        this.updatePage(options.element, options);
    }

    /**
     * Load new content from the URL into the frame
     *
     * ### options
     * - lockid Add lockid to send it in a POST request, which unlocks the entity
     * - target Target frame or window:
     *          external (opens a new browser tab)
     *          main (opens the URL in the current window)
     *          Leave empty to load asynchronously into the current frame.
     * - All options are passed to updatePage().
     * @param {string} url The URL to load
     * @param {object} options
     */
    loadUrl(url, options = {}) {
        if (options.target === 'external') {
            window.open(url);
            return;
        } else if (options.target === 'main') {
            window.location = url;
            return;
        }
        //if (button.classList.contains('external')) {
        //} else if (button.classList.contains('noframe')) {

        this.currentUrl = url;
        this.showLoader();

        // TODO: fetch-API benutzen. Folgende Zeilen sind ein nicht funktionaler Anfang
        // TODO: How can I make this asynchronous...?
        // try {
        //     response = await fetch(url);
        //     data = await response.text();
        //
        //     self.updatePage(data);
        // } catch (error) {
        //     self.dialog.html(error);
        // }
        // App.hideLoader();

        // Issue request
        let requestData = null;
        let requestType = 'GET';
        let contentType = false;
        if (options.lock) {
            requestData = $.param({lock: options.lock});
            requestType = 'POST';
            contentType = 'application/x-www-form-urlencoded';
        }

        const self = this;
        $.ajax({
            type: requestType,
            url: url,
            data: requestData,
            dataType: 'html',  // We expect HTML as response
            async: true,
            retryLimit: 3,
            tryCount: 0,
            contentType: contentType,
            processData: false, // payload is already encoded
            success: function (data) {
                self.updatePage(data, options);
            },
            error: function (xhr, status, error) {
                const errorContent = 'Error loading data:' + xhr.responseText;
                self.emitEvent('app:show:message', {'msg' : errorContent, 'status': 'error'});
                self.updatePage(errorContent, options);

                // Try again
                if (xhr.status === 403) {
                    let request = this;

                    request.tryCount++;

                    App.user.showLogin({
                        onLogin: function () {
                            if (request.tryCount <= request.retryLimit) {
                                $.ajax(request);
                            }
                        }
                    });
                }
            },
            complete: function(xhr, textStatus) {
                self.isSaving = false;
                self.hideLoader();
            }
        });
    }
}

/**
 * Main content
 */
export class MainFrame extends BaseFrame {
    constructor(options) {

        const wrapper = document.querySelector('.content-content');
        super(wrapper, 'content-pane');

        this.buttonPane = undefined;

        if (wrapper) {
            wrapper.classList.add('widget-content-pane');
            wrapper.classList.add('widget-content-pane-main');
            this.buttonPane = wrapper.querySelector('.actions-content');
            this.contentPane = wrapper;

            const form = this.widgetElement.querySelector('.widget-entity form, .widget-document form');
            this.attachForm(form);
        }
    }

    /**
     * After loading new data, update buttons
     *
     * @param {string|HTMLElement} data Data containing title and button elements
     */
    updateNavigation(data, options) {
        if (this.buttonPane) {
            App.replaceDataSnippets(data, this.buttonPane);
        }

        const form = this.widgetElement.querySelector('form');
        this.attachForm(form);
    }

    /**
     * Load the page in a new tab or in the current window
     *
     * ## Options
     * - message A message to show while loading the page
     *
     * @param {string|URL} url
     * @param {Object} options
     */
    loadUrl(url, options = {}) {
        if (options.target === 'external') {
            window.open(url);
            return;
        } else  {

            if (options.message) {
                this.emitEvent('app:open:dialog', {'message': options.message, 'loader': true, 'id': 'reload-dialog','delay':0});
            }

            // URLs with a hash fragment can only be loaded using window.location.reload();
            const currentUrlWithoutHash = window.location.href.split('#')[0];
            if (typeof url !== 'string') {
                url = url.toString();
            }
            const newUrlWithoutHash = url.split('#')[0];

            if (currentUrlWithoutHash === newUrlWithoutHash) {
                window.location.href = url;
                window.location.reload();
            } else {
                window.location.href = url;
            }

            return;
        }
    }

    /**
     * Update the page
     *
     * @implements BaseForm.onSaveProceed()
     * @param {string} data Data returned from the request
     * @param {XMLHttpRequest} xhr The XMLHttpRequest object
     */
    onSaveProceed(data, xhr) {

        // Call parent method
        if (!super.onSaveProceed(data, xhr)) {
            return;
        }

        // Open in new tab
        // TODO: does not work yet for new articles
        const responseUrl = new URL(xhr.responseURL);
        let proceedUrl = responseUrl.searchParams.get('open');

        if (!proceedUrl) {
            proceedUrl = this.formElement.dataset.proceedUrl;
            proceedUrl = new URL(
                proceedUrl ? Utils.decodeHtmlAttribute( proceedUrl) : xhr.responseURL,
                App.baseUrl
            );
        }

        const msg = i18n.t('Reloading document');
        this.loadUrl(proceedUrl, {message : msg});
    }
}

/**
 * Sidebar content in the default tabsheet
 *
 * @param {HTMLElement} element A tabsheet
 *
 */
export class TabFrame extends BaseFrame {
    constructor(element, name, parent) {

        super(element, name, parent);

        this.widgetElement.classList.add('widget-content-pane');

        const titleElement = Utils.spawnFromString('<div class="frame-title">' +
            '<div class="frame-title-caption"></div>' +
            '<div class="frame-title-manage">' +
            '<button class="btn-close" title="Close" aria-label="Close"></button>' +
            '</div>' +
            '</div>'
        );

        this.widgetElement.appendChild(titleElement);
        this.titlePane = titleElement.querySelector('.frame-title-caption');
        this.titleButtons = titleElement.querySelector('.frame-title-manage');

        this.toolbarPane = Utils.spawnFromString('<div class="frame-toolbar"></div>')
        this.widgetElement.appendChild(this.toolbarPane);

        this.contentPane = Utils.spawnFromString('<div class="frame-content"></div>');
        this.widgetElement.appendChild(this.contentPane);

        this.footerPane = Utils.spawnFromString('<div class="frame-footer"></div>');
        this.widgetElement.appendChild(this.footerPane);

        this.buttonPane = Utils.spawnFromString('<div class="frame-buttons actions-bottom"></div>');
        this.widgetElement.appendChild(this.buttonPane);


        this.isClosing = false;

        this.defaults = {
            title: "",
            width: 800,
            ajaxButtons: true,
            external: false,
            dialogButtons: {},
            focus: true
        };

        this.options = {};
        this.currentUrl = undefined;
        this.closeButton = undefined;
        this.openExtButton = undefined;
    }

    /**
     * Open the sidebar and activate the named tab
     *
     * @param {String} frame
     * @param {boolean} force Whether to force open (true) or to respect the user settings (false).
     */
    openWindow(frame = 'default', force=true) {
        // Clear
        // TODO: Smooth the tab: Only clear if the frame will come from nowhere:
        //       - sidebar is closed
        //       - tab is not visible
        //       Otherwise delay the loading of the loader
        //if (!App.sidebarright.isVisible()) {
            this.clearWindow();
        //}

        // Open
        this.isClosing = false;
        App.sidebarright.showSidebar(force);

        const tabsheetsWidget = this.getWidget(this.widgetElement, 'tabsheets');
        if (tabsheetsWidget) {
            tabsheetsWidget.showTab(frame, 'last');
        }

    }

    /**
     * Close the frame if not requested by cancel button
     *
     * @param {Boolean} canceled
     */
    closeWindow(canceled) {
        if (this.isClosing) {
            return true;
        }

        // Call onClose callback
        if (this.options.onClose) {
            this.options.onClose(this);
        }

        if (canceled) {
            return false;
        }
        this.isClosing = true;


        this.clearData();
        App.sidebarright.hideSidebar();
    }


    /**
     * Set the frame title
     *
     * @param title
     */
    updateTitle(title) {
        this.titlePane.innerHTML = title || '';
    }

    /**
     * Add close and open buttons to the title bar
     *
     * When initializing the class, set the option "external" to true to show the open button.
     * Set the option "external" to a valid URL to open it instead of the current URL.
     *
     * @param actions
     */
    updateTitleButtons(actions) {

        if (this.options.external) {
            if (this.openExtButton === undefined) {
                this.openExtButton = Utils.spawnFromString(
                    '<button class="btn-open" '
                    + 'title="' + i18n.t("Open in new tab") + '" '
                    + 'aria-label="' + i18n.t("Open in new tab") + '"></button>'
                );
                this.titleButtons.appendChild(this.openExtButton);
            }
            this.openExtButton.dataset.targetUrl = this.options.external !== true ? this.options.external : this.currentUrl;
            Utils.toggle(this.openExtButton, this.options.external !== false);
        }

        if (this.closeButton === undefined) {
            this.closeButton = Utils.spawnFromString(
                '<button class="btn-close" '
                + 'title="' + i18n.t('Close') + '" '
                + 'aria-label="' + i18n.t('Close') + '"></button>'
            );
            this.titleButtons.appendChild(this.closeButton);
        }

        if ((this.options.ajaxButtons) && this.titlePane) {
            const buttons = this.attachButtons(actions);
            this.addButtons(buttons, this.titlePane, true);
        }

        super.updateTitleButtons(actions);
    }

    /**
     * Add toolbar
     *
     * @param {HTMLElement} toolbarElement
     */
    updateToolbar(toolbarElement) {
        if (toolbarElement) {
            this.toolbarPane.replaceChildren(toolbarElement);
        } else {
            this.toolbarPane.innerHTML = '';
        }
    }

    /**
     * Add buttons to the button pane
     *
     * @param actions
     * @param options
     * @return buttons
     */
    updateActionButtons(actions, options) {
        super.updateActionButtons(actions, options);
        this.buttonPane.innerHTML = '';
        this.addButtons(this.actionButtons, this.buttonPane);
        this.listenButtons();
    }

    /**
     * Clear the frame content
     * TODO: delay clearing for smooth transitions
     *
     * @param {boolean} showLoader
     * @param {boolean} removePanes Whether to remove the panes (true) or just clear them (false)
     */
    clearData(showLoader, removePanes = false) {

        // Remove the title buttons
        if (this.openExtButton) {
            this.openExtButton.remove();
            this.openExtButton = undefined;
        }

        if (this.closeButton) {
            this.closeButton.remove();
            this.closeButton = undefined;
        }

        // Clear the content
        if (this.widgetElement) {
            this.emitEvent('epi:clear:widgets');

            // Clear all panes
            if (removePanes) {
                this.titlePane.remove();
                this.titleButtons.remove();
                this.toolbarPane.remove();
                this.footerPane.remove();
                this.buttonPane.remove();
                this.contentPane.remove();
            } else {
                this.titlePane.innerHTML = '';
                this.titleButtons.innerHTML = '';
                this.toolbarPane.innerHTML = '';
                this.footerPane.innerHTML = '';
                this.buttonPane.innerHTML = '';
                this.contentPane.innerHTML = '';
            }
        }

        super.clearData(showLoader);
    }

    /**
     * Update the page
     *
     * @implements BaseForm.onSaveProceed()
     * @param {string} data Data returned from the request
     * @param {XMLHttpRequest} xhr The XMLHttpRequest object
     */
    onSaveProceed(data, xhr) {

        // Call parent method
        if (!super.onSaveProceed(data, xhr)) {
            return;
        }

        // Open in new tab
        const responseUrl = new URL(xhr.responseURL);
        let proceedUrl = responseUrl.searchParams.get('open');

        if (proceedUrl) {
            window.setTimeout(function () {
                window.open(proceedUrl);
            }, 1000);
            this.updatePage(data, this.formOptions);
        } else if (this.formElement && this.formElement.dataset.proceedUrl) {
            proceedUrl = new URL(Utils.decodeHtmlAttribute(this.formElement.dataset.proceedUrl), App.baseUrl);
            const msg = i18n.t('Reloading document');
            this.loadUrl(proceedUrl, {message: msg});
        } else {
            this.updatePage(data, this.formOptions);
        }
    }

}


/**
 * Popup window
 *
 * ### Options
 * - closeIcon The icon to use for the close button (e.g. "ui-icon-closethick")
 *
 * @param {Object} options
 */
export class PopupWindow extends BaseFrame {
    constructor(options) {
        super();

        this.dialog = false;
        this.isClosing = false;
        this.isInitialized = false;
        this.isCollapsed = false;

        this.defaults = {
            title: "",
            width: 800,
            height: 600,
            autosize: true,
            modal: true,
            ajaxButtons: true,
            external: false,
            valueKey: 'id',
            dialogButtons: {},
            focus: true
        };

        this.options = options;
        this.currentUrl = undefined;
        this.buttonPane = undefined;
        this.openExtButton = undefined;
        this.contentPane = undefined;
    }


    /**
     * Create the window markup and a dialog instance
     * (if not already created)
     */
    initWindow() {
        if (!this.isInitialized) {
            const popupWindow = Utils.spawnFromString('<div class="popup-window"></div>');
            document.body.appendChild(popupWindow);

            this.attachElement(popupWindow, 'content-pane');
            this.initWidget();
            this.widgetElement.classList.add('widget-content-pane');
            this.contentPane = this.widgetElement;

            const resizeListener = event => this.resizeWindow(event);

            // Create jQuery UI dialog
            const self = this;
            this.dialog = $(popupWindow);

            this.dialog.dialog({
                autoOpen: false,
                maxHeight: 800,
                position: {my: "center", at: "center", of: window},
                create: function(event, ui) {
                    if (self.options.closeIcon) {
                        $(".ui-dialog-titlebar-close span", $(this).dialog("widget"))
                            .removeClass("ui-icon-closethick")
                            .addClass(self.options.closeIcon);
                    }
                },
                open: function (event, ui) {
                    window.addEventListener("resize", resizeListener);
                    self.listenEvent(self.widgetElement, 'keypress', (event) => self.onKeyPress(event));
                    self.dialog.focus();
                    $(self.buttonPane).find(':tabbable').eq(-1).focus();
                },
                close: function (event, ui) {
                    window.removeEventListener("resize", resizeListener);
                    self.closeWindow();
                    self.clearWidget();
                    self.dialog.remove();
                    self.dialog = false;
                    self.isInitialized = false;
                }
            });

            this.isInitialized = true;
        }
    }

    openWindow() {

        // Init window
        this.initWindow();

        // Update popup options
        this.dialog.dialog("option", this.options);

        // Title & buttons
        this.clearWindow();

        // Open
        this.isClosing = false;
        if (!this.dialog.dialog("isOpen")) {
            this.dialog.dialog({
                width: this.options.width || 800,
                height: this.options.height || 600
            });
            this.dialog.dialog("open");
        }

        // Watch reload events (e.g. from the UploadWidget / dropzone)
        if (!this.listenerReload) {
            this.listenerReload = event => this.showData(this.options);
            this.listenEvent(this.widgetElement, 'epi:reload:page', this.listenerReload);
        }
    }

    resizeWindow(event) {
        const docHeight = document.documentElement.clientHeight;
        const docWidth = document.documentElement.clientWidth;

        const winHeight = this.dialog.dialog("option", "height");
        const winWidth = this.dialog.dialog("option", "width");

        if (docHeight < winHeight) {
            this.dialog.dialog("option", "height", docHeight);
        }

        if (docWidth < winWidth) {
            this.dialog.dialog("option", "width", docWidth);
        }
    }

    collapseWindow(event) {
        this.isCollapsed = !this.isCollapsed;

        // Collapse
        if (this.isCollapsed) {
            this.defaults.height = this.dialog.dialog("option", "height");
            Utils.toggle(this.contentPane, false);
            // this.dialog.dialog("option", "height", "auto");
        }
        // Expand
        else {
            // this.dialog.dialog("option", "height", this.defaults.height);
            Utils.toggle(this.contentPane, true);
        }
    }

    centerWindow() {
        // TODO: resize to fit window
        this.dialog.dialog("option", "position", {my: "center", at: "center", of: window});
    }

    closeWindow(canceled) {
        if (this.isClosing) {
            return true;
        }
        this.isClosing = true;

        // Call onClose callback
        if (this.options.onClose) {
          this.options.onClose(this);
        }

        this.clearData();
        if (this.dialog) {
            this.dialog.dialog("close");
        }

        if (this.listenerReload) {
            this.unlistenEvent(this.widgetElement, 'epi:reload:page', this.listenerReload);
        }

        return true;
    }

    /**
     * Set the window title
     *
     * @param title
     */
    updateTitle(title) {
        if (this.dialog) {
            this.dialog.dialog("option", "title", title || this.options.title);
        }
    }

    /**
     * Add close and open buttons to the title bar
     *
     * When initializing the class, set the option "external" to true to show the open button.
     * Set the option "external" to a valid URL to open it instead of the current URL.
     *
     * @param actions
     */
    updateTitleButtons(actions) {
        const self = this;

        // Add an open button to the title bar
        if (this.options.external) {
            if (this.dialog && (this.openExtButton === undefined)) {
                let titleBar = self.dialog.dialog("instance").uiDialogTitlebar.get(0);
                this.openExtButton = Utils.spawnFromString('<button class="btn-open" title="Open in new tab" aria-label="Open in new tab"></button>');
                titleBar.appendChild(this.openExtButton);

                $(this.openExtButton).button({
                    icon: "ui-icon-extlink",
                    showLabel: false,
                });

                this.listenEvent(this.openExtButton, 'click', function () {
                    const targetUrl = self.options.external !== true ? self.options.external : self.currentUrl;
                    window.open(targetUrl);
                });
            }
            Utils.toggle(this.openExtButton, this.options.external !== false);
        }

        // Add a collapse button to the title bar
        if (this.options.collapsable) {
            let titleBar = self.dialog.dialog("instance").uiDialogTitlebar.get(0);
            if (this.dialog && (!titleBar.querySelector('.btn-collapse'))) {
                const button = Utils.spawnFromString('<button class="btn-collapse" title="Collapse / expand" aria-label="Collapse / expand the window."></button>');
                titleBar.appendChild(button);

                $(button).button({
                    icon: 'ui-icon-arrow-2-n-s',
                    showLabel: false
                });

                this.listenEvent(button, 'click',  (event) => this.collapseWindow(event));
            }
        }

        super.updateTitleButtons(actions);
    }

    updateActionButtons(actions, options) {
        super.updateActionButtons(actions, options);

        if (this.dialog) {
            this.dialog.dialog("option", "buttons", this.actionButtons);
            this.buttonPane = this.widgetElement.closest('.ui-dialog').querySelector('.ui-dialog-buttonpane');
            // App.initWidgets(this.buttonPane);
            $(this.buttonPane).find(':tabbable').eq(-1).focus();
        }
    }

    clearData(showLoader) {
        if (this.openExtButton) {
            this.openExtButton.remove();
            this.openExtButton = undefined;
        }

        if (this.widgetElement) {
            this.emitEvent('epi:clear:widgets');
            this.widgetElement.innerHTML = '';
        }
        super.clearData(showLoader);
    }

    /**
     * Update the page
     *
     * @implements BaseForm.onSaveProceed()
     * @param {string} data Data returned from the request
     * @param {XMLHttpRequest} xhr The XMLHttpRequest object
     */
    onSaveProceed(data, xhr) {

        let doClose = false;
        let proceedUrl =null;

        if (super.onSaveProceed(data, xhr)) {
            // Open in new tab
            const responseUrl = new URL(xhr.responseURL);
            proceedUrl = responseUrl.searchParams.get('open');
            doClose = Boolean(Number(responseUrl.searchParams.get('close') || 1));
        }

        if (proceedUrl) {
            this.closeWindow();
            window.setTimeout(function () {
                window.open(proceedUrl);
            }, 1000);
        } else if (doClose) {
            this.closeWindow();
        } else {
            this.updatePage(data, this.formOptions);
        }
    }

    /**
     * Confirm on enter
     *
     * @param {Event} event
     */
    onKeyPress(event) {
        if (event.keyCode === 13) {
            if (this.clickButton('apply')) {
                event.preventDefault();
            }
        }
    }
}


/**
 * Ask for confirmation: a dialog with message, confirmation and cancel button
 *
 * Call showData with the following options:
 * - message The message
 * - onConfirm A callback with one boolean parameter for the answer
 *
 */
export class ConfirmWindow extends PopupWindow {

    constructor(options) {
        super(options);

        this.defaults = {
            title: "Confirm",
            dialogClass: 'popup-confirm',
            width: 400,
            height: 150,
            modal: true,
            focus: true,
            dialogButtons: {
                close: {
                    text: 'Cancel',
                    handler: (dialog) => dialog.closeWindow()
                },
                confirm: {
                    text: 'Confirm',
                    handler: (dialog) => dialog.onConfirm()
                }
            }
        };

        // Holds the state after the confirmation button has been clicked
        this.isConfirmed = false;
    }

    /**
     * Pass the message in options.message
     *
     * @param options
     */
    showData(options) {
        this.isConfirmed = false;
        options.element = Utils.spawnFromString('<div>' + options.message + '</div>');
        super.showData(options);
    }

    /**
     * When closing, don't confirm if not done otherwise before
     */
    closeWindow(canceled) {
        if (!this.isConfirmed && (typeof this.options.onConfirm === "function")) {
            this.options.onConfirm(false);
        }

        return super.closeWindow(canceled);
    }

    onConfirm() {
        if (typeof this.options.onConfirm === "function") {
            this.isConfirmed = true;
            this.options.onConfirm(true);
        }

        this.closeWindow();
    }
}


/**
 * Show a modal message dialog
 *
 * Call showData with the following options:
 * - message The message
 * - delay Milliseconds to wait until the message shows up
 *
 */
export class MessageWindow extends PopupWindow {

    constructor(options) {
        super(options);

        this.defaults = {
            title: "Epigraf",
            dialogClass: 'popup-message',
            width: 400,
            height: 150,
            modal: true,
            focus: true,
            dialogButtons: {}
        };

        setTimeout((event) => this.showData(options), options.delay || 0);
    }

    /**
     * Pass the message in options.message
     *
     * @param {Object} options Leave empty to use the default options
     */
    showData(options) {
        if (this.isClosing) {
            return;
        }

        options = options || this.options || {};
        options.element = Utils.spawnFromString('<div class="messagebox"></div>');
        options.element.appendChild(Utils.spawnFromString('<div class="messagebox-loader loader"></div>'));
        options.element.appendChild(Utils.spawnFromString('<div class="messagebox-message">' + options.message + '</div>'));

        super.showData(options);
    }

}

/**
 * Select a value from a data list
 *
 * Either provide the URL to load the data list from or an HTML element containing the list
 *
 * ### Options
 * - url {String} Page to be loaded
 * - element {Element} Element to be shown
 * - onSelect {function} Will be called when an element is clicked.
 * - onRemove {function} Will be called when the remove button is clicked.
 * - selectOnClick {boolean} Whether to select an element on click
 * - selectList {boolean} Whether to select the list (=folder) instead of an item (=file or record)
 * - itemtype {string} Only select items of this type
 */
export class SelectWindow extends PopupWindow {

    constructor(options) {

        let buttons = {};
        buttons.cancel = {
            'text': 'Cancel',
            'handler': (dialog) => {
                if (dialog.options.removeOnCancel) {
                    this.onRemove(dialog);
                } else {
                    dialog.closeWindow();
                }
            }
        };

        if (options.buttonRemove) {
            buttons.remove = {
                'text': 'Remove',
                'handler': (dialog) => this.onRemove(dialog)
            };
        }

        if (options.buttonSelect) {
            buttons.select = {
                'text': 'Select',
                'handler': (dialog) => this.onSelect(dialog)
            }
        }

        options.dialogButtons = buttons;

        super(options);
        this.showData(options);
    }

    openWindow() {
        super.openWindow();

        // Watch select click
        if (!this.listenerClick) {
            this.listenerClick = event => this.onClick(event);
            this.listenEvent(this.widgetElement, 'click', this.listenerClick);
        }

        // Watch dropdown change
        if (!this.listenerChanged) {
            this.listenerChanged = event => this.onChange(event);
            this.listenEvent(this.widgetElement, 'changed', this.listenerChanged);
        }
    }

    closeWindow(canceled) {
        if (this.listenerClick) {
            this.unlistenEvent(this.widgetElement, 'click', this.listenerClick);
        }
        if (this.listenerChanged) {
            this.unlistenEvent(this.widgetElement, 'changed', this.listenerChanged);
        }

        return super.closeWindow();
    }

    updatePage(data, options) {
        super.updatePage(data, options);

        // Open the dropdown
        // TODO: Is this the right place?
        if (this.options.openDropdown) {
            const dropdownWidget = App.findWidget(this.widgetElement, 'dropdown-selector');
            if (dropdownWidget) {
                dropdownWidget.openDropdown();
            }
        }
    }

    /**
     * Item click handler
     *
     * @param {Event} event The click event
     */
    onClick(event) {
        const selected = event.target.closest('[data-list-itemof]');
        const matches = !this.options.selectList && selected && (!this.options.itemtype || (this.options.itemtype === selected.dataset.listItemtype));

        // Select on click
        if (matches && this.options.selectOnClick) {
            if (typeof this.options.onSelect === "function") {
                this.options.onSelect(selected);
            }
            event.preventDefault();
            this.closeWindow();
        }

        // Open folder on click
        else if (selected && (selected.dataset.listItemtype === 'folder')) {
            this.options.url = selected.dataset.url;
            this.showData(this.options);
            event.preventDefault();
        }
    }

    /**
     * Select button handler
     *
     * @param {SelectWindow} dialog The select window instance
     */
    onSelect(dialog) {
        // Select the list (=folder)
        let selected = this.widgetElement.querySelector('[data-list-name]');
        let matches = true;

        // Or select an item (=file or record)
        if (!this.options.selectList) {
            selected = selected ? selected.querySelector('.row-selected') : undefined;
            matches = selected && (!this.options.itemtype || (this.options.itemtype === selected.dataset.listItemtype));
        }

        if (matches && (typeof this.options.onSelect === "function")) {
            this.options.onSelect(selected);
        }

        this.options.removeOnCancel = false;
        this.closeWindow();
    }

    /**
     * Remove button handler
     *
     * @param {SelectWindow} dialog The select window instance
     */
    onRemove(dialog) {
        if (typeof dialog.options.onRemove === "function") {
            dialog.options.onRemove();
        }
        dialog.closeWindow();
    }
    /**
     * Change event handler for dropdown-selector
     *
     * // TODO: better return the selected item, as in onClick and onSelect?
     *
     * @param {event} event
     */
    onChange(event) {
        if (!event.target.classList.contains('widget-dropdown-selector')) {
            return;
        }

        // Reference to record
        let selected = {
            value: event.target.querySelector('input[type=hidden]').value,
            new: event.target.querySelector('input[type=hidden]').dataset.append,
            type: event.target.querySelector('input[type=hidden]').dataset.type,
            label: event.target.querySelector('input[type=text]').value,
            caption: event.target.querySelector('input[type=text]').dataset.label
        };

        // Select
        if (!this.options.required || (selected.value !== '')) {
            if (typeof this.options.onSelect === "function") {
                this.options.onSelect(selected);
            }

            this.options.removeOnCancel = false;
            this.closeWindow();
        }
    }

}

export class DetachedWindow extends PopupWindow {

    constructor(element, options) {
        super(options);

        this.defaults = {
            dialogClass: 'popup-detach',
            width: 600,
            height: 500,
            position: {my: "right", at: "right-100", of: window},
            modal: false,
            focus: true,
            closeIcon: "ui-icon-check"
        };

        this.showData(element, options);
    }

    /**
     * Detach and show an element
     *
     * @param {Element} element
     * @param {Object} options Options passed to showData()
     */
    showData(element, options = {}) {
        if (!element) {
            return;
        }

        this.storeElement(element);
        options.element = element;
        super.showData(options);
    }

    /**
     * When closing, don't confirm if not done otherwise before
     */
    closeWindow(canceled) {
        this.restoreElement();
        super.closeWindow(canceled);
    }

    storeElement(element) {
        this.restoreElement();

        this.detachedElement = element;
        this.placeholder = document.createElement('div');
        this.placeholder.classList.add('frame-placeholder');

        element.parentElement.insertBefore(this.placeholder, element);
    }

    restoreElement() {
        if (this.placeholder) {
            this.placeholder.parentElement.insertBefore(this.detachedElement, this.placeholder);
            this.placeholder.remove();
            this.placeholder = undefined;
        }
    }
}

export class DetachedTab extends TabFrame {

    constructor(element, tabsheetsWidget, options) {

        // Get or create the more tabsheet
        const tabsheet = tabsheetsWidget.createTab('more', options.title);

        // Remove old detached tab
        tabsheet.widgets = tabsheet.widgets || {};
        if (tabsheet.widgets.more) {
            tabsheet.widgets.more.closeWindow();
        }

        // Add the new detached tab
        super(tabsheet, 'more');
        tabsheet.widgets['more'] = this;
        this.listenEvent(tabsheet,'epi:remove:tabsheet', (event) => this.closeWindow(true));

        this.defaults = {
            // dialogClass: 'popup-detach',
            focus: true,
            closeIcon: "ui-icon-check"
        };

        this.showData(element, options);
    }

    /**
     * Detach and show an element
     *
     * @param {Element} element
     * @param {Object} options Options passed to showData()
     */
    showData(element, options = {}) {
        if (!element) {
            return;
        }

        this.storeElement(element);
        options.element = element;

        options.frameTarget = options.frameTarget || 'more';
        delete options.title;
        delete options.frameCaption;

        super.showData(options);
    }

    /**
     * When closing, don't confirm if not done otherwise before
     */
    closeWindow(canceled) {
        this.restoreElement();
        super.closeWindow(true);
        this.clearData(false, true);
    }

    storeElement(element) {
        this.restoreElement();

        this.detachedElement = element;
        this.placeholder = document.createElement('div');
        this.placeholder.classList.add('frame-placeholder');

        element.parentElement.insertBefore(this.placeholder, element);
    }

    restoreElement() {
        if (this.placeholder) {
            this.placeholder.parentElement.insertBefore(this.detachedElement, this.placeholder);
            this.placeholder.remove();
            this.placeholder = undefined;
        }
    }
}

window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['frame'] = TabFrame;
