
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import Utils from "./utils.js";

/**
 * Base class for models
 *
 */
export class BaseModel {

    constructor(parent) {

        this.modelParent = parent;
        this.modelChildren = [];

        if (parent) {
            parent.modelChildren.push(this);
        }

        this.eventListeners = [];
        this.resizeListeners = [];
        this.hasFocus = false;
    }

    /**
     * Add an event listener to an element
     *
     * @param {Document|HTMLElement|NodeList} element Document or an element/nodelist containing elements
     * @param {string} event The event name
     * @param {function} handler The event handler, with event as first parameter
     * @param {string} selector Optionally, for deferred listeners, a selector
     */
    listenEvent(element, event, handler, selector) {
        if (!element) {
            return;
        }

        if (element instanceof NodeList) {
            element.forEach(node => this.listenEvent(node, event, handler, selector));
        }

        else {
            Utils.listenEvent(element, event, handler, selector);
            //element.addEventListener(event, handler);
            const item = {
                element: element,
                event: event,
                handler: handler,
                selector: selector
            };
            this.eventListeners.push(item);

            // Add event listener for ajax form submissions
            if (event === 'submit') {
                this.listenEvent(element, 'epi:submit:form', handler);
            }
        }
    }

    /**
     * Remove event listeners
     *
     * @param {HTMLElement|document} element
     * @param {string} event Event name, e.g. 'epi:save:form'
     * @param {function} handler Let undefined to remove all handlers
     * @param {string} selector Optionally, for deferred listeners, a selector
     *
     */
    unlistenEvent(element, event, handler, selector) {
        let i = this.eventListeners.length;
        while (i--) {
            const item = this.eventListeners[i];
            if ((item.element === element) && (item.event === event) &&
                ((item.handler === handler) || !handler) &&
                ((item.selector === selector) || !selector)
            ) {
                Utils.unlistenEvent(element, item.event, item.handler, item.selector);
                //item.element.removeEventListener(item.event,item.handler);
                this.eventListeners.splice(i, 1);
            }
        }

        if (event === 'submit') {
            this.unlistenEvent(element, 'epi:submit:form', handler);
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
        return Utils.emitEvent(element, name, data, this, cancelable);
    }

    /**
     * Get widget of the element by name
     *
     * @param {HTMLElement} element An element which has widgets attached
     * @param {string} name The css name used to initialize the widget
     * @param {boolean} closest Whether to search the closest ancestor element
     *                          with a widget class matching the widget name
     *
     * @return {BaseWidget|undefined}
     */
    getWidget(element, name, closest = true) {
        if (!element) {
            return;
        }

        if (closest) {
            element = element.closest('.widget-' + name);
        }

        if (element && element.widgets) {
            return element.widgets[name];
        }
    }

    /**
     * Remove all event listeners and all child models
     */
    clearWidget() {
        this.eventListeners.forEach(
            (item) => Utils.unlistenEvent(item.element, item.event, item.handler, item.selector)
        );
        this.eventListeners = [];

        this.resizeListeners.forEach(
            (item) => item.observer.unobserve(item.element)
        );
        this.resizeListeners = [];

        this.modelChildren.forEach((item) => item.clearWidget());
        this.modelChildren = [];
    }
}

/**
 * A widget is a model attached to a DOM element
 *
 * initWidget: is called once after all widgets were constructed and are ready to be used.
 * updateWidget: is called each time, a widget is initialized: when it was created and when it is updated.
 *
 */
export class BaseWidget extends BaseModel {

    widgetElement = undefined;
    widgetName = undefined;
    widgetInitialized = false;
    hasFocus = false;

    /**
     * The base class handles construction and destruction.
     *
     * @param {HTMLElement} element The DOM element
     * @param {string} name A name of the widget. Each DOM element can have one widget of a name.
     * @param {BaseWidget} parent The parent model or widget. Can be undefined.
     */
    constructor(element, name, parent) {
        super(parent);

        this.widgetInitialized = false;
        this.attachElement(element, name);
        this.listenEvent(document,'epi:clear:widgets', (event) => this.onClearWidgets(event.target));
        this.listenEvent(document,'epi:init:widgets', (event) => this.onInitWidgets(event));

        // Widget focus handing
        this.listenEvent(document,'focusout',(event) => this.onFocusChanged(event));
        this.listenEvent(document,'focusin',(event) => this.onFocusChanged(event));
        this.listenEvent(document,'click',(event) => this.onFocusChanged(event));
    }

