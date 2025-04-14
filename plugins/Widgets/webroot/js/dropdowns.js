/**
 * Dropdown widgets - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {BaseWidget} from '/js/base.js';
import Utils from '/js/utils.js';

/**
 * Base class for dropdown widgets
 */
class DropdownWidgetBase extends BaseWidget {

    constructor(element, name, parent) {
        super(element, name, parent);

        // TODO: @deprecated, use App.findWidget() instead
        element.widgetDropdownSelector = this;

        this.input_id;
        this.pane;
        this.pane_id;
    }

    /**
     * Check if dropdown pane is expanded or not.
     *
     * @returns {boolean} True if expanded, false if collapsed
     */
    isOpen() {
        return this.widgetElement.classList.contains('active');
    }

    /**
     * Align the dropdown pane to the toggle or a container
     *
     * There are three cases:
     *  a) The pane is inside a wrapper that also contains the input:
     *    The pane will be resized to fit the input.
     *
     *  b) The pane was moved to the body element:
     *     It will be absolutely positioned below the input.
     *
     *  c) The toggle or the container has a data-pane-align-to attribute with a css selector:
     *     The pane will be aligned to the closest element matched by the selector.
     *
     * @param {boolean} first True if the function is called for the first time.
     *                        In this case, it is called a second time to adjust for potential scrollbar
     *                        changes due to positioning.
     */
    positionDropdown(first = true) {
        if (!this.widgetElement || !this.pane || this.pane.classList.contains('widget-dropdown-pane-frame')) {
            return;
        }

        // The reference element to align the pane to.
        // - Usually the container
        // - Alternatively a parent element matched by the selector provided in data-pane-align-to.
        // TODO: can this.input_label and this.input_toggle be removed? Do widgets without this.widgetElement exist?
        let alignTo = this.widgetElement.dataset.paneAlignTo;
        alignTo= alignTo ? this.widgetElement.closest(alignTo) : undefined;
        alignTo = alignTo || this.widgetElement || this.input_label || this.input_toggle;
        let alignRect = alignTo.getBoundingClientRect();

        // Fix width for small devices
        if (window.innerWidth < 500) {
            alignRect = new DOMRect(0, alignRect.top, window.innerWidth, alignRect.height);
        }

        // Shift vertically if requested
        let alignToY= this.widgetElement.dataset.paneAlignToY;
        alignToY = alignToY ? this.widgetElement.closest(alignToY) : undefined;
        if (alignToY) {
            const alignRextY = alignToY.getBoundingClientRect();
            alignRect = new DOMRect(alignRect.left, alignRextY.top, alignRect.width, alignRextY.height);
        }

        // Calculate the target width of the pane:
        // - The panes scroll width or the reference element width, whichever is bigger.
        // - If the pane contains a tree, at least the width of the widest node label
        let targetWidth = this.pane.scrollWidth + [...this.pane.querySelectorAll('.tree-content label')].reduce(
            (width, element) => Math.max(width, element.scrollWidth - element.parentElement.clientWidth)
            , 0
        );
        targetWidth = Math.max(targetWidth, alignRect.width);

        // Width of the content container (window, main content, sidebar, popup)
        // to prevents the dropdown pane growing bigger than the content container width.
        let referenceWidth;
        let referenceOffsetLeft;
        const contentPane = this.getFrame(false);
        if  (contentPane && (contentPane !== document)) {
            referenceWidth = contentPane.getBoundingClientRect().width;
            referenceOffsetLeft = this.widgetElement.getBoundingClientRect().left - contentPane.getBoundingClientRect().left;
        } else {
            referenceWidth = window.innerWidth;
            referenceOffsetLeft = alignRect.left;
        }

        // Contrain the pane size
        const maxWidth = Math.min(referenceWidth - referenceOffsetLeft, 600);
        const maxHeight = (window.innerHeight - alignRect.bottom - 50);
        const minHeight = 250;

        // Constrain the pane size
        this.pane.style.maxHeight = maxHeight + 'px';
        if (this.pane.offsetHeight > maxHeight) {
            this.pane.style.height = maxHeight + 'px';
        }

        // Case a) The pane is inside a wrapper that also contains the input:
        if (!this.pane.classList.contains('widget-dropdown-pane-moved')) {
            const minWidth = 100;
            this.pane.style.width = Math.min(Math.max(minWidth, targetWidth), maxWidth) + 'px';
        }

        // Cases b) and c) The pane is moved to the body element and/or aligned to another element
        else {
            // Flip position if the pane does not fit
            const spaceLeft = window.innerWidth - alignRect.left;
            const spaceRight = window.innerWidth - alignRect.right;
            const spaceTop = alignRect.top;
            const spaceBottom = window.innerHeight - alignRect.bottom;

            let position = this.pane.dataset.widgetDropdownPosition || 'bottomleft';

            if ((spaceLeft < targetWidth) && (position.includes('left'))) {
                const newPosition = (spaceRight < targetWidth) ? 'full' : 'right';
                position = position.replace('left', newPosition);
            }
            else if ((spaceRight < targetWidth)  && (position.includes('right'))) {
                const newPosition = (spaceLeft < targetWidth) ? 'full' : 'left';
                position = position.replace('right', newPosition);
            }
            else if ((spaceLeft < targetWidth) && (!position.includes('right'))) {
                const newPosition = (spaceLeft < targetWidth) ? 'full' : 'left';
                position = position.replace('right', newPosition);
            }
            else if ((spaceRight < targetWidth) && (!position.includes('left'))) {
                const newPosition = (spaceRight < targetWidth) ? 'full' : 'right';
                position = position.replace('left', newPosition);
            }

            if (spaceTop < minHeight) {
                position = position.replace('top', 'bottom');
            } else if (spaceBottom < minHeight) {
                position = position.replace('bottom', 'top');
            }

            // Position the pane
            if (position === 'left') {
                // Left align
                this.pane.style.left = (alignRect.left - targetWidth) + 'px';
                this.pane.style.width = targetWidth + 'px';
                this.pane.style.top = alignRect.top + 'px';
            } else if (position === 'right') {
                // Right align
                this.pane.style.left = alignRect.right + 'px';
                this.pane.style.width = targetWidth + 'px';
                this.pane.style.top = alignRect.top + 'px';
            } else if (position === 'full') {
                // Full width align
                this.pane.style.left = '0px';
                this.pane.style.width = '100%';
                this.pane.style.top = alignRect.top + 'px';
            } else if (position === 'bottomright') {
                // Align to the bottom right corner of the toggle
                this.pane.style.left = (alignRect.right - targetWidth) + 'px';
                this.pane.style.width = targetWidth + 'px';
                this.pane.style.top = (alignRect.bottom + 1) + 'px';
            } else if (position === 'topright') {
                // Align to the top right corner of the toggle
                this.pane.style.left = (alignRect.right - targetWidth) + 'px';
                this.pane.style.width = targetWidth + 'px';
                this.pane.style.top = (alignRect.top - 1 - this.pane.clientHeight) + 'px';
            } else if (position === 'topleft') {
                // Align to the top right corner of the toggle
                this.pane.style.left = alignRect.left + 'px';
                this.pane.style.width = targetWidth + 'px';
                this.pane.style.top = (alignRect.top - 1 - this.pane.clientHeight) + 'px';
            } else if (position === 'topfull') {
                // Align to the top of the toggle with full width
                this.pane.style.left = '0px';
                this.pane.style.width = '100%';
                this.pane.style.top = (alignRect.top - 1 - this.pane.clientHeight) + 'px';
            } else if (position === 'bottomfull') {
                // Align to the bottom of the toggle with full width
                this.pane.style.left = '0px';
                this.pane.style.width = '100%';
                this.pane.style.top = (alignRect.bottom + 1) + 'px';
            } else {
                // Align below the toggle and adjust width to the toggle or the minWidth
                this.pane.style.left = alignRect.left + 'px';
                this.pane.style.top = (alignRect.bottom + 1) + 'px';
                this.pane.style.width = Math.max(targetWidth, alignRect.width) + 'px';
            }
        }

        if (first) {
            this.positionDropdown(false);
        }
    }
}
/**
 * Create a dropdown button with a pane
 *
 * A dropdown consists of a toggle button and of
 * a pane containing the content. The pane is hidden by default
 * and will be toggled by clicking the button.
 *
 * Optionally, you can wrap the button in a container element.
 * The pane will be positioned below the container element and get
 * the width of the container.
 *
 *  Add the class widget-dropdown-toggle to the toggle button.
 *  Add the class widget-dropdown-pane to the dropdown pane.
 *
 *  To connect the toggle button and the pane,
 *  make sure the dropdown pane has an ID and add
 *  data-toggle="idofthedropdownpane" to the toggle.
 *
 *  Add the class widget-dropdown to the container.
 *  If you don't have a containing element, add the class
 *  widget-dropdown to the toggle button.
 *
 * You can align the pane by adding one of the following values
 * to the pane's data-widget-dropdown-position attribute:
 * - bottomleft: Bottom left corner of the toggle (default)
 * - bottomright: Bottom right corner of the toggle
 * - left: Left side next to the button
 * - right: Right side next to the button
 * - topright: Top right corner, the pane will open to the top.
 *
 * @param wrapper The HTML element containing the toggle (e.g. an input group)
 *                or the toggle element itself.
 * @constructor
 */
