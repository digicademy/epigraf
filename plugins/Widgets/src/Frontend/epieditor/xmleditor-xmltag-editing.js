/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

/**
 * @module xmlbuttons/formattagediting
 */

import {Plugin} from 'ckeditor5/src/core';
import XmleditorXmltagCommand from './xmleditor-xmltag-command';
import {viewToModelPositionOutsideModelElement, toWidget} from '@ckeditor/ckeditor5-widget/src/utils';
import Utils from '/js/utils.js';
import UpcastWriter from '@ckeditor/ckeditor5-engine/src/view/upcastwriter';

const XML_TAG = 'xml_text';
const XML_FORMAT = 'xml_format';
const XML_BRACKET = 'xml_bracket';
const XML_BRACKET_OPEN = 'xml_bracket_open';
const XML_BRACKET_CLOSE = 'xml_bracket_close';
const XML_BRACKET_CONTENT = 'xml_bracket_content';
const XML_COMMAND = 'xml_command';
export {XML_TAG, XML_FORMAT, XML_BRACKET, XML_BRACKET_CONTENT, XML_BRACKET_OPEN, XML_BRACKET_CLOSE, XML_COMMAND};

/**
 * Annotate with XML tags
 * - Up- & Downcasting
 * - Add XML command
 * - Schema definition
 *
 *
 * @extends module:core/plugin~Plugin
 */
export class XmleditorXmltagEditing extends Plugin {


    /**
     * @inheritDoc
     */
    static get pluginName() {
        return 'XmlTagEditing';
    }

    //
    // constructor( editor ) {
    // 	super(editor);
    // }

    init() {
        // Create XML command
        this.editor.commands.add(XML_COMMAND, new XmleditorXmltagCommand(this.editor));

        // Allow XML_FORMAT on text nodes and XML_TAG in blocks.
        this.editor.model.schema.extend('$root', {
            allowChildren: [
                XML_TAG, XML_FORMAT, XML_BRACKET, '$text'
            ]
        });

        // Allow all data attributes, e.g.
        // data-type, data-tagid, data-value, data-new
        this.editor.model.schema.addAttributeCheck(
            (context, attributeName) => {
                if (attributeName.startsWith('data-')) {
                    return true;
                }
            }
        );

        this.initXmlFormat();
        this.initXmlBracket();
        this.initXmlTag();

        // Map positions of empty model tags (view contains text, model attribute)
        this.editor.editing.mapper.on(
            'viewToModelPosition',
            viewToModelPositionOutsideModelElement(this.editor.model, viewElement => {
                    return (viewElement.hasClass('xml_text'));
                }
            )
        );

        //Map positions of brackets
        this.editor.editing.mapper.on(
            'viewToModelPosition',
            this.viewToModelPositionBracket(this.editor.model)
        );

        // Listen to model changes (deletion)
        this.editor.model.document.on('change:data', event => this.onDocumentChanged(this.editor, event));

        // Disable spell checking
        this.editor.editing.view.change(writer => {
            writer.setAttribute('spellcheck', 'false', this.editor.editing.view.document.getRoot());
        });

        // Hook enter
        this.editor.editing.view.document.on('enter', (evt, data) => {
            if (data.isSoft) {
                this.editor.execute('enter');
            } else {
                this.editor.execute('shiftEnter');
            }

            data.preventDefault();
            evt.stop();
            this.editor.editing.view.scrollToTheSelection();
        }, {priority: 'high'});


        /**
         * Iterate all children and remove the tagid attribute
         *
         * @param elm CKEditor view element or view document fragment
         * @param {UpcastWriter} writer A view writer
         */
        const removeTagidFromView = function (elm, writer) {
            for (const node of elm.getChildren()) {
                if (node.is('element')) {
                    writer.removeAttribute('data-tagid', node);
                    removeTagidFromView(node, writer);
                }
            }
        }

        /**
         * Paste handling: remove the tagid from the view document fragment prepared by CKEditor
         */
        this.editor.plugins.get('ClipboardPipeline').on('inputTransformation', (evt, data) => {
            const writer = new UpcastWriter();
            removeTagidFromView(data.content, writer);
        });


        return this;
    }