    clearWidget() {
        super.clearWidget();
        this.detachElement(this.widgetElement, this.widgetName);
    }

    /**
     * Widget initialization: Override in child classes and put all initialization code here.
     *
     * The method is called once after all widgets were constructed and are ready to be used.
     */
    initWidget() {

    }

    /**
     * Widget initialization: Override in child classes
     *
     * The method is called each time, a widget is initialized:
     * when it was created and when it is updated.
     */
    updateWidget() {

    }


    /**
     * Finishes the widget if its container is cleared.
     *
     * @param {HTMLElement} container
     */
    onClearWidgets(container) {
        if (container && this.widgetElement && (container !== this.widgetElement) && container.contains(this.widgetElement)) {
            this.clearWidget();
        }
    }

    /**
     * Connect the dom element and the widget object
     *
     * @param {HTMLElement} element
     * @param {string} name
     * @param {boolean} own Whether to set this.widgetElement
     */
    attachElement(element, name, own = true) {
        if (own) {
            this.widgetElement = element;
            this.widgetName = name;
        }

        if (element && name) {
            element.widgets = element.widgets || {};
            element.widgets[name] = this;
            //element.classList.add('widget-' + name);
        }
    }

    detachElement(element, name) {
        if (element && name) {
            element.widgets = element.widgets || {};
            delete element.widgets[name];
        }
    }

    /**
     * Emit an event from the widget element
     *
     * @param {string} name
     * @param {Object} data
     * @param {boolean} cancelable
     * @return {boolean}
     */
    emitEvent(name, data, cancelable) {
        return super.emitEvent(this.widgetElement, name, data, cancelable);
    }


    listenResize(element, handler) {
        if (element) {
            const observeResize = new ResizeObserver(entries => handler(entries));
            observeResize.observe(element);

            const item = {
                element: element,
                observer: observeResize,
                handler: handler
            };
            this.resizeListeners.push(item);
        }
    }

    unlistenResize(element, observer) {
        observer.unobserve(element);
    }


    /**
     * Instantiate a template from the templates collection
     *
     * Add a script element with type 'text/template' and the class 'template'
     * to the template element within the widget element. Add the template name as a class,
     * e.g. 'template-footnote-app1'.
     *
     * @param {String} name The name of the template, e.g. template-footnotes-editor
     * @return {DocumentFragment}
     */
    spawnFromTemplate(name, data) {
        const template = this.widgetElement.querySelector('.template.' + name);
        return Utils.spawnFromTemplate(template, data);
    }

    /**
     * Called when all widgets were initialized after page or snippet updates.
     * Calls initWidget() once.
     *
     * @param {Event} event
     */
    onInitWidgets(event) {
        if (!this.widgetInitialized) {
            this.widgetInitialized = true;
            this.initWidget();
        }
    }

    /**
     * Get or loose focus
     *
     * @param {Event} event Leave empty to focus
     */
    onFocusChanged(event) {
        if (this.widgetElement && this.widgetElement instanceof Element) {
            if (event === undefined) {
                this.setFocus(true);
            } else {
                if (this.widgetElement.contains(event.target)) {
                    this.setFocus(event.type !== 'focusout');
                } else if (event.type !== 'focusout') {
                    this.setFocus(false);
                }
            }
        }
    }

    /**
     * Update the focus attributes
     *
     * @param {boolean} focus
     */
    setFocus(focus = true) {
        if (this.widgetElement && this.widgetElement instanceof Element) {

            if (this.hasFocus === focus) {
                return;
            }

            this.hasFocus = focus;
            this.widgetElement.classList.toggle('widget-focused', this.hasFocus);
            this.emitEvent('epi:focus:widgets', {focus: this.hasFocus});

            // let debugMessage = focus ? 'focus  ' : 'unfocus';
            // debugMessage = debugMessage + ': ' + this.widgetName + ': ' + this.widgetElement.tagName + ': ' + this.widgetElement.className;
            //
            // if (focus) {
            //     console.log(debugMessage);
            // } else if (this.widgetName === 'table') {
            //     console.log(debugMessage);
            //     if (this.widgetElement.classList.contains('widget-dragitems-enabled')) {
            //         console.log('WHY');
            //     }
            // }
        }
    }

    /**
     * Focus the widget
     */
    doFocus() {
        if (this.widgetElement && this.widgetElement instanceof Element) {
            this.setFocus(true);
            this.widgetElement.focus();
        }
    }