export class DropdownWidget extends DropdownWidgetBase {
    constructor(element, name, parent) {
        super(element, name, parent);

        if (this.widgetElement.classList.contains('widget-dropdown-toggle')) {
            this.toggle = this.widgetElement;
        } else {
            this.toggle = this.widgetElement.querySelector('.widget-dropdown-toggle');
        }

        if (!this.toggle) {
            return;
        }

        this.toggle.widgetDropdown = this;
        this.detachPane();

        this.listenEvent(this.toggle,'click', event => this.toggleDropdown(event));
        this.listenEvent(document,'click', event => this.outsideDropdown(event));
        this.listenEvent(window,'resize', event => this.positionDropdown());
    }

    /**
     * Move the pane element to the direct childlist of body.
     * This makes it possible to position the pane.
     */
    detachPane() {
        // Remove old pane (if updated by data snippet mechanism)
        const oldPane = document.querySelector('#' + this.toggle.dataset.toggle + '.widget-dropdown-pane-moved');
        if (oldPane) {
            oldPane.remove();
        }

        // Move pane to body element
        const selector = '#' + this.toggle.dataset.toggle;
        this.pane = document.querySelector(selector);

        if (this.pane) {
            document.querySelector('body').append(this.pane);
            this.pane.classList.add('widget-dropdown-pane-moved');
        }
        this.positionDropdown();
    }