    viewToModelPositionBracket(model) {
        return (evt, data) => {
            const {mapper, viewPosition} = data;

            const viewParent = mapper.findMappedViewAncestor(viewPosition);
            const modelParent = mapper.toModelElement(viewParent);

            // In the wrappers
            if (viewParent.hasClass('xml_bracket')) {
                data.modelPosition = model.createPositionAt(modelParent, viewPosition.isAtStart ? 'before' : 'after');
            }

            // Between bracket and wrappers
            if (viewParent.hasClass('xml_bracket_open') && viewPosition.isAtStart) {
                const bracket = modelParent.parent;
                data.modelPosition = model.createPositionAt(bracket, 'before');
            } else if (viewParent.hasClass('xml_bracket_open') && viewPosition.isAtEnd) {
                const content = modelParent.nextSibling;
                data.modelPosition = model.createPositionAt(content, 0);
            } else if (viewParent.hasClass('xml_bracket_close') && viewPosition.isAtEnd) {
                const bracket = modelParent.parent;
                data.modelPosition = model.createPositionAt(bracket, 'after');
            } else if (viewParent.hasClass('xml_bracket_close') && viewPosition.isAtStart) {
                const content = modelParent.previousSibling;
                data.modelPosition = model.createPositionAt(content, 'end');
            }

            // Cursor is right after the bracket
            else if (viewParent.hasClass('xml_bracket_open')) {
                const content = modelParent.nextSibling;
                data.modelPosition = model.createPositionAt(content, 0);
            } else if (viewParent.hasClass('xml_bracket_close')) {
                const bracket = modelParent.parent;
                data.modelPosition = model.createPositionAt(bracket, 'after');
            } else {
                return false;
            }


        };
    }

    /**
     * Watch element removals
     *
     * @param event
     */
    onDocumentChanged(editor, event) {

        // Get changes
        const differ = event.source.differ;
        if (differ.isEmpty) {
            return;
        }

        const changes = differ.getChanges({
            includeChangesInGraveyard: true
        });
        if (changes.length === 0) {
            return;
        }

        const partialTypes = [XML_BRACKET_OPEN, XML_BRACKET_CLOSE, XML_BRACKET_CONTENT];
        const elementTypes = [XML_TAG, XML_FORMAT, XML_BRACKET];

        // Check partial removements of brackets
        const partialRemoves = changes.filter(change => (change.type === 'remove' && partialTypes.includes(change.name)));
        partialRemoves.forEach(change => {
            const bracket = change.position.parent;
            if (elementTypes.includes(bracket.name)) {
                this.onElementRemoved(editor, bracket);
                this.removeTag(editor, bracket);
            }
        });

        // Check if any of the elements was inserted or removed
        const removeChanges = changes.filter(change => (change.type === 'remove' && elementTypes.includes(change.name)));
        const insertChanges = changes.filter(change => (change.type === 'insert' && elementTypes.includes(change.name)));

        insertChanges.forEach(change => {
            const node = change.position.nodeAfter;
            if (removeChanges.length > 0) {
                this.onElementRemoved(editor, node);
            } else {
                this.onElementInserted(editor, node);
            }
        });

    }

    /**
     * Called when an element was removed
     *
     * @editor {XmlEditor} editor
     * @param {ModelElement} element
     */
    onElementRemoved(editor, element) {
        editor.emitEvent('tag:remove', Object.fromEntries(element.getAttributes()));
    }

    /**
     * Called when an element was inserted
     *
     * @param {XmlEditor} editor
     * @param {ModelElement} element
     */
    onElementInserted(editor, element) {
        let elementAttributes = Object.fromEntries(element.getAttributes());

        // Remove new flag
        if (elementAttributes['data-new']) {
            editor.model.change(writer => {
                writer.setAttribute('data-new', false, element);
            });
        }

        // Only emit for content that is attached to the editor
        // (ignore operations for clipboard data)
        if (element.isAttached()) {
            editor.emitEvent('tag:create', elementAttributes);
        }
    }

    initXmlBracket() {
        this.bracketSchema();

        this.bracketUpcast();
        this.bracketDowncast();

        this.bracketOpenCloseUpcast(XML_BRACKET_CLOSE);
        this.bracketOpenCloseDowncast(XML_BRACKET_CLOSE);

        this.bracketOpenCloseUpcast(XML_BRACKET_OPEN);
        this.bracketOpenCloseDowncast(XML_BRACKET_OPEN);

        this.bracketContentUpcast();
        this.bracketContentDowncast();

    }

    bracketSchema() {

        // XML_BRACKET schema
        this.editor.model.schema.register(XML_BRACKET, {
            isInline: true,
            isLimit: false,
            isObject: false,
            allowWhere: ['$text'],
            allowChildren: [XML_BRACKET_OPEN, XML_BRACKET_CONTENT, XML_BRACKET_CLOSE, '$text'],
            allowAttributesOf: '$text'
        });

        this.editor.model.schema.register(XML_BRACKET_CONTENT, {
            isInline: true,
            isLimit: false,
            isObject: false,
            allowWhere: [XML_BRACKET],
            allowContentOf: '$root',
            allowAttributesOf: '$text'
        });

        //BRACKET_OPEN
        this.editor.model.schema.register(XML_BRACKET_OPEN, {
            isLimit: true,
            isInline: true,
            isObject: true,
            allowWhere: [XML_BRACKET],
            allowAttributesOf: '$text'
        });


        //BRACKET_CLOSE
        this.editor.model.schema.register(XML_BRACKET_CLOSE, {
            isLimit: true,
            isInline: true,
            isObject: true,
            allowWhere: [XML_BRACKET],
            allowAttributesOf: '$text'
        });

    }