    getSetting(key, defaultValue) {
        if (this.widgetElement && this.widgetElement.dataset.uiKey) {
            const value = App.user.session.get('ui', this.widgetElement.dataset.uiKey);
            if ((value !== undefined) && (key in value)) {
                return value[key];
            }
        }
        return defaultValue;
    }

    setSetting(key, value) {
        if (this.widgetElement && this.widgetElement.dataset && this.widgetElement.dataset.uiKey) {
            let valueObject = {};
            valueObject[key] = value;
            App.user.session.save('ui', this.widgetElement.dataset.uiKey, valueObject);
        }
    }

    /**
     * Get the current URL controller name
     */
    getController() {
        return Utils.getClassValue(document.querySelector('body'), 'controller_');
    }

    /**
     * Get the current URL action name
     */
    getAction() {
        return Utils.getClassValue(document.querySelector('body'), 'action_');
    }


    /**
     * Return the frame widget which contains the current widget.
     *
     * Used to determine whether the scope of the widget should be constrained to AJAX content.
     * See popups.js.
     * For document level widgets, this is undefined,
     * for widgets within a popup, this is the popup elements widget,
     * for widgets within the sidebar, this is the sidebar elements widget.
     *
     * @param {boolean} widget By default, return the widget. Set to false to return the HTML element or document instead.
     * @param {boolean} getSelf Whether to return the widget itself if it is a content pane
     * @return {BaseWidget}
     */
    getFrame(widget = true, getSelf = false) {

        if (!this.widgetElement || (this.widgetElement === document)) {
            return widget ? undefined : document;
        }

        let pane;
        if (getSelf && this.widgetElement.classList.contains('widget-content-pane')) {
           pane = this.widgetElement;
        }

        if (!pane) {
            pane = this.widgetElement.closest('.widget-content-pane');
        }

        if (!widget) {
            return (pane && !pane.classList.contains('widget-content-pane-main')) ? pane : document;
        }
        else {
            return pane ? pane.widgets['content-pane'] : undefined;
        }
    }

    /**
     * Return whether the widget is contained in a sidebar or popup
     *
     * @return {boolean}
     */
    isInFrame() {
        return (this.widgetElement)
            && (this.widgetElement.closest('.widget-content-pane') !== null)
            && (!this.widgetElement.closest('.widget-content-pane-main'));
    }
}
/**
 * Base class for widgets interacting with a document and its models
 *
 */
export class BaseDocument extends BaseWidget {

    /**
     * Constructor
     *
     * @param {HTMLElement} element The DOM element
     * @param {string} name A name of the widget. Each DOM element can have one widget of a name.
     * @param {BaseWidget} parent The parent model or widget. Can be undefined.
     */
    constructor(element, name, parent) {
        super(element, name, parent);
    }

    /**
     * Get the document widget where the widget belongs to
     *
     * All elements with the same data-root-table and data-root-id values
     * belong to the same document. For each root table and root ID,
     * there should be exactly one instance of a DocumentWidget
     * (TODO: make this sure, although the document can be loaded a second time in a sidebar or popup)
     * @return {BaseWidget} The document widget
     */
    getDocumentWidget() {
        const rootTable = this.widgetElement.closest('[data-root-table]');
        const rootId = this.widgetElement.closest('[data-root-id]');

        if (!rootTable || !rootTable.dataset.rootTable ||
            !rootId || !rootId.dataset.rootId) {
            return;
        }

        const documentElement = document.querySelector(
            '.widget-document' +
            '[data-root-table="' + rootTable.dataset.rootTable + '"]' +
            '[data-root-id="' + rootId.dataset.rootId + '"]'
        );

        return this.getWidget(documentElement, 'document', false);
    }
}

/**
 * A base form is a widget which handles form submissions
 *
 * All add, edit, cancel and delete operations go through the BaseForm widget.
 * Four variations have to be considered:
 * - A single page view with submit buttons in the footer.
 *   See EntityWidget and DocumentWidget in documents.js.
 *   They call attachForm() to init form handlers.
 * - A framed view in the sidebar with submit buttons in the sidebar.
 *   See the TabFrame class in layouts.js.
 *   The buttons are hijacked by calling attachForm() in the button handler.
 * - A popup view with submit buttons in the dialog
 *   See the PopupWindow class in layouts.js.
 *   The buttons are hijacked by calling attachForm() in the button handler.
 *
 * To handle forms, call attachForm() to bind the submitForm() method to the form submit event.
 * Alternatively, you can call submitForm() directly.
 *
 * The submitForm() method will initiate the following workflow:
 *
 * 1. validateForm() is called which fires the epi:save:form event.
 *    Editor widgets should observe this event and prepare the data.
 *    If the event is canceled, the form will not be saved.
 *
 * 2. saveForm() is called which triggers the following workflow:
 *    - onSaveStart: Shows the loader.
 *    - onSavePost: Sends the data to the server.
 *    - onSaveSuccess or onSaveFailed: Handle the response.
 *    - onSaveEnd: Fires the events app:close:dialog and app:hide:loader.
 *                 Dialogs and the app should observe these events to close the dialog and hide the loader.
 *
 * 3. The onSaveSuccess() method
 *    - Fires epi:update:row, epi:move:row, epi:create:row, epi:delete:row events.
 *      Observe these events to update the user interface.
 *    - Calls onSaveProceed().
 *      Overwrite this method to initiate followup actions,
 *      for example, to open a new tab sheet or close a popup.
 *
 * You should unlock the entity in cancel operations.
 * Deliberately close the form by calling unlockForm().
 * In frames and popups, clearWidget() unlocks the form automatically.
 */
export class BaseForm extends BaseWidget {

