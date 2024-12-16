/*
 * Table widget - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {BaseWidget} from '/js/base.js';
import Utils from "/js/utils.js";

/**
 * The table widget makes columns resizable, provides keyboard navigation and
 * triggers actions for the rows.
 *
 * Add the class 'table-widget' to the table element
 * (see widgets.js for initialization code).
 * and wrap the table element in a scrollbox (div with class widget-scrollbox).
 *
 * 1. Settings
 * Settings are saved with App.user.saveSettings() and
 * retrieved with App.user.settings.get().
 *
 * For user settings to be persisted, add the attribute data-model="mymodelname"
 * to the table element (replace "mymodelname" by a unique name of the model, e.g. "articles").
 *
 * Each th element of the table needs a data-col="mycolumnname" attribute
 * (replace "mycolumnname" by a unique key for the column, e.g. "name").
 * You can set a default width for every column in the data-width attribute
 * (only pixel values are supported by now, TODO: em support)
 *
 * In the controller, pass user settings to JavaScript, for example
 * (replace 'mymodelname' accordingly):
 *
 *        // Get column sizes from user settings
 *        $columns = $this->getUserSettings('columns','mymodelname');
 *        $this->addToJsUser(['settings'=>['columns'=>['mymodelname'=>$columns]]]);
 *
 * 2. Select mode
 * You can switch between click and select mode, see the methods toggleSelectMode and setMode.
 *
 * 3. Actions
 * Add actions to the last column. The last column will be hidden and when you select a row
 * (by mouse click or keyboard navigation) the first action will be executed.
 * If a row belongs to another row (e.g. it contains full text content of the previous row),
 * add the class "row-supplement". In this case, the action of the preceding row will be executed.
 *
 *
 * @param {HTMLTableElement} element
 * @constructor
 */