    /**
     * Toggle dropdown pane.
     *
     * @param event Click
     */
    toggleDropdown(event) {
        if (this.widgetElement.classList.contains('active')) {
            this.closeDropdown(event);
        } else {
            this.openDropdown(event);
        }
    }

    /**
     * Check if click event occured outside or inside dropdown wrapper.
     *
     * @param {Event} event Click
     * @returns {boolean} True if mouseclick was inside the dropdown pane
     */
    outsideDropdown(event) {
        if (this.widgetElement.contains(event.target)) {
            return true;
        } else if (this.pane && this.pane.contains(event.target)) { //  && event.target.matches('div.checkbox *, .selector-grouplabel *, input')
            return true;
        } else {
            this.closeDropdown(event);
        }
    }

    /**
     * Collapse dropdown pane.
     *
     * @param event Click
     */
    closeDropdown(event) {
        this.widgetElement.classList.remove('active');
        if (this.pane) {
            this.pane.classList.remove('active');
        }
        if (this.toggle) {
            this.toggle.classList.remove('active');
        }
    }

    /**
     * Expand dropdown pane.
     *
     * @param event Click
     */
    openDropdown(event) {
        this.widgetElement.classList.add('active');
        this.pane.classList.add('active');
        this.toggle.classList.add('active');

        this.positionDropdown();

        const input = this.pane.querySelector('input[type="text"]');

        if (input && !input.readOnly && input.select) {
            input.select();
        }

    }
}