    /**
     * Constructor
     *
     * @param {HTMLElement} element The DOM element
     * @param {string} name A name of the widget. Each DOM element can have one widget of a name.
     * @param {BaseWidget} parent The parent model or widget. Can be undefined.
     */
    constructor(element, name, parent) {
        super(element, name, parent);
        this.formElement = undefined;
        this.cancelButton = undefined;
        this.deleteButton = undefined;
        this.lockTimeout = undefined;
        this.isSaving = false;
    }

    /**
     * Connect the base form widget with the form element and listen the submit event
     *
     * @param {HTMLFormElement} element
     * @para {boolean} force Whether to attach the form even if it is already attached
     */
    attachForm(element, force= true) {
        this.detachForm();

        if (!force && this.getWidget(element,'baseform', false)) {
            return;
        }

        this.formElement = element;
        if (this.formElement) {
            this.attachElement(element, 'baseform', false);

            // Listen to cancel action etc., handles unlocking
            this.listenButtons();

            // Listen to sidebar close event
            this.listenEvent(document, 'epi:open:tab', event => this.onExternalClick(event));

            // Listen to form submit event
            this.listenEvent(this.formElement, 'submit', event => this.submitForm(event));

            // Prevent implicit submit
            this.listenEvent(this.formElement, 'keydown', event => this.onKeyDown(event));


            // Leave page listener
            this.listenEvent(window, 'beforeunload', (event) => {
                return this.onExit(event);
            });
        }
    }

    /**
     * Disable all events of the form
     *
     */
    detachForm() {
        this.unlistenButtons();

        if (this.formElement) {
            this.unlistenEvent(document, 'epi:open:tab');
            this.unlistenEvent(this.formElement, 'submit');
            this.unlistenEvent(window, 'beforeunload');
            this.formElement = undefined;
        }
    }

    listenButtons() {
        if (!this.formElement) {
            return;
        }

        // Listen to button events
        if (!this.cancelButton) {
            this.cancelButton = document.querySelector('button[data-role="cancel"][form="' + this.formElement.getAttribute('id') + '"]');
            if (this.cancelButton) {
                this.listenEvent(this.cancelButton, 'click', event => this.onCancelClick(event));
            }
        }

        if (!this.deleteButton) {
            this.deleteButton = document.querySelector('button[data-role="delete"][form="' + this.formElement.getAttribute('id') + '"]');
            if (this.deleteButton) {
                this.listenEvent(this.deleteButton, 'click', event => this.onDeleteClick(event));
            }
        }

        // Update locks every 15 seconds
        if (!this.lockTimeout) {
            this.setLockTimeout(() => this.lock(true), 15000);
        }
    }

    unlistenButtons() {
        if (this.formElement) {
            this.clearLockTimeout();
        }

        if (this.deleteButton) {
            this.unlistenEvent(this.deleteButton, 'click');
        }

        if (this.cancelButton) {
            this.unlistenEvent(this.cancelButton, 'click');
        }
    }