export class TableWidget extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        // Init elements and add widget to DOM element
        this.scrollbox = element.parentElement;

        // State properties
        this.mode = 'select';
        this.lastSelected = null;
        this.lastInCurrentSelection = null;
        this.selectedRows = [];
        this.currentSelection = [];
        this.focusChanged = false;

        // Init event listeners
        this.initEvents();

        // Init column resizing
        this.initResizableCols();
    }

    initWidget() {
        this.initList();

        //Select row
        if (!this.isInFrame()) {
            this.initSelection();
        }
    }

    /**
     * Init list attributes.
     */
    initList() {
        this.datalist = this.widgetElement.querySelector('[data-list-name]');

        this.modelName = this.widgetElement.dataset.model || '';
        const tableName = this.modelName.split('.',2);
        this.tableName = tableName.length === 1 ? tableName[0] : tableName[1];
    }

    /**
     * Initializes event listeners
     */
    initEvents() {
        // Keyboard and mouse events for row selection
        this.listenEvent(document,'keydown', event => this.onKeyDown(event));
        this.listenEvent(document,'keyup', event => this.onKeyUp(event));

        this.listenEvent(this.widgetElement,'click', event => this.onClickRow(event));
        this.listenEvent(this.widgetElement, 'dblclick', event => this.onDoubleClickRow(event));

        // Hover effects -> wrap values in links, then they can be opened in new tabs by ctrl+click
        this.listenEvent(this.widgetElement, 'mouseover', event => this.linkAction(event));
        this.listenEvent(this.widgetElement, 'mouseout', event => this.unlinkAction(event));
        // this.listenEvent(this.widgetElement, 'mouseout', event => this.clickWrapper(event));

        // Select mode -> single or multi select
        const body = document.querySelector('body');
        this.listenEvent(body, 'click', event => this.toggleSelectMode(event));

        // Resize table container
        this.listenResize(this.scrollbox, (entries) => this.expandLastColumn());
    }

    /**
     * TODO: implement
     */
    updateWidget() {

    }

    /**
     * Add markup and event handling to column headers
     */
    initResizableCols() {
        let pageX;
        let curCol, curColWidth;

        const row = this.widgetElement.getElementsByTagName('tr')[0];
        const cols = row ? row.children : undefined;

        if (!cols) {
            return;
        }
        this.widgetElement.style.tableLayout = 'fixed';


        const self = this;
        function setColResizeListeners(div) {
            self.listenEvent(div, 'mousedown', event => mouseDown(event));
            self.listenEvent(document, 'mousemove', event => mouseMove(event));
            self.listenEvent(document, 'mouseup', event => mouseUp(event));
        }

        function mouseDown(event) {
            curCol = event.target.parentElement;
            pageX = event.pageX;
            const padding = paddingDiff(curCol);
            curColWidth = curCol.offsetWidth - padding;
            self.widgetElement.classList.add('is-resizing');
        }

        function mouseMove(event) {
            if (curCol) {
                const diffX = event.pageX - pageX + 8;
                self.setColumnWidth(curCol, curColWidth + diffX);

                event.preventDefault();
            }
        }

        function mouseUp(event) {
            if (curCol) {
                self.saveColumnSizes();
                self.widgetElement.classList.remove('is-resizing');
            }

            pageX = undefined;
            curCol = undefined;
            curColWidth = undefined;
        }

        function createDiv() {
            const div = document.createElement('div');
            div.classList.add('resize-bar');
            return div;
        }

        function paddingDiff(col) {
            const padLeft = getStyleVal(col, 'padding-left');
            const padRight = getStyleVal(col, 'padding-right');
            return (parseInt(padLeft) + parseInt(padRight));
        }

        function getStyleVal(elm, css) {
            return (window.getComputedStyle(elm, null).getPropertyValue(css));
        }


        for (let i = 0; i < cols.length; i++) {
            if (cols[i].classList.contains('cols-fixed')) {
                continue;
            }
            const div = createDiv();
            cols[i].appendChild(div);
            cols[i].style.position = 'relative';
            setColResizeListeners(div);
        }

        this.setColumnSizes();
        this.setRowNumbers();
    }

    /**
     * Set the width  of a column
     *
     * @param col The table header cell element
     * @param width The width in pixels
     */
    setColumnWidth(col, width) {

        const oldTableWidth = this.widgetElement.offsetWidth;
        const oldColWidth = col.clientWidth;
        const newTableWidth = oldTableWidth + (width - oldColWidth);

        this.widgetElement.style.width = newTableWidth + 'px';

        col.style.maxWidth = width + 'px';
        col.style.width = width + 'px';
        col.dataset.targetWidth = width;

        this.expandLastColumn();
    }

    /**
     *  Expand last column
     */
    expandLastColumn() {
        const containerWidth = this.widgetElement.parentElement.clientWidth;
        const containerDiff = containerWidth - this.widgetElement.offsetWidth;

        const oldTableWidth = this.widgetElement.offsetWidth;
        const lastCol = this.getLastVisibleColumn();

        if (!lastCol) {
            return false;
        }

        const oldColWidth = lastCol.clientWidth;
        const colDiff = lastCol.dataset.targetWidth - oldColWidth;

        if (lastCol.dataset.targetWidth === undefined) {
            lastCol.dataset.targetWidth = oldColWidth;
        }

        let newWidth = oldColWidth;

        // Shrink
        if ((containerDiff < 0) && (colDiff < 0)) {
            newWidth = oldColWidth + Math.max(colDiff, containerDiff);
        }
        // Grow
        else if (containerDiff > 0) {
            newWidth = oldColWidth + containerDiff;
        }

        if (newWidth !== oldColWidth) {
            lastCol.style.maxWidth = newWidth + 'px';
            lastCol.style.width = newWidth + 'px';

            const newTableWidth = oldTableWidth + (newWidth - oldColWidth);
            this.widgetElement.style.width = newTableWidth + 'px';
        }
    }

    /**
     * Returns last visible column (that has no "action" class) of table
     *
     * @returns {HTMLTableHeaderCellElement} header cell
     */
    getLastVisibleColumn() {
        const columns = this.widgetElement.querySelectorAll('thead tr th:not(.actions)');
        for (let i = columns.length; i !== 0; i--) {
            if (columns[i - 1].offsetWidth !== 0) {
                return columns[i - 1];
            }
        }
    }

    /**
     * Set column sizes based on user settings
     */
    setColumnSizes() {
        const cols = this.widgetElement.querySelectorAll("th[data-col]");
        const model = this.widgetElement.dataset.model;

        // TODO: use query parameters or create a user model in models.js
        let columns = App.user.settings.get('columns', model, {});

        cols.forEach(col => {
            const colName = col.dataset.col;
            let width = columns.hasOwnProperty(colName) ? columns[colName] : col.dataset.width;
            width = width ? width : App.settings.colwidth;
            this.setColumnWidth(col, width);
        });
    }

    /**
     * Return object of column sizes
     *
     * @returns {{Object}}
     */
    getColumnSizes() {
        const columns = {};
        const cols = this.widgetElement.querySelectorAll("th[data-col]");

        cols.forEach((col) => {
            const colName = col.dataset.col;
            if (colName) {
                columns[colName] = col.offsetWidth;
            }
        });

        return columns;
    }

    /**
     * Save column sizes to user settings
     */
    saveColumnSizes() {
        const columns = this.getColumnSizes();
        const model = this.widgetElement.dataset.model;

        if (model && columns) {
            App.user.settings.save('columns', model, columns, true);
        }
    }

    setRowNumbers() {
        this.widgetElement.querySelectorAll('tbody tr:not(.row-supplement) td.first.cols-fixed').forEach((td, index) => {
            td.textContent = index + 1;
        });
    }

    /**
     * Return table row that was an event target (click or mouseover), or the previous row
     *
     * @param element Row
     * @returns {HTMLTableRowElement} Row
     */
    getRow(element) {
        let tr = element.closest('tr');

        // Call action from previous row (in search results)
        while (tr && tr.classList.contains("row-supplement")) {
            tr = tr.previousElementSibling;
        }
        return tr;
    }

    /**
     * Return the URL of the first action in a table row
     *
     * @param {HTMLElement} tr The table row element
     * @param {string} role If provided, return the action with the given role. If no matching action is available, return the first action.
     * @returns {string} The action URL
     */
    getFirstAction(tr, role) {
        if (!tr) {
            return;
        }

        let action;
        if (role) {
            action = tr.querySelector('.actions a[data-role="' + role + '"]');
        }
        if (!action) {
            action = tr.querySelector('.actions a');
        }

        if (action) {
            action = action.getAttribute('href');
        }
        return action;
    }

    /**
     * Get the open action of a row, or the first action as fallback
     *
     * @param {HTMLElement} tr The table row
     * @returns {HTMLElement}
     */
    getOpenAction(tr) {
        if (!tr) {
            return;
        }
        let action = tr.querySelector('.actions a[data-role="open"]');
        if (!action) {
            action = tr.querySelector('.actions a');
        }
        return action;
    }

    /**
     * Fired on mouseover event...
     *
     * @param {Event} event
     * @returns {boolean}
     */
    linkAction(event) {
        if (
            event.target.closest('thead')
            || event.target.closest('a')
            || Utils.childMatches(event.target, 'a')
            || event.target.closest('.tree-indent')
        ) {
            return true;
        }

        let tdContent = event.target.closest('td:not(.actions)');
        let treeContent =  tdContent ? tdContent.querySelector('.tree-content') : undefined;
        let content = treeContent || tdContent;
        if (!content || (content.dataset.linked === 'true')) {
            return false;
        }

        const tr = this.getRow(content);
        const url = this.getFirstAction(tr, 'tab');

        if (!url) {
            return false;
        }

        const a = document.createElement('a');
        a.setAttribute('href', url);
        a.dataset.linkwrapper = 'true';

        if (this.widgetElement.classList.contains("actions-topopup") && url) {
            a.classList.add('popup');
        } else if (this.widgetElement.classList.contains("actions-toframe") && !tr.classList.contains("actions-noframe") && url) {
            a.classList.add('frame');
        }

        Utils.wrapAll(content, a);
        content.dataset.linked = 'true';
    }

    /**
     * Unlink rows
     *
     * @param event
     */
    unlinkAction(event) {
        if (!event.target.closest('tbody tr td:not(.actions)')) {
            return;
        }

        //TODO: remove link wrapper
    }

    /**
     * Fired on dblclick event, opens URL in same window
     *
     * @param event dblclick
     */
    onDoubleClickRow(event) {
        if (!event.target.closest('tr') || event.target.closest('.tree-indent')) {
            return;
        }

        // Ignore clicks in popups
        const container = event.target.closest('.ui-dialog, .sidebar');
        if (container) {
            return;
        }

        //this.setFocus(event);

        const tr = this.getRow(event.target);

        const action = this.getOpenAction(tr);
        const url = action ? action.getAttribute('href') : undefined;
        if (url) {
            window.location = url;
        }
    }

    /**
     * Fired on keyup event
     *
     * @param event keyup
     */
    onKeyUp(event) {
        if (!this.hasFocus) {
            return;
        }

        if (event.key === 'Shift') {
            this.lastSelected = this.lastInCurrentSelection;
            this.selectedRows = this.selectedRows.concat(...this.currentSelection);
            this.selectedRows = this.selectedRows.filter((row, index) => {
                return this.selectedRows.indexOf(row) === index;
            });
            this.currentSelection = [];
        }

        if (this.focusChanged) {
            this.openRow(this.getFocusedRow());
        }

    }

    /**
     * Fired on keydown event
     *
     * @param event keydown
     * @returns {boolean}
     */
    onKeyDown(event) {
        if (!this.hasFocus) {
            return;
        }

        // Ctrl key is reserved for move operations, see DragItemsWidget
        if (event.ctrlKey) {
            return;
        }

        const treeWidget = this.getWidget(this.widgetElement, 'tree');

        const key = event.key;
        const allowedKeys = [
            'ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight',
            'PageUp', 'PageDown', 'Home', 'End', 'Enter'
        ];
        let currentRow = this.lastInCurrentSelection;

        if (this.mode !== 'select' || !allowedKeys.includes(key)) {
            return;
        }

        if (key === 'ArrowDown' && event.shiftKey) {
            currentRow = this.selectUntil(this.lastSelected, this.getNextVisibleNode(currentRow));
        }
        else if (key === 'ArrowDown') {
            // If no row selected, select first
            if (!currentRow) {
                currentRow = this.selectSingleRow(this.getFirstVisibleRow());
            } else {
                currentRow = this.selectSingleRow(this.getNextVisibleNode(currentRow));
            }
        }
        else if (key === 'ArrowUp' && event.shiftKey) {
            currentRow = this.selectUntil(this.lastSelected, this.getPreviousVisibleNode(currentRow));
        }
        else if (key === 'ArrowUp') {
            // If no row selected, select last
            if (!currentRow) {
                currentRow = this.selectSingleRow(this.getLastVisibleRow());
            } else {
                currentRow = this.selectSingleRow(this.getPreviousVisibleNode(currentRow));
            }
        }

        // Expand or collapse tree items
        else if (key === 'ArrowLeft' && treeWidget) {
            if (currentRow.treeLevel > 0 &&
                (!currentRow.treeChildren || currentRow.classList.contains('item-collapsed'))
            ) {
                currentRow = this.selectSingleRow(treeWidget.treeGetParent(currentRow));
            } else {
                treeWidget.treeToggleItem(currentRow, true);
            }
        }
        else if (key === 'ArrowRight' && treeWidget) {
            treeWidget.treeToggleItem(currentRow, false);
        }

        // Open action
        else if (key === 'Enter') {
            if (currentRow) {
                const dblclick = new Event('dblclick', {"bubbles": true, "cancelable": false});
                currentRow.dispatchEvent(dblclick);
            }
        }

        // Jump one page
        else if (key === 'PageUp') {
            let jumpToRow = this.paginateRows(currentRow, 'up');
            if (event.shiftKey) {
                currentRow = this.selectUntil(this.lastSelected, jumpToRow);
            } else {
                currentRow = this.selectSingleRow(jumpToRow);
            }
        }
        else if (key === 'PageDown') {
            let jumpToRow = this.paginateRows(currentRow, 'down');
            if (event.shiftKey) {
                currentRow = this.selectUntil(this.lastSelected, jumpToRow);
            } else {
                currentRow = this.selectSingleRow(jumpToRow);
            }
        }

        // Jumb to first or last row
        else if (key === 'Home') {
            const firstRow = this.getFirstVisibleRow();
            if (event.shiftKey) {
                currentRow = this.selectUntil(this.lastSelected, firstRow);
            } else {
                currentRow = this.selectSingleRow(firstRow);
            }
            if (!currentRow) {
                return false;
            }
        }
        else if (key === 'End') {
            const lastRow = this.getLastVisibleRow();
            if (event.shiftKey) {
                currentRow = this.selectUntil(this.lastSelected, lastRow);
            } else {
                currentRow = this.selectSingleRow(lastRow);
            }
            if (!currentRow) {
                return false;
            }
        }

        event.preventDefault();

        // Focus row
        if (currentRow) {
            Utils.scrollIntoViewIfNeeded(currentRow, this.scrollbox, false, 'y');
            currentRow.focus();
            currentRow.classList.add('row-selected');

            if (!currentRow.classList.contains('row-focused')) {
                this.widgetElement.querySelectorAll('.row-focused').forEach(row => {
                    row.classList.remove('row-focused');
                });
                currentRow.classList.add('row-focused');
                this.focusChanged = true;
            }
        }
    }

    /**
     * Unselect all rows
     */
    unselectRows() {
        if (this.datalist) {
            this.datalist.querySelectorAll('tr').forEach(elm => {
                elm.classList.remove('row-selected');
            });
        }
    }

    /**
     * Select or unselect rows, based on table mode
     *
     * @param event click
     */
    handleSelectRow(event) {
        let currentRow = event.target.closest('tr');

        if (this.mode === 'select') {

            if (!event.target.closest('.actions')) {

                if (currentRow) {
                    currentRow.focus();
                }

                // Remove or add row from/to selection
                if (event.ctrlKey) {
                    this.selectAdd(currentRow);
                }

                // Select multiple rows with shift key
                if (event.shiftKey && this.lastSelected) {
                    this.selectUntil(this.lastSelected, currentRow);
                }

                // Remove selection
                if (!event.ctrlKey && !event.shiftKey) {
                    this.selectSingleRow(currentRow);
                }

                this.updateSelectLinks();
                // event.stopPropagation();
                // return false;
            }
        } else {
            this.unselectRows();
            currentRow.classList.add('row-selected');
        }

    }

    /**
     * Call row actions, based on whether in popup or not
     *
     * @param event click
     * @returns {boolean}
     */
    handleRowAction(event) {
        if (event.target.closest('a')) {
            return false;
        }

        const tr = this.getRow(event.target);
        this.openRow(tr);
    }

    /**
     * Open selected row in popup, frame or in the same window, based on table action
     *
     * TODO: when selected by keyboard, in the articles index action, update buttons in the footer
     *
     * @param tr Selected row
     * @returns {boolean}
     */
    openRow(tr) {
        this.focusChanged = false;

        // TODO: implement data-action attribute and remove the table cells
        const url = this.getFirstAction(tr);
        if (!url) {
            return;
        }
        // Ignore clicks in popups
        const container = this.widgetElement.closest('.ui-dialog, .sidebar');
        if (container) {
            return false;
        }

        // Open popup
        if (this.widgetElement.classList.contains("actions-topopup") && url) {
            App.openPopup(url);
        }

        // Open frame
        else if (this.widgetElement.classList.contains("actions-toframe") && !tr.classList.contains("actions-noframe") && url) {
            App.openDetails(url, {external: true, force: false});
        }
        // Open page
        else if (url) {
            window.location = url;
        }

        return true;
    }

    /**
     * Fired on click event
     *
     * @param event click
     */
    onClickRow(event) {
        if (!event.target.closest('tbody tr') || event.target.closest('.tree-indent')) {
            return;
        }
        //App.setFocus(event);

        // Only handle singe clicks.
        // TODO: not working because double clicks are two single clicks?
        if (event.detail > 1) {
            return;
        }

        this.handleSelectRow(event);
        this.handleRowAction(event);
    }

    /**
     * Select the first row
     */
    initSelection() {
        let row = this.widgetElement.querySelector('tbody tr.row-selected');
        // if (!row) { //  && App.sidebarright.isVisible()
        //     row = this.widgetElement.querySelector('tbody tr:not(.actions-noframe, .node-cursor)');
        // }
        if (row) {
            this.activateRow(row);
        }
    }

    getRowCount() {
        return this.widgetElement.querySelectorAll('[data-list-itemof]:not(.node-cursor, .item-hidden)').length;
    }

    /**
     * Select a row, perform its action and set focus
     *
     * @param {HTMLTableRowElement} row
     * @param {boolean} open Perform the row action
     */
    activateRow(row, open = true) {
        if (row) {
            this.selectSingleRow(row);
            this.focusRow(row);
            if (open && this.widgetElement.classList.contains('actions-toframe')) {
                this.openRow(row);
            }

        }
    }

    /**
     * Reset row selection and update links
     */
    deactivateRows() {
        this.updateSelectLinks();
    }

    /**
     * Focus the row for keyboard input
     *
     * @param {HTMLTableRowElement} row
     */
     focusRow(row) {
        if (row) {
            row.focus();
        }
        //this.setFocus(true);
        this.updateSelectLinks();
    }

    /**
     * Return next visible (non-collapsed) row.
     *
     * @param row Row that was selected before.
     * @returns {Element|undefined} Next non-collapsed row.
     */
    getNextVisibleNode(row) {

        while (row.nextElementSibling) { // && !row.nextElementSibling.classList.contains('node-cursor')
            row = row.nextElementSibling;
            if (!row.classList.contains('item-hidden')) {
                break;
            }
        }
        return row;
    }

    /**
     * Return previous visible (non-collapsed) row.
     *
     * @param row Row that was selected before.
     * @returns {Element|undefined} Previous non-collapsed row.
     */
    getPreviousVisibleNode(row) {
        while (row.previousElementSibling) {
            row = row.previousElementSibling;
            if (!row.classList.contains('item-hidden')) {
                break;
            }
        }
        return row;
    }

    /**
     * Return first visible (non-collapsed) row.
     *
     * @returns {HTMLTableRowElement} First non-collapsed row.
     */
    getFirstVisibleRow() {
        return this.widgetElement.querySelector('[data-list-itemof]');
    }

    /**
     * Return last visible (non-collapsed) row.
     *
     * @returns {HTMLTableRowElement} Last non-collapsed row.
     */
    getLastVisibleRow() {
        return [...this.widgetElement.querySelectorAll('[data-list-itemof]:not(.node-cursor, .item-hidden)')].pop();
    }

    /**
     * Return row at given index (exclude collapsed rows from counting).
     *
     * @returns {HTMLTableRowElement} Row at given index.
     */
    getRowAt(rowIndex) {
        return [...this.widgetElement.querySelectorAll('[data-list-itemof]:not(.node-cursor, .item-hidden)')][rowIndex - 1];
    }

    /**
     * Return index of given row (exclude collapsed rows from indexing).
     *
     * @param row Row of which index is needed
     * @returns {number} Index of given row.
     */
    getRowIndex(row) {
        const visibleRows = this.widgetElement.querySelectorAll('[data-list-itemof]:not(.node-cursor, .item-hidden)');
        return Array.prototype.indexOf.call(visibleRows, row);
    }

    /**
     * Select rows by paginating with PageUp/PageDown key.
     *
     * @param currentRow Last selected row
     * @param direction Direction of pagination, 'up' or 'down'
     * @returns {HTMLTableRowElement} Selected row after pagination
     */
    paginateRows(currentRow, direction) {
        const rowCount = this.getRowCount();
        const rowIndex = this.getRowIndex(currentRow);
        const jumpArea = Math.floor(this.scrollbox.offsetHeight / currentRow.offsetHeight);
        let jumpToPosition;

        if (direction === 'up') {
            jumpToPosition = rowIndex > jumpArea ? rowIndex - jumpArea : 1;
        } else {
            jumpToPosition = rowIndex > rowCount - jumpArea ? rowCount : rowIndex + jumpArea;
        }

        return this.getRowAt(jumpToPosition);
    }

    /**
     * Select group of rows
     *
     * @param {HTMLTableRowElement} selectFrom Start row
     * @param {HTMLTableRowElement} selectUntil End row
     * @returns {HTMLTableRowElement} Last added row in selection
     */
    selectUntil(selectFrom, selectUntil) {
        this.currentSelection = [selectFrom];
        this.widgetElement.querySelectorAll('.row-selected').forEach(row => {
            row.classList.remove('row-selected');
        });

        const down = selectFrom.compareDocumentPosition(selectUntil) & Node.DOCUMENT_POSITION_FOLLOWING;
        let node = selectFrom;

        while (down && (node = node.nextElementSibling)) {
            if (!this.currentSelection.includes(node)) {
                this.currentSelection.push(node);
            }
            if (node === selectUntil)
                break;
        }

        node = selectFrom;

        while (!down && (node !== selectUntil) && (node = node.previousElementSibling)) {
            if (!this.currentSelection.includes(node)) {
                this.currentSelection.push(node);
            }
            if (node === selectUntil)
                break;
        }

        this.currentSelection.forEach(row => {
            row.classList.add('row-selected');
        });

        this.selectedRows.forEach(row => {
            row.classList.add('row-selected');
        });

        this.lastInCurrentSelection = selectUntil;

        return selectUntil;
    }

    /**
     * Add new row to selection
     *
     * @param {HTMLTableRowElement} addRow New row
     * @returns {HTMLTableRowElement} Added row
     */
    selectAdd(addRow) {
        if (!this.selectedRows.includes(addRow)) {
            this.selectedRows.push(addRow);
            addRow.classList.add('row-selected');
        } else {
            this.selectedRows = this.selectedRows.filter(item => item !== addRow);
            addRow.classList.remove('row-selected');
        }

        this.lastSelected = addRow;
        this.lastInCurrentSelection = addRow;

        return addRow;
    }

    /**
     * Select single row
     *
     * @param {HTMLTableRowElement} selectRow Selected row
     * @returns {HTMLTableRowElement} Selected row
     */
    selectSingleRow(selectRow) {
        this.widgetElement.querySelectorAll('tr').forEach(row => {
            row.classList.remove('row-selected');
            row.classList.remove('row-focused');
        });

        if (selectRow) {
            this.selectedRows = [selectRow];
            selectRow.classList.add('row-selected');
            selectRow.classList.add('row-focused');
        } else {
            this.selectedRows = [];
        }

        this.focusChanged = true;
        this.lastSelected = selectRow;
        this.lastInCurrentSelection = selectRow;

        return selectRow;
    }

    /**
     * Search a row by ID and select it
     *
     * @param {string|number} id
     */
    selectRowById(id) {
        let currentRow = this.widgetElement.querySelectorAll('[data-id=' + id + ']');
        this.selectSingleRow(currentRow);
        this.updateSelectLinks();
    }

    /**
     * Fired on mouseout
     *
     * @param event mouseout
     */
    clickWrapper(event) {
        if (!event.target.matches('a[data-linkwrapper]')) {
            return;
        }
        //TODO: allow selection inside of links
        // const cellText = document.getSelection();
        // if (cellText.type === 'Range') event.stopPropagation();
    }

    /**
     * Switch between click and select mode on button click
     *
     * @param event click
     */
    toggleSelectMode(event) {
        if (!event.target.matches('.button-toggle-selectmode')) {
            return;
        }
        const mode = this.mode === 'click' ? 'select' : 'click';
        this.setMode(mode);
    }

    /**
     * Set mode
     *
     * @param mode string click|select
     */
    setMode(mode) {
        this.mode = mode;
        this.widgetElement.classList.toggle('widget-table-mode-select', mode === 'select');
        document.querySelector('.button-toggle-selectmode').classList.toggle('active', this.mode === 'select');

        if (mode === 'click') {
            this.unselectRows();
        }

        this.updateSelectLinks();
    }

    /**
     * Transfer the selected IDs to link parameters
     *
     * To pass the IDs of selected table rows to an URL, add the data-list-name attribute to the table (see the method addToList in app.js).
     * Set the data-list-select attribute of the link/button to the same name of the data list.
     * Set the  data-list-parameter to the name of the query parameters that handles the IDs.
     * data-list-select
     *
     * @returns {boolean}
     */
    updateSelectLinks() {
        // Get IDs
        if (!this.datalist) {
            return false;
        }

        const listName = this.datalist.dataset.listName;
        const ids = this.mode !== 'select' ? '' : Array.from(this.datalist.querySelectorAll('.row-selected[data-id]'))
            .map((elm) => {
                return elm.dataset.id;
            })
            .join(",");


        // Set IDs
        document.querySelectorAll('[data-list-select="' + listName + '"]').forEach((elm) => {
            const param = elm.dataset.listParam;

            const url = new URL(elm.getAttribute('href'), App.baseUrl);
            url.searchParams.set(param, ids);
            elm.setAttribute('href', url.toString());
        });
    }

    /**
     * Return focused row
     *
     * @returns {HTMLTableRowElement} Table row
     */
    getFocusedRow() {
        return this.widgetElement.querySelector('.row-focused');
    }
}