    bracketDowncast() {
        let self = this;

        this.editor.conversion
            .for('downcast').elementToElement({
            model: XML_BRACKET,
            view: (modelElement, {writer: viewWriter}) => {
                return self.tagCreateElement(modelElement, viewWriter);
            }
        })

        // Convert attribute "data-*" to content and update brackets
        .add(dispatcher => dispatcher.on('attribute',
            (evt, data, conversionApi) => {
            const modelElement = data.item;
            if (modelElement.name !== XML_BRACKET) {
                return false;
            }

            if (modelElement.childCount < 3) {
                return false;
            }

            if (!conversionApi.consumable.test(data.item, evt.name)) {
                return;
            }

            // Mark element as consumed by conversion.
            if (self.tagCreateAttributes(modelElement, data, conversionApi)) {
                conversionApi.consumable.consume(data.item, evt.name);
            }

            let newAttributes = {};
            newAttributes[data.attributeKey] = data.attributeNewValue;

            const tagContent = self.tagRenderAttributes(modelElement, newAttributes);

            // Get opening and closing bracket and set their values
            const openElement = modelElement.getChild(0);
            if (openElement) {
                const openViewElement = conversionApi.mapper.toViewElement(openElement);
                if (openViewElement) {
                    this.tagReplaceText(openViewElement, tagContent['prefix'] || '', conversionApi.writer, false);
                }
            }

            const closeElement = modelElement.getChild(modelElement.childCount - 1);
            if (closeElement) {
                const closeViewElement = conversionApi.mapper.toViewElement(closeElement);
                if (closeViewElement) {
                    this.tagReplaceText(closeViewElement, tagContent['postfix'] || '', conversionApi.writer, false);
                }
            }
        }));
    }

    bracketUpcast() {
        this.editor.conversion.for('upcast').add(dispatcher => {

            // Look for every view span element.
            dispatcher.on('element:span', (evt, data, conversionApi) => {
                // Get all the necessary items from the conversion API object.
                const {
                    consumable,
                    writer,
                    safeInsert,
                    convertChildren,
                    updateConversionResult
                } = conversionApi;

                // Get view item from data object.
                const {viewItem} = data;

                // Define elements consumables.
                const wrapper = {name: true, classes: 'xml_bracket'};

                // Tests if the view element can be consumed.
                if (!consumable.test(viewItem, wrapper)) {
                    return;
                }

                // Create model element.
                let typeName = viewItem.getAttribute('data-type') || '';
                let tagId = viewItem.getAttribute('data-tagid') || generateUUID();
                let typeData = this.editor.config.get('tagSet')[typeName];

                //TODO: remove 'data-' prefix in model elements?
                let tagData = {
                    'data-type': typeName,
                    'data-tagid': tagId
                };

                // Default: use all attributes
                // TODO: make dry, see tagCreateElement()
                if (!typeData) {
                    tagData['data-unstyled'] = true;

                    const typeAttributes = Array.from(viewItem.getAttributeKeys()).reduce(function(carry, attrKey) {
                        if (attrKey.startsWith('data-attr-')) {
                            attrKey =  attrKey.substring('data-attr-'.length);
                            carry[attrKey] = attrKey;
                        }
                        return carry;
                    }, {});
                    typeData = {'config' : {'attributes': typeAttributes}};
                }

                const tagAttributes = Utils.getValue(typeData, 'config.attributes');
                if (tagAttributes) {
                    for (const [key, value] of Object.entries(tagAttributes)) {
                        tagData['data-attr-' + key] = viewItem.getAttribute('data-attr-' + key) || '';
                    }
                }

                const modelElement = writer.createElement(XML_BRACKET, tagData);

                // Insert element on a current cursor location.
                if (!safeInsert(modelElement, data.modelCursor)) {
                    return;
                }

                // Consume the view elements
                consumable.consume(viewItem, wrapper);

                // Handle children conversion inside the bracket content
                convertChildren(viewItem, modelElement);

                // Necessary function call to help setting model range and cursor
                // for some specific cases when elements being split.
                updateConversionResult(modelElement, data);
            });
        });

    }