    /**
     * Handles the beforeunload event and asks the user whether to leave the page
     *
     * @param {Event} event
     */
    onExit(event) {
        if (!this.isSaving && this.hasLock()) {
            const msg = "You have opened a dataset. The dataset may stay locked if you don't close it. Do you want to leave the page anyways?";
            event.preventDefault();
            event.returnValue = msg;
            return msg;
        }
    }

    /**
     * Add an event listener to an element
     *
     * @param {function} handler The timeout handler
     * @param {number} delay The delay in milliseconds
     */
    setLockTimeout(handler, delay) {
        this.lockTimeout = setTimeout(() => handler(), delay);
    }

    clearLockTimeout() {
        if (this.lockTimeout) {
            clearTimeout(this.lockTimeout);
            this.lockTimeout = undefined;
        }
    }

    hasLock() {
        if (!this.formElement) {
            return false;
        }

        const lockInput = this.formElement.querySelector('input[name="lock"][data-lock-url]');
        return lockInput !== null;
    }

    /**
     * Send a lock request to the server
     *
     * @param {Boolean} poll Whether to start sending lock requests every 15 seconds
     * @return {Promise<void>}
     */
    async lock(poll = true) {
        // Clear lock timeout
        this.lockTimeout = undefined;

        if (!this.formElement) {
            return;
        }

        if (this.isSaving) {
            return;
        }

        const lockInput = this.formElement.querySelector('input[name="lock"][data-lock-url]');
        const lockUrl = lockInput ? lockInput.dataset.lockUrl : null;
        const lockId = lockInput ? lockInput.value : null;

        if (lockUrl && lockId) {
            await fetch(lockUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({lock : lockId})
            });

            if (poll) {
                this.setLockTimeout(() => this.lock(true), 15000);
            }
        }
    }

