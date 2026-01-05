/*
 * Sidebars, tab sheets and accordeons - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import Utils from '/js/utils.js';
import {BaseWidget} from '/js/base.js';

/**
 * Resizable Sidebar.
 */
export class ResizableSidebar extends BaseWidget {
    constructor(element, orientation, snapwidth, collapsed) {
        super(element);
        if (!element) {
            return;
        }

        this.wrapper = Utils.getValue(element, 'parentElement');
        this.sidebar = element;
        this.orientation = orientation || 'left';
        this.snapWidth = snapwidth || 150;

        this.collapsed = collapsed || (element ? element.classList.contains('sidebar-init-collapsed') : true);
        this.closeButton = element ? element.querySelector('.btn-close, .btn-apply') : undefined;
        this.empty = element ? element.classList.contains('sidebar-empty') : false;
        this.width = undefined;

        // Set ui key for saving settings
        if (!this.getFrame()) {
            this.widgetElement.dataset.uiKey = this.getController() + '-' + this.getAction() + '-sidebar-' + orientation;
        }

        // Add elements
        const resizerHtml = '<div class="sidebar-resizer sidebar-resizer-' + this.orientation + '"></div>';
        this.sidebar.insertAdjacentHTML('afterbegin', resizerHtml);
        this.resizer = this.sidebar.firstChild;
        this.isResizing = false;

        const imploderHtml = '<div class="sidebar-imploder sidebar-imploder-' + this.orientation + '"></div>';
        this.resizer.insertAdjacentHTML('afterbegin', imploderHtml);
        this.imploder = this.resizer.firstChild;

        let classes = 'sidebar-exploder sidebar-exploder-' + this.orientation;
        if (this.empty) {
            classes = classes + ' sidebar-empty';
        }

        const exploderHtml = '<div class="' + classes + '"></div>';
        this.sidebar.insertAdjacentHTML('afterend', exploderHtml);
        this.exploder = this.sidebar.nextSibling;

        // Event listeners
        this.resizer.addEventListener('mousedown', event => this.toggleResize(event));
        this.imploder.addEventListener('click', event => this.toggleSidebar(event));
        this.exploder.addEventListener('click', event => this.toggleSidebar(event));

        this.listenEvent(this.widgetElement, 'click', (event) => {
                if (event.target.classList.contains('btn-close') || event.target.classList.contains('btn-apply')) {
                    this.hideSidebar();
                } else if (event.target.classList.contains('btn-open')) {
                    if (this.emitEvent('epi:open:tab', undefined, true)) {
                         window.open(event.target.dataset.targetUrl);
                    }
                }
            }
        );

        // Init state
        if (this.collapsed === true) {
            this.hideSidebar();
        } else {
            this.showSidebar();
        }
    }

    /**
     * Hide sidebar.
     */
    hideSidebar(hideExploder = false) {
        this.sidebar.classList.remove('sidebar-expanded');
        this.sidebar.classList.add('sidebar-collapsed');

        this.exploder.classList.remove('sidebar-expanded');
        this.exploder.classList.add('sidebar-collapsed');

        if (hideExploder) {
            this.exploder.classList.add('sidebar-empty');
        }

        // TODO: use events
        if (typeof App !== 'undefined'  && App.accordion) {
            App.accordion.showMain();
        }
    }

    /**
     * Show sidebar.
     *
     * @param force Whether to force open (true) or to respect the user setting (false)
     * @param width Customized sidebar width (optional)
     */
    showSidebar(force, width) {
        if ((width !== undefined) && !this.sidebar.classList.contains('sidebar-expanded')) {
            this.setWidth(width);
        }

        if (force === false) {
            force = Utils.isTrue(this.getSetting('expanded', true));

        }

        if ((force === undefined) || force) {
            this.sidebar.classList.add('sidebar-expanded');
            this.sidebar.classList.remove('sidebar-collapsed');

            this.exploder.classList.add('sidebar-expanded');
            this.exploder.classList.remove('sidebar-collapsed');

            this.exploder.classList.remove('sidebar-full');
        } else {
            this.exploder.classList.add('sidebar-full');
        }

        this.sidebar.classList.remove('sidebar-empty');
        this.exploder.classList.remove('sidebar-empty');

        // TODO: use events
        if (typeof App !== 'undefined' && App.accordion) {
            App.accordion.showPanel(this.sidebar);
        }
    }

