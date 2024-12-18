/*
 * ScrollPOaginator widget - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {BaseWidget} from '/js/base.js';
import Utils from '/js/utils.js';

/**
 * Infinite scroll box with asynchronous links that handles lists, tables and tree structures.
 *
 * Add the following markup:
 * - Add the class 'widget-scrollbox' to the list container (not the list element itself).
 * - For horizontal scrolling, add the class 'widget-scrollbox-horizontal' to the list container.
 *
 * - Name the list element: data-list-name="mylist"
 * - Name the list items: data-list-itemof="mylist"
 * - Add node IDs to the list items: data-id="2" data-tree-parent="1".
 *
 * - Add the URL for retrieving next results to the list: data-list-action-next="/getitems?page=3"
 *   Optionally, add the URL for retrieving previous results to the list: data-list-action-prev="/getitems?page=1"
 *   Optionally, add the URL for retrieving a single row to the list: data-list-action-get="/getitems=id=123".
 *
 *   The paginator supports cursor based and page based pagination. For cursor based pagination,
 *   the results should contain cursor nodes (see below for implementation details).
 *   TODO: Add documentation for data-cursor-id, data-cursor-dir
 *
 *   You can update single rows (e.g. when they were changed outside of the widget) by calling fetchRow(id)
 *   or updateRows().
 *
 *   The widget listens for epi:update:row events and automatically updates the list rows,
 *   when the attributes data-list-name, data-list-action-get (on the list container, e.g. tbody)
 *   and data-id, data-list-itemof (on the list elements, e.g. tr) are provided as described above.
 *
 *   TODO: update the following description
 *   The data of the epi:update:row event is parsed and elements that contain the configured list name
 *   in the the data-row-table attribute are extracted. For each of these elements, on the basis of their
 *   data-row-id attributes, the endpoint provided in data-list-action-get is requested.
 *   Make sure, the configured data-list-action-get endpoint supports the id parameter.
 */