    async unlock() {
        this.clearLockTimeout();

        if (!this.formElement) {
            return;
        }

        if (this.isSaving) {
            return;
        }

        const lockInput = this.formElement.querySelector('input[name="lock"][data-lock-url]');
        const unlockUrl = lockInput ? lockInput.dataset.unlockUrl : null;
        const lockId = lockInput ? lockInput.value : null;

        if (unlockUrl && lockId) {
            await fetch(unlockUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({lock : lockId})
            });
        }
    }

    clearWidget() {
        // Now handled by clearData() in frames.js
        //this.unlock();
        super.clearWidget();
    }

    /**
     * Send a post request including the lock id to the target URL
     * This unlocks the entity.
     *
     * @param {string} targetUrl The URL that should be requested
     * @param {boolean} async Whether to send the request asynchronously by loadUrl()
     */
    unlockForm(targetUrl, async=false) {
        if (!targetUrl) {
            this.cancelForm(true);
            return;
        }

        let lockId;
        if (this.formElement) {
            // Get cancel URL and lock id
            const lockInput = this.formElement.querySelector('input[name="lock"][data-lock-url]');
            lockId = lockInput ? lockInput.value : null;
        }
        // Create cancel form
        if (lockId) {
            // Submit cancel form
            this.isSaving = true;
            this.clearLockTimeout();

            // TODO: overwrite in BaseFrame?
            if (async && this.loadUrl) {
              this.loadUrl(targetUrl, {lock: lockId, focus: false});
            } else {
                const cancelInput = Utils.spawnFromString('<input type="hidden" name="lock" value="' + lockId + '">');
                const cancelForm = document.createElement("form");
                cancelForm.action = targetUrl;
                cancelForm.method = "POST";
                cancelForm.appendChild(cancelInput);
                document.body.appendChild(cancelForm);
                cancelForm.submit();
            }
        } else if (this.loadUrl) {
            this.loadUrl(targetUrl);
        }
        this.cancelForm(false);
    }

    /**
     * Footer cancel button event handler
     *
     * Bypassed in frames and popups, see attachButtons()
     *
     * @param {Event} event
     */
    onCancelClick(event) {
        if (this.formElement) {
            const url = Utils.decodeHtmlAttribute(this.formElement.dataset.cancelUrl);
            this.unlockForm(url);
        }
    }

    onExternalClick(event) {
        if (this.formElement && event.target.contains(this.formElement)) {
            const url = Utils.decodeHtmlAttribute(this.formElement.dataset.cancelUrl);
            this.unlockForm(url, true);
        }
    }


    /**
     * Footer delete button event handler
     *
     * Bypassed in frames and popups, see attachButtons()
     *
     * @param {Event} event
     */
    onDeleteClick(event) {
        if (this.formElement) {
            const url = Utils.decodeHtmlAttribute(this.formElement.dataset.deleteUrl);
            this.unlockForm(url);
        }
    }

    /**
     * Prevent form submit on enter
     *
     * @param {Event} event
     */
    onKeyDown(event) {
        if ((event.keyCode === 13) && (event.target.tagName === 'INPUT')) {
            event.preventDefault();
        }
    }

    /**
     * Get the entity ID of the form
     *
     * @return {string}
     */
    getEntityId() {
        if (!this.formElement) {
            return;
        }

        let entityDoc = this.formElement.querySelector('[data-root-table][data-root-id]');
        entityDoc = entityDoc || this.formElement.closest('[data-root-table][data-root-id]');
        if (entityDoc && entityDoc.dataset.rootTable && entityDoc.dataset.rootId) {
            return entityDoc.dataset.rootTable + '-' + entityDoc.dataset.rootId;
        }
    }

    /**
     * Focus the first input element in the widget
     */
    focusWidget() {

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

    /**
     * Close the form and emit teh event epi:cancel:row
     *
     * @param {boolean} close Whether to close the window
     * @return {boolean}
     */
    cancelForm(close = true) {
        if (!this.formElement) {
            return false;
        }

        if (close) {
            this.closeWindow(true);
        }

        const entityId = this.getEntityId();
        if (entityId) {
            this.emitEvent('epi:cancel:row', {row: entityId, sender: this});
        }
    }

    /**
     * Prepare and save document
     *
     * Set data-role to 'submit' for classical submit requests.
     * Set data-role to 'save' for AJAX submit requests.
     * //TODO: better handling of fixed sections: submit without moving
     *
     * @param {Event} event
     */
    submitForm(event) {
        if (!this.formElement) {
            return false;
        }

        // Integrate fixed sections
        let fixedSections = [];
        if (this.footerPane) {
            fixedSections = Array.from(this.footerPane.querySelectorAll('[data-position=fixed]'));
            fixedSections.forEach((elm, idx) => {
                this.formElement.appendChild(elm);
            });
        }

        try {
            // Restore fixed sections
            if (!this.validateForm()) {
                return;
            }

            // Option 1: classical submit request, no AJAX
            if (event.submitter && (event.submitter.dataset.role === 'submit')) {
                this.isSaving = true;
                return false;
            }

            // Option 2: AJAX submit request
            event.preventDefault();
            event.stopPropagation();

            this.saveForm();
        }
        finally {

            // Restore fixed sections
            fixedSections.forEach((elm, idx) => {
                this.footerPane.appendChild(elm);
            });

        }
        return false;
    }

    /**
     * Validate the form before submit
     *
     * @return {boolean} Whether the form can be saved.
     */
    validateForm() {
        // Call browser validation method
        if (!this.formElement || !this.formElement.reportValidity()) {
            return false;
        }

        // Give all listening widgets the chance to downcast their data
        if (!this.emitEvent('epi:save:form', {}, true)) {
            return false;
        }
        return true;
    }

    /**
     * Send form data to the server
     *
     */
    saveForm() {
        if (!this.formElement) {
            return false;
        }

        if (this.isSaving) {
            return false;
        }

        // Last chance to cancel saving
        if (!this.onSaveStart()) {
            return false;
        }

        return this.onSavePost();
    }

    /**
     * Save start hook. Override to show a loader.
     * Will be called for every attached form widget.
     *
     * @return {boolean}
     */
    onSaveStart() {
        this.emitEvent('app:show:loader');
        const msg = this.formElement && "message" in this.formElement.dataset ? this.formElement.dataset.message : 'Saving document';
        if (msg) {
            this.emitEvent('app:open:dialog', {'message': msg, 'loader': true, 'id': 'save-dialog', 'delay': 150});
        }
        this.clearLockTimeout();
        this.isSaving = true;
        return true;
    }

    /**
     * Serialize form data and send it to the server
     *
     * @return {boolean}
     */
    onSavePost() {

        // Serialize form data
        let url;
        let formData = null;
        let contentType = false;
        const requestType = this.formElement.method  || "POST";

        // GET request forms
        if (requestType.toUpperCase() ===  "GET") {
            url = Utils.formToUrl(this.formElement, App.baseUrl);
        }

        // POST request forms
        else {
            url = this.formElement.getAttribute('action');

            this.formElement.action = ''; //Workaround: form data is empty if query parameters in action
            formData = new FormData(this.formElement);
            if (this.formElement.dataset.format === 'json') {
                formData = JSON.stringify(Utils.parseFormData(formData));
                contentType = 'application/json';
            }
            this.formElement.action = url; //Workaround: form data is empty if query parameters in action
        }

        // Issue request
        const self = this;
        const xhr = new XMLHttpRequest(); // Allows access to xhr.responseURL
        xhr.method = requestType.toUpperCase();
        $.ajax({
            type: requestType,
            url: url,
            data: formData,
            xhr: () => xhr,
            async: true,
            contentType: contentType, //this is required
            processData: false, //this is required
            success: (data, textStatus, jqXHR) => self.onSaveSuccess(self.getEntityId(), data, textStatus, xhr),
            error: (jqXHR) => self.onSaveFailed(xhr),
            complete: (jqXHR) =>  self.onSaveEnd(xhr)
        });

        return true;
    }

    /**
     * Update the page, hide the loader and close or redirect
     *
     * @implements BaseForm.onSaveSuccess()
     * @param {string} entityId Entity ID in the form. For existing entities, this is the combination of table and ID.
     *                          New entities IDs start with the prefix 'new-'.
     * @param {string} data Data returned from the request
     * @param {string} textStatus Status message
     * @param {XMLHttpRequest} xhr The XMLHttpRequest object
     * @return {boolean} Whether to further process the request
     */
    onSaveSuccess(entityId, data, textStatus, xhr) {
        this.hideLoader();

        // Don't take temporary IDs for real IDs
        const entityIdParts = entityId ? entityId.split('-') : [];
        if ((entityIdParts.length > 1) && (entityIdParts[1] === 'new')) {
            entityId = undefined;
        }

        // Not used anymore, but may be useful later
        if (this.formOptions && this.formOptions.onSubmitted) {
            this.formOptions.onSubmitted(this, data, textStatus, xhr);
            return false;
        }

        // Update entities
        const contentType = xhr.getResponseHeader("content-type");
        let entityState = 'update';
        let deletedIds = [];
        if (xhr.method === 'GET') {
            entityState = 'unchanged';
        }
        else {
            if (!entityId) {
                entityState = 'create';
                // For new records: extract the new id from the response

                if (contentType === 'application/json') {
                    const entityDoc = data[Object.keys(data)[0]];
                    if (entityDoc) {
                        entityId = entityDoc.id;
                    }
                } else {
                    const entityDoc = new DOMParser().parseFromString(data, 'text/html').querySelector('[data-root-table][data-root-id]');
                    if (entityDoc) {
                        entityId = entityDoc.dataset.rootTable + '-' + entityDoc.dataset.rootId;
                    }
                }
            } else {
                // TODO: handle JSON respnses
                const entityDoc = new DOMParser().parseFromString(data, 'text/html').querySelector('[data-root-table][data-root-id]');
                if (entityDoc && entityDoc.dataset.deleted === '1') {
                    deletedIds.push(entityId);
                }

                if (entityDoc && entityDoc.dataset.mergedIds) {
                    let mergedIds = entityDoc.dataset.mergedIds
                        .split(',')
                        .map(id => entityDoc.dataset.rootTable + '-' + id);
                    deletedIds = deletedIds.concat(mergedIds);
                }

                if (entityDoc && entityDoc.dataset.moved === '1') {
                    entityState = 'moved';
                }
            }
        }
        // Advertise the new data
        if ((entityState === 'update') && entityId && !deletedIds.includes(entityId)) {
            this.emitEvent('epi:update:row', {row: entityId, sender: this});
        }
        else if ((entityState === 'moved') && entityId) {
            this.emitEvent('epi:move:row', {row: entityId, sender: this});
        }
        else if ((entityState === 'create') && entityId) {
            this.emitEvent('epi:create:row', {row: entityId, sender: this});
        }

        deletedIds.forEach((id) => this.emitEvent('epi:delete:row', {row: id, sender: this}));

        return this.onSaveProceed(data, xhr);
    }

    onSaveProceed(data, xhr) {
        // TODO: update current page or at least new records, otherwise double saving will produce duplicate records

        let message;
        let status;
        let errors;

        const contentType = xhr.getResponseHeader("content-type");
        if (contentType  === 'application/json') {
            message = Utils.getValue(data, 'status.message');
            status = Utils.getValue(data, 'status.success', false) ? 'success' : 'error';
            errors = Utils.getValue(data, 'status.errors');
        } else {
            message = new DOMParser().parseFromString(data, 'text/html').querySelector('[data-snippet=message]');
            status = (message && message.classList.contains('error')) ? 'error' : 'success';
            message = message ? message.textContent : undefined;
        }

        if (message) {
            this.emitEvent('app:show:message', {'msg': message, 'status': status, 'errors': errors});
        }

        return status === 'success';
    }

    /**
     * Failed saving hook. Override to show an error message.
     * Will be called for the form widget that issued the saveForm() call.
     *
     * @param {XMLHttpRequest} xhr The XMLHttpRequest object
     */
    onSaveFailed(xhr)
    {
        this.emitEvent('app:show:message', {'msg' : 'Saving failed: ' + xhr.responseText, 'status': 'error' });
    }

    /**
     * Save end hook. Override in children to hide a loader.
     * Will be called for every attached form widget.
     *
     * @param {XMLHttpRequest} xhr The XMLHttpRequest object
     * @return {boolean}
     */
    onSaveEnd(xhr) {
        // Emit document level event because popups may already be destroyed,
        // so the event would not bubble up
        Utils.emitEvent(document, 'app:close:dialog', {'id': 'save-dialog'});
        Utils.emitEvent(document,'app:hide:loader');
        this.isSaving = false;
        return true;
    }

}