/**
 * Create a dropdown selector
 *
 * A dropdown selector consists of a toggle and of
 * a pane containing the content. The pane is hidden by default
 * and will be toggled when activating the toggle.
 *
 * There are two types of toggles:
 * - Search mode: The toggle is an input element. Entering the input will open the pane,
 *               you can type text to search items in the pane.
 * - Button mode: Clicking the toggle opens or closes the pane.
 *
 * There are three types of panes from which you can choose:
 * - Static pane: The pane is generated outside the dropdown selector
 *                 and contains the items.
 * - Dynamic pane: An empty pane is generated outside or by the dropdown selector
 *                and dynamically filled from outside when the epi:dropdown:load event is fired.
 * - Ajax pane: When entering the widget or searching items,
 *              the pane will be requested from the server.
 *
 *  There are two modes of the dropdown selector:
 *  - Choose: Choose one value from a list of items (default)
 *  - Select: Select multiple values from a checkbox list
 *            (when the pane has the class widget-checkboxlist).
 *
 *  Add the following markup to use the widget:
 *  - Wrapper: a div with the class "widget-dropdown-selector", usually
 *             generated in PHP by using the ReferenceWidget.
 *
 *             The wrapper supports the following attributes:
 *
 *             - data-url Optional, only for ajax panes.
 *                        Contains the URL to retrieve the pane.
 *                        The endpoint needs to support a term query parameter
 *                        for text input dropdowns to search items.
 *             - data-pane-id The ID of the pane that will be toggled.
 *             - data-pane-align-to Optional. The pane will be aligned to the wrapper
 *                                  by default. Alternatively, another element that contains
 *                                  the wrapper can be used as reference. In this case,
 *                                  provide a css selector to address the container.
 *
 *             Example to generate the widget for static panes (pane attribute):
 *
 *             $this->Form->input('projects',[
 *               'type' => 'reference',
 *               'paneAlignTo' => '.input-group', // Align to a container
 *               'paneId' => $paneId,             // Optional. Allows moving the pane to the body element.
 *               'pane' => $pane,                 // <- The pane div (see below)
 *               'search' => true                 // Generate a text input
 *             ])
 *
 *            Example to generate the widget for dynamic panes
 *            (you should listen to the epi:dropdown:load event and fill this.pane):
 *
 *             $this->Form->input('projects',[
 *               'type' => 'reference',
 *               'paneAlignTo' => '.input-group', // Align to a container
 *               'search' => true                 // Generate a text input
 *             ])
 *
 *            Example to generate the widget for ajax panes (url attribute):
 *
 *             $this->Form->input('projects',[
 *               'type' => 'reference',
 *               'paneAlignTo' => '.input-group', // Align to a container
 *               'paneId' => $paneId,             // Optional. Allows moving the pane to the body element.
 *               'url' => $url,                   // <- The url to request the pane (see below)
 *               'search' => true                 // Generate a text input
 *             ])
 *
 * - Pane: A div containing the items from which to choose.
 *
 *         The PHP reference widget will automatically create the pane div when
 *         used with the URL parameter. Otherwise, create the div yourself.
 *
 *         If the div has an ID (optional), the pane will be moved to the body element,
 *         which allows positioning the pane on top of all other page content.
 *
 *         Add the following classes:
 *         - Mandatory: widget-dropdown-pane
 *         - Optional: widget-scrollbox if you want to use the scroll paginator
 *                     for loading more items
 *         - Optional: widget-checkboxlist to allow selecting multiple items
 *                     This will prevent that the pane closes on clicks.
 *
 *         Inside the pane, add an element with a data-snippet parameter.
 *         The data snippet will be replaced when loading the pane content by
 *         ajax requests. E.g.: <ul data-snippet="widget-reference">...</ul>
 *
 *         The PHP reference widget by default uses the data snippet name "widget-reference".
 *         You can change it with the paneSnippet option.
 *
 *         The data snippet finally contains the items. Add the following attributes
 *         to the items:
 *         - data-value (default) or data-id: The ID of the item.
 *           When using data-id, add the attribute data-list-value="id" to the pane.
 *
 *  The widget (=wrapper) emits a changed event when items are selected that,
 *  for example, is observed in the FilterSelector (filter.js)
 *
 * @param wrapper
 * @constructor
 */
