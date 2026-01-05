/*
 * Json, HTml and XML editors - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {BaseWidget} from '/js/base.js';
import {BaseDocument} from '/js/base.js';

/**
 * JSON Editor class for editing JSON-like input (configurations etc.)
 */
export class JsonEditor extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        this.editor = undefined;
        this.initEditor();

        this.listenEvent(document, 'epi:save:form', event => this.onBeforeSave(event));
    }

    /**
     * Initialize editor.
     */
    async initEditor() {
        await import(/*webpackIgnore: true*/'./ace/ace.js');
        window.ace.config.set('basePath', '/widgets/js/ace');

        this.editor = window.ace.edit(this.widgetElement, {
            mode: 'ace/mode/json',
            selectionStyle: 'text',
            showPrintMargin: false,
            indentedSoftWrap: true,
            wrap: "free",
            theme: 'ace/theme/chrome',
            fontSize: 16,
        });

        this.editor.setValue(this.getInputValue());
        setTimeout(() => this.foldAll(), 70);
    }

    /**
     * Collapse all lines in the editor except the root
     */
    foldAll() {
        const session = this.editor.getSession();
        const lineCount = session.getLength();
        if (lineCount > 2) {
            session.foldAll(1, lineCount - 1);
        }
    }

    /**
     * Return the hidden element in which JSON data is stored
     *
     * @returns {HTMLInputElement} The hidden input element
     */
    getInputElement() {
        return this.widgetElement.parentElement.querySelector('input[type=hidden]');
    }

    /**
     * Extract JSON from the HTML data element.
     *
     * The JSON string contains an empty object if the input element is not found
     *
     * @returns {string} JSON string
     */
    getInputValue() {
        const inputElement = this.getInputElement();
        let inputData = inputElement && inputElement.value ? inputElement.value : '{}';

        // Pretty print
        try {
            inputData = JSON.parse(inputElement.value);
            inputData = JSON.stringify(inputData, null, '  ');
        } catch {
            inputData = inputElement.value;
        }

        return inputData;
    }

    /**
     * Handle form submission by updating the hidden input element with the current JSON editor content.
     *
     */
    onBeforeSave(event) {
        const inputElement = this.getInputElement();
        inputElement.value = this.editor.getValue() ?? '';
    }

}

/**
 * Instantiate the customized HTML-Ckeditor
 */
export class HtmlEditor extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        this.editor = null;
        EpiEditor.DocEditor.create(element).then((editor) => {this.editor = editor;});
        this.listenEvent(document, 'epi:save:form', event => this.onBeforeSave(event));
    }

    /**
     * Handle form submission by updating the hidden input element
     *
     */
    onBeforeSave(event) {
        if (this.editor && this.widgetElement) {
            this.widgetElement.textContent = this.editor.getData();
            // this.widgetElement.ckeditorInstance.updateSourceElement();
        }
    }
}


/**
 * Interface for the customized XML editor.
 * Does not create the CKEditor instance, see models.js.
 */
export class XmlEditor extends BaseDocument {
    constructor(element, name, parent) {
        super(element, name, parent);

        // Whether the editor is about to be activated.
        // Prevents circular focusing
        this.isActivating = false;
        this.position = false;

        this.listenEvent(document, 'epi:save:form', event => this.onBeforeSave(event));
    }

    initWidget(event) {
        this.initEditor();
    }

    initEditor() {
        const self = this;
        const documentWidget = self.getDocumentWidget();

        if (documentWidget.editMode) {
            if (!this.widgetElement.getAttribute('contentenditable')) {
                this.widgetElement.setAttribute('contenteditable', 'true');
                this.widgetElement.addEventListener('mouseenter', function (event) {
                        self.createEditor();
                    }
                );
                this.widgetElement.addEventListener('focusin', function (event) {
                        self.activateEditor();
                    }
                );
            }

            // Listen to change events
            this.listenEvent(
                this.widgetElement,
                'tag:create',
                (event) => {
                    documentWidget.models.tags.onCreateTag(
                        this.widgetElement,
                        event.detail.data
                    );
                }
            );

            this.listenEvent(
                this.widgetElement,
                'tag:remove',
                (event) => {
                    documentWidget.models.tags.onRemoveTag(
                        this.widgetElement,
                        event.detail.data
                    );
                }
            );
        }
    }

