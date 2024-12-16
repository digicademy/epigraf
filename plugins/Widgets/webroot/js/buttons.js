/**
 * Button widgets - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import Utils from '/js/utils.js';
import {BaseWidget} from '/js/base.js';
import {SelectWindow} from "./frames.js";

/**
 * Switch handler: toggles a class of the target elements.
 *
 * Add the following markup:
 * - Create a button with the class widget-switch
 * - Provide a css selector for all target elements in the data-switch-element attribute of your switch.
 * - Set data-switch-closest to '1' if the selector should be used to find the closest ancestor.
 * - Set data-switch-reverse to '1' if the selector is activated in the beginning
 * - Provide a css class that will be toggled on all target elements in the data-switch-class attribute of your switch
 * - Add a key to the data-ui-key attribute to always save the state of the switch to the user settings
 *
 * The switch will toggle its own class 'widget-switch-active' and emit the event 'epi:toggle:switch'
 *
 * The switch widget is not attaced to all switch elements, but to the document.
 * It handles all elements with the class widget-switch.
 */
export class SwitchButtons extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);
        if (element) {
            element.widgetSwitchButtons = this;
        }

        document.addEventListener('click', event => this.switchClick(event));
    }

    /**
     * Called after switch button was clicked.
     * Activates or deactivates functionality for corresponding
     * target elements on page.
     *
     * @param event Click
     */
    switchClick(event) {
        if (
            event.target.matches('input, select, a, button') &&
            (!event.target.matches('.widget-switch, .widget-switch-button'))
        ) {
            return;
        }
        const button = event.target.closest('.widget-switch');
        if (button) {
            this.switchButton(button);
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }

    /**
     * Toggles the visibility of corresponding target elements on page.
     *
     * @param {HTMLElement} button The element with the widget-switch class
     * @param {boolean} active If undefined, toggles the button.
     *                         Otherwise, activates or deactivates the button.
     */
    switchButton(button, active= undefined) {
        if (button) {

            // Confirm switching
            const beforeEvent = new CustomEvent('epi:toggle:switch:before', {bubbles: true, cancelable: true});
            if (!button.dispatchEvent(beforeEvent)) {
                return;
            }

            // Toggle switch class
            button.classList.toggle('widget-switch-active', active);
            active = button.classList.contains('widget-switch-active');

            const switchClass = button.dataset.switchClass;
            const switchContent = button.dataset.switchContent;

            let switchSelector = button.dataset.switchElement;
            const switchClosest = button.dataset.switchClosest === '1';

            let targetElements;
            if (switchClosest && switchSelector) {
                targetElements = [button.closest(switchSelector)];
            } else {
                targetElements = document.querySelectorAll(switchSelector);
            }

            // Toggle target elements' classes
            if (switchClass && targetElements) {
                let targetActive = button.dataset.switchReverse === '1' ? !active : active;
                targetElements.forEach(elm => elm.classList.toggle(switchClass, elm.dataset.switchReverse === '1' ? !targetActive : targetActive));
            }

            // Toggle target elements' content
            if (switchContent && targetElements) {
                targetElements.forEach(elm => {
                    button.dataset.switchContent = elm.textContent;
                    elm.textContent = switchContent;
                });
            }

            // Create event
            const newEvent = new Event('epi:toggle:switch', {bubbles: true, cancelable: false});
            button.dispatchEvent(newEvent);

            // Save setting
            const uiKey = button.dataset.uiKey;
            this.setSetting(uiKey, 'active', active);
        }
    }

    setSetting(uiKey, key, value) {
        if (uiKey) {
            let valueObject = {};
            valueObject[key] = value;
            App.user.session.save('ui', uiKey, valueObject);
        }
    }
}

/**
 * Toggle handler: toggles the visibility of a target element
 *
 * Add the following markup:
 * - Create a button with the class widget-switch
 * - Provide a css selector for all target elements in the data-switch-element attribute of your switch
 * - Provide a css class that will be toggled on all target elements in the data-switch-class attribute of your switch
 *
 * The toggle will toggle its own class 'active' and emit the event 'toggled'
 *
 * TODO: replace by SwitchButtons
 */
export class ToggleButtons extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);
        if (element) {
            element.widgetToggleButtons = this;
            this.widgetElement = element;
        }

        document.addEventListener('click', event => this.toggleClick(event));
    }

    /**
     * Called on button click. Toggles visibility of corresponding target elements on page.
     *
     * @param {Event} event Click event
     */
    toggleClick(event) {
        const toggleElement = event.target.closest('[data-toggle-element]');
        if (toggleElement) {
            const id = toggleElement.dataset.toggleElement;
            const elm = document.getElementById(id);
            elm.classList.toggle('toggle-hide');

            toggleElement.classList.toggle('active', !elm.classList.contains('toggle-hide'));

            // Create event
            const newEvent = new Event('toggled', {bubbles: true, cancelable: false});
            event.currentTarget.dispatchEvent(newEvent);
        }
    }
}