    // Upcasting XML_BRACKET_CONTENT
    bracketContentUpcast() {

        this.editor.conversion.for('upcast').elementToElement({
            view: {
                classes: ['xml_bracket_content']
            },
            model: XML_BRACKET_CONTENT,
            converterPriority: 'high'
        });

    }

    // Downcasting XML_BRACKET_CONTENT
    bracketContentDowncast() {

        this.editor.conversion
            .for('downcast')
            .elementToElement({
                model: XML_BRACKET_CONTENT,

                view: {
                    name: 'span',
                    classes: 'xml_bracket_content'
                }
            });
    }

    // Upcasting XML_BRACKET_OPEN / CLOSE
    bracketOpenCloseUpcast(modelName) {

        this.editor.conversion.for('upcast').elementToElement({
            view: {
                classes: [modelName]
            },
            model: (viewElement, {writer}) => {
                const tagValue = viewElement.childCount > 0 ? viewElement.getChild(0).data : '';
                return writer.createElement(modelName, {
                    'data-value': tagValue
                });
            },
            converterPriority: 'high'
        });


    }

    /**
     * Downcaster for opening and closing brackets
     *
     * @param {string} modelName Model name and css class name of the element
     */
    bracketOpenCloseDowncast(modelName) {

        this.editor.conversion.for('editingDowncast')
            .elementToElement({
                model: modelName,

                view: (modelElement, {writer}) => {
                    let viewElement = writer.createContainerElement(
                        'span',
                        {'class': modelName}
                    );
                    let tagContent = modelElement.getAttribute('data-value');
                    this.tagCreateText(viewElement, tagContent, writer, true);
                    return this.tagToWidget(viewElement, writer);
                }
            });

        // Same as above without tagToWidget()
        // TODO: make dry
        this.editor.conversion
            .for('dataDowncast')
            .elementToElement({
                model: modelName,

                view: (modelElement, {writer}) => {
                    let viewElement = writer.createContainerElement(
                        'span',
                        {'class': modelName}
                    );
                    let tagContent = modelElement.getAttribute('data-value');
                    this.tagCreateText(viewElement, tagContent, writer, false);
                    return viewElement;
                }
            });
    }


    initXmlTag() {

        // XML_TAG schema
        this.editor.model.schema.register(XML_TAG, {
            isInline: true,
            isObject: true,
            allowWhere: '$text',
            allowAttributesOf: '$text',
        });

        this.tagUpcast();
        this.tagDowncast();
    }

    /**
     *  Upcast text tags
     *
     *  A text tag supports the following attributes:
     *  - data-type The type name
     *  - data-tagid The ID matching the from_tagid annotation field
     *  - data-link-value The content of linked values, to be rendered
     *  - Additional data-attr-* attributes as configured in the type config
     */
    tagUpcast() {

        // Upcasting XML_TAG
        this.editor.conversion.for('upcast').elementToElement({
            view: {
                classes: ['xml_text']
            },
            model: (viewElement, {writer: modelWriter}) => {

                // Base attributes
                let typeName = viewElement.getAttribute('data-type');
                let tagId = viewElement.getAttribute('data-tagid') || generateUUID();
                let tagValue = viewElement.getAttribute('data-link-value');
                // const linkTarget = = viewElement.getAttribute('data-link-target');

                const typeData = this.editor.config.get('tagSet')[typeName];
                if (!tagValue) {
                    // Get the text content and remove prefix and postfix
                    tagValue = viewElement.childCount > 0 ? viewElement.getChild(0).data : '';

                    if (tagValue && typeData) {
                        const prefix = Utils.getValue(typeData,['config.prefix','config.html.prefix'], '');
                        if (prefix && (typeof tagValue === "string") && tagValue.startsWith(prefix)) {
                            tagValue = tagValue.slice(prefix.length);
                        }

                        const postfix = Utils.getValue(typeData,['config.postfix','config.html.postfix'], '');
                        if (postfix && (typeof tagValue === "string") && tagValue.endsWith(postfix)) {
                            tagValue = tagValue.slice(0, -postfix.length);
                        }

                    }
                }


                let tagData = {
                    'data-type': typeName,
                    'data-tagid': tagId,
                    'data-value': tagValue
                    // 'data-link-target': linkTarget
                };

                // Additional attributes
                const tagAttributes = Utils.getValue(typeData, 'config.attributes');
                if (tagAttributes) {
                    for (const [key, value] of Object.entries(tagAttributes)) {
                        tagData['data-attr-' + key] = viewElement.getAttribute('data-attr-' + key) || '';
                    }
                }

                return modelWriter.createElement(XML_TAG, tagData);
            },
            converterProperty: 'low'
        });
    }

