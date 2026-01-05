/*
 * Init widgets - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 */

// TODO: import in app.js instead of adding functions to window.App
// TODO: Create the registry in widgets.js
// TODO: Revise import order

// The widget classes are not all used directly. They are imported here,
// so they can register their widget classes.
// For example, in tables.js you find at the bottom:
//   window.App.widgetClasses = window.App.widgetClasses || {};
//   window.App.widgetClasses['table'] = TableWidget;

import {DocumentWidget, AnnoSelectorWidget} from './documents.js';
import {DropdownWidget, DropdownSelectorWidget} from './dropdowns.js';
import {FilterWidget} from './filter.js';
import {TableWidget, DragItemsWidget} from './tables.js';
import {TreeWidget} from './trees.js';
import {DragAndDrop} from './dragdrop.js';
import {ScrollPaginator} from './paginator.js';
import {ResizableSidebar, Accordion, Tabsheets, ScrollSync, ContentLoader} from './layout.js';
import {ConfirmWindow, MessageWindow, PopupWindow, MainFrame, TabFrame} from './frames.js';
import {HighlightText} from './highlight.js';
import {JsonEditor} from './editors.js';
import {MapWidget} from './map.js';
import {GridWidget} from './grid.js';
import {ImagesWidget} from './images.js';
import {UploadWidget} from './uploads.js';
import {SandwichButton, ChooseButtons, SwitchButtons, ToggleButtons} from './buttons.js';
import {JobWidget} from './jobs.js';
import {ServiceButtonWidget, ServiceWidget, ReconcileButtonWidget} from './services.js';
import {PlotWidget} from './plots.js';

import './dropzone/dropzone.js';
Dropzone.autoDiscover = false;

import Utils from '/js/utils.js';

/**
 * Init app wide widgets
 *
 */
window.App.initApp = function() {

    if (!App.mainframe && (MainFrame !== undefined)) {
        App.mainframe = new MainFrame();
    }

    if (!App.confirmWindow && (ConfirmWindow !== undefined)) {
        App.confirmWindow = new ConfirmWindow();
    }

    // Switch buttons
    if (typeof SwitchButtons === 'function') {
        if (App.switchbuttons === undefined) {
            App.switchbuttons = new SwitchButtons(document);
        }
    }

    // Toggle buttons
    // TODO: replace by switch buttons
    if (typeof ToggleButtons === 'function') {
        if (document.widgetToggleButtons === undefined) {
            new ToggleButtons(document);
        }
    }

    // File and folder chooser
    new ChooseButtons(document, 'filename');

    // Synchronized scrolling (layout.js)
    if (!App.scrollsync && (ScrollSync !== undefined)) {
        App.scrollsync = new ScrollSync();
    }

    //Sidebars (layout.js)
    if (typeof ResizableSidebar === 'function') {
        App.sidebarleft = new ResizableSidebar(document.querySelector('.sidebar-left'), 'left');
        App.sidebarright = new ResizableSidebar(document.querySelector('.sidebar-right'), 'right');
    }

    //Accordion (layout.js)
    // TODO: merge accordion and sidebar logic, without App.sidebarleft and App.sidebarright and App.accordion
    if (typeof Accordion === 'function') {
        App.accordion = new Accordion(document.querySelector('.accordion'), 'accordion');
    }

};

/**
 * Init widgets
 *
 * // TODO: use JS modules / JS import to load all the widgets
 * // TODO: make the code DRY
 *
 * @param {HTMLElement|document} scope Set to the parent element of widgets, used after AJAX requests
 */