/**
 * Base class for model entities, i.e. elements in a form containing fields
 *
 * Used by widgets to get the data of a entity using getEntityData().
 * The object will be attached to the modelEntity property of the element.
 * If an element already has a modelEntity property, the existing object will be returned.
 *
 */
export class ModelEntity  {

    constructor(elm) {
        if (elm && elm.modelEntity) {
            return elm.modelEntity;
        }

        this.element = elm;
        const entityElement = elm ? elm.closest('[data-row-table][data-row-id]') : undefined;

        if (entityElement) {
            elm.modelEntity = this;

            const table = entityElement.closest('[data-row-table]');
            const id = entityElement.closest('[data-row-id]');
            const type = entityElement.closest('[data-row-type]');

            const rootTable = entityElement.closest('[data-root-table]');
            const rootId = entityElement.closest('[data-root-id]');

            this.table = table ? table.dataset.rowTable : undefined;
            this.id = id ? id.dataset.rowId : undefined;
            this.type = type ? type.dataset.rowType : undefined;
            this.rootTable = rootTable ? rootTable.dataset.rootTable : undefined;
            this.rootId = rootId ? rootId.dataset.rootId : undefined;
            this.deleted = id ? id.dataset.deleted : undefined;
        }
    }

}

/**
 * Base class for model fields, i.e. elements in a form.
 *
 * Used by widgets to get the data of a field using getFieldData().
 * The object will be attached to the modelField property of the element.
 * If an element already has a modelField property, the existing object will be returned.
 *
 */