    /**
     * Downcast text tag
     *
     */
    tagDowncast() {
        let self = this;

        this.editor.conversion
            .for('editingDowncast')
            .elementToElement({
                model: XML_TAG,

                view: (modelElement, {writer: viewWriter}) => {
                    const viewElement = self.tagCreateElement(modelElement, viewWriter);

                    let tagContent = self.tagRenderAttributes(modelElement);
                    tagContent = (tagContent['prefix'] || '') + (tagContent['text'] || '') + (tagContent['postfix'] || '');
                    this.tagCreateText(viewElement, tagContent, viewWriter);

                    // Enable widget handling
                    return self.tagToWidget(viewElement, viewWriter);
                }
            })

            // Convert attribute "data-*" to content
            .add(dispatcher => dispatcher.on('attribute', (evt, data, conversionApi) => {
                const modelElement = data.item;
                if (modelElement.name !== XML_TAG) {
                    return false;
                }

                // Create tag attributes
                if (!self.tagCreateAttributes(modelElement, data, conversionApi)) {
                    return false;
                }

                // Mark element as consumed by conversion.
                conversionApi.consumable.consume(data.item, evt.name);

                // Update attributes
                let newAttributes = {};
                newAttributes[data.attributeKey] = data.attributeNewValue;

                // Get new tag content
                let tagContent = self.tagRenderAttributes(modelElement, newAttributes);
                tagContent = (tagContent['prefix'] || '') + (tagContent['text'] || '') + (tagContent['postfix'] || '');

                // Update content
                const viewElement = conversionApi.mapper.toViewElement(modelElement);
                return this.tagReplaceText(viewElement, tagContent, conversionApi.writer);
            }));

        this.editor.conversion
            .for('dataDowncast')
            .elementToElement({
                model: XML_TAG,

                view: (modelElement, {writer: viewWriter}) => {
                    const viewElement = self.tagCreateElement(modelElement, viewWriter);
                    let tagContent = self.tagRenderAttributes(modelElement);
                    tagContent = (tagContent['prefix'] || '') + (tagContent['text'] || '') + (tagContent['postfix'] || '');

                    this.tagCreateText(viewElement, tagContent, viewWriter, false);

                    return viewElement;
                }
            });
    }


    /**
     * Helper method for data and editing downcasters
     * that creates the tag in the view
     *
     * @param modelElement CKEditor model element
     * @param viewWriter
     * @returns {*}
     */
    tagCreateElement(modelElement, viewWriter) {

        const tagId = modelElement.getAttribute('data-tagid') || '';
        const typeName = modelElement.getAttribute('data-type');
        let typeData = this.editor.config.get('tagSet')[typeName];

        // Find or create link in the link container
        this.onElementInserted(this.editor, modelElement);

        let elementAttributes = {
            'data-type': typeName,
            'data-tagid': tagId,
            'class': modelElement.name + ' xml_tag_' + typeName,
        };

        if (typeData === undefined) {
            // const msg = 'Missing config for ' + typeName + '. Please check the types table.';
            // this.editor.emitEvent('app:show:message', {'msg' : msg});

            elementAttributes.class = elementAttributes.class + ' xml_notstyled';

            // Add default attributes
            // TODO: make dry, see bracketUpcast()
            const typeAttributes = Array.from(modelElement.getAttributeKeys()).reduce(function(carry, attrKey) {
                if (attrKey.startsWith('data-attr-')) {
                    attrKey =  attrKey.substring('data-attr-'.length);
                    carry[attrKey] = attrKey;
                }
                return carry;
            }, {});
            typeData = {'config' : {'attributes': typeAttributes}};
        }

        // Create markup in the editor
        const tagAttributes = Utils.getValue(typeData, 'config.attributes');
        if (tagAttributes) {
            for (const [attrKey, attrConfig] of Object.entries(typeData.config.attributes)) {
                elementAttributes['data-attr-' + attrKey] = modelElement.getAttribute('data-attr-' + attrKey);
            }
        }

        // Create the element in the editor
        return viewWriter.createContainerElement(
            Utils.getValue(typeData, 'config.html.tag') || Utils.getValue(typeData, 'config.html_tag') || Utils.getValue(typeData, 'config.tag') || 'span',
            elementAttributes
        );
    }

