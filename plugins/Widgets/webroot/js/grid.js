/*
 * Grid widget - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {BaseWidget} from '/js/base.js';
import Utils from '/js/utils.js';

/**
 * Manages the display and editing functionality of items in a grid, for example heraldry items.
 * The widget is tightly coupled with the corresponding ItemsModel and, therefore,
 * contains properties for the item data container as well as the grid (the visual representation of the data).
 *
 * Changes made from within the item container (adding, deleting, modifying items)
 * are handled in the ItemsModel and sent to the GridWidget via custom events
 * such as `epi:change:item`, that are registered below, see `initWidget()`.
 *
 * Conversely, when changes are initiated from the grid via drag and drop,
 * the item container data are synchronized, see `onGridItemDropped()`.
 */
export class GridWidget extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        /**
         * The item types managed by this widget (for example "heraldry")
         * @type {string}
         */
        this.itemtype = element.dataset.itemtype;

        /**
         * The HTML element that contains input fields for the grid size ("Columns" and "Rows").
         * @type {HTMLElement}
         */
        this.gridSizeElement = element.querySelector('.doc-section-grid-size');

        /**
         * The HTML element that contains the grid table (for example heraldry items).
         * @type {HTMLElement}
         */
        this.gridTable = element.querySelector('.doc-section-grid-table');

        /**
         * The HTML element that contains the item list.
         * @type {HTMLElement}
         */
        this.itemContainer = element.closest('.doc-section').querySelector('.doc-section-groups');
    }

    /**
     * Bind events
     */
    initWidget() {
        this.sectionElement = this.widgetElement.closest('.doc-section');
        if (this.sectionElement) {
            this.listenEvent(this.sectionElement,'epi:add:item', event => this.onItemAdded(event));
            this.listenEvent(this.sectionElement,'epi:remove:item', event => this.onItemRemoved(event));
            this.listenEvent(this.sectionElement,'epi:change:item', event => this.onItemChanged(event));
            this.listenEvent(this.sectionElement,'epi:drop:item', event => this.onGridItemDropped(event));
        }

        this.listenEvent(this.widgetElement.querySelector('.doc-section-grid-size'), 'change', event => this.onLayoutChanged(event));
    }

    /**
     *  Initialize the grid layout inputs and the grid size
     */
    initLayout() {
        if (!this.gridSizeElement) {
            return;
        }

        const gridSize = this.getGridSize();
        this.setGridSize(gridSize);
        this.setMaxValues(gridSize);
    }

    /**
     * Handles layout changes (row and col number)
     *
     * @param event
     */
    onLayoutChanged(event) {
        this.initLayout();
    }

    /**
     * Fires when a new item in the section is created.
     *
     * @param {Event} event The added item can be found in the event.target property
     */
    onItemAdded(event) {
        if (event.target.dataset.rowType !== this.itemtype) {
            return;
        }


        this.initPosition(event.target);
        this.updateGridItem(event.target);
        this.initLayout();
    }

    /**
     * Fires when an item in the section is deleted.
     *
     * @param event Item removed
     */
    onItemRemoved(event) {
        if (!event.target.dataset.rowType === this.itemtype) {
            return;
        }

        this.removeGridItem(event.target);
    }

    /**
     * Fires when an item in the section was changed
     *
     * @param event Item changed
     */
    onItemChanged(event) {
        if (!event.target.dataset.rowType === this.itemtype) {
            return;
        }

        this.updateGridItem(event.target);
    }

    /**
     * Fires when an item in the grid was dropped. Updates the position inputs.
     *
     * @param {CustomEvent} event Item dropped
     */
    onGridItemDropped(event) {
        const id = event.target.dataset.rowId;

        const tableItem = this.getTableItem(id);
        const inputPosX = tableItem.querySelector('input[data-row-field="pos_x"]')
        const inputPosY = tableItem.querySelector('input[data-row-field="pos_y"]')

        inputPosX.value = [...event.target.closest('tr').children].indexOf(event.target.closest('td')) + 1;
        inputPosY.value = [...event.target.closest('tbody').children].indexOf(event.target.closest('tr')) + 1;

        this.updateZInputs(event.detail.data.dragOrigin);
        this.updateZInputs(event.detail.data.dragTarget);
    }

    /**
     * Get an item in the table by its ID
     *
     * @param {string} id ID of the table item that should be returned
     * @returns {HTMLElement | void} Table item with given ID
     */
    getTableItem(id) {
        if (!this.itemContainer) {
            return;
        }

        return this.itemContainer.querySelector(`.doc-section-item[data-row-id='${id}']`)
    }

    /**
     * Get or create the group within a cell,
     * ready to append new children
     *
     * TODO: expand grid if necessary
     *
     * @param {number} x The column index, starting from 1
     * @param {number} y The row index, starting from 1
     * @return {HTMLElement | void} Item group in td element if table exists.
     */
    getGridCell(x, y) {
        if (!this.gridTable) {
            return;
        }

        let group;
        try {
            const cell = this.gridTable.rows[y-1].cells[x-1];
            group = cell.querySelector('.doc-section-item-group');
            if (!group) {
                group = Utils.spawnFromString('<div class="doc-section-item-group"></div>');
                cell.appendChild(group);
            }
        } catch (error) {
            group = undefined;
        }
        return group;
    }

    /**
     * Get an item in the grid by its ID
     *
     * @param {string} id ID of the grid item that should be returned
     * @returns {HTMLElement | void} Grid item with given ID
     */
    getGridItem(id) {
        if (!this.gridTable) {
            return;
        }

        return this.gridTable.querySelector('[data-row-table="items"][data-row-id="' + id + '"]');
    }

    /**
     * Remove an item from grid.
     *
     * @param {HTMLElement} tableItem Table item for corresponding grid item that should be removed
     */
    removeGridItem(tableItem) {
        const itemId = tableItem.dataset.rowId;
        const item = this.getGridItem(itemId);
        if (item) {
            const itemOrigin = item.parentElement;
            item.remove();
            this.updateZInputs(itemOrigin);
        }
    }

    /**
     * Initialize the position of a new item in the grid
     * behind the last item in the grid.
     *
     * @param {HTMLElement} tableItem The new item in the table
     */
    initPosition(tableItem) {

        const gridItems = this.gridTable.querySelectorAll('.doc-section-item');
        if (gridItems.length === 0) {
            return;
        }
        const lastGridItem = gridItems[gridItems.length- 1];
        const lastTableItem = this.getTableItem(lastGridItem.dataset.rowId);
        if (!lastTableItem) {
            return;
        }

        // Get position of the last item in the grid
        const x = parseInt(lastTableItem.querySelector('[data-row-field="pos_x"]').value);
        const y = parseInt(lastTableItem.querySelector('[data-row-field="pos_y"]').value);
        const z = parseInt(lastTableItem.querySelector('[data-row-field="pos_z"]').value);

        // Set the position of the new item behind the last item
        Utils.setInputValue(tableItem.querySelector('[data-row-field="pos_x"]'), x);
        Utils.setInputValue(tableItem.querySelector('[data-row-field="pos_y"]'), y);
        Utils.setInputValue(tableItem.querySelector('[data-row-field="pos_z"]'), z + 1);
    }

    /**
     * Update a grid item or add it if not already present
     *
     * @param {HTMLElement} tableItem The table item
     */
    updateGridItem(tableItem) {
        const itemId = tableItem.dataset.rowId;

        // Get position
        const x = parseInt(tableItem.querySelector('[data-row-field="pos_x"]').value);
        const y = parseInt(tableItem.querySelector('[data-row-field="pos_y"]').value);
        const z = parseInt(tableItem.querySelector('[data-row-field="pos_z"]').value);

        let gridItem = this.getGridItem(itemId);

        // Create grid item
        if (!gridItem) {
            if (x && y && z) {
                const templateItem = this.widgetElement.querySelector('.template-doc-section-grid-item');
                gridItem = Utils.spawnFromTemplate(templateItem, {"id": itemId});
                const group = this.getGridCell(x, y);
                if (group) {
                    group.appendChild(gridItem);
                }
            }
        }

        if (!gridItem) {
            return false;
        }

        // Move grid item to its position
        this.moveGridItem(gridItem, x, y, z);

        // Transfer input values from the table to the grid item
        gridItem.querySelectorAll('.doc-field').forEach(
            function(docField) {
                const fieldName = docField.dataset.rowField;
                const fieldContent = docField.querySelector('.doc-field-content');
                let fieldText;
                let fieldValue;

                if (docField.dataset.rowFormat === 'property') {
                    fieldValue = tableItem.querySelector('input.input-reference-value').value;
                    fieldText = tableItem.querySelector('input.input-reference-text').value;
                }
                else {
                    const fieldInput = tableItem.querySelector('[data-row-field="' + fieldName + '"] input');
                    fieldText = fieldInput ? fieldInput.value : undefined;
                }

                if (fieldContent && fieldText)  {
                    fieldContent.textContent = fieldText;
                }
                if (fieldContent && fieldValue)  {
                    fieldContent.dataset.rowValue = fieldValue;
                }
            }
        );
    }

    /**
     * Move an item to a new position
     *
     * @param {HTMLElement} gridItem Grid item
     * @param {number} x The column index, starting from 1
     * @param {number} y The row index, starting from 1
     * @param {number} z The item order, starting from 1
     * @return {boolean}
     */
    moveGridItem(gridItem, x, y, z) {

        // Expand grid if necessary
        let currentSize = this.getGridSize();
        if (currentSize.cols < x) {
            currentSize.cols = x;
            Utils.setInputValue(this.gridSizeElement.querySelector('input.doc-section-grid-cols'), x);
        }
        if (currentSize.rows < y) {
            currentSize.rows = y;
            Utils.setInputValue(this.gridSizeElement.querySelector('input.doc-section-grid-rows'), y);
        }
        this.setGridSize(currentSize);

        try {
            const itemOrigin = gridItem.parentElement;
            gridItem.remove();

            const itemTarget = this.getGridCell(x,y);
            if (itemTarget) {
                if (z >= 1) {
                    z = Math.min(z, itemTarget.childElementCount + 1);
                    if (z > itemTarget.childElementCount) {
                        itemTarget.appendChild(gridItem);
                    } else {
                        itemTarget.insertBefore(gridItem, itemTarget.children[z - 1]);
                    }
                }
                this.updateZInputs(itemTarget);
            }
            this.updateZInputs(itemOrigin);
        } catch (error) {
            return false;
        }
        return true;
    }

    /**
     * Sort items according to their grid position
     */
    sortItems() {
        const focusedElement =  document.activeElement;

        const headerItem = this.itemContainer.querySelector('.doc-group-headers');
        const items = this.gridTable.querySelectorAll('[data-row-table="items"][data-row-id]');
        for (let i=items.length-1; i >= 0; i--) {
            const tableItem = this.getTableItem(items[i].dataset.rowId);
            const itemInput = tableItem ? tableItem.querySelector('.doc-fieldname-sortno input') : undefined;
            if (itemInput) {
                itemInput.value = i+1;
            }

            const gridItem = items[i];
            const fieldSortno = gridItem.querySelector('[data-row-field="sortno"] .doc-field-content');
            if (fieldSortno) {
                fieldSortno.innerHTML = i+1;
            }

            // Move the table item
            if (headerItem && tableItem) {
                headerItem.insertAdjacentElement('afterend', tableItem);
                tableItem.classList.remove('doc-section-item-first');
            }
        }

        const firstTableItem = this.itemContainer.querySelector('[data-row-table="items"][data-row-id]');
        if (firstTableItem) {
            firstTableItem.classList.add('doc-section-item-first');
        }

        focusedElement.focus();
    }

    /**
     * Search items in the table and add them to the new cell.
     *
     * @param {HTMLElement} cell
     * @param {number} x Column index starting from 1
     * @param {number} y Row index starting from 1
     */
    fetchItems(cell, x, y) {
        if (!this.itemContainer) {
            return;
        }

        // Add group
        this.getGridCell(x, y);

        // Add items
        const self = this;
        this.itemContainer
            .querySelectorAll('[data-row-table="items"]')
            .forEach(
                (item) => {
                    const xInput = item.querySelector('[data-row-field="pos_x"]');
                    const yInput = item.querySelector('[data-row-field="pos_y"]');
                    if (xInput && yInput && (xInput.value == x) && (yInput.value == y)) {
                        self.updateGridItem(item);
                    }
                }
            );
    }

    /**
     * Get x and y index of given item in grid. Indexes start with 1.
     *
     * @param {string} id Id of the item
     * @returns {{x: number, y: number}} Object with indexes
     */
    findPositionInGrid(id) {
        let x;
        let y;

        y = [...this.gridTable.rows].findIndex(row => {
            x = [...row.cells].findIndex(cell => cell.querySelector(`[data-row-id="${id}"]`)) + 1;
            return x !== 0; // Return true if the cell was found, otherwise false
        }) + 1;

        return {x, y};
    }

    /**
     * Retrieve the current grid size from the widget's gridSize element (rows and cols)
     * and ensure that only non-negative values are used.
     * The resulting grid size is returned as an object with 'rows' and 'cols' properties.
     *
     * @returns {object} An object with 'rows' and 'cols' properties representing the current grid size.
     */
    getGridSize() {
        const inputRows = this.gridSizeElement.querySelector('input[data-row-field="layout_rows"]');
        const inputCols = this.gridSizeElement.querySelector('input[data-row-field="layout_cols"]');

        const rows = Math.max(1, Number(inputRows.value || 1));
        const cols = Math.max(1, Number(inputCols.value || 1));

        return {rows, cols};
    }

    /**
     * Set the grid size to the given number of rows and columns.
     *
     * @param {object} gridSize The target grid size as an object with rows and cols
     */
    setGridSize(gridSize) {
        if (this.gridSizeElement) {
            Utils.setInputValue(this.gridSizeElement.querySelector('input[data-row-field="layout_rows"]'), gridSize.rows);
            Utils.setInputValue(this.gridSizeElement.querySelector('input[data-row-field="layout_cols"]'), gridSize.cols);
        }

        if (!this.gridTable) {
            return;
        }

        const currentRows = this.gridTable.rows.length;
        const currentCols = currentRows ? this.gridTable.rows[0].cells.length : 0;

        // Add rows
        for (let y = currentRows; y < gridSize.rows; y++) {
            let row = this.gridTable.insertRow(-1);
            for (let x = 0; x < gridSize.cols; x++) {
                let cell = row.insertCell(-1);
                this.fetchItems(cell, x+1, y+1);
            }
        }

        // Add cols
        if (currentCols < gridSize.cols) {
            for (let y = 0; y < currentRows; y++) {
                let row = this.gridTable.rows[y];
                for (let x = currentCols; x < gridSize.cols; x++) {
                    let cell = row.insertCell(-1);
                    this.fetchItems(cell, x+1, y+1);
                }
            }
        }

        // Hide rows
        for (let y = currentRows; y > gridSize.rows; y--) {
            let row = this.gridTable.rows[y-1];
            for (let x = 0; x < row.cells.length; x++) {
                let col = row.cells[x];
                col.classList.add('hide');
            }
            row.classList.add('hide');
        }

        // Hide cols
        if (currentCols > gridSize.cols) {
            for (let row of this.gridTable.rows) {
                for (let x = row.cells.length; x > gridSize.cols; x--) {
                    let col = row.cells[x-1];
                    col.classList.add('hide');
                }
            }
        }

        // Show rows and cols
        for (let y = 0; y < gridSize.rows; y++) {
            let row = this.gridTable.rows[y];
            for (let x = 0; x < gridSize.cols; x++) {
                let col = row.cells[x];
                col.classList.remove('hide');
            }
            row.classList.remove('hide');
        }
    }
    /**
     * Set maximum values (`max` attribute) for x- and y-coordinate input fields within the given HTMLElement,
     * ensuring they do not exceed the grid size. Input elements from deleted items (`data-deleted="1"`)
     * are excluded to prevent resetting the maximum values on the next grid layout change.
     *
     * @param {Object} value An object with 'cols' and 'rows' properties representing the number of grid columns and rows.
     */
    setMaxValues(value) {
        if (!this.itemContainer) {
            return;
        }

        /**
         * The list of input elements representing grid position (x, y, z).
         * @type {NodeListOf<HTMLInputElement>}
         */
        const inputs = this.itemContainer.querySelectorAll('.doc-section-item:not([data-deleted]) .doc-fieldname-pos input');

        inputs.forEach(input => {
            if (input.dataset.rowField === 'pos_x') {
                input.setAttribute('max', value.cols);
            }
            if (input.dataset.rowField === 'pos_y') {
                input.setAttribute('max', value.rows);
            }
        });
    }

    /**
     * Recalculate z-indexes after drop and item change.
     *
     * @param {HTMLElement} group The container of the items
     */
    updateZInputs(group) {
        for (let z = 1; z <= group.children.length; z++) {
            const itemId = group.children[z - 1].dataset.rowId;
            const tableItem = this.getTableItem(itemId);
            const inputPosZ = tableItem.querySelector('input[data-row-field="pos_z"]');
            inputPosZ.setAttribute('max', group.children.length);
            inputPosZ.value = z;
        }
        this.sortItems();
    }
}

window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['grid'] = GridWidget;