export class SandwichButton extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        this.sandwichButton = this.widgetElement.querySelector('button');
        this.sandwichPane = document.getElementById(this.sandwichButton.dataset.toggle);
        this.sandwichSources = document.querySelectorAll(this.widgetElement.dataset.sandwichSources);

        this.listenEvent(this.sandwichSources, 'epi:replace:content', event => this.onReplaced(event));
        this.listenEvent(window,'resize', event => this.onResize(event));
        this.onResize();
    }

    /**
     * Update sandwich button and menu on every resize. Collapses item list
     * and shows only on button click at small viewports.
     *
     * @param {Event} event The event that triggered the update
     */
    onResize(event) {

        this.sandwichSources.forEach(
            source => this.updateSandwichSource(source)
        )
    }

    onReplaced(event) {
        const sourceElement = event.target;
        const menuDot = this.sandwichPane.querySelector(
            'ul[data-sandwich-source="' + sourceElement.dataset.sandwichSource + '"]'
        );
        if (menuDot) {
            menuDot.remove();
        }

        this.listenEvent(event.detail.data.newTarget, 'epi:replace:content', event => this.onReplaced(event));
        this.sandwichSources = document.querySelectorAll(this.widgetElement.dataset.sandwichSources);
        this.onResize();
    }

    updateSandwichSource(sourceElement) {
        // Find or create dot ul element
        let menuDot = this.sandwichPane.querySelector(
            'ul[data-sandwich-source="' + sourceElement.dataset.sandwichSource + '"]'
        );

        if (!menuDot) {
            menuDot = document.createElement('ul');
            menuDot.dataset.sandwichSource = sourceElement.dataset.sandwichSource;
            this.sandwichPane.appendChild(menuDot);
        }

        // Get source container
        // TODO: handle margin and padding
        const margins = 16;
        const menuList = sourceElement.querySelector('ul');
        let menuSpace =  sourceElement.offsetWidth - margins;

        if (this.widgetElement.classList.contains('hidden')) {
            menuSpace -= 50;
        }

        const listLast = menuList.lastElementChild;
        const listFirst = menuList.firstElementChild;

        if (listLast && listFirst) {
            menuSpace = menuSpace - (listLast.offsetLeft + listLast.offsetWidth - listFirst.offsetLeft);
        }

        //Move from dot/vertical to list/horizontal
        while ((menuSpace > 0) && (menuDot.childElementCount > 0)) {
            const nextItem = menuDot.firstElementChild;
            let items = [nextItem];
            if (nextItem.classList.contains('action-group')) {
                const actionGroup = Utils.getClassValue(nextItem, 'action-group-');
                items = [...menuDot.querySelectorAll(':scope > .action-group-' + actionGroup)];
            }

            items.forEach((item) => {
                menuList.appendChild(item);
                menuSpace -= (item.offsetWidth + margins);
            });
        }

        // Move from list/horizontal to dot/vertical
        while ((menuSpace < 0) && (menuList.childElementCount > 0)) {
            const nextItem = menuList.lastElementChild;

            let items = [nextItem];
            if (nextItem.classList.contains('action-group')) {
                const actionGroup = Utils.getClassValue(nextItem, 'action-group-');
                items = [...menuList.querySelectorAll(':scope > .action-group-' + actionGroup)];
            }

            items.reverse().forEach((item) => {
                menuSpace += (item.offsetWidth + margins);
                menuDot.prepend(item);
            });
        }

        //Hide or show button
        this.widgetElement.classList.toggle('hidden', !this.sandwichPane.querySelector('li'));
    }
}

/**
 * Shortcut handler
 *
 */
export class Shortcuts extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        let shortcuts = element.dataset.shortcuts.split(' ');

        // Filter shortcuts that were already taken
        // TODO: But not for hidden buttons...e.g. in the property move function
        // TODO: remove tooltip of taken shortcuts
        App.shortcuts = App.shortcuts || [];

        // Register new shortcuts
        this.shortcuts = shortcuts.filter((item) => !App.shortcuts.includes(item));
        App.shortcuts.push(...this.shortcuts);
        this.handledShortcuts = this.shortcuts;

        this.shortcuts = this.shortcuts.map(shortcut => shortcut.split('+'));
        this.listenEvent(document,'keydown', (event) => this.onKeydown(event));
    }

    clearWidget() {
        super.clearWidget();

        // Unregister handled shortcuts
        App.shortcuts = App.shortcuts || [];
        App.shortcuts = App.shortcuts.filter((shortcut) => !this.handledShortcuts.includes(shortcut));
        this.handledShortcuts = [];
    }

    onKeydown(event) {
        let isShortcut = false;
        //Only check key events from F1 to F10 or in combination with Alt/Ctrl modifiers
        if (event.ctrlKey || event.altKey || ((event.keyCode >= 112) && (event.keyCode <= 123) )) {

            const key = event.key.toUpperCase(); //String.fromCharCode(event.Code);

            this.shortcuts.forEach(
                (shortcut) => {
                    if (event.ctrlKey && !shortcut.includes('Ctrl')) {
                        return;
                    }

                    if (event.altKey && !shortcut.includes('Alt')) {
                        return;
                    }


                    if (!shortcut.includes(key)) {
                        return;
                    }

                    if (this.widgetElement.classList.contains('hide')) {
                        return;
                    }

                    this.widgetElement.click();
                    isShortcut = true;
                }
            );
        }
        if (isShortcut) {
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
    }
}