    /**
     * Toggle sidebar on click on exploder icon.
     *
     * @param event Click
     */
    toggleSidebar(event) {
        const isExpanded = this.sidebar.classList.contains('sidebar-expanded');
        if (isExpanded) {
            this.hideSidebar();
        } else {
            this.showSidebar();
        }
        this.setSetting('expanded', !isExpanded);
    }

    /**
     * Called on click on resizer element. Creates new mousemove event listener that is firing
     * on every following mousemove and removes it when mouseup event is registered.
     *
     * @param event Mousedown
     */
    toggleResize(event) {
        // TODO: Does not look beautiful, but it works...
        this.isResizing = true;
        this.resizer.classList.add('is-resizing');
        document.body.classList.add('dragging');

        const resizeListener = event => this.resizeSidebar(event);
        document.addEventListener('mousemove', resizeListener, false);
        document.addEventListener('mouseup', () => {
            this.isResizing = false;
            this.resizer.classList.remove('is-resizing');
            document.body.classList.remove('dragging');
            document.removeEventListener('mousemove', resizeListener, false);
            this.setSetting('width', this.width);
        }, false);
    }

    isVisible() {
        return this.sidebar && (this.sidebar.clientWidth > 0);
    }

    /**
     * Set width of sidebar.
     *
     * @param width Width
     */
    setWidth(width) {
        this.width = width;
        if (typeof width == 'string') {
            this.sidebar.style.flexBasis = width;
        } else {
            this.sidebar.style.flexBasis = `${width}px`;
        }
    }

    /**
     * Resize sidebar depending from mouse cursor coordinates.
     *
     * @param event Mousemove
     */
    resizeSidebar(event) {
        if (this.isResizing === false) {
            return;
        }

        event.preventDefault();

        const offset = this.wrapper.getBoundingClientRect().left;
        let size;

        if (this.orientation === 'right')
            size = this.wrapper.clientWidth + this.resizer.clientWidth - (event.x - offset);
        else {
            size = this.resizer.clientWidth + (event.x - offset);
        }

        if (size < this.snapWidth)
            this.hideSidebar();
        else {
            this.setWidth(size);
        }
    }
}

/**
 * Accordion widget
 *
 * Used to toggle sidebars and main content for mobile devices.
 * TODO: merge sidebar and accordion logic
 *
 * Add the following markup to your document:
 * - Add the class 'accordion' to the accordion container
 * - Add the class 'accordion-item' to each accordion item
 * - In addition, add the class 'accordion-main' to the main content item
 * - In addition, for each accordion item, set the data-accordion-item property to a unique name
 *
 * - Add the class accordion-collapsed to each collapsed accordion item.
 *   The class will be removed when the item is expanded and the class accordion-expanded will be added instead.
 *
 * Add the following markup to accordion toggles
 * - Add the class 'accordion-toggle' to each accordion toggle
 * - Add the attribute data-toggle-accordion to each toggle and set the value to the unique name of the accordion item to toggle
 *
 */