    /**
     * Helper method that creates text content
     *
     * @param container
     * @param value
     * @param viewWriter
     * @param {boolean} addZeroWidth Add a zero widh whitespace to the end
     * @return {*}
     */
    tagCreateText(container, value, viewWriter, addZeroWidth=true) {
        if (value === undefined) {
            value = '~NA~';
        } else if (value === null) {
            value = '~NA~';
        } else if (value === '') {
            value = '';
        }
        value = value.toString().trim();

        const innerText = viewWriter.createText(value);
        viewWriter.insert(viewWriter.createPositionAt(container, 0), innerText);

        if (addZeroWidth) {
            const zw = viewWriter.createText("\u200b");
            viewWriter.insert(viewWriter.createPositionAt(container, 'end'), zw);
        }

        return innerText;
    }

    /**
     * Helper method that replaces text content
     *
     * @param viewElement
     * @param newContent
     * @param viewWriter
     * @param {boolean} addZeroWidth Add a zero widh whitespace to the end
     * @return {*}
     */
    tagReplaceText(viewElement, newContent, viewWriter, addZeroWidth = true) {
        // Remove old Content
        const oldRange = viewWriter.createRangeIn(viewElement);
        viewWriter.remove(oldRange);

        // Insert new content
        return this.tagCreateText(viewElement, newContent, viewWriter, addZeroWidth);
    }

    /**
     * Transfer model attributes to view element
     *
     * @param modelElement
     * @param data
     * @param conversionApi
     * @return {boolean}
     */
    tagCreateAttributes(modelElement, data, conversionApi) {
        const attrKey = data.attributeKey;
        if (!attrKey.startsWith('data-value') && !attrKey.startsWith('data-attr-')) {
            return false;
        }

        // Get mapped view element
        const viewElement = conversionApi.mapper.toViewElement(modelElement);

        // Set attributes
        let newAttributes = {};
        newAttributes[attrKey] = data.attributeNewValue;

        this.tagSetAttributes(
            viewElement,
            newAttributes,
            conversionApi.writer
        );

        return true;
    }


    /**
     * Depending on the type configuration,
     * return the text, prefix and postfix
     * of an annotation
     *
     * @param {Element} modelElement The CKEditor model element to be rendered
     * @param {object} newAttributes After changes to the model attribute, pass the new value
     * @return {string}
     */
    tagRenderAttributes(modelElement, newAttributes= {}) {


        const typeName = modelElement.getAttribute('data-type');
        let typeData = this.editor.config.get('tagSet')[typeName];

        // Default rendering
        if (!typeData) {
            typeData = {
                'config' : {
                    'prefix' : '<' + typeName + '>',
                    'postfix' : '</' + typeName + '>',
                    'tag_type' : 'bracket'
                }
            };
        }

        const tagConfig = Utils.getValue(typeData, 'config', {});
        const tagType = tagConfig.tag_type || 'bracket';

        // Begin with the static content
        let content = {};
        content.prefix = Utils.getValue(tagConfig, "html.prefix", Utils.getValue(tagConfig, "html_prefix", tagConfig.prefix || ''));
        content.text = Utils.getValue(tagConfig, "html.content", Utils.getValue(tagConfig, "html_content", tagConfig.content || ''));
        content.postfix = Utils.getValue(tagConfig, "html.postfix", Utils.getValue(tagConfig, "html_postfix", tagConfig.postfix || ''));

        // Render fields
        const tagFields = Utils.getValue(typeData, 'config.fields', {});
        let tagContent = {};
        for (const [fieldKey, fieldConfig] of Object.entries(tagFields)) {
            if (fieldConfig['render']) {
                tagContent[fieldConfig['render']] = tagContent[fieldConfig['render']] || '';

                if (fieldConfig['format'] === 'counter') {
                    tagContent[fieldConfig['render']] += newAttributes['data-value'] || modelElement.getAttribute('data-value');
                }
                else if (fieldConfig['format'] === 'record') {
                    tagContent[fieldConfig['render']] += newAttributes['data-value'] || modelElement.getAttribute('data-value');
                }
                else if (fieldConfig['format'] === 'relation') {
                    tagContent[fieldConfig['render']] += newAttributes['data-value'] || modelElement.getAttribute('data-value');
                }
            }
        }
        content.prefix += tagContent.prefix || '';
        content.text += tagContent.text || '';
        content.postfix = (tagContent.postfix || '') + content.postfix;

        // Render attribute values (if configured in the attributes)
        const tagAttributes = Utils.getValue(typeData, 'config.attributes', {});
        let attrContent = {};
        for (const [attrKey, attrConfig] of Object.entries(tagAttributes)) {
            if (attrConfig['render']) {

                // Get the attribute value
                let attrValue = newAttributes['data-attr-' + attrKey] ||
                    modelElement.getAttribute('data-attr-' + attrKey) ||
                    undefined;

                // Repeat renderer, in the config:
                // - 'default' key contains the value if the attribute is 0 or not a number
                // - 'repeat' key contains the value that is repeated if the attribute is greater than 0
                if (attrConfig['repeat']) {
                    let num = Number(attrValue);
                    num = num === 0 ? NaN : num;
                    attrValue = attrConfig['default'] || attrValue;
                    attrValue = isNaN(num) ? attrValue : attrConfig['repeat'].repeat(num);
                }

                // Set value
                attrValue = attrValue || attrConfig['default'] || '';
                attrContent[attrConfig['render']] = (attrContent[attrConfig['render']] || '') + attrValue;
            }
        }

        content.prefix += attrContent.prefix || '';
        content.text += attrContent.text || '';
        content.postfix = (attrContent.postfix || '') + content.postfix;

        return content;
    }