export class ScrollPaginator extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        this.horizontal = element.classList.contains('widget-scrollbox-horizontal');
        this.dataList = null;
        this.listName = null;

        this.isLoading = false;
        this.stopped = false;


        element.addEventListener('scroll', event => this.onBoxScrolled(event));
        element.addEventListener('click', event => this.onHeaderClicked(event));
        element.addEventListener('epi:toggle:node', event => this.onNodeToggled(event));

        this.tabsheet = this.widgetElement.closest('.widget-tabsheets-sheet');
        if (this.tabsheet) {
            this.listenEvent(this.tabsheet, 'epi:show:tabsheet', () => this.seekRow());
        }

        this.updateWidget();

        // Listen for row updates
        document.addEventListener('epi:update:row',(event) => this.onUpdateRow(event));
        document.addEventListener('epi:move:row',(event) => this.onUpdateRow(event));
        document.addEventListener('epi:create:row',(event) => this.onUpdateRow(event));
        document.addEventListener('epi:delete:row',(event) => this.onUpdateRow(event));
    }

    /**
     * Init list of scrollbox.
     */
    initList() {
        this.dataList = this.widgetElement.querySelector('[data-list-name]');
        this.listName = this.dataList ? this.dataList.dataset.listName : null;

        this.modelName = Utils.querySelectorData(this.widgetElement, '[data-model]', 'model', '');
        const tableName = this.modelName.split('.',2);
        this.tableName = tableName.length === 1 ? tableName[0] : tableName[1];

        const treeWidgetElement = this.widgetElement.querySelector('.widget-tree');
        this.treeWidget = this.getWidget(treeWidgetElement, 'tree', false);

        const tableWidgetElement = this.widgetElement.querySelector('.widget-table');
        this.tableWidget = this.getWidget(tableWidgetElement, 'table', false);

    }

    initWidget() {
        this.initList();
    }

    /**
     * Update widget after events, for example when filter widget is updated.
     */
    updateWidget() {
        this.initList();
        this.seekRow();

        if (this.treeWidget) {
            this.treeWidget.treeUpdatePositions();
        }

        this.onBoxScrolled();
    }

    /**
     * Called after table head cell is clicked. Resorts table rows.
     *
     * TODO: move to tablewidget?
     * TODO: update column selector filter widget
     *
     * @param event Click
     */
    onHeaderClicked(event) {
        if (!event.target.closest('thead a') || !this.widgetElement.contains(event.target)) {
            return;
        }


        event.preventDefault();
        App.showLoader();

        const url = event.target.closest('thead a').getAttribute('href');
        this.pushHistory = window.history.pushState && url && !this.isInFrame();

        $.ajax({
            type: 'GET',
            url: url,
            dataType: 'html',
            success: data => {
                if (this.pushHistory) {
                    window.history.pushState(url, "Epigraf - search results", url);
                }

                // TODO: jquery left. Is combined with replaceDataSnippets -> leave it as is for now...
                App.replaceDataSnippets(data, this.getContentPane(false));
                this.loadCursors();

                // TODO: Why hide here and in the complete callback?
                App.hideLoader();
                this.onBoxScrolled();
            },

            //TODO: Dry all ajax error handlers
            error: (xhr, textStatus, errorThrown) => {
                // If aborted (see above, https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/abort)
                if ((xhr.readyState === XMLHttpRequest.UNSENT) && (xhr.status === 0)) {
                    this.stopped = true;
                    return;
                }
                let msg = Utils.getValue(xhr.responseJSON,'error.message');
                msg = msg ? (errorThrown + ': ' + msg) : errorThrown;
                App.showMessage(msg,textStatus);
            },
            complete: (xhr, textStatus) => {
                this.isLoading = false;
                App.hideLoader();
            }
        });
    }

    /**
     * Handle opening and closing tree nodes.
     *
     * @param event Click
     */
    onNodeToggled(event) {
        //TODO: The scrollbox looses its datalist when it is a data-snippet. Can this be improved?
        this.initList();
        this.loadCursors();
    }

    /**
     * Load properties after scroll.
     *
     * @param event Scroll
     */
    onBoxScrolled(event) {
        if (this.horizontal) {
            const boxWidth = this.widgetElement.offsetWidth;
            const contentWidth = this.widgetElement.scrollWidth;
            const scrollLeft = this.widgetElement.scrollLeft;

            if ((boxWidth > 0) && (scrollLeft < 120)) {
                this.prevRows();
            }

            if (scrollLeft > (contentWidth - boxWidth - 120)) {
                this.nextRows();
            }
        } else {
            const boxHeight = this.widgetElement.offsetHeight;
            const contentHeight = this.widgetElement.scrollHeight;
            const scrollTop = this.widgetElement.scrollTop;

            if ((boxHeight > 0) && (scrollTop < 120)) {
                this.prevRows();
            }

            if ((boxHeight > 0) && (scrollTop > (contentHeight - boxHeight - 120))) {
                this.nextRows();
            }
        }
        this.loadCursors();
    }

    /**
     * Activate, focus and scroll a row into view
     *
     * @param {string} id The row ID. If not set, the currently selected row is focused.
     * @param {boolean} open Open the row
     */
    seekRow(id, open = false) {
        this.initList();
        if (!this.dataList) {
            return;
        }

        let row;

        if (id) {
            row = this.dataList.querySelector('[data-id="' + id + '"]');

            // Focus and optionally open
            const table = this.dataList.closest('table');
            const tableWidget = table ? table.widgets['table'] : undefined;
            if (tableWidget && row) {
                tableWidget.activateRow(row, open);
                tableWidget.setFocus(true);
            }
        } else {
            row = this.dataList.querySelector('.row-selected, .active');
        }

        // Scroll into view
        if (row) {
            Utils.scrollIntoViewIfNeeded(row, this.widgetElement, 'y', 'y');
        }
    }

    /**
     * Get next cursor node in scrollbox.
     *
     * @param {string} direction
     * @returns {Element} Cursor node
     */
    getCursor(direction) {
        if (direction === 'prev') {
            return Utils.querySelectorLast(this.dataList, '.node-cursor[data-cursor-dir="' + direction + '"]:not(.item-hidden)');
            //return this.dataList.querySelector('.node-cursor[data-cursor-dir="' + direction + '"]:not(.item-hidden)');
        } else {
            return this.dataList.querySelector('.node-cursor[data-cursor-dir="' + direction + '"]:not(.item-hidden)');
        }
    }

    /**
     * Load the next visible cursor.
     */
    loadCursors() {
        this.initList();

        if (!this.dataList || (this.dataList.offsetHeight === 0)) {
            return false;
        }

        const prevCursor = Array.from(this.dataList.querySelectorAll('.node-cursor[data-cursor-dir="prev"]:not(.item-hidden)'))
            .filter(elm => Utils.isVisible(elm, this.widgetElement)).at(-1);

        if (prevCursor) {
            this.fetchRows(prevCursor);
        }

        const nextCursor = Array.from(this.dataList.querySelectorAll('.node-cursor[data-cursor-dir="next"]:not(.item-hidden)'))
            .filter(elm => Utils.isVisible(elm, this.widgetElement)).at(0);

        if (nextCursor) {
            this.fetchRows(nextCursor);
        }
    }

    /**
     * Fetch previous rows.
     *
     * TODO: implement fetching preceding rows
     */
    prevRows() {
        return;
        this.fetchRows('prev');
    }

    /**
     * Fetch next rows.
     */
    nextRows() {
        this.fetchRows('next');
    }

    /**
     * Fetch and merge a single row.
     *
     * @param {number} id Row id
     * @param {boolean} focus Focus the node after update
     * @param {boolean} open Open the node after update
     * @param {boolean} clear Clear the list before updating
     * @returns {boolean} False if there is no url provided to reload data.
     */
    updateRow(id, focus = false, open=false, clear = false) {
        // Check datalist and listname properties
        if (!this.dataList || !this.listName) {
            return false;
        }

        let url = this.dataList.dataset.listActionGet;
        if (!url) {
            return false;
        }

        const row = this.dataList.querySelector('[data-id="' + id + '"]');
        if (row) {
            row.classList.add('row-stale');
        }

        url = new URL(url, App.baseUrl);

        if (clear) {
            url.searchParams.set('seek', id);
            url.searchParams.delete('find');
        } else {
            url.searchParams.set('id', id);
        }
        url = url.toString();

        // Request data
        App.ajaxQueue.add(this.listName,
            {
                type: 'GET',
                url: url,
                dataType: 'html',
                beforeSend: xhr => {
                    App.showLoader();
                },
                success: (data, textStatus, xhr) => {
                    // Extract data
                    data = new DOMParser().parseFromString(data, 'text/html');
                    const items = data.querySelectorAll('[data-list-itemof=' + this.listName + ']');

                    // Update items
                    if (items.length > 0) {
                        if (clear) {
                            this.clearRows();
                        }

                        this.mergeRows(items, undefined, clear ? 'next' : 'mount');

                        // Focus the row (for keyboard interaction)
                        if (clear) {
                            this.updateWidget();
                        }
                        if (focus) {
                            this.seekRow(id, open);
                        }
                    }
                    // TODO: remove deleted items
                },
                error: (xhr, textStatus, errorThrown) => {
                    // If aborted (see above, https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/abort)
                    if ((xhr.readyState === XMLHttpRequest.UNSENT) && (xhr.status === 0)) {
                        this.stopped = true;
                        return;
                    }
                    let msg = Utils.getValue(xhr.responseJSON,'error.message');
                    msg = msg ? (errorThrown + ': ' + msg) : errorThrown;
                    App.showMessage(msg, textStatus);
                },
                complete: (xhr, textStatus) => {
                    App.hideLoader();
                }
            }
        );
    }

    /**
     * Remove all rows
     */
    clearRows() {
        // Check datalist and listname properties
        if (!this.dataList || !this.listName) {
            return false;
        }

        const table = this.dataList.closest('table');
        const tableWidget = table ? table.widgets['table'] : undefined;
        if (tableWidget) {
            tableWidget.deactivateRows();
        }

        let row = this.dataList.querySelector('[data-list-itemof]');
        while (row) {
            row.remove();
            row = this.dataList.querySelector('[data-list-itemof]');
        }

        return true;
    }

    /**
     * Delete a single row.
     *
     * @param {number} id Row id
     * @param {boolean} focus Focus the next node
     * @param {boolean} open Open the next node
     * @returns {boolean} False if there is no row matching the id
     */
    deleteRow(id, focus = false, open=false) {
        // Check datalist and listname properties
        if (!this.dataList || !this.listName) {
            return false;
        }

        const row = this.dataList.querySelector('[data-id="' + id + '"]');
        if (row) {
            let nextRow;

            const table = this.dataList.closest('table');
            const tableWidget = table ? table.widgets['table'] : undefined;
            const treeWidget = table ? table.widgets['tree'] : undefined;

            if (focus && row && treeWidget) {
                nextRow = treeWidget.treeGetNextSibling(row);
                if ((nextRow === row) || !nextRow) {
                    nextRow = treeWidget.treeGetPrevSibling(row);
                }
                if (nextRow === row) {
                    nextRow = undefined;
                }
            } else if (focus && row && tableWidget) {
                nextRow = tableWidget.getNextVisibleNode(row);
                if ((nextRow === row) || !nextRow) {
                    nextRow = tableWidget.getPreviousVisibleNode(row);
                }
                if (nextRow === row) {
                    nextRow = undefined;
                }
            }

            if (row && treeWidget) {
                treeWidget.treeRemoveItem(row);
            } else if (row) {
                row.remove();
            }

            if (tableWidget && nextRow) {
                tableWidget.activateRow(nextRow, open);
                tableWidget.setFocus(true);
            } else {
                tableWidget.deactivateRows();
            }
            return true;
        }
        return false;
    }

    /**
     * Fetch and merge more rows.
     *
     * @param {HTMLElement|string} cursorNode Cursor node at the end or start of a subtree.
     *                                        When set to 'next' or 'prev', the cursor node will be
     *                                        retrieved from the tree bei getCursor().
     * @returns {boolean} False if no list exists
     */
    fetchRows(cursorNode) {
        // Find list elements
        this.initList();

        // Check datalist and listname properties
        if (!this.dataList || !this.listName || this.isLoading) {
            return false;
        }

        // Get next cursor and direction
        let cursorDir = 'next';
        if ((cursorNode === 'next')) {
            cursorDir = 'next';
            cursorNode = this.getCursor(cursorDir);
        } else if ((cursorNode === 'prev')) {
            cursorDir = 'prev';
            cursorNode = this.getCursor(cursorDir);
        } else if (cursorNode) {
            cursorDir = cursorNode.dataset.cursorDir;
        }
        const listAction = cursorDir === 'next' ? 'listActionNext' : 'listActionPrev';

        //Cursored pagination
        let url;
        if (cursorNode) {
            url = cursorNode ? cursorNode.dataset.cursorAction : null;
        }

        // Page based pagination
        else {
            url = this.dataList.dataset[listAction];
        }

        if (!url) {
            return false;
        }

        // Request data
        this.isLoading = true;
        this.stopped = false;

        App.ajaxQueue.add(this.listName,
            {
                type: 'GET',
                url: url,
                dataType: 'html',
                beforeSend: xhr => {
                    App.showLoader();
                },
                success: (data, textStatus, xhr) => {
                    data = new DOMParser().parseFromString(data, 'text/html');

                    // Update items
                    const items = data.querySelectorAll('[data-list-itemof=' + this.listName + ']');
                    this.mergeRows(items, cursorNode, cursorDir);

                    // Update action url
                    // (The action url should be undefined if the results only contain detail nodes)
                    const dataList = data.querySelector('[data-list-name=' + this.listName + ']')
                    const newUrl = dataList ? dataList.dataset[listAction] : undefined;
                    if (newUrl !== undefined) {
                        this.dataList.dataset[listAction] = newUrl;
                    }
                },
                error: (xhr, textStatus, errorThrown) => {
                    // Prevent further queries
                    this.stopped = true; //TODO: stop polling if 404 or similar

                    // If aborted (see above, https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/abort)
                    if ((xhr.readyState === XMLHttpRequest.UNSENT) && (xhr.status === 0)) {
                        this.stopped = true;
                        return;
                    }

                    let msg = Utils.getValue(xhr.responseJSON,'error.message');
                    msg = msg ? (errorThrown + ': ' + msg) : errorThrown;
                    App.showMessage(msg, textStatus);
                },
                complete: (xhr, textStatus) => {
                    this.isLoading = false;
                    App.hideLoader();

                    if (!this.stopped) {
                        this.onBoxScrolled();
                    }
                }
            }
        );
    }


    /**
     * Add loaded rows to existing table.
     * //TODO: compare columns
     * //TODO: Complete documentation
     *
     * @param {NodeList} items Rows
     * @param {HTMLElement} cursor Cursor node
     * @param {string} dir Whether to append (next), prepend (prev), or mount (mount) reloaded items.
     */
    mergeRows(items, cursor, dir) {
        this.initList();

        // Sort items ascending or descending
        dir = cursor ? cursor.dataset.cursorDir : dir;
        const collapsed = cursor && (cursor.dataset.cursorCollapsed === '1');

        // Store scroll position
        const scrollPos = (dir === 'prev') ? this.getScrollPosition() : undefined;

        // In prev direction, process the items bottom up
        if (dir === 'prev') {
            items = [...items].reverse();
        }

        // In next direction, step by step, new items are inserted after the reference,
        // in prev direction, new items are inserted before the reference.
        // The new item becomes the new reference.
        let reference = cursor;

        // The items in next direction contain ancestors before the cursor.
        // Therefore, capture parent IDs in a stack to monitor whether the subtree
        // containing the cursor is reached. The region contains one of three values:
        // - above The subtree containing the cursor is not reached yet.
        // - inside The item is inside the cursor subtree.
        // - below The subtree has passed.
        // For the prev direction, the region always stays 'above'.
        let region = 'above';
        let stack = [''];

        // Don't leave the subtree where the cursor originates
        const stopNode = Utils.getNextSibling(cursor, ".node:not([data-cursor-id])");
        let stopped = false;

        items.forEach(item => {

            // For child cursors, don't leave the subtree
            if (item && item.dataset.cursorId &&
                stopNode && (Number(item.dataset.level) <= Number(stopNode.dataset.level))
            ) {
                stopped = true;
            }

            if (stopped) {
                return;
            }

            // Skip cursor nodes pointing in the wrong direction
            if (item.dataset.cursorDir && (item.dataset.cursorDir !== dir)) {
                return;
            }

            // Monitor the region
            if (dir === 'next') {
                // Truncate stack to include only ancestors
                while ((stack.length) && stack[stack.length - 1] !== (item.dataset.treeParent)) {
                    stack.pop();
                }
                stack.push(item.dataset.id);

                // if (region === 'below') {
                //     return;
                // }
                // // Skip items that are not descendants or siblings of the cursor
                // if (cursor && !stack.includes(cursor.dataset.treeParent)) {
                //     return;
                // }
            }

            // Get already rendered item if available
            let current = null;
            let relatedCursor = null;
            if (item.dataset.cursorId !== undefined) {
                current = this.dataList.querySelector('[data-cursor-id="' + item.dataset.cursorId + '"][data-cursor-child="' + item.dataset.cursorChild + '"]');
                relatedCursor = this.dataList.querySelector('[data-parent="' + item.dataset.parent + '"][data-cursor-id]:not([data-cursor-child="1"])');
            } else if ((item.dataset.id !== undefined)) {
                current = this.dataList.querySelector('[data-id="' + item.dataset.id + '"]:not([data-cursor-id])');
                relatedCursor = this.dataList.querySelector('[data-cursor-id="' + item.dataset.id + '"]:not([data-cursor-child="1"])');
            }

            // Already present cursors of updated nodes will be
            // inserted as new items, remove the obsolete cursors.
            if (relatedCursor && !collapsed) {
                relatedCursor.remove();
            }

            // Update items already present on the page
            if (current) {
                //if (item.dataset.cursorId === undefined) {
                    current.classList.remove('row-stale');
                    current.replaceChildren(...item.children);

                    //TODO: check checked checkboxes
                    //TODO: keep focus
                    //TODO: any updates on the item level (not the children)?
                //}

                // Don't update the reference for ancestors above the cursor
                if (dir === 'next') {
                    reference = region !== 'above' ? current : reference;
                } else if (dir === 'prev') {
                    reference = current;
                }

                if (cursor && stack.includes(cursor.dataset.treeParent)) {
                    region = 'inside';
                } else if (cursor) {
                    region = region === 'inside' ? 'below' : region;
                }

                return;
            }

            //if (cursor && (cursor.dataset.level >= item.dataset.level )) {
            if (cursor && (cursor.dataset.treeParent === item.dataset.treeParent )) {
                if (item.classList.contains('item-hidden')) {
                    item.classList.remove('item-hidden');
                    item.classList.toggle('item-collapsed', !item.classList.contains('item-nochildren'));
                }
            }

            // Find parent and preceding sibling and add to the tree
            // but skip cursors
            // TODO: handle virtual nodes (references from)
            if (dir === 'mount') {
                if (!item.dataset.cursorId) {
                    const parentId = item.dataset.parent;
                    const precedingId = item.dataset.preceding;
                    let referenceNode;

                    // Add after a reference node
                    if (precedingId) {
                        referenceNode = this.dataList.querySelector('[data-id="' + precedingId + '"]');

                        if (referenceNode && this.treeWidget) {
                            this.treeWidget.treeAppendAfter(referenceNode, item);
                        } else if (referenceNode) {
                            this.dataList.insertBefore(item, referenceNode.nextElementSibling);
                        }
                    }

                    // Add as first child
                    else if (parentId) {
                        referenceNode = this.dataList.querySelector('[data-id="' + parentId + '"]');
                        if (referenceNode && this.treeWidget) {
                            this.treeWidget.treePrependChild(referenceNode, item);
                        } else if (referenceNode) {
                            this.dataList.insertBefore(item, referenceNode.nextElementSibling);
                        }
                    }

                    // Add to empty tree
                    else if (!parentId && !precedingId) {
                        if (this.treeWidget) {
                            this.treeWidget.treeAppendChild(this.dataList, item);
                        } else {
                            this.dataList.prepend(item);
                        }
                    }
                }
            }

            // Prepend new items
            else if (dir === 'prev') {
                if (reference) {
                    reference = this.dataList.insertBefore(item, reference);
                } else {
                    reference = this.dataList.prepend(item);
                }
            }

            // Append new items
            else {
                if (reference) {
                    reference = this.dataList.insertBefore(item, reference.nextElementSibling);
                } else {
                    reference = this.dataList.append(item);
                }
            }

        });

        if (cursor) {
            cursor.remove();
        }

        if (this.treeWidget) {
            this.treeWidget.treeUpdatePositions();
        }

        // Update row numbers
        if (this.tableWidget) {
            this.tableWidget.setRowNumbers();
        }

        // Restore scroll position
        this.restoreScrollPosition(scrollPos);

        this.loadCursors();
    }

    /**
     * Listen for data update events and reload related rows
     *
     * @param {CustomEvent} event
     */
    onUpdateRow(event) {
        if (!this.dataList || !this.listName) {
            return;
        }

        if (!event.detail.data || !event.detail.data.row) {
            return;
        }

        const rowValue = event.detail.data.row.split('-');
        const rowTab = rowValue[0] || '';
        const rowId = rowValue.slice(1).join('-');
        const isNew = event.type === 'epi:create:row';
        const isDeleted = event.type === 'epi:delete:row';
        const isMoved = event.type === 'epi:move:row';

        if (rowTab && rowId &&  (rowTab === this.tableName)) {
            if (isDeleted) {
                this.deleteRow(rowId, true, true);
            } else if (isMoved) {
                this.updateRow(rowId, true, true, true);
            } else {
                this.updateRow(rowId, true, isNew);
            }
        }
    }

    /**
     * Store the current scroll position to an object
     *
     * @return {{scrollHeight: *, scrollLeft: (*|string|number), scrollTop: (*|string|number), scrollWidth: *}}
     */
    getScrollPosition() {
        if (!this.widgetElement) {
            return;
        }

        return {
            clientHeight : this.widgetElement.clientHeight,
            clientWidth : this.widgetElement.clientWidth,
            scrollHeight: this.widgetElement.scrollHeight,
            scrollTop: this.widgetElement.scrollTop,
            scrollWidth: this.widgetElement.scrollWidth,
            scrollLeft: this.widgetElement.scrollLeft
        };
    }

    /**
     * Scroll to the given position
     *
     * @param {Object} pos An objects with the keys scrollHeight, scrollTop, scrollWidth and scrollLeft
     */
    restoreScrollPosition(pos) {
        if (!pos) {
            return;
        }

        const scrollDiffHeight = (this.widgetElement.scrollHeight - pos.scrollHeight);
        this.widgetElement.scrollTop = pos.scrollTop + scrollDiffHeight;

        const scrollDiffWidth = this.widgetElement.scrollWidth - pos.scrollWidth;
        this.widgetElement.scrollLeft = pos.scrollLeft + scrollDiffWidth;

    }
}

window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['scrollbox'] = ScrollPaginator;