export class Accordion extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        this.items = element.querySelectorAll('.accordion-item');
        this.toggles = element.querySelectorAll('.accordion-toggle');
        this.main = element.querySelector('.accordion-main');

        // Attach event listeners
        this.toggles.forEach(elm =>
            this.listenEvent(elm,'click', event => this.onToggleClick(event))
        );
        this.items.forEach(elm => {
            if (!elm.classList.contains('accordion-main')) {
                this.listenEvent(elm,'click', event => this.onLinkClick(event));
            }
        });
    }

    /**
     * Show main panel of accordion.
     */
    showMain() {
        this.showPanel(this.main);
    }

    /**
     * Show an accordion panel
     *
     * @param {HTMLElement} panel Panel element
     * @param {HTMLElement} toggle Target element of toggle event
     */
    showPanel(panel, toggle) {
        if (!panel) {
            return;
        }

        this.toggles.forEach(elm => {
            elm.classList.remove('accordion-active');
        });

        this.items.forEach(elm => {
            elm.classList.remove('accordion-expanded');
            elm.classList.add('accordion-collapsed');
        });

        panel.classList.add('accordion-expanded');
        panel.classList.remove('accordion-collapsed');

        if (!toggle && panel.dataset.accordionItem) {
            toggle = document.querySelector('[data-toggle-accordion="' + panel.dataset.accordionItem + '"]');
        }

        if (toggle) {
            toggle.classList.add('accordion-active');
        }
    }

    /**
     * Hide an accordion panel
     *
     * @param {HTMLElement} panel Panel element
     * @param {HTMLElement} toggle Target element of toggle event
     */
    hidePanel(panel, toggle) {
        if (!panel) {
            return;
        }

        toggle.classList.remove('accordion-active');

        panel.classList.remove('accordion-expanded');
        panel.classList.add('accordion-collapsed');

        this.main.classList.add('accordion-expanded');
        this.main.classList.remove('accordion-collapsed');
    }

    /**
     * Toggle event handler
     *
     * Toggles the panel specified in the data-toggle-accordion property
     *
     * @param {Event} event Click
     */
    onToggleClick(event) {
        const toggle = event.target;
        const panel = document.querySelector('[data-accordion-item=' + toggle.dataset.toggleAccordion + ']');

        if (toggle.classList.contains('accordion-active')) {
            this.hidePanel(panel, toggle);
        } else {
            this.showPanel(panel, toggle);
        }
    }

    /**
     * Link handler
     *
     * Show the main panel if a link targets a section within the main panel
     * (as indicated by a hash fragment).
     *
     * @param {Event} event Click event
     */
    onLinkClick(event) {
        if (event.target.tagName === 'A') {
            const url = event.target.getAttribute('href');
            if (url && url.startsWith('#')) {
                this.showMain();
            }
        }
    }
}