    /**
     * Get a tag's attribute value
     *
     * @param editor
     * @param tag The tag Id
     * @param attr The attribute name, one of
     *             "value" (usually the visible text) or
     *             "attr-value" (usually attributes used to generate the visible text)
     */
    tagGetAttribute(editor, tag, attr) {
        let root = editor.model.document.getRoot();

        if (typeof tag === 'string') {
            tag = this.findDescendant(root, (elm) => {
                return elm.getAttribute('data-tagid') === tag;
            });
        }

        if (!tag) {
            return false;
        }

        return tag.getAttribute(attr);
    }

    /**
     * Get all attribute values of a tag
     *
     * @param {XmlEditor} editor
     * @param {String|Element} tag The tag ID or the ckeditor models element
     *
     */
    tagGetModelAttributes(editor, tag) {
        let root = editor.model.document.getRoot();

        if (typeof tag === 'string') {
            tag = this.findDescendant(root, (elm) => {
                return elm.getAttribute('data-tagid') === tag;
            });
        }

        if (!tag) {
            return false;
        }

        return Object.fromEntries(tag.getAttributes());
    }

    /**
     * Helper to set attributes on view elements
     *
     * @param viewElement
     * @param tagAttributes
     * @param viewWriter
     * @return {*}
     */
    tagSetAttributes(viewElement, tagAttributes, viewWriter) {
        for (const [attrKey, attrValue] of Object.entries(tagAttributes)) {
            viewWriter.setAttribute(attrKey, attrValue, viewElement);
        }
    }

    /**
     * See {toWidget} from '@ckeditor/ckeditor5-widget/src/utils';
     *
     * Adapted to omit highlight handling
     *
     * @param {Element} viewElement
     * @param viewWriter
     * @param {object} options
     * @returns {Element}
     */
    tagToWidget(viewElement, viewWriter, options = {}) {
        if (!viewElement.is('containerElement')) {
            throw new CKEditorError(
                'widget-to-widget-wrong-element-type',
                null,
                {viewElement}
            );
        }

        viewWriter.setAttribute('contenteditable', 'false', viewElement);
        viewElement.getFillerOffset = function () {
            return null;
        };

        return viewElement;
    }

    initXmlFormat() {
        // XML_FORMAT schema
        this.editor.model.schema.register(XML_FORMAT, {
            isInline: true,
            isLimit: false,
            isObject: false,
            //copyOnEnter: true, // TODO: WHY?
            allowWhere: '$text',
            allowContentOf: '$root',
            allowAttributesOf: '$text'
        });

        this.formatUpcast();
        this.formatDowncast();
    }

    /**
     *  Upcast format tags
     *
     *  A text tag supports the following attributes:
     *  - data-type The type name
     *  - data-tagid The ID matching the from_tagid annotation field
     *  - Additional data-attr-* attributes as configured in the type config
     */
    formatUpcast() {
        this.editor.conversion.for('upcast').add(dispatcher => {

            // Look for every view span element.
            dispatcher.on('element:span', (evt, data, conversionApi) => {

                // Get all the necessary items from the conversion API object.
                const {
                    consumable,
                    writer,
                    safeInsert,
                    convertChildren,
                    updateConversionResult
                } = conversionApi;

                // Get view item from data object.
                const {viewItem} = data;

                // Define elements consumables.
                const wrapper = {name: true, classes: 'xml_format'};

                // Tests if the view element can be consumed.
                if (!consumable.test(viewItem, wrapper)) {
                    return;
                }

                // Base attributes
                let typeName = viewItem.getAttribute('data-type') || '';
                let tagId = viewItem.getAttribute('data-tagid') || generateUUID();

                let tagData = {
                    'data-type': typeName,
                    'data-tagid': tagId,
                };

                // Additional attributes
                const typeData = this.editor.config.get('tagSet')[typeName];
                const tagAttributes = Utils.getValue(typeData, 'config.attributes');
                if (tagAttributes) {
                    for (const [key, value] of Object.entries(tagAttributes)) {
                        tagData['data-attr-' + key] = viewItem.getAttribute('data-attr-' + key) || '';
                    }
                }

                let modelElement = writer.createElement(XML_FORMAT, tagData);

                // Insert element on a current cursor location.
                if (!safeInsert(modelElement, data.modelCursor)) {
                    return;
                }

                // Consume the view elements
                consumable.consume(viewItem, wrapper);

                // Handle children conversion inside the bracket content
                convertChildren(viewItem, modelElement);

                // Necessary function call to help setting model range and cursor
                // for some specific cases when elements being split.
                updateConversionResult(modelElement, data);
            });
        });
    }