    /**
     * Create the XML editor
     */
    createEditor() {
        const self = this;
        if (!self.widgetElement.editorPromise) {

            self.widgetElement.editorPromise = new Promise(function (resolve, reject) {
                const documentWidget = self.getDocumentWidget();
                if (!self.widgetElement) {
                    reject('Missing element');
                } else if (!documentWidget) {
                    reject('Missing document');
                } else if (self.widgetElement.ckeditorInstance) {
                    resolve(self.widgetElement.ckeditorInstance);
                } else {
                    documentWidget.models.types.getTagSet(self.widgetElement, false).then(
                        (fieldConfig) => {

                            // Remove focus, otherwise clicking into the element disturbs
                            //  Ckeditor's position initialization
                            if (document.activeElement === self.widgetElement) {
                                self.widgetElement.blur();
                            }

                            EpiEditor.XmlEditor.create(self.widgetElement, fieldConfig)
                                .then(editor => {

                                    // Activate inspector
                                    // TODO: load async
                                    if (App.debug && EpiEditor.CKEditorInspector) {
                                        EpiEditor.CKEditorInspector.attach(editor);
                                        const toolbar = document.querySelector('footer');
                                        if (toolbar) {
                                            toolbar.style.marginBottom = '2em';
                                        }
                                        const inspector = document.querySelector('.ck-inspector');
                                        if (inspector) {
                                            inspector.style.paddingRight = '3em';
                                            inspector.style.position = '';
                                        }
                                    }

                                    resolve(editor);
                                })
                                .catch(error => {
                                    reject('XMLEditor could not be created: ' + error);
                                });
                        }
                    )
                        .catch(error => {
                            reject('XMLEditor config could not be loaded: ' + error);
                        });
                }
            });
        }

        return self.widgetElement.editorPromise;
    }

    /**
     * Store the current position of the widget
     */
    posStore() {
        if (this.widgetElement) {
            this.position = this.widgetElement.getBoundingClientRect();
        }
    }

    /**
     * Scroll the widget to the stored position after the toolbox was activated
     */
    posRestore() {
        if (this.position && this.widgetElement) {
            const posAfter = this.widgetElement.getBoundingClientRect();
            const scrollBy = posAfter.top - this.position.top;
            if (scrollBy !== 0) {
                const pane = this.widgetElement.closest('.widget-content-pane');
                if (pane) {
                    const panePos = pane.getBoundingClientRect();

                    if ((panePos.top < this.position.top)) {
                        pane.scrollTop += scrollBy;
                    }
                }
            }
        }
    }

    /**
     * Activate the XML editor, called when the editor gets focus
     *
     * @param {boolean} focus Whether the editor should get focus
     * // TODO: use promise
     */
    activateEditor(focus=true) {

        if (this.isActivating) {
            return;
        }
        this.isActivating = true;

        const doc = this.getDocumentWidget();
        if (!doc) {
            return;
        }

        this.createEditor().then(editor => {
            this.posStore();
            doc.attachToolbar(editor.ui.view.toolbar.element);

            // TODO: moving focus to popup not working when the following line is active
            if (focus) {
                editor.editing.view.focus();
                this.posRestore();
            }

            // TODO: not working, caret jumps to beginning.
            //       Current workaround: create editors on mouseenter.
            // // Set caret position
            // const caretPosition = editor.config.get('initialCaretPosition');
            // if (caretPosition) {
            //     editor.model.change((writer) => {
            //         const range = writer.createPositionAt(editor.model.document.getRoot(), caretPosition);
            //         writer.setSelection(range);
            //     });
            // }
            this.isActivating = false;

        })
        .catch(error => {
            console.log('XMLEditor could not be focused: ' + error);
            this.isActivating = false;
        });
    }