export class Tabsheets extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        this.buttonContainer = this.widgetElement.querySelector('.widget-tabsheets-selectors-tabs');
        this.tabContainer = this.widgetElement.querySelector('.widget-tabsheets-sheets');

        const addButton = this.widgetElement.querySelector('.btn-add');
        if (addButton) {
            const tabAddPane = document.querySelector('#' + addButton.dataset.toggle);
            this.listenEvent(tabAddPane, 'changed', (event) => this.onTabAdd(event));
        }

        this.listenEvent(this.buttonContainer, 'click', (event) => this.onTabClick(event));
    }

    /**
     * Activate default tabsheet
     */
    initWidget() {
        let tabSheet = this.widgetElement.querySelector('.widget-tabsheets-sheet.active');
        if (!tabSheet) {
            tabSheet = this.widgetElement.querySelector('.widget-tabsheets-sheet');
        }

        if (tabSheet) {
            this.showTab(tabSheet.dataset.tabsheetId);
        }
    }

    /**
     * Create a tabsheet if it does not exist yet
     *
     * @param {string} name The tab identifier
     * @param {string} caption The tab caption
     * @return {HTMLElement} The tabsheet element
     */
    createTab(name, caption) {
        let tabSheet = this.widgetElement.querySelector('.widget-tabsheets-sheet[data-tabsheet-id="' + name + '"]');
        let tabButton = this.widgetElement.querySelector('.widget-tabsheets-button[data-tabsheet-id="' + name + '"]');

        if (!tabSheet) {
            tabSheet = Utils.spawnFromString('<div class="widget-tabsheets-sheet empty" data-tabsheet-id="' + name + '"></div>');
            this.tabContainer.appendChild(tabSheet);
        }

        if (!tabButton) {
            caption = caption || 'Details';
            tabButton = Utils.spawnFromString(
                '<div class="widget-tabsheets-button empty" data-tabsheet-id="' + name + '">' +
                '<button class="caption">' + caption + '</button>' +
                '<button class="btn-remove">x</button>' +
                '</div>'
            );

            this.buttonContainer.appendChild(tabButton);
        }

        this.emitEvent('epi:create:tabsheet', {name: name});
        return tabSheet;
    }

    removeTab(name) {

        let tabSheet = this.widgetElement.querySelector('.widget-tabsheets-sheet[data-tabsheet-id="' + name + '"]');
        let tabButton = this.widgetElement.querySelector('.widget-tabsheets-button[data-tabsheet-id="' + name + '"]');

        if (Utils.emitEvent(tabSheet, 'epi:remove:tabsheet', {name: name}, this, true)) {

            if (tabButton && tabButton.classList.contains('active')) {
                const nextButton = tabButton.previousElementSibling || tabButton.nextElementSibling;
                if (nextButton) {
                    this.showTab(nextButton.dataset.tabsheetId);
                }
            }

            tabSheet.remove();
            tabButton.remove();
            App.finishWidgets(tabSheet);
        }
    }

    /**
     * Show a tab
     *
     * @param {string} name
     * @param {string} position Set to 'last' to move the tab to the last position
     * @return {HTMLElement}
     */
    showTab(name, position) {

        const tabSheet = this.widgetElement.querySelector('.widget-tabsheets-sheet[data-tabsheet-id="' + name + '"]');
        const tabButton = this.widgetElement.querySelector('.widget-tabsheets-button[data-tabsheet-id="' + name + '"]');

        if (tabSheet && tabButton) {
            if (position === 'last') {
                this.buttonContainer.appendChild(tabButton);
            }

            tabButton.classList.remove('empty');
            tabSheet.classList.remove('empty');

            this.widgetElement.querySelectorAll('.widget-tabsheets-button').forEach(
                button => button.classList.remove('active')
            );
            this.widgetElement.querySelectorAll('.widget-tabsheets-sheet').forEach(
                sheet => sheet.classList.remove('active')
            );

            tabButton.classList.add('active');
            tabSheet.classList.add('active');

            Utils.emitEvent(tabSheet, 'epi:show:tabsheet');
        }

        const tabCount = this.buttonContainer.querySelectorAll('.widget-tabsheets-button:not(.empty)').length;
        if (tabCount === 0) {
            this.widgetElement.classList.add('empty');
            this.widgetElement.classList.remove('simple');
        } else if (tabCount === 1) {
            this.widgetElement.classList.remove('empty');
            this.widgetElement.classList.toggle('simple', !this.widgetElement.classList.contains('extendable'));
        } else {
            this.widgetElement.classList.remove('empty');
            this.widgetElement.classList.remove('simple');
        }

        return tabSheet;
    }

    /**
     * Spawn a new tab from a tabsheet template
     *
     * Looks for a script element with type="text/template" and class="template template-tabsheet"
     * inside the tabsheet container and instantiates it.
     *
     * @param {string} name Name of the tab and the template
     * @param {string} caption Caption of the new tabsheet
     * @return {HTMLElement}
     */
    loadTab(name, caption) {
        const content = this.spawnFromTemplate('template-tabsheet', {name: name, caption: caption});

        //TODO: prefix the tabsheet name with "properties-filter-" for the properties filter
        const tabsheet = this.createTab(name, caption);
        tabsheet.replaceChildren(content);
        App.initWidgets(tabsheet);

        this.showTab(name);

        return tabsheet;
    }

    /**
     * Set the title of a tabsheet
     *
     * @param {string} name
     * @param {string} title
     */
    setTitle(name, title) {
        const tabButton = this.widgetElement.querySelector('.widget-tabsheets-button[data-tabsheet-id="' + name + '"] button.caption');
        if (tabButton) {
            tabButton.textContent = title;
        }
    }

    /**
     * Fired by the dropdown selector when a value was selected
     *
     * @param{Event} event The changed event
     */
    onTabAdd(event) {
        if (event.type === 'changed') {
            this.loadTab(event.detail.data.id, event.detail.data.label);
            const addButton = this.widgetElement.querySelector('.btn-add');
            const addWidget = this.getWidget(addButton, 'dropdown', false);
            addWidget.closeDropdown();
        }
    }

    onTabClick(event) {
        const selector = event.target.closest('.widget-tabsheets-button');

        if (selector) {
            if (event.target.classList.contains('btn-remove')) {
                this.removeTab(selector.dataset.tabsheetId);
            } else {
                this.showTab(selector.dataset.tabsheetId);
            }
        }
    }

}