/**
 * Makes list items and table rows draggable.
 *
 * The widget works best in combination with ScrollPaginator, TableWidget and TreeWidget.
 *
 * Add the following markup (same as in ScrollPaginator):
 * - Name the list container: data-list-name="mylist"
 * - Name the list items: data-list-itemof="mylist"
 * - Add the IDs to the list items: data-id="2" data-tree-parent="1" data-parent="1".
 *
 *   Note: there can be two types of parents, the parent in the database and the parent in the
 *   visible tree. Usually they are the same, but if you want to show nodes in other positions
 *   than in the database (e.g. for visualizing references between nodes) you can change the
 *   tree parent.
 *
 * Then add the class widget-tree to the list container (e.g. the ul element)
 * or a parent of the list container (e.g. the table).
 * Drag & drop is disabled by default and only enabled when enableWidget()
 * is called or the class widget-dragitems-enabled is present on the list container.
 *
 * Whenever a row is dropped, the widget element emits the event 'epi:move:row'.
 */
export class DragItemsWidget extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        this.switchButton;
        this.toolbar = null;
        this.datalist = null;
        this.listname = null;

        /**
         * @type {TreeWidget}
         */
        this.treeWidget = null;

        /**
         * @type {TableWidget}
         */
        this.widgetElementWidget = null;

        // Drag and Drop variables
        this.dragObject = null;
        this.currentMouseCoords = [0, 0];
        this.oldMouseCoords = [0, 0];

        this.enabled = element.classList.contains('widget-dragitems-enabled');

        // Drag events
        this.widgetElement.addEventListener('dragstart', event => event.preventDefault());
        this.widgetElement.addEventListener('mousedown', event => this.onMouseDown(event));
        this.widgetElement.addEventListener('mousemove', event => this.onMouseMove(event));
        this.widgetElement.addEventListener('mouseup', event => this.onMouseUp(event));

        // Button and key events
        this.listenEvent(document,'click', event => this.onMoveButtonClicked(event),'.actions-set-move');
        this.listenEvent(document,'keydown', event => this.onKeyDown(event));

        // Switch event
        this.listenEvent(document,'epi:toggle:switch', event => this.onSwitched(event));
        this.listenEvent(document,'click', event => this.onSaveClick(event));

        this.updateWidget();
    }

    /**
     * Activate or update dragitems widget.
     */
    initWidget() {
        this.datalist = Utils.querySelectorAndSelf(this.widgetElement, '[data-list-name]');
        this.listname = this.datalist ? this.datalist.dataset.listName : null;

        this.treeWidget = this.getWidget(this.widgetElement,'tree');
        this.widgetElementWidget = this.getWidget(this.widgetElement,'table');
    }

    /**
     * Activate or deactivate the widget.
     *
     * @param enable Boolean, whether the widget is active or not
     */
    enableWidget(enable) {
        this.enabled = enable;
        this.widgetElement.classList.toggle('widget-dragitems-enabled', this.enabled);

        if (this.switchButton && enable) {
            this.createButtons();
        } else {
            this.clearButtons();
        }

        if (this.treeWidget) {
            this.treeWidget.setFocus();
        }
        if (this.widgetElementWidget) {
            this.widgetElementWidget.setFocus();
        }

    }

    /**
     * Generate a button for moving items
     *
     * @param {string} direction left, right, up, down
     * @param {string} symbol The font awesome symbol to display on the button
     * @param {HTMLElement} toolbar The container element
     * @return {HTMLButtonElement}
     */
    createButton(direction, symbol, toolbar) {
        const button = document.createElement('button');
        button.dataset.role = 'move-' + direction;
        button.classList.add('actions-set-move');
        button.title = 'Move item ' + direction +' (Ctrl + Arrow ' + direction + ')';
        button.textContent = symbol;

        toolbar.appendChild(button);
        return button;
    }

    createButtons() {
        if (!this.toolbar) {
            this.toolbar = document.createElement('div');
            this.toolbar.classList.add(['widget-dragitems-toolbar']);
            this.switchButton.parentElement.insertBefore(this.toolbar, this.switchButton);

            this.createButton('left', "\uf0d9", this.toolbar);
            this.createButton('up', "\uf0d8", this.toolbar);
            this.createButton('down', "\uf0d7", this.toolbar);
            this.createButton('right', "\uf0da", this.toolbar);
        }
    }

    clearButtons() {
        if (this.toolbar) {
            this.toolbar.remove();
            this.toolbar = null;
        }
    }

    onMoveButtonClicked(event) {
        if (!this.enabled || !this.datalist) {
            return;
        }
        const role = event.target.dataset.role;
        if (!['move-left', 'move-right', 'move-up', 'move-down'].includes(role)) {
            return;
        }

        const currentRow = this.getSelectedRow();
        const direction = role.split('-')[1];
        if (currentRow) {
            this.moveRow(currentRow, direction);
        }
    }

    /**
     * Fired on keydown event
     *
     * @param event keydown
     * @returns {boolean}
     */
    onKeyDown(event) {
        // Ctrl key is reserved for move operations
        if (!event.ctrlKey || !this.enabled) {
            return;
        }

        if (this.widgetElementWidget && !this.widgetElementWidget.hasFocus) {
            return;
        }

        const key = event.key;
        let direction;
        if (key === 'ArrowDown') {
            direction = 'down';
        }
        else if (key === 'ArrowUp') {
            direction = 'up';
        }
        else if (key === 'ArrowLeft') {
            direction = 'left';
        }
        else if (key === 'ArrowRight') {
            direction = 'right';
        }

        if (direction) {
            const currentRow = this.getSelectedRow();
            if (currentRow) {
                this.moveRow(currentRow, direction);
            }
        }

    }

    /**
     * Handler for the save button
     *
     * @param {Event} event Click event
     */
    async onSaveClick(event) {
        if ((event.target.dataset.role === 'save') && this.enabled) {
            if (!await this.saveOrder()) {
                return;
            }

            // Switch back
            if (this.switchButton) {
                App.switchbuttons.switchButton(this.switchButton);
            }
        }
    }

    /**
     * Enable the widget
     *
     * @param {Event} event Click event
     */
    onSwitched(event) {
        if (event.target.dataset.role === 'move') {
            this.switchButton = event.target;
            const enabled = this.switchButton.classList.contains('widget-switch-active');
            this.enableWidget(enabled);

            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }


    /**
     * Save new row order. Sends an ajax post request to the
     * URL provided in data-list-action-move of the list container.
     *
     * @return {boolean}
     */
    async saveOrder() {
        const url = this.datalist.dataset.listActionMove;

        // if (! await App.confirmAction("Save the new order?")) {
        //     return false;
        // }

        if (!url) {
            return false;
        }
        const dirtyRows = this.datalist.querySelectorAll('[data-dirty=true]');
        const moves = [...dirtyRows].map(item => {
                const itemId = item.dataset.id;
                const preceding = this.treeWidget ? this.treeWidget.treeGetPrevSibling(item, false) : null;
                const precedingId = preceding ? preceding.dataset.id : null;
                const parentId = item.dataset.treeParent;
                return {'id': itemId, 'parent_id': parentId, 'preceding_id': precedingId};
            }
        );

        const payload = JSON.stringify({'moves': moves});
        const containerWidget = this.getContentPane();
        App.ajaxQueue.add(url,
            {
                type: 'POST',
                url: url,
                dataType: 'json',
                data: payload,
                contentType: 'application/json;charset=utf-8',
                processData: false, //this is required
                beforeSend: xhr => {
                    App.showLoader();
                },
                success: (data, textStatus, xhr) => {
                    const msg = Utils.getValue(data,'status.message');
                    const status = Utils.getValue(data,'status.success',false) ? 'success' : 'error';
                    App.showMessage(msg, status, containerWidget);
                    dirtyRows.forEach(item => delete item.dataset.dirty);
                },
                error: (xhr, textStatus, errorThrown) => {
                    // If aborted (see above, https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/abort)
                    if ((xhr.readyState === XMLHttpRequest.UNSENT) && (xhr.status === 0)) {
                        return;
                    }
                    let msg = Utils.getValue(xhr.responseJSON,'error.message');
                    msg = msg ? (errorThrown + ': ' + msg) : errorThrown;
                    App.showMessage(msg, textStatus, containerWidget);
                },
                complete: (xhr, textStatus) => {
                    App.hideLoader();
                }
            }
        );
        return true;
    }

    /**
     * Called on mousedown (replaces dragstart event). Create a new dragObject recording the event information.
     *
     * @param event mousedown
     */
    onMouseDown(event) {
        if (!this.enabled) {
            return;
        }

        const currentRow = event.target.closest('[data-list-itemof]:not(.item-virtual)');
        if (!currentRow || currentRow.classList.contains('fixed')) {
            return;
        }

        this.dragObject = this.createDragObject(currentRow);
        this.dragObject.initMouseCoords = this.getMouseCoords(event);
        this.widgetElement.classList.add('is-dragging');
    }

    /**
     * Create a drag object
     *
     * @param {HTMLElement} row The current row
     * @return {{}}
     */
    createDragObject(row) {
        let dragObject = {};
        if (row) {
            dragObject.draggedRow = row;
            dragObject.oldParentRow = row.treeParent;
            dragObject.newParentRow = null;
            dragObject.draggedRow.classList.add('is-dragging');
            dragObject.draggedDescendants = this.treeWidget ? this.treeWidget.treeGetDescendants(row) : [];
            dragObject.draggedDescendants.forEach(node => node.classList.add('is-dragging'));

            if (dragObject.draggedDescendants.length > 0) {
                dragObject.lastRow = dragObject.draggedDescendants[dragObject.draggedDescendants.length - 1];
            } else {
                dragObject.lastRow = dragObject.draggedRow;
            }

            dragObject.isMouseMoving = false;
        }
        return dragObject;
    }

    /**
     * Finish the drag object
     *
     * @param {object} dragObject
     * @return {void}
     */
    releaseDragObject(dragObject) {
        dragObject.draggedRow.classList.remove('is-dragging');
        dragObject.draggedDescendants.forEach(node => node.classList.remove('is-dragging'));
        dragObject.draggedRow.dataset.dirty = true;
    }

    /**
     * Called on dragend, delete dragObject.
     *
     * @param event mouseup
     */
    onMouseUp(event) {
        if (!this.dragObject) {
            return;
        }

        this.widgetElement.classList.remove('is-dragging');
        const draggedRow = this.dragObject.draggedRow;
        this.releaseDragObject(this.dragObject);
        this.dragObject = null;
        this.emitEvent('epi:move:row',{row: draggedRow});
    }

    /**
     * Called on dragover, move dragged row and children horizontally and vertically
     *
     * @param {MouseEvent} event Mouse move event
     */
    async onMouseMove(event) {
        if (!this.dragObject) {
            return;
        }

        this.currentMouseCoords = this.getMouseCoords(event);

        if (this.dragObject.isMouseMoving) {
            return;
        }

        let keepMoving = true;
        while (keepMoving) {

            const targetRow = this.getDropTarget(this.currentMouseCoords.x, this.currentMouseCoords.y);

            // Move left / right
            const hierarchical = targetRow && targetRow.querySelector('.tree-indent');
            this.dragObject.newParentRow = null; // WHY?
            if (hierarchical) {
                const moveDistance = this.dragObject.draggedRow.querySelector('.tree-indent').offsetWidth;
                if (this.dragObject.initMouseCoords.x - this.currentMouseCoords.x > moveDistance) {
                    this.dragObject.isMouseMoving = true;
                    this.moveHorizontally(this.dragObject, 'left');
                } else if (this.currentMouseCoords.x - this.dragObject.initMouseCoords.x > moveDistance) {
                    this.dragObject.isMouseMoving = true;
                    this.moveHorizontally(this.dragObject, 'right');
                }
            }

            // Move up / down
            keepMoving = false;
            if (targetRow) {
                const verticalDirection = this.currentMouseCoords.y > this.oldMouseCoords.y ? 'down' : 'up';
                this.dragObject.isMouseMoving = true;
                this.moveVertically(this.dragObject, verticalDirection, targetRow);
                keepMoving = await this.scrollIfNeeded(targetRow);
            }
        }

        this.oldMouseCoords = this.currentMouseCoords;
        this.dragObject.isMouseMoving = false;
    }

    /**
     * Get the row that will be subject to move operations
     *
     * // TODO: Harmonize classes between tableWidget und treeWidget
     *
     * @return {*}
     */
    getSelectedRow() {
        if (this.widgetElementWidget) {
            return this.datalist.querySelector('.row-selected');
        } else {
            return this.datalist.querySelector('.node.active, .node.selected');
        }

    }

    /**
     * Get current cursor position.
     *
     * @param event Mousemove event
     * @returns {{}} Current mouse coords
     */
    getMouseCoords(event) {
        const pos = {};
        pos.x = event.pageX;
        pos.y = event.pageY;
        return pos;
    }

    /**
     * Find row by coords
     *
     * Skips fixed rows
     *
     * @param {number} x x coordinate
     * @param {number} y y coordinate
     * @returns {HTMLElement} Hovered row
     */
    getDropTarget(x, y) {
        let targetRow;
        const rows = this.datalist.querySelectorAll('[data-list-itemof]');

        for (let i = 0; i <= rows.length - 1; i++) {
            const element = rows[i];
            const rowTop = element.getBoundingClientRect().top;
            if ((y > rowTop) && (y < (rowTop + element.offsetHeight))) {
                targetRow = element;
                break;
            }
        }

        if (targetRow && targetRow.classList.contains('fixed')) {
            return;
        }

        return targetRow;
    }

    /**
     * Move a row
     *
     * TODO: Implement row moving by buttons and keyboard (ctrl + up/down/left/right)
     *
     * @param {HTMLElement} row
     * @param {String} direction
     */
    moveRow(row, direction, targetRow) {
        if (!row) {
            return;
        }

        const dragObject = this.createDragObject(row);
        const widget = this.widgetElementWidget || this.treeWidget;

        if ((direction === 'left') || (direction === 'right')) {
            this.moveHorizontally(dragObject, direction);
        }
        else if (((direction === 'up') || (direction === 'down')) && (widget)) {

            // Get target row
            if (!targetRow && (direction === 'up')) {
                targetRow = widget.getPreviousVisibleNode(dragObject.draggedRow);
            }
            else if (!targetRow && (direction === 'down')) {
                targetRow = widget.getNextVisibleNode(dragObject.lastRow);
            }

            if (targetRow) {
                this.moveVertically(dragObject, direction, targetRow);
            }
        }

        this.releaseDragObject(dragObject);
        Utils.scrollIntoViewIfNeeded(row, row.closest('.widget-scrollbox'), false, 'y');

        if (widget) {
            widget.setFocus();
        }
    }

    /**
     * Drag row in horizontal direction and updates tree.
     *
     * @param {object} dragObject
     * @param {String} direction Drag direction 'left' or 'right
     */
    moveHorizontally(dragObject, direction) {
        if (!dragObject || !dragObject.draggedRow || !dragObject.draggedRow.querySelector('.tree-indent')) {
            return;
        }

        const isLastChild = dragObject.draggedRow.treePosition === dragObject.draggedRow.treeParent.treeChildren;
        dragObject.initMouseCoords = this.currentMouseCoords;

        if (direction === 'left') {
            if (!isLastChild) {
                return;
            }
            dragObject.newParentRow = dragObject.oldParentRow ? dragObject.oldParentRow.treeParent : undefined;
        } else {
            dragObject.newParentRow = this.findNewParentRow(dragObject);
        }

        dragObject.oldParentRow = dragObject.newParentRow;
        this.updateHierarchy(dragObject);
    }

    /**
     * Find new parent row after horizontal drag to the right
     *
     * @param {object} dragObject
     * @returns {HTMLElement} New parent row
     */
    findNewParentRow(dragObject) {
        const prevRow = Utils.getPrevSibling(dragObject.draggedRow, '[data-list-itemof]');
        if (!prevRow || !Utils.isElementVisible(prevRow) || prevRow.classList.contains('fixed')) {
            return dragObject.oldParentRow;
        }

        let newParentRow = null;
        if (prevRow.treeLevel === dragObject.draggedRow.treeLevel) {
            newParentRow = prevRow;
        } else if (prevRow.treeLevel === (dragObject.draggedRow.treeLevel + 1)) {
            newParentRow = prevRow.treeParent;
        } else if (prevRow.treeLevel !== (dragObject.draggedRow.treeLevel - 1)) {
            for (let row of this.treeWidget.treeGetAncestors(prevRow)) {
                if (row.treeLevel === dragObject.draggedRow.treeLevel) {
                    newParentRow = row;
                    break;
                }
            }
        } else {
            newParentRow = dragObject.oldParentRow;
        }

        return newParentRow;
    }

    /**
     * Drag row in vertical direction and update tree.
     *
     * @param {object} dragObject
     * @param {String} direction Drag direction: 'up' or 'down'
     * @param {HTMLElement} targetRow Row that is dragged over
     */
    moveVertically(dragObject, direction, targetRow) {
         if ((targetRow === dragObject.draggedRow) || [...dragObject.draggedDescendants].includes(targetRow)) {
             return false;
         }

        if (direction === 'down') {
            if (targetRow.classList.contains('item-haschildren') && targetRow.classList.contains('item-collapsed')) {
                const rowChildren =  this.treeWidget ? this.treeWidget.treeGetDescendants(targetRow) : [];
                rowChildren[rowChildren.length - 1].after(dragObject.draggedRow);
            } else {

                targetRow.after(dragObject.draggedRow);
            }

            if (targetRow.classList.contains('item-haschildren') && !targetRow.classList.contains('item-collapsed')) {
                dragObject.newParentRow = targetRow;
            } else {
                dragObject.newParentRow = targetRow.treeParent;
            }
        } else {
            targetRow.before(dragObject.draggedRow);
            dragObject.newParentRow = targetRow.treeParent;
        }

        dragObject.oldParentRow = dragObject.newParentRow;
        dragObject.draggedRow.after(...dragObject.draggedDescendants);

        this.updateHierarchy(dragObject);
        return true;
    }

    /**
     * Update parent and levels of the dragged rows
     *
     * @param {object} dragObject
     */
    updateHierarchy(dragObject) {
        if (this.treeWidget) {
            const newParentId = dragObject.newParentRow ? dragObject.newParentRow.dataset.id || '' : '';
            const newParentLevel = dragObject.newParentRow ? dragObject.newParentRow.treeLevel : 0;

            dragObject.draggedRow.dataset.parent = newParentId;
            dragObject.draggedRow.dataset.treeParent = newParentId;

            const draggedRows = [dragObject.draggedRow, ...dragObject.draggedDescendants];

            const levelOffset = (newParentLevel + 1) - dragObject.draggedRow.treeLevel;
            if (levelOffset !== 0) {
                draggedRows.forEach(
                    (node) => {
                        node.treeLevel = node.treeLevel + levelOffset;
                        node.dataset.level = node.treeLevel;
                    }
                );
                this.treeWidget.treeRecreateIndentation(draggedRows);
            }

            this.treeWidget.treeUpdatePositions();
        }
    }


    /**
     * Scroll datalist to keep dragged row in viewport.
     *
     * TODO: move row to new position and keep scrolling if necessary
     *
     * @param {HTMLElement} row Dragged row
     */
    async scrollIfNeeded(row) {
        const container = row.closest('.widget-scrollbox') || row.closest('.widget-scrollsync-content');

        const topHotArea = 90;
        const bottomHotArea = 80;

        let frameRate = 40;
        let topOffset =  this.currentMouseCoords.y - (container.getBoundingClientRect().top + topHotArea);
        let bottomOffset = this.currentMouseCoords.y - (container.getBoundingClientRect().bottom - bottomHotArea);

        // Up
        if (topOffset < 0) {
            topOffset = topOffset / 2;
            container.scrollBy({top: topOffset, behavior: 'instant'});
            await new Promise(r => setTimeout(r, frameRate));
            return true;
        }

        // Down
        else if (bottomOffset > 0) {
            bottomOffset = bottomOffset / 2;
            container.scrollBy({top: bottomOffset, behavior: 'instant'});
            await new Promise(r => setTimeout(r, frameRate));
            return true;
        }

        return false;

    }

}


window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['table'] = TableWidget;
window.App.widgetClasses['dragitems'] = DragItemsWidget;
