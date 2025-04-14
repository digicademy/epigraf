/**
 * Text highlight widget - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import Utils from '/js/utils.js';
import {BaseWidget} from '/js/base.js';

/**
 * Highlight text in a container
 *
 * Add the following markup to the container:
 * - The class widget-highlight
 * - A comma separated list of words to highlight in the data-highlight attribute
 */
export class HighlightText extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);
        this.terms = Utils.getDataValue(this.widgetElement, 'highlight');
        this.listenEvent(document, 'epi:save:form', event => this.onBeforeSave(event));
    }

    initWidget() {
        this.highlightDocument();
        this.jumpToHighlighted();
    }

    /**
     * Highlight document content
     */
    highlightDocument() {
        this.highlightTerms(this.widgetElement);

        // Highlight terms in the satellites as well
        // TODO: move to the satellites code in documents.js
        if (this.widgetElement.classList.contains('widget-document')) {
            document.querySelectorAll('.widget-document-satellite')
                .forEach(satellite =>  this.highlightTerms(satellite));
        }
    }

    unHighlightDocument() {
        this.unHighlightTerms(this.widgetElement);

        // Highlight terms in the satellites as well
        // TODO: move to the satellites code in documents.js
        if (this.widgetElement.classList.contains('widget-document')) {
            document.querySelectorAll('.widget-document-satellite')
                .forEach(satellite =>  this.unHighlightTerms(satellite));
        }
    }

    /**
     * Highlight terms in a container
     *
     * Wraps each matched term in a <mark> element.
     *
     * @param {Element} container The container element
     */
    highlightTerms(container) {
        if (!container || !this.terms) {
            return;
        }

        // Split the comma-separated list into an array,
        // trim and remove empty terms
        let terms = this.terms
            .split(',')
            .map(term => term.trim())
            .filter(term => term.length > 0);

        // Escape special characters in the terms to prevent issues in the regex
        //terms = terms.map(term => term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'));

        // Highlight
        if (terms.length > 0) {
            const markInstance = new Mark(container);
            markInstance.mark(terms);
        }
    }

    /**
     * Remove highlight markup in a container
     *
     * @param {Element} container The container element
     */
    unHighlightTerms(container) {
        if (!container || !this.terms) {
            return;
        }

        const markInstance = new Mark(container);
        markInstance.unmark();
    }

    jumpToHighlighted() {
        const highlight = this.widgetElement.querySelector('mark');
        if (highlight) {
            highlight.scrollIntoView();
        }
    }

    /**
     * Remove markup before saving.
     *
     * TODO: implement event priority to make sure this is always called before
     *       XMLEditor.onBeforeSave().
     *
     */
    onBeforeSave(event) {
        this.unHighlightDocument();
    }
}

/**
 * Register widget classes in the app
 */
window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['highlight'] = HighlightText;