/**
 * Activate sections by scrolling
 *
 * Add the following markup to your document:
 * - Add the class 'widget-scrollsync-content' to the scrollable element that contains the sections
 * - Add the class 'widget-scrollsync-section' to each section
 * - Add the ID attribute to each section and assign a unique ID
 *
 * - Add the class 'widget-scrollsync' to the menu that should be synchronized. Menu items must be li-elements.
 * - Add the attribute data-section-id to every menu item and assign the ID of the corresponding section element.
 *
 * Instantiate the ScrollSync class to activate the behavior.
 * Call updateWidget everytime sections were added or removed.
 * You can manually activate a section by calling the activateLi or activateSection method.
 * The widget listens to the event 'epi:move:row' and updates the section order.
 *
 * @constructor
 */
export class ScrollSync extends BaseWidget {
    constructor() {
        super();
        this.content = null;
        this.menu = null;

        this.observer = null;
        this.activeSection = null;
        this.isScrolling = 0;

        this.content = document.querySelector('.widget-scrollsync-content');
        this.menu = document.querySelector('.widget-scrollsync');
        if (this.menu) {
            this.listenEvent(this.menu, 'click', event => this.itemClicked(event));
            this.listenEvent(document, 'keydown', event => this.onKeydown(event));

            this.initScrollObserver();
            this.updateWidget();
        }

        this.listenEvent(document, 'epi:focus:widgets', (event) => this.focusWidget(event));
    }


    /**
     * Init widget
     */
    initWidget() {
        this.scrollToHashFragment();
    }

    /**
     * Creates widget.
     */
    updateWidget() {
        this.isScrolling += 1;
        const sections = this.getSections();

        sections.forEach(section => {
            if (section.widgetScrollSync === undefined) {
                section.widgetScrollSync = this;
                if (this.observer) {
                    this.observer.observe(section);
                }
            }
        });

        this.isScrolling -= 1;
    }


    /**
     * Initialize IntersectionObserver object that observes scrollbox sections.
     * When a section is in viewport, the corresponding menu item gets highlighted.
     */
    initScrollObserver() {

        const options = {
            threshold: [0],
            root: this.content
        };

        // The callback is triggered each time the intersection state of an element changes
        // isIntersecting is true when element and viewport are overlapping
        // isIntersecting is false when element and viewport don't overlap
        this.observer = new IntersectionObserver(entries => {
            // TODO: still skips menu items
            entries.forEach(entry => {
                entry.target.isVisible = (entry.isIntersecting === true);
                entry.target.isAbove = entry.boundingClientRect.y < entry.rootBounds.y;
            });

            let section = this.activeSection;

            // Active section vanished from view -> find next section
            if (this.activeSection && !this.activeSection.isVisible) {
                if (section.isAbove) {
                    while (
                        !section.isVisible &&
                        section.nextElementSibling
                        ) {
                        section = section.nextElementSibling;
                        // !section.nextElementSibling.classList.contains('widget-scrollsync-section')
                    }
                } else {
                    while (
                        !section.isVisible &&
                        section.previousElementSibling
                        ) {
                        section = section.previousElementSibling;
                        // !section.previousElementSibling.classList.contains('widget-scrollsync-section')
                    }
                }
            }
            // No active section -> activate the first visible section
            else if (!section && entries[0].target.isVisible) {
                section = entries[0].target;
            }

            if (section && section.classList.contains('widget-scrollsync-section')) {
                this.scrollToLi(this.getLi(section), true);
                this.scrollToSection(section, false);
            }
        }, options);
    }


    /**
     * Get sections of scrollbox.
     *
     * @returns {NodeListOf<Element>} List of sections
     */
    getSections() {
        return this.content.querySelectorAll('.widget-scrollsync-section');
    }

    /**
     * Get corresponding menu item of scrollbox section.
     *
     * @param section Section in scrollbox
     * @returns {HTMLElement} Corresponding menu item
     */
    getLi(section) {
        return (section && this.menu) ? this.menu.querySelector('[data-section-id="' + section.id + '"]') : null;
    }