export class DropdownSelectorWidget extends DropdownWidgetBase {
    constructor(element, name, parent) {
        super(element, name, parent);

        // TODO: replace null with undefined?
        this.input_id = null;
        this.pane = null;
        this.pane_id = null;

        // From which attribute will the value be retrieved, e.g. data-value or data-id?
        this.valueAttribute = 'value';

        this.url = null;
        this.inputTimeout = 200;

        this.input_id = this.widgetElement.querySelector('input[type="hidden"]');
        this.input_label = Utils.querySelectorAndSelf(this.widgetElement,'input[type="text"],input[type="button"]');
        this.input_toggle = Utils.querySelectorAndSelf(this.widgetElement,'input[type="text"],input[type="button"],button');

        this.detachPane();

        this.url = this.widgetElement.dataset.url;
        this.param = this.widgetElement.dataset.urlParam || 'term';

        // Find attribute value definition
        if (this.pane) {
            this.valueAttribute = this.pane.dataset.listValue || this.valueAttribute;
        }

        // Init text input
        if (this.input_label) {
            this.input_label.dataset.oldvalue = this.input_label.value;
        }

        if (this.input_toggle) {
            this.input_toggle.addEventListener('input', event => this.onInputChanged(event));
            this.input_toggle.addEventListener('keydown', event => this.onKeyDown(event));
            this.input_toggle.addEventListener('click', event => this.onLabelClick(event));
        }
        //this.input_label.addEventListener('focus', this.openDropdown);

        // CLick
        // - on items (select)
        // - outside the widget (close)
        // - reset option (uncheck items)
        this.listenEvent(document,'click', event => this.onDocumentClick(event));

        // Position dropdown
        this.listenEvent(window,'resize', event => this.positionDropdown());
        this.listenEvent(this.widgetElement,'epi:load:content', event => this.positionDropdown());
        this.listenEvent(this.pane,'epi:load:content', event => this.positionDropdown());
        //document.addEventListener('scroll', this.positionDropdown);
    }

    /**
     * Activate or update dragitems widget.
     */
    updateWidget() {
        //this.listenEvent(document,'click', event => this.onDocumentClick(event));
    }

    /**
     * Move the pane element to the direct child list of the body element.
     * This makes it possible to position the pane freely on the page.
     */
    detachPane() {
        this.pane_id = this.widgetElement.dataset.paneId;

        // Get pane from child element
        this.pane = this.widgetElement.querySelector('.widget-dropdown-pane');

        if (!this.pane_id && !this.pane) {
            this.pane = Utils.spawnFromString('<div class="widget-dropdown-pane widget-dropdown-pane-moved"><div data-snippet="widget-dropdown-pane"></div></div>');
            document.querySelector('body').append(this.pane);
        }

        // Remove old pane (if updated by data snippet mechanism)
        if (this.pane_id) {
            const selector = '#' + this.pane_id;
            const oldPane = document.querySelector(selector + '.widget-dropdown-pane-moved');
            if (oldPane) {
                oldPane.remove();
            }
            this.pane = document.querySelector(selector);
        }

        // Open frame or move pane to body element
        if (this.pane && this.pane_id) {
            if (this.pane.classList.contains('widget-dropdown-pane-frame')) {
                this.openDropdown();
            } else {
                document.querySelector('body').append(this.pane);
                this.pane.classList.add('widget-dropdown-pane-moved');
            }
        }

    }

    /**
     * Close the dropwdown if clicked outside
     * Select the value if clicked on an item. Ignore tree indents.
     *
     * @param event Click
     */
    onDocumentClick(event) {
        if (!this.widgetElement || !this.pane) {
            return;
        }

        if (!this.widgetElement.contains(event.target) && !this.pane.contains(event.target)) {
            this.resetValue();
        }
        else if (event.target.closest('[data-role="manage"]')) {
            this.resetValue();
        }
        else if (event.target.closest('.selector-reset')) {
            this.resetSelection();
        } else if (event.target.closest('.tree-indent')) {
            return;
        } else {
            const selected = event.target.closest('[data-' + this.valueAttribute + ']');

            if (selected) {
                // When a checkbox is nested inside a label, two clicks are triggered.
                // Ignore the label click by checking if the target contains a checkbox
                if (!event.target.querySelector('input[type=checkbox]')) {
                    this.selectValue(selected);
                    event.stopPropagation();
                }
            }
        }
    }