/**
 * File, folder and database choose buttons
 *
 */
export class ChooseButtons extends BaseWidget {

    constructor(element, name, parent) {
        super(element, name, parent);

        // Listen button clicks
        this.listenEvent(document, 'click', (event) => {
            if (event.target.matches('.widget-choose button')) {
                const itemtype = event.target.dataset.itemtype;
                const inputElement = event.target.closest('.widget-choose').querySelector('input[type="text"]');

                const options = {
                    title: "Select " + itemtype,
                    value: inputElement.value,

                    height: 600,
                    width: 600,

                    url: event.target.dataset.url,
                    itemtype: itemtype,
                    selectList: itemtype === 'folder',

                    ajaxButtons: 'exclusive',
                    buttonSelect: itemtype === 'folder',
                    selectOnClick: itemtype !== 'folder',

                    onSelect: (element) => {
                        // Set value
                        inputElement.value = element.dataset.value;

                        // Set path and URL
                        let dataList;
                        if (element.dataset.listItemof) {
                            dataList = element.closest('[data-list-name="' + element.dataset.listItemof + '"]');
                        } else if (element.dataset.listName) {
                            dataList = element;
                        }

                        if (dataList) {
                            inputElement.dataset.path = dataList.dataset.value || inputElement.dataset.path;
                            event.target.dataset.url = dataList.dataset.url || event.target.dataset.url;
                        }
                    }
                };

                new SelectWindow(options);
                return false;
            }
        });

        // Listen select events
        this.listenEvent(document, 'epi:select',(event) => {
            let options = {
                title: "Select " + event.detail.data.itemtype,
                height: 600,
                width: 600,
            }
            options = {...options, ...event.detail.data};

            new SelectWindow(options);
            return false;
        });
    }
}


/**
 * Represents a widget for managing code blocks in Docs pages and adding copy-to-clipboard buttons to them.
 *
 * TODO: rename to CodeblockWidget
 *
 * This class provides functionality for creating copy buttons for each code block
 * within the specified container, allowing users to copy the code content to the clipboard.
 */
export class Codeblocks extends BaseWidget {
    /**
     * Creates a new instance of the Codeblocks class.
     *
     * @param {HTMLElement} element The HTML element representing the Codeblocks widget.
     * @param {string} name The name of the Codeblocks widget.
     * @param {BaseWidget} parent The parent widget or null if this is a root widget.
     */
    constructor(element, name, parent) {
        super(element, name, parent);
        /**
         * The collection of code block elements within the Codeblocks widget.
         * @type {NodeListOf<HTMLElement>}
         */
        this.codeblocks = element.querySelectorAll('[data-row-table="docs"] pre code');
        this.codeblocks.forEach(codeblock => this.createCopyButton(codeblock))
    }

    /**
     * Create copy button for specified codeblock, allowing users to copy the code content to the clipboard.
     *
     * @param {HTMLElement} codeblock The HTML <code> element.
     */
    createCopyButton(codeblock) {
        const buttonElm = Utils.spawnFromString('<button class="btn-copy"></button>');
        codeblock.appendChild(buttonElm);
        this.listenEvent(buttonElm, 'click', event => this.copyToClipboard(event));
    }

    /**
     * Copy the content associated with the CopyButton instance to the clipboard.
     *
     * @async
     * @param {MouseEvent} event Click
     * @returns {Promise<void>} A promise that resolves once the content is copied to the clipboard.
     */
    async copyToClipboard(event) {
        const textToCopy = event.target.closest('code').innerText;
        await navigator.clipboard.writeText(textToCopy);
        // TODO: Add tooltip after successful copy? Maybe later
    }
}


/**
 * Show tooltips on click in additon to on hover
 *
 * @deprecated. Tooltips become visible by the css rule for
 *              .doc-content-help[data-help]:hover:after
 */
export class Tooltip extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);
        element.dataset.help = element.getAttribute('title');
        element.removeAttribute('title');
        //this.listenEvent(element, 'click', event => this.onClick(event));
    }

    /**
     * On click event handler: shows the tooltip
     *
     * @param {DragEvent} event Dragstart
     */
    onClick(event) {
        //this.widgetElement.classList.toggle('active');
    }
}

/**
 * Register widget classes in the app
 */
window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['shortcut'] = Shortcuts;
window.App.widgetClasses['sandwich'] = SandwichButton;
window.App.widgetClasses['codeblocks'] = Codeblocks;
window.App.widgetClasses['tooltip'] = Tooltip;