    /**
     * Get id of scrollbox section.
     *
     * @param li Scrollbox section
     * @returns {String} Id of section
     */
    getSection(li) {
        return li ? document.getElementById(li.dataset.sectionId) : null;
    }

    /**
     * Called on click on menu item. Updates menu and scrollbox.
     *
     * @param event Click
     */
    itemClicked(event) {
        const li = event.target.closest('li');
        const section = this.getSection(li);
        this.scrollToLi(li, false);
        this.scrollToSection(section, true);
        this.expandSection(section);
    }

    /**
     * Called on widget focus changes. Updates menu and scrollbox.
     *
     * @param {CustomEvent} event epi:focus:widgets
     */
    focusWidget(event) {
        if (!event.detail.data.focus) {
            return;
        }
        const section = event.target.closest('.widget-scrollsync-section');
        if (section) {
            this.scrollToLi(this.getLi(section), true);
            this.scrollToSection(section, false);
        }
    }

    /**
     * Page up or down
     *
     * @param {Event} event Keydown
     */
    onKeydown(event) {
        let nextSection;
        if (event.key === 'PageDown') {
            if (!Utils.bottomAboveViewport(this.activeSection, this.content)) {
                return;
            }
            nextSection = Utils.getNextVisibleSibling(this.activeSection, '.widget-scrollsync-section');
        } else if (event.key === 'PageUp') {
            if (!Utils.topBelowViewport(this.activeSection, this.content)) {
                return;
            }

            nextSection = Utils.getPrevVisibleSibling(this.activeSection, '.widget-scrollsync-section');
        }

        if (nextSection) {
            this.activateSection(nextSection);
            event.preventDefault();
        }
    }


    /**
     * Called on page load. Scrolls directly to given section on page.
     *
     * @returns {boolean} False if section id does not exist
     */
    scrollToHashFragment() {
        const hash = window.location.hash;
        if (!hash) {
            return false;
        }

        let section;
        try {
            section = document.querySelector(hash);
        } catch {
            section = null;
        }

        if (!section) {
            return false;
        }

        this.expandSection(section);
        this.activateSection(section);
        this.focusSection(section);
    }

    /**
     * Scroll to the section and to the li
     *
     * @param {HTMLElement} section
     */
    activateSection(section) {
        this.scrollToLi(this.getLi(section), true);
        this.scrollToSection(section, true);
    }

    /**
     * Scroll the section to the top
     *
     * @param {HTMLElement} section
     */
    focusSection(section) {
        Utils.scrollToTop(section, section.closest('.widget-scrollsync-content'));
    }

    /**
     * Scroll to the li and to the section
     *
     * @param {HTMLElement} li
     */
    activateLi(li) {
        this.scrollToLi(li, true);
        this.scrollToSection(this.getSection(li), true);
    }

    /**
     * Highlight menu item and scroll into view if needed. Called on page load and after item was clicked.
     *
     * @param li Menu item
     * @param scroll If true, scroll menu item into view if needed
     * @returns {boolean}
     */
    scrollToLi(li, scroll = false) {
        if (this.isScrolling > 0) {
            return false;
        }

        if (li && li.classList.contains('active')) {
            return false;
        }

        this.isScrolling += 1;

        if (li) {
            if (scroll) {
                Utils.scrollIntoViewIfNeeded(li, li.closest('.frame-content'), false, 'y');
            }

            this.menu.querySelectorAll('li').forEach(elm =>
                elm.classList.remove('active')
            );
            li.classList.add('active');
        }
        this.isScrolling -= 1;
    }

    /**
     * Expand a section
     *
     * @param {HTMLElement} section
     * @param {boolean} force Usually, sections marked with the class doc-section-collapsed,
     *                        stay collapsed. You can force expanding by this parameter.
     */
    expandSection(section, force = false) {
        if (force || (section && !section.classList.contains('doc-section-collapsed'))) {
            Utils.emitEvent(section, 'epi:focus:section', {}, this);
        }
    }