    /**
     * Called on click on input label (Input field, dropdown toggle).
     *
     * @param event Click
     */
    onLabelClick(event) {
        if (!Utils.querySelectorSelfAndContainer(event.currentTarget, '.widget-dropdown-selector')) {
            return;
        }

        if (this.pane.classList.contains('widget-dropdown-pane-frame')) {
            return;
        }


        if (this.isOpen()) {
            this.resetValue();
        } else {
            this.openDropdown();
        }
    }

    /**
     * Trigger loading or filtering of items when input is changed.
     *
     * @param event Input
     */
    onInputChanged(event) {
        // Set dirty
        this.input_label.classList.add('dirty');

        // Get term
        // const term = this.input_label.value.toLowerCase();
        const term = this.input_label.value;

        // Delay loading for ajax requests
        if (this.url) {
            clearTimeout(this.inputTimeout);
            this.inputTimeout = setTimeout(() => this.loadResults(term), 200);
        }

        // Instant filtering for non ajax panes
        else {
            this.loadResults(term);
        }
    }

    /**
     * Enable key navigation and selection in dropdown.
     *
     * @param event Keydown (up, down, enter)
     * @returns {boolean}
     */
    onKeyDown(event) {
        const key = event.keyCode;

        // Ignore all keys except up, down, enter
        if (key !== 40 && key !== 38 && key !== 13 && key !== 27) {
            return;
        }

        // Close and reset on ESCAPE key
        if (key === 27) {
            this.resetValue();
            return;
        }

        // Open dropdown on all keys
        if (!this.isOpen()) {
            this.openDropdown();
            return false;
        }

        // Determine active item
        let current = this.pane.querySelector('.selected');

        // Remove selected class
        if (current) {
            current.classList.remove('selected');
        }

        // Down key: focus next
        if (key === 40) {
            if (!current) {
                current = this.firstItem();
            } else {
                current = this.nextItem(current);
            }

            if (!current) {
                return false;
            }
            Utils.scrollIntoViewIfNeeded(current, this.pane, false, 'y');
        }
        // Up key: focus previous
        else if (key === 38) {
            if (!current) {
                current = this.firstItem();
            } else  {
                current = this.prevItem(current);
            }

            if (!current) {
                return false;
            }
            Utils.scrollIntoViewIfNeeded(current, this.pane, false, 'y');
        }
        // Enter key
        else if (key === 13) {
            if (current) {
                this.selectValue(current, true);
            } else {
                this.clearValue();
            }

            event.preventDefault();
            return false;
        }

        // Add selected class
        if (current) {
            current.classList.add('selected');
        }
    }


    /**
     * Open the dropdown and load the pane without a filter term.
     */
    openDropdown() {
        if (this.isOpen()) {
            return;
        }

        //this.detachPane();
        // if (this.pane && !this.pane.classList.contains('widget-dropdown-pane-moved')) {
        //     document.querySelector('body').append(this.pane);
        //     this.pane.classList.add('widget-dropdown-pane-moved');
        // }

        if (this.input_label && !this.input_label.readOnly && this.input_label.select) {
            this.input_label.select();
        }

        this.pane.classList.add('active');
        this.widgetElement.classList.add('active');
        this.positionDropdown();

        this.loadResults();
    }

    /**
     * Close the dropdown.
     */
    closeDropdown() {
        if (!this.isOpen()) {
            return;
        }

        if (this.pane.classList.contains('widget-dropdown-pane-frame')) {
            return false;
        }

        this.widgetElement.classList.remove('active');
        this.pane.classList.remove('active');
    }