    /**
     * Downcast format tag
     */
    formatDowncast() {
        let self = this;

        this.editor.conversion
            .for('downcast').elementToElement({
            model: XML_FORMAT,
            view: (modelElement, {writer}) => {

                return this.tagCreateElement(modelElement, writer);
                // 'class': 'xml_format xml_tag_' + typeName,
            }
        });
    }

    /**
     * Remove the tag, keep the content
     *
     * @param {XmlEditor} editor
     * @param {string|Element} tag The tag ID or an element
     */
    removeTag(editor, tag) {
        let root = editor.model.document.getRoot();

        if (typeof tag === 'string') {
            tag = this.findDescendant(root, (elm) => {
                if (elm.getAttribute('data-tagid') === tag) {
                    return true;
                }
                return false;
            });
        }

        if (!tag) {
            return false;
        }

        // Do not remove the root tag
        if (tag.name === '$root') {
            return false;
        }

        // TODO: wrap all changes into one undo step, see https://ckeditor.com/docs/ckeditor5/latest/framework/architecture/editing-engine.html#changing-the-model

        // Move formatted content behind the format tag
        if (tag.name === XML_FORMAT) {
            editor.model.change(writer => {
                writer.move(writer.createRangeIn(tag), tag, 'after');
            });

        }

        // Move bracket content outside the bracket
        else if (tag.name === XML_BRACKET) {
            let content = tag ? this.findDescendant(tag, (elm) => {
                return elm.name === XML_BRACKET_CONTENT;
            }) : null;

            if (content) {
                editor.model.change(writer => {
                    writer.move(writer.createRangeIn(content), tag, 'after');
                });
            }
        }

        // Move bracket content outside the bracket
        else if (tag.name === XML_BRACKET_CONTENT) {
            let bracket = tag.parent;
            editor.model.change(writer => {
                writer.move(writer.createRangeIn(tag), bracket, 'after');
            });
        }

        // Remove tag
        editor.model.change(writer => {
            writer.remove(tag);
        });

        return true;
    }


    findDescendant(modelElement, predicate) {
        if (predicate(modelElement)) {
            return modelElement;
        }
        if (modelElement.getChildren) {
            for (let child of modelElement.getChildren()) {
                const found = this.findDescendant(child, predicate);
                if (found) {
                    return found;
                }
            }
        }
    }

    /**
     * Update the tag (and the text)
     *
     * @param editor The editor instance
     * @param tag The ID of the tag
     * @param attributes The new value, an object with the keys
     *              - label: text content of the tag or annotation
     *              - further attributes will be added as data-attr-values
     *              The target "tab" and "id" of annotations should be removed from the value
     */
    updateTag(editor, tag, attributes) {

        // Get the tag element
        const root = editor.model.document.getRoot();
        if (typeof tag === 'string') {
            tag = this.findDescendant(
                root,
                (elm) => {
                    return elm.getAttribute('data-tagid') === tag;
                }
            );
        }

        if (!tag) {
            return false;
        }

         // Update tag attributes
        // This will trigger the downcasters
        editor.model.change(writer => {
            for (const [key, value] of Object.entries(attributes)) {
                if (key === 'label') {
                    writer.setAttribute('data-value', value, tag);
                } else {
                    writer.setAttribute('data-attr-' + key, value, tag);
                }

            }
        });
    }
}


/**
 * Generate UUID (without -)
 */
export const generateUUID = function() {
    let
        d = new Date().getTime(),
        d2 = (performance && performance.now && (performance.now() * 1000)) || 0;
    return 'xxxxxxxxxxxx4xxxyxxxxxxxxxxxxxxx'.replace(/[xy]/g, c => {
        let r = Math.random() * 16;
        if (d > 0) {
            r = (d + r) % 16 | 0;
            d = Math.floor(d / 16);
        } else {
            r = (d2 + r) % 16 | 0;
            d2 = Math.floor(d2 / 16);
        }
        return (c == 'x' ? r : (r & 0x7 | 0x8)).toString(16);
    });
}

