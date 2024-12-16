/*
 * Tree widget - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 *
 */

import {BaseWidget} from '/js/base.js';
import Utils from '/js/utils.js';

/**
 Transform lists and tables into trees.
 *
 * This widget works best in combination with ScrollPaginator and TableWidget
 *
 * Add the following markup (same as in ScrollPaginator):
 * - Name the list container: data-list-name="mylist"
 * - Name the list items: data-list-itemof="mylist"
 * - Add the IDs to the list items: data-id="2" data-tree-parent="1"
 *
 * Then add the class widget-tree to the list container (e.g. the ul element)
 * or the parent of the list container (e.g. the table). See widgets.js for the initialization code.
 *
 * Note that the parent in the database may be different from the parent in the tree.
 * For example, references between nodes can be rendered in two places at the same time,
 * below the source and the target node.
 *
 */
export class TreeWidget extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);
        this.widgetElement = element;

        this.datalist = null;
        this.listname = null;
        this.isLoading = false;

        this.widgetElement.addEventListener('click', event => this.treeItemClicked(event));

        this.updateWidget();
    }

    /**
     * Update tree positions.
     */
    updateWidget() {
        this.datalist = Utils.querySelectorAndSelf(this.widgetElement, '[data-list-name]');
        this.listname = this.datalist ? this.datalist.dataset.listName : null;
        if (!this.listname) {
            return;
        }

        this.treeUpdatePositions();
    }

    /**
     * Fired when tree item is clicked.
     *
     * @param event Click event
     * @returns {boolean}
     */
    treeItemClicked(event) {
        const item = event.target.closest('.node.item-haschildren, .node.tree-indent-leaf');
        if (!item) {
            return false;
        }

        if (event.target.classList.contains('tree-indent-leaf')) {
            this.treeToggleItem(item);
            //event.preventDefault();
        }

        return false;
    }

    /**
     * Get all siblings of an element.
     *
     * @param item Dom element
     * @returns {NodeListOf<HTMLElement>} Tree siblings of item
     */
    treeGetSiblings(item) {
        return document.querySelectorAll(
            `tr[data-tree-parent='${item.dataset.treeParent}']:not([data-id='${item.dataset.id}'])`
        );
    }



    /**
     * Get the next element on the same level.
     *
     * @param item Dom element
     * @returns {Element|undefined} Next sibling of item
     */
    treeGetNextSibling(item) {
        const level = item ? Number(item.dataset.level || 0) : 0;

        while (item) {
            item = item.nextElementSibling;
            if (item && item.classList.contains('node') && (Number(item.dataset.level || 0) === level) && !item.classList.contains('item-removed')) {
                return item;
            } else if (item && item.classList.contains('node') && (Number(item.dataset.level || 0) < level) && !item.classList.contains('item-removed')) {
                return undefined;
            }
        }
        return undefined;
    }

    /**
     * Get the previous element on the same level.
     *
     * @param {Element} item The table row or list item
     * @param {boolean} virtualNodes Whether to include virtual nodes
     * @returns {HTMLElement|undefined} Previous sibling of item
     */
    treeGetPrevSibling(item, virtualNodes = true) {
        const level = Number(item.dataset.level);

        while (item) {
            item = item.previousElementSibling;

            const isMatch =
                item &&
                (Number(item.dataset.level) === level) &&
                ! item.classList.contains('item-removed') &&
                (virtualNodes || ! item.classList.contains('item-virtual'));

            if (isMatch) {
                return item;
            } else if (item && (Number(item.dataset.level) < level) && !item.classList.contains('item-removed')) {
                return undefined;
            }
        }
        return undefined;
    }

    /**
     * Get list of descendants of an element.
     *
     * @param item Dom element
     * @returns {Array|NodeListOf<HTMLElement>} Descendants of item
     */
    treeGetDescendants(item) {
        const level = item ? Number(item.dataset.level) : 0;
        let descendants = [];
        let next = item ? item.nextElementSibling : undefined;

        while (next && next.dataset.level > level && !next.classList.contains('item-removed')) {
            descendants.push(next);
            next = next.nextElementSibling;
        }

        return descendants;
    }

    /**
     * Get list of children of an element.
     *
     * @param item Dom element
     * @return {array} Children of item
     */
    treeGetChildren(item) {
        const level = (item ? Number(item.dataset.level) : 0) + 1;

        return this.treeGetDescendants(item).filter(desc => {
            return Number(desc.dataset.level || 0) === level && !desc.classList.contains('item-removed');
        });
    }

    /**
     * Get first child of an element.
     *
     * @param parentItem Parent element
     * @returns {HTMLElement|undefined} First child of parentItem
     */
    treeGetFirstChild(parentItem) {
        const children = this.treeGetChildren(parentItem);
        return children.length > 0 ? children[0] : undefined;
    }

    /**
     * Get last child of an element.
     *
     * @param parentItem Parent element
     * @returns {HTMLElement|undefined} Last child of parentItem
     */
    treeGetLastChild(parentItem) {
        const children = this.treeGetChildren(parentItem);
        return children.length > 0 ? children[children.length - 1] : undefined;
    }

    /**
     * Get the last descendants of an element.
     *
     * @param item Dom element
     * @returns {HTMLElement} The last descented, which is the last child of the last children
     */
    treeGetLastDescendant(item) {
        const level = item ? Number(item.dataset.level) : 0;
        let descendant;
        let next = item ? item.nextElementSibling : undefined;

        while (next && next.dataset.level > level && !next.classList.contains('item-removed')) {
            descendant = next;
            next = next.nextElementSibling;
        }

        return descendant;
    }

    /**
     * The number of the item within its parent
     *
     * @param item
     * @return {number|*}
     */
    treeGetPosition(item) {
        return item.treePosition;
    }

    /**
     *  Get children and all their non-collapsed children of an element.
     *
     * @param item Dom element
     * @return {NodeListOf<HTMLElement>} Descendants of item
     */
    treeGetOpenDescendants(item) {
        const children = this.treeGetChildren(item);
        let openDescendants = children;

        children.forEach(child => {
            if (!child.classList.contains('item-collapsed')) {
                const newChildren = this.treeGetOpenDescendants(child);
                openDescendants = openDescendants.concat(newChildren);
            }
        });

        return openDescendants;
    }

    /**
     * Iterator that yields the parents.
     *
     * @param item Dom element
     * @returns {Generator<*, void, *>} Yielded ancestor
     */
    * treeGetAncestors(item) {
        let parentItem = item.treeParent;
        while (parentItem && parentItem.classList.contains('node')) {
            yield parentItem;
            parentItem = parentItem.treeParent;
        }
    }

    /**
     * Get the parent node.
     *
     * @param item Dom element
     * @returns {HTMLElement|undefined} Parent element of item
     */
    treeGetParent(item) {
        const parentItem = item ? item.treeParent : undefined;
        if (parentItem && parentItem.classList.contains('node')) {
            return parentItem;
        }
        return undefined;
    }

    /**
     * Get the label of a node including all path segments.
     * Each node needs a label element (e.g. in select trees)
     * @param node
     * @returns {string}
     */
    treeGetPath(node) {
        let path = [node];
        for (let ancestor of this.treeGetAncestors(node)) {
            path.unshift(ancestor);
        }

        path = path.map(x => {
            let label = x.querySelector('label');
            label = label ? (label.dataset.label ?? label.textContent) : (x.dataset.label ?? x.textContent);
            return label.trim();
        });

        return path.join(' › ');
    }

    /**
     * Append new child to the end of parent's children.
     *
     * @param parentItem Parent item where to append new child
     * @param newItem New item to append
     * @returns {HTMLElement} New item
     */
    treeAppendChild(parentItem, newItem) {
        let nextItem;
        if (parentItem && (parentItem !== this.datalist)) {
            const prevItem = this.treeGetLastDescendant(parentItem);
            nextItem = prevItem ? prevItem.nextElementSibling : parentItem.nextElementSibling;
        }

        if (nextItem) {
            nextItem.before(newItem);
            newItem = nextItem.previousElementSibling;
        }
        else {
            this.datalist.appendChild(newItem);
            newItem = this.datalist.lastElementChild;
        }

        newItem.treeParent = parentItem || this.datalist;
        newItem.treeLevel = parentItem ? parentItem.treeLevel + 1 : 0;

        newItem.dataset.treeParent = newItem.treeParent.dataset.id || '';
        newItem.dataset.parent = newItem.treeParent.dataset.id || '';
        newItem.dataset.level = newItem.treeLevel;

        this.treeRecreateIndentation([newItem]);
        this.treeUpdatePositions();
        return newItem;
    }

    /**
     * Prepend new child as first child of the parent
     *
     * @param parentItem Parent item where to prepend new child
     * @param newItem New item to prepend
     * @returns {HTMLElement} New item
     */
    treePrependChild(parentItem, newItem) {
        let nextItem;
        if (parentItem && (parentItem !== this.datalist)) {
            nextItem = this.treeGetFirstChild(parentItem);
        }

        if (nextItem) {
            nextItem.before(newItem);
            newItem = nextItem.previousElementSibling;

            newItem.treeParent = parentItem;
            newItem.treeLevel = parentItem.treeLevel + 1;

        }
        else {
            this.treeAppendChild(parentItem, newItem);
        }

        newItem.dataset.treeParent = newItem.treeParent.dataset.id || '';
        newItem.dataset.parent = newItem.treeParent.dataset.id || '';
        newItem.dataset.level = newItem.treeLevel;

        this.treeRecreateIndentation([newItem]);
        this.treeUpdatePositions();
        return newItem;
    }

    /**
     * Insert a node after a reference node
     *
     * @param {HTMLElement} prevItem The reference node
     * @param {HTMLElement} newItem The new node
     * @returns {HTMLElement} The inserted node
     */
    treeAppendAfter(prevItem, newItem) {
        const nextItem = this.treeGetNextSibling(prevItem);

        if (nextItem) {
            nextItem.before(newItem);
            newItem = nextItem.previousElementSibling;

            newItem.treeParent = nextItem.treeParent;
            newItem.treeLevel = nextItem.treeLevel;

            newItem.dataset.treeParent = newItem.treeParent.dataset.id || '';
            newItem.dataset.parent = newItem.treeParent.dataset.id || '';
            newItem.dataset.level = newItem.treeLevel;

            this.treeRecreateIndentation([newItem]);
            this.treeUpdatePositions();
            return newItem;
        } else {
            const parentItem = this.treeGetParent(prevItem);
            return this.treeAppendChild(parentItem, newItem);
        }


    }

    /**
     * Remove an item and all its descendants
     *
     * @param {HTMLElement} item
     */
    treeRemoveItem(item) {
        const descendants = this.treeGetDescendants(item);
        descendants.forEach(child => {
            child.remove();
        });
        item.remove();
        this.treeUpdatePositions();
    }

    /**
     * Called on treeclick event, toggles visibility of item and its descendants
     *
     * @param item Item to toggle
     * @param hide Determines toggle direction
     * @param emitEvent Emit event. Set to false on initialization to avoid multiple events
     */
    treeToggleItem(item, hide, emitEvent = true) {
        // Hide or show
        if (hide === undefined) {
            hide = !item.classList.contains('item-collapsed');
        }

        // Get children or descendants
        let children;
        if (hide) {
            children = this.treeGetDescendants(item);
        } else {
            children = this.treeGetOpenDescendants(item);
        }

        // Set classes
        item.classList.toggle('item-collapsed', hide);

        children.forEach(child => {
            child.classList.toggle('item-hidden', hide);
        });


        // Will be handled by ScrollPaginator::onNodeToggled()
        if (emitEvent) {
            this.emitEvent('epi:toggle:node');
        }
    }

    /**
     * Recalculate the tree classes based on data-id and data-tree-parent:
     * - item-last, item-first, item-haschildren, item-level-*
     * - tree indentation classes
     * - active-trail
     *
     * Recalculate the dataset properties:
     * - dataset.level
     * - dataset.first
     * - dataset.last
     */
    treeUpdatePositions() {
        // Create temporary tree root
        const rootItem = this.datalist;
        if (!rootItem) {
            return;
        }

        rootItem.treeChildren = 0;
        rootItem.treePosition = 0;
        rootItem.treeLevel = -1;

        // Stack to keep track of parents
        let stack = [rootItem];

        // Iterate all items and set tree properties
        this.widgetElement.querySelectorAll('[data-list-itemof], .node').forEach(item => {
            // Skip nodes that contain actions (e.g. manage links)
            if (item.dataset.role || false) {
                item.treeParent = rootItem;
                item.treeChildren = 0;
                item.treePosition = 0;
                item.treeLevel = 0;
                item.treeIndents = [];

                return;
            }

            // Skip deleted nodes
            if (item.classList.contains('item-removed')) {
                return;
            }

            // Truncate stack to include only ancestors
            while ((stack.length) && (stack[stack.length - 1].dataset.id !== (item.dataset.treeParent || ''))) {
                stack.pop();
            }

            if (!stack.length) {
                stack.push(rootItem);
            }

            // Update parentItem
            const parentItem = stack[stack.length - 1];
            parentItem.treeChildren += 1;

            // Update item
            item.treeParent = parentItem;
            item.treeChildren = 0;
            item.treePosition = parentItem.treeChildren;
            item.treeLevel = stack.length - 1;

            // Update indent parents
            item.treeIndents = Array.from(item.querySelectorAll('.tree-indent')).reverse();

            let indentParent = item;
            item.treeIndents.forEach(indent => {
                indent.treeParent = indentParent;
                indentParent = indentParent !== undefined ? indentParent.treeParent : undefined;
            });

            stack.push(item);
        });

        // Update classes
        this.widgetElement.querySelectorAll('[data-list-itemof], .node').forEach(item => {
            // Skip manage nodes
            if (item.dataset.role || false) {
                return;
            }

            // Skip deleted nodes
            if (item.classList.contains('item-removed')) {
                return;
            }

            item.treeFirst = item.treePosition === 0;
            item.treeLast = item.treePosition === (item.treeParent.treeChildren || false);

            // Update data attributes
            item.dataset.level = item.treeLevel;
            item.dataset.first = item.treeFirst;
            item.dataset.last = item.treeLast;

            // Update tree classes
            Utils.removeClassByPrefix(item,'item-level-');
            item.classList.add('item-level-' + item.treeLevel);
            item.classList.toggle('item-haschildren', item.treeChildren > 0);
            item.classList.toggle('item-nochildren', item.treeChildren === 0);
            if (item.treeChildren === 0) {
                item.classList.remove('item-collapsed');
            }
            item.classList.toggle('item-first', item.treeFirst);
            item.classList.toggle('item-last', item.treeLast);

            // Update indents
            item.treeIndents.forEach((indent, idx) => {
                if (item.classList.contains('node-cursor')) {
                    indent.classList.add('tree-indent-cursor');
                    // indent.classList.remove('tree-indent-leaf');
                    // indent.classList.remove('tree-indent-empty');
                    // indent.classList.remove('tree-indent-line');
                } else if (idx === 0) {
                    indent.classList.add('tree-indent-leaf');
                    // indent.classList.remove('tree-indent-empty');
                    // indent.classList.remove('tree-indent-line');
                } else {
                    // indent.classList.remove('tree-indent-leaf');
                    const empty = !indent.treeParent || indent.treeParent.treeLast;
                    indent.classList.toggle('tree-indent-line', !empty);
                    indent.classList.toggle('tree-indent-empty', empty);

                }
            });
        });

        // Open active path
        const activeItem = this.widgetElement.querySelector('.node.active');
        this.widgetElement.querySelectorAll('.active-trail').forEach(item => {
                item.classList.remove('active-trail');
            }
        );

        if (activeItem) {
            for (let parentItem of this.treeGetAncestors(activeItem)) {
                parentItem.classList.add('active-trail');
                this.treeToggleItem(parentItem, false);
            }
            this.treeToggleItem(activeItem, false);
        }

        // Toggle visibility
        this.widgetElement.querySelectorAll('.node.item-collapsed').forEach(elm => {
            this.treeToggleItem(elm, true, false);
        });
    }

    /**
     * Recreate node indentation elements based on the data-level property
     *
     * @param {array} nodes Rows that need updates
     */
    treeRecreateIndentation(nodes) {
        nodes.forEach(node => {
            const indents = node.querySelectorAll('.tree-indent');
            if (indents.length !== 0) {
                const indentContainer = indents[0].parentElement;
                indents.forEach((indent) => indent.remove());

                const treeLeaf = document.createElement('div');
                treeLeaf.classList.add('tree-indent', 'tree-indent-leaf');
                indentContainer.prepend(treeLeaf);

                const nodeLevel = Number(node.dataset.level);
                for (let level= 0; level < nodeLevel; level++) {
                    const treeBranch = document.createElement('div');
                    treeBranch.classList.add('tree-indent', 'tree-indent-line');
                    indentContainer.prepend(treeBranch);
                }
            }
        });
    }

    /**
     * Get the next element
     *
     * @param item Dom element
     * @param {boolean} virtualNodes Whether to include virtual nodes
     * @returns {Element|undefined} Next visible item
     */
    getNextVisibleNode(item, virtualNodes = true) {
        while (item) {
            item = item.nextElementSibling;

            const isMatch =
                item &&
                item.classList.contains('node') &&
                ! item.classList.contains('item-removed') &&
                ! item.classList.contains('item-hidden') &&
                (virtualNodes || ! item.classList.contains('item-virtual'));

            if (isMatch) {
                return item;
            }
        }
        return undefined;
    }

    /**
     * Get the previous element
     *
     * @param {Element} item The table row or list item
     * @param {boolean} virtualNodes Whether to include virtual nodes
     * @returns {HTMLElement|undefined} Previous item
     */
    getPreviousVisibleNode(item, virtualNodes = true) {

        while (item) {
            item = item.previousElementSibling;

            const isMatch =
                item &&
                item.classList.contains('node') &&
                ! item.classList.contains('item-removed') &&
                ! item.classList.contains('item-hidden') &&
                (virtualNodes || ! item.classList.contains('item-virtual'));

            if (isMatch) {
                return item;
            }
        }
        return undefined;
    }
}

window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['tree'] = TreeWidget;