    /**
     * Update the pane using a query term.
     * The pane will be opened if necessary.
     *
     * @param term Search term.
     */
    loadResults(term = '') {
        // Open if not open
        this.openDropdown();

        // Load by AJAX...
        if (this.url) {
            clearTimeout(this.inputTimeout);
            this.widgetElement.classList.add('active');

            // if (this.pane) {
            //     this.pane.querySelectorAll('[data-' + this.valueAttribute + ']').forEach(node => node.remove());
            // }

            let url = new URL(this.url, App.baseUrl);
            if (term !== '') {
                url.searchParams.set(this.param, term);
            } else {
                url.searchParams.set('seek', this.getValue());
            }
            url = url.toString();

            // TODO: handle empty results when seeking, old result should stay?
            App.loadDataSnippets(url, this.pane);

            // TODO: after loading?
            // const scrollbox = App.findWidget(this.widgetElement,'scrollbox');
            // if (scrollbox) {
            //     scrollbox.seekRow();
            // }
        }

        // ... or emit event and then filter items
        else {
            // Listeners may update the pane
            this.emitEvent('epi:load:dropdown',{term: term, pane: this.pane}, true);

            // Filter nodes
            const nodes = this.pane.querySelectorAll('[data-' + this.valueAttribute + ']');
            nodes.forEach(node => {
                const checked = node.querySelector('input:checked');
                const matchAgainst = node.dataset.searchText || node.textContent;
                const matches = (term === '') || (matchAgainst.toLowerCase().indexOf(term.toLowerCase()) > -1);
                node.classList.toggle('list-item-hide', !matches && !checked);
            });

            // Filter tree
            // TODO: implement filter function WidgetTree() class
            const treeNodes = this.pane.querySelectorAll('.item-nochildren[data-' + this.valueAttribute + ']');
            treeNodes.forEach(node => {
                const matchAgainst = node.dataset.searchText || node.textContent;
                const matches = (term === '') || (matchAgainst.toLowerCase().indexOf(term.toLowerCase()) > -1);
                node.classList.toggle('item-hidden', !matches);
            });

            // Filter groups
            const groups = this.pane.querySelectorAll('.selector-actionlist');
            groups.forEach(group => {
                const groupEmpty = group.querySelectorAll('[data-' + this.valueAttribute + ']:not(.list-item-hide)').length === 0;
                group.classList.toggle('list-item-hide', groupEmpty);

                const groupLabel = Utils.getPrevSibling(group,'.selector-grouplabel');
                if (groupLabel) {
                    groupLabel.classList.toggle('list-item-hide', groupEmpty);
                }
            });

        }
    }

    /**
     * Get an array of checked inputs.
     *
     * @returns {Array} Checked inputs
     */
    getCheckedInputs() {
        return this.pane ? Array.from(this.pane.querySelectorAll('input:checked')) : [];
    }

    /**
     * Get the full label of an item.
     * For tree widgets, the label is requested from the tree widget.
     *
     * @param node Item from dropdown
     */
    getNodeLabel(node) {
        if (!node) {
            return '';
        }

        const treeWidget = this.getWidget(node, 'tree');
        if (treeWidget) {
            return treeWidget.treeGetPath(node);
        }

        return node.dataset.label || node.textContent.trim();
    }

    /**
     * Get the first visible item
     *
     * @return {HTMLElement}
     */
    firstItem() {
        return this.pane.querySelector('[data-' + this.valueAttribute + ']:not(.list-item-hide)');
    }

    /**
     * Get the next visible item
     *
     * @param {HTMLElement} current
     * @return {Element}
     */
    nextItem(current) {
        const selector = '[data-' + this.valueAttribute + ']:not(.list-item-hide)';
        let next = Utils.getNextSibling(current, selector);

        // Go into next groups
        if (!next && current) {
            let nextGroup = Utils.getNextSibling(current.closest('ul'),'ul');
            while (nextGroup && !next) {
                next = nextGroup.querySelector(selector);
                nextGroup = Utils.getNextSibling(nextGroup,'ul');
            }
        }

        return next;
    }

    /**
     * Get the previous visible item
     *
     * @param {HTMLElement} current
     * @return {Element}
     */
    prevItem(current) {
        const selector = '[data-' + this.valueAttribute + ']';
        let prev = Utils.getPrevSibling(current,selector + ':not(.list-item-hide)');

        // Go into prev groups
        if (!prev && current) {
            let prevGroup = Utils.getPrevSibling(current.closest('ul'),'ul');
            while (prevGroup && !prev) {
                prev = Utils.querySelectorLast(prevGroup, selector);
                prevGroup = Utils.getPrevSibling(prevGroup,'ul');
            }
        }

        return prev;
    }

    /**
     * Uncheck all checkboxes.
     */
    resetSelection() {
        const inputs = this.getCheckedInputs();
        inputs.forEach(item => {
            item.checked = false;
        });

        this.clearValue();
    }