    /**
     * Remove a tag
     *
     * @param {string|HTMLElement} tag The tag element or its id
     */
    removeTag(tag) {
        if (!tag) {
            return;
        }

        if (tag instanceof Element) {
            tag = tag.dataset.tagid;
        }

        this.createEditor(this.widgetElement).then(editor => {
            editor.plugins.get('XmlTagEditing').removeTag(editor, tag);
        });
    }

    /**
     * Update or remove a tag
     *
     * @param {string|HTMLElement} tag The tag element or its ID
     * @param {Object|string|null} value The value, either null or an object with the keys
     *              - label The text that will be displayed in the annotation and as textContent of the tag
     *              - tab Set the to_tab property of the link (table name, e.g. properties)
     *              - id Set the to_id property of the link (ID)
     *              - further attributes will be added as data-attr-* attributes
     *              If the value is a string, it will be parsed as JSON and converted to an object.
     *              If the value is null, the tag will be removed.
     * @param {boolean} focus Whether the element should get focus
     */
    updateTag(tag, value, focus) {
        if (!tag) {
            return;
        }

        if (tag instanceof Element) {
            tag = tag.dataset.tagid;
        }

        this.createEditor().then(editor => {
                if (value === null) {
                    editor.plugins.get('XmlTagEditing').removeTag(editor, tag);
                } else {
                    delete value.tab;
                    delete value.id;
                    editor.plugins.get('XmlTagEditing').updateTag(editor, tag, value);
                }

                if (focus) {
                    this.widgetElement.focus();
                }
            }
        );
    }

    /**
     * Replace the editor content
     *
     * @param {string} value The new content
     * @param {boolean} isXml Whether the input contains XML that needs to be transformed to HTML
     */
    setContent(value, isXml= true) {
        this.createEditor().then(editor => editor.setData(value));
    }

    /**
     * Handle form submission by updating the hidden input element
     *
     */
    onBeforeSave(event) {
        const input = this.widgetElement.parentElement.querySelector('input[type=hidden]');
        let value;
        if (input && !input.disabled) {
            //console.log(input.name);
            if (this.widgetElement.ckeditorInstance) {
                value = this.widgetElement.ckeditorInstance.getData();
            } else {
                value = this.widgetElement.innerHTML;
            }

            // Convert to XML
            try {
                value = new XMLSerializer().serializeToString(
                    document
                        .createRange()
                        .createContextualFragment(value)
                )
                    .replaceAll(/ xmlns="[^"]+"/g, '')
                    .replaceAll(/\u00a0/g, " ");

            } catch (error) {
                console.log(error);
            }

            input.value = value;
        }
    }
}


/**
 * Dates Editor class for validating historic dates
 */
export class DatesEditor extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);
        this.initEditor();
    }

    /**
     * Initialize editor.
     */
    async initEditor() {
        this.listenEvent(this.widgetElement,'input', (ev) => this.onInput(ev));
        this.listenEvent(this.widgetElement,'keydown', (ev) => this.onKeyDown(ev));
    }

    async onInput(event) {
        this.widgetElement.classList.add('dirty');
    }

    async onKeyDown(event) {
        if (event.keyCode == 13) {
            let value = this.widgetElement.value;

            const mod = await import(/* webpackIgnore: true */ './historicdates.js');
            window.HistoricDates = mod.default;
            const norm = HistoricDates.normalize(value);

            this.widgetElement.value = norm;
            this.widgetElement.classList.remove('dirty');
            event.preventDefault();
        }
    }

}

window.App.widgetClasses['jsoneditor'] = JsonEditor;
window.App.widgetClasses['htmleditor'] = HtmlEditor;
window.App.widgetClasses['xmleditor'] = XmlEditor;
window.App.widgetClasses['dateseditor'] = DatesEditor;