    /**
     * Highlight scrollbox section and scroll into view if needed. Called on page load and after
     * corresponding menu item was selected.
     *
     * @param {HTMLElement} section Section to focus
     * @param {boolean} scroll If true, scroll section into view if needed
     * @returns {boolean}
     */
    scrollToSection(section, scroll = false) {
        if (this.isScrolling > 0) {
            return false;
        }

        //TODO: what for?
        if (section === this.scrollToSection) {
            return false;
        }

        this.isScrolling += 1;
        this.activeSection = section;

        if (section) {
            if (scroll) {
                window.location.hash = '#' + section.id;
                Utils.scrollIntoViewIfNeeded(section, section.closest('.widget-scrollsync-content'), false, 'y');
            }

            if (section.parentElement) {
                [...section.parentElement.children].forEach(elm => {
                        elm.classList.remove('active');
                    }
                );
            }

            this.setActive(section);
        }
        this.isScrolling -= 1;
    }

    /**
     * Add classed and change data attributes for all section related elements
     *
     * @param {HTMLElement} section The section element
     */
    setActive(section) {
        // Add class to section
        section.classList.add('active');

        // Add class to secondary panes (-> notes)
        const note = document.querySelector('.doc-section-note[data-section-id="' + section.id + '"]');
        Utils.toggleClass(
            'active',
            note,
            document.querySelectorAll('.doc-section-note')
        );

        const notesContainer = note ? note.closest('.widget-tabsheets-sheets') : undefined;
        Utils.scrollIntoViewIfNeeded(note, notesContainer);

        // Update links and buttons (so they stay at the section after clicking edit or save or cancel).
        // Add a data-target attribute containing the table prefixed root id to all links and forms.
        // A hash fragment or hash parameter will be added to the links or forms respectively.
        const doc = section.closest('.widget-document');
        if (doc) {
            const target = doc.dataset.rootTable + '-' + doc.dataset.rootId;
            document.querySelectorAll('a[data-target="' + target + '"]').forEach(
                (button) => {
                    const href = button.getAttribute('href');
                    if (href) {
                        const url = new URL(href, App.baseUrl);
                        url.hash = '#' + section.id;
                        button.setAttribute('href', url.toString());
                    }
                }
            );

            const form = doc.querySelector('form[data-target="' + target + '"]');
            if (form) {
                const href = form.getAttribute('action');
                if (href) {
                    const url = new URL(href, App.baseUrl);
                    url.searchParams.set('hash', section.id);
                    form.setAttribute('action', url.toString());
                }
                if (form.dataset.proceedUrl) {
                    const url = new URL(form.dataset.proceedUrl, App.baseUrl);
                    url.hash = '#' + section.id;
                    form.dataset.proceedUrl = url.toString();
                }
                if (form.dataset.cancelUrl) {
                    const url = new URL(form.dataset.cancelUrl, App.baseUrl);
                    url.hash = '#' + section.id;
                    form.dataset.cancelUrl = url.toString();
                }
            }
        }

        // TODO: emit event of section (the ScrollSync doesn't have a widget element)
        //this.emitEvent('epi:activate:section',{id:section.id})
    }
}

/**
 * Async load content into a widget
 *
 * If the widget is a tabsheet, the content will not be loaded until the tabsheet is activated.
 * // TODO: What if it is the first, already visible tab?
 */
export class ContentLoader extends BaseWidget {
    constructor(element) {
        super(element);

        this.tabsheet = undefined;
        this.loaded = false;
        this.url = this.widgetElement.dataset.url;
    }

    initWidget() {

        // Load when tabsheet is shown
        this.tabsheet = this.widgetElement.closest('.widget-tabsheets-sheet');
        if (this.tabsheet) {
            // TODO: return a listener ID that can be used to remove the listener without exactly knowing the handler
            this.listenEvent(this.tabsheet, 'epi:show:tabsheet', (event) => this.loadTabsheet(event));
        } else {
            App.loadDataSnippets(url, this.widgetElement);
        }
    }

    loadTabsheet(event) {
        if (this.loaded) {
            return;
        }
        this.loaded = true;
        App.loadDataSnippets(this.url, this.widgetElement);
        this.unlistenEvent(this.tabsheet, 'epi:show:tabsheet');
    }
}

window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['tabsheets'] = Tabsheets;
window.App.widgetClasses['loadcontent'] = ContentLoader;