    /**
     * Set the value
     *
     * Single values: Set the value and close the dropdown
     * Checkbox values: Assemble comma separated list (dropdown stays open)
     *
     * @param current Selected value
     * @param toggle True if selection is made via enter key. Toggles selection
     */
    selectValue(current, toggle = false) {
        if (!current) {
            return;
        }

        let id;
        let label;
        const append = current.dataset.append || false;

        // Assemble a comma separated list of the selected IDs
        if (this.pane.classList.contains('widget-checkboxlist')) {
            const checkbox = current.querySelector('input[type=checkbox]');

            if (checkbox && toggle) {
                checkbox.checked = !checkbox.checked;
            }

            const inputs = this.getCheckedInputs();
            id = inputs
                .map(item => item.closest('[data-' + this.valueAttribute + ']').dataset[this.valueAttribute])
                .join(',');

            if (inputs.length === 0) {
                label = '';
            } else if (inputs.length === 1) {
                const item = inputs[0].closest('[data-' + this.valueAttribute + ']');
                label = this.getNodeLabel(item);
            } else {
                label = inputs.length + ' ' + (this.input_label.dataset.label || '');
            }
        } else {
            id = current.dataset[this.valueAttribute];
            label = this.getNodeLabel(current);
        }

        if (this.input_id) {
            this.input_id.value = id;
            this.input_id.dataset.append = append;
            this.input_id.dataset.type = current.dataset.type;
        }

        if (this.input_label) {
            this.input_label.value = label;
            this.input_label.setAttribute('title', label);
            this.input_label.dataset.oldvalue = label;

            this.input_label.classList.remove('dirty');
            this.input_label.classList.toggle('append', append);
        }

        this.emitEvent('changed', {'id': id, 'label' : label});

        if (!this.pane.classList.contains('widget-checkboxlist')) {
            this.closeDropdown();
        }
    }

    /**
     * Set the value to null (no item selected).
     */
    clearValue() {
        this.input_id.value = '';

        this.input_label.value = '';
        this.input_label.dataset.oldvalue = '';
        this.input_label.classList.remove('dirty');

        const changedEvent = new Event('changed', {bubbles: true, cancelable: false});
        this.widgetElement.dispatchEvent(changedEvent);

        if (!this.pane.classList.contains('widget-checkboxlist')) {
            this.closeDropdown();
        }
    }

    /**
     * Reset the label to the original value and close the dropdown.
     */
    resetValue() {
        if (this.input_label) {
            this.input_label.value = this.input_label.dataset.oldvalue;
            this.input_label.classList.remove('dirty');
        }

        this.closeDropdown();
    }

    /**
     * Return the selected value(s)
     *
     * @returns {string} Comma-separated values
     */
    getValue() {
        return this.input_id ? this.input_id.value : undefined;
    }

}

/**
 * Update forms after changed selection (e.g. for export and mutate tasks)
 *
 * @param {HTMLFormElement} element The form element
 * @constructor
 */
export class FormUpdateWidget extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        this.listenEvent(element,'change', event => this.updateForm(event));
    }

    /**
     * After changing a form input, retrieves an updated form
     * by adding the input value as query string parameter.
     * The name of the query string parameter is determined
     * by the data-form-update attribute.
     *
     * @param event
     * @returns {boolean}
     */
    updateForm(event) {
        const input_param = event.target.dataset.formUpdate;
        if (!input_param) {
            return;
        }
        const form = event.target.closest('form');
        if (!form) {
            return;
        }

        let url = new URL(form.getAttribute('action'), App.baseUrl);
        const input_val = event.target.value;
        url.searchParams.set(input_param, input_val);
        url = url.toString();

        this.disableForm();

        const pushHistory = !this.isInFrame();
        App.loadDataSnippets(url, this.widgetElement, pushHistory, true);

        return false;
    }

    disableForm() {
        this.widgetElement
            .querySelectorAll('input, textarea, button, select')
            .forEach(element => element.setAttribute('disabled','disabled') );
    }
}

window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['formupdate'] = FormUpdateWidget;
window.App.widgetClasses['dropdown'] = DropdownWidget;
window.App.widgetClasses['dropdown-selector'] = DropdownSelectorWidget;
