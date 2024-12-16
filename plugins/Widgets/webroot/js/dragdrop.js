/*
 * Drag & drop widgets - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {BaseWidget} from '/js/base.js';
import Utils from "/js/utils.js";

/**
 * Add drag-and-drop behaviour to a container and its items. Uses the HTML Drag and Drop API.
 * Used in widget_grid.php.
 *
 * TODO: rename to DragDropWidget
 */
export class DragAndDrop extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        this.dropZone = this.widgetElement.querySelector('.doc-section-grid-table');
        this.draggedItem = null;
        this.dragOrigin = null;
        this.lastDragoverTarget = null;
        this.mouseCoords = {x: null, y: null}

        this.listenEvent(this.dropZone, 'dragstart', event => this.onDragStart(event))
        this.listenEvent(document, 'dragover', event => this.onDragOver(event));
        this.listenEvent(document, 'drop', event => this.onDrop(event));
    }

    /**
     * Called on dragstart event. Set variable at item that is dragged.
     *
     * @param {DragEvent} event Dragstart
     */
    onDragStart(event) {
        this.draggedItem = event.target.closest('[draggable="true"]');
        this.draggedItem.position = Array
            .from(this.draggedItem.parentElement.querySelectorAll('.doc-section-item:not(.doc-section-item-empty)'))
            .findIndex(item => item === this.draggedItem);
        this.dragOrigin = event.target.parentElement;
    }

    /**
     * Called on dragover event. Create/move/delete placeholder items in hovered cells.
     *
     * @param {DragEvent} event Dragover
     */
    onDragOver(event) {
        event.preventDefault();

        if (!event.target.closest('.doc-section-grid-table td')) {
            // TODO: Styling does not work as intended, why? CSS solution also didn't help...
            event.dataTransfer.effectAllowed = 'none';
            this.dropZone.querySelector('.doc-section-item-empty')?.remove();
            return;
        }

        event.dataTransfer.effectAllowed = 'move';

        const currentDragoverTarget = event.target.closest('td');

        // Remove item from DOM
        this.draggedItem?.remove();

        // Return if dragging over placeholder (nothing should happen)
        if (event.target.closest('.doc-section-item-empty')) {
            return;
        }

        // Remove placeholder when new cell is hovered AND other cell was hovered before
        if (this.lastDragoverTarget && currentDragoverTarget !== this.lastDragoverTarget) {
            this.dropZone.querySelector('.doc-section-item-empty')?.remove();
        }

        const newGroup = currentDragoverTarget.querySelector('.doc-section-item-group');

        // Cell has no item in it
        if (newGroup.querySelectorAll('.doc-section-item:not(.doc-section-item-empty)').length === 0) {
            // Placeholder exists
            if (newGroup.querySelector('.doc-section-item-empty')) {
                return;
            } else {
                // NO PLACEHOLDER
                const placeholder = Utils.spawnFromString('<div class="doc-section-item doc-section-item-empty"></div>');
                newGroup.prepend(placeholder);
            }
            // Cell has items in it AND cursor position changed (to prevent index finding on every event)
        } else {
            if (event.target.closest('.doc-section-item:not(.doc-section-item-empty)')
                && this.mouseCoords.x !== event.clientX && this.mouseCoords.y !== event.clientY
            ) {
                this.mouseCoords.x = event.clientX;
                this.mouseCoords.y = event.clientY;
                const index = this.findPositionToInsert(event, currentDragoverTarget);
                this.dropZone.querySelector('.doc-section-item-empty')?.remove();

                const placeholder = Utils.spawnFromString('<div class="doc-section-item doc-section-item-empty"></div>');
                newGroup.insertBefore(placeholder, newGroup.children[index]);
            }
        }

        this.lastDragoverTarget = currentDragoverTarget;
    }

    /**
     * Called on drop event. Evaluate if drop happened in dropzone area (applies drop) or outside (cancels drop).
     *
     * @param {DragEvent} event Drop
     */
    onDrop(event) {
        event.preventDefault();

        if (event.target.closest('.doc-section-grid-table') && this.dropZone.querySelector('.doc-section-item-empty')) {
            this.dropItem(event);
        } else {
            this.cancelDrop(event);
        }

        this.draggedItem = null;
        this.lastDragoverTarget = null;
    }

    /**
     * Drop item in cell. Called when drop event occurs in valid dropzone.
     *
     * @param {DragEvent} event Drop
     */
    dropItem(event) {
        const placeholder = this.dropZone.querySelector('.doc-section-item-empty');
        placeholder.replaceWith(this.draggedItem);

        // Emit event to update grid widget
        Utils.emitEvent(
            this.draggedItem,
            'epi:drop:item',
            {
                dragOrigin: this.dragOrigin,
                dragTarget: this.draggedItem.parentElement
            }
        );

        // Stop propagation to prevent document-wide event listener from registering the event
        event.stopPropagation();
    }

    /**
     * Cancel drop. Called when drop event occurs outside valid dropzone.
     *
     * @param {DragEvent} event Drop
     */
    cancelDrop(event) {
        this.dragOrigin.insertBefore(this.draggedItem, this.dragOrigin.children[this.draggedItem.position]);
        event.stopPropagation();
    }

    /**
     * Find position in item list of hovered cell where dragged item should be inserted.
     *
     * @param {DragEvent} event Dragover
     * @param {HTMLTableCellElement} dragOverTarget Currently hovered cell
     * @returns {number} Position in item list to insert dragged item
     */
    findPositionToInsert(event, dragOverTarget) {
        const groupItems = [...dragOverTarget.querySelectorAll('.doc-section-item')];
        const hoveredItem = event.target.closest('.doc-section-item');
        const itemRect = hoveredItem.getBoundingClientRect();
        const itemCenter = itemRect.left + (itemRect.width / 2);
        const index = groupItems.findIndex(item => item === hoveredItem);

        if (event.clientX < itemCenter) {
            return index;
        } else {
            return index + 1;
        }
    }
}

window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['dragdrop'] = DragAndDrop;