export class ModelField  {

    constructor(elm) {
        if (elm && elm.modelField) {
            return elm.modelField;
        }

        this.element = elm;
        const field = elm ? elm.closest('[data-row-field]') : undefined;

        if (field) {
            elm.modelField = this;

            const table = field.closest('[data-row-table]');
            const id = field.closest('[data-row-id]');
            const type = field.closest('[data-row-type]');

            const rootTable = field.closest('[data-root-table]');
            const rootId = field.closest('[data-root-id]');

            this.field =  field ? field.dataset.rowField : undefined;
            this.table = table ? table.dataset.rowTable : undefined;
            this.id = id ? id.dataset.rowId : undefined;
            this.type = type ? type.dataset.rowType : undefined;
            this.rootTable = rootTable ? rootTable.dataset.rootTable : undefined;
            this.rootId = rootId ? rootId.dataset.rootId : undefined;
            this.deleted = id ? id.dataset.deleted : undefined;
        }
    }

    /**
     * Get the number of the row where the element belongs to
     * Only implemented for items. Other row types always return 1.
     *
     * @return {number}
     */
    get rowNumber() {
        if (!this.element) {
            return;
        }
        const row = this.element.closest('[data-row-table="items"]');
        if (!row) {
            return 1;
        }
        const sortno = row.querySelector('[data-row-field="sortno"] input');
        if (!sortno) {
            return 1;
        }

        return parseInt(sortno.value);
    }
}