window.App.initWidgets = function(scope=null) {

    // Count the number of function calls
    App.widgetsInitializing = (App.widgetsInitializing || 0) + 1;

    if (scope === null) {
        scope = document;
    }

    /**
     * Attach all widget classes
     *
     * To implement a widget class, inherit it from BaseWidget.
     * Add the following snippets to the bottom of the widget class.
     * This registers the class as a widget in the application
     * (make sure to replace 'filter' and FilterWidget by your css identifier and class name):
     *
     * window.App.widgetClasses = window.App.widgetClasses || {};
     * window.App.widgetClasses['filter'] = FilterWidget;
     */
    for (const [cssName, widgetClass] of Object.entries(window.App.widgetClasses)) {
        Utils.querySelectorAllAndSelfAndContainer(scope,'.widget-' + cssName).forEach(
            widgetElement => App.createWidget(widgetElement, cssName, scope)
        );
    }

    // Finally, emit finished event
    App.widgetsInitializing -= 1;
    if (App.widgetsInitializing === 0) {
        let event = new CustomEvent(
            'epi:init:widgets',
            {
                bubbles: true,
                cancelable: false,
                detail: {
                    data: {},
                    sender: this
                }
            }
        );
        document.dispatchEvent(event);
    }
};

/**
 * Shutdown widgets
 *
 * @param {HTMLElement|document} scope
 */
window.App.finishWidgets = function(scope=null) {
    if (scope) {
        App.emitEvent(scope, 'epi:remove:content');
    }

    for (const [cssName, widgetClass] of Object.entries(window.App.widgetClasses)) {
        Utils.querySelectorAllAndSelf(scope,'.widget-' + cssName).forEach(
            widgetElement => {
                widgetElement.widgets = widgetElement.widgets || {};
                const widgetObject = widgetElement.widgets[cssName];
                if (widgetObject) {
                    widgetObject.clearWidget();
                }
            }
        );
    }
};

/**
 * Search the widget inside the element and in the element's ancestors
 *
 * @param {Element} element
 * @param {string} cssName The widget name
 * @return {BaseWidget}
 */
window.App.findWidget = function(element, cssName) {
    if (!element) {
        return undefined;
    }

    let widgetElement = element.querySelector('.widget-' + cssName);
    widgetElement = widgetElement ? widgetElement : element.closest('.widget-' + cssName);

    if (widgetElement) {
        widgetElement.widgets = widgetElement.widgets || {};
        return widgetElement.widgets[cssName];
    } else {
        return undefined;
    }
}

/**
 * Get widget of the element by name
 *
 * @param {HTMLElement} element An element which has widgets attached
 * @param {string} cssName The css name used to initialize the widget
 * @return {BaseWidget|undefined}
 */
window.App.createWidget = function(element, cssName, scope) {
    if (!element) {
        return;
    }

    element.widgets = element.widgets || {};
    let widget = element.widgets[cssName];

    if (!widget) {
        const widgetClass = App.widgetClasses[cssName];
        if (widgetClass) {
            widget = new widgetClass(element, cssName);
        }
    } else {
        widget.updateWidget(scope);
    }

    return widget;
}

window.App.showDialog = function(event) {
    const dialog = new MessageWindow(event.detail.data || {});
    window.App.addWidget(event.detail.data.id || undefined, dialog);
};

window.App.hideDialog = function(event) {
    const dialog = window.App.clearWidget(event.detail.data.id || undefined);
    if (dialog) {
        dialog.closeWindow();
    }
};

/**
 * Open a popup window and load the URL
 *
 * ### Options
 * - url: Url to be loaded
 * - element: Element to be shown
 *
 * Further options are passed to showData()
 *
 * @param {String|Object|undefined} data The URL to be loaded, the element to be shown or undefined
 *     if the options are provided as the second parameter.
 * @param {Object} options
 *
 * @returns {PopupWindow}
 */
window.App.openPopup = function(data, options) {

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

    // Remove old popup
    if (options.name) {
        App.hidePopup(options.name);
    }

    // Create new popup
    const popup = new PopupWindow();
    if (options.name) {
        App.addWidget(options.name, popup);
    }

    popup.showData(options);

    return popup;
}

window.App.hidePopup = function(name) {
    const dialog = window.App.clearWidget(name);
    if (dialog) {
        dialog.closeWindow();
    }
    return dialog;
};

window.App.initApp();
window.App.initWidgets();
