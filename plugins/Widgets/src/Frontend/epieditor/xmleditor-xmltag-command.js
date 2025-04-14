/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

/**
 * Create toolbuttons and link them to the command
 *
 * @module xmlbuttons/formattagcommand
 */

import Utils from '/js/utils.js';
import { Command } from 'ckeditor5/src/core';

import {
    XML_TAG,
    XML_FORMAT,
    XML_BRACKET,
    XML_BRACKET_CONTENT,
    XML_BRACKET_OPEN,
    XML_BRACKET_CLOSE,
    generateUUID
} from './xmleditor-xmltag-editing';


/**
 * The XML tag command
 *
 * **Note**: Executing the command with the `null` value removes the attribute from the model.
 *
 * @extends module:core/command~Command
 */
export default class XmleditorXmltagCommand extends Command {

	constructor( editor ) {
		super( editor );
	}

	/**
	 * Executes the command
	 *
	 * @protected
	 * @param {Object} commandData Options for the executed command.
	 */
	execute(commandData = {} ) {

		const typeData = this.editor.config.get('tagSet')[commandData['data-type']];

		// Format tag
		if (typeData.config.tag_type === 'format') {
            this.insertFormat(commandData['data-type']);
		}

		// Bracket
		else if (typeData.config.tag_type === 'bracket') {
            this.insertBracket(commandData['data-type']);
		}

		// Standalone tag
		else {
            this.insertTag(commandData['data-type']);
		}

        // Workaround: remove selection on shortcuts
        if (commandData.initiator === 'shortcut') {
            this.collapseSelection();
        }
	}

	refresh() {
		// See https://ckeditor.com/docs/ckeditor5/latest/framework/guides/tutorials/implementing-an-inline-widget.html#fixing-position-mapping
		// const model = this.editor.model;
		// const selection = model.document.selection;
		// const isAllowed = model.schema.checkChild( selection.focus.parent, XML_TAG );
		// this.isEnabled = isAllowed;
		//
		// const model = this.editor.model;
		// const doc = model.document;
		// this.value = doc.selection.getAttribute( XML_FORMAT );
		// model.schema.checkAttributeInSelection( doc.selection, this.attributeKey );


		this.isEnabled = true;
	}

    collapseSelection() {
        const model = this.editor.model;
        const selection = model.document.selection;
        let pos = selection.getLastPosition();

        model.change( writer => {
            writer.setSelection(pos);
        });
    }

	/**
	 * Insert XML TAG
     *
	 * @param {string} typeName Name of the annotation type
     * @return {Object} Attribute data
	 */
	insertTag (typeName) {
		const model = this.editor.model;

		let attributeData = this.newAttributeData(typeName);
		const selection = this.editor.model.document.selection;
		attributeData = {...Object.fromEntries( selection.getAttributes() ), ...attributeData};

		model.change( writer => {
			const element = writer.createElement( XML_TAG, attributeData);
            let pos = selection.getLastPosition();
			model.insertContent( element, pos, 'before');

            pos = writer.createPositionAfter(element);
            writer.setSelection(pos);
		});

		return attributeData;
	}

	/**
	 * Insert XML BRACKET
     *
	 * @param {string} typeName Name of the annotation type
     * @return {Object} Attribute data
	 */
	insertBracket (typeName) {
		const model = this.editor.model;

		let attributeData = this.newAttributeData(typeName);

		model.change( writer => {
				//const ranges = model.schema.getValidRanges( model.document.selection.getRanges(), XML_BRACKET );
				const ranges = model.document.selection.getRanges();
                let newPos;
                let isEmpty = false;

				for ( const range of ranges ) {
                    let flatranges;
                    isEmpty = range.isCollapsed;
                    if (range.isCollapsed) {
                        flatranges = [range];
                    } else {
                        flatranges = range.getMinimalFlatRanges();
                    }

					for (const flatrange of flatranges ) {
                        // Pass a copy of attributes so data-new can be deleted
						let bracketWrapper = writer.createElement( XML_BRACKET, { ...attributeData});
                        delete attributeData['data-new'];
						writer.wrap(flatrange, bracketWrapper);

						let wrapperRange = model.createRangeIn(bracketWrapper);
						let bracketContent = writer.createElement( XML_BRACKET_CONTENT);
						writer.wrap( wrapperRange, bracketContent);

                        // TODO: create in bracket downcaster
						let bracketOpenData = {'data-value' : this.getTagBracketOpen(typeName)};
						let bracketCloseData = {'data-value' : this.getTagBracketClose(typeName)};

						let bracketOpen = writer.createElement(XML_BRACKET_OPEN, bracketOpenData);
						let bracketClose = writer.createElement(XML_BRACKET_CLOSE, bracketCloseData);

						writer.insert(bracketOpen, bracketWrapper,0);
						writer.append(bracketClose, bracketWrapper,'after');

                        newPos = writer.createPositionAt(bracketContent, 'end');
					}
				}

                // if (isEmpty) {
                //     const textNode = writer.createText('');
                //     writer.insert(textNode, newPos, 'before');
                //     const posBefore = writer.createPositionBefore(textNode);
                //     const posAfter = writer.createPositionAfter(textNode);
                //     const newSel = writer.createRange(posBefore, posAfter);
                //     writer.setSelection(newSel);
                // } else

                if (newPos) {
                    writer.setSelection(newPos);
                }
		});

        return attributeData;
	}


    /**
     * Insert XML FORMAT
     *
     * @param {string} typeName Name of the annotation type
     * @return {Object} Attribute data
     */
	insertFormat (typeName) {
		const model = this.editor.model;

		let attributeData = this.newAttributeData(typeName);

		model.change( writer => {
			if ( model.document.selection.isCollapsed ) {
                //TODO
			} else {
                let flatranges =Array.from(model.document.selection.getRanges()).flatMap(
                    range => {
                        return Array.from(range.getMinimalFlatRanges());
                    }
                );
                for ( const flatrange of flatranges) {
                    // Pass a copy of attributes so data-nbew can be deleted
                    let formatWrapper = writer.createElement( XML_FORMAT, {...attributeData});
                    delete attributeData['data-new'];
                    writer.wrap(flatrange, formatWrapper);
                }

                const content = this.getTagContent(typeName);
                if (content.prefix) {
                    writer.insertText(content.prefix, model.document.selection.getFirstPosition());
                }
                if (content.postfix) {
                    writer.insertText(content.postfix, model.document.selection.getLastPosition());
                }
			}
		});

        return attributeData;
	}

    /**
     * Get the opening brackets value
     *
     * @param typeName
     * @returns {*|string}
     */
	getTagBracketOpen(typeName) {
		let typeData = this.editor.config.get('tagSet')[typeName];
		return Utils.getValue(typeData, "config.html.prefix", Utils.getValue(typeData, "config.html_prefix", typeData.config.prefix || '<'));
	}

    /**
     *  Get the closing brackets value
     *
     * @param typeName
     * @returns {*|string}
     */
	getTagBracketClose(typeName) {
		let typeData = this.editor.config.get('tagSet')[typeName];
		return Utils.getValue(typeData, "config.html.postfix", Utils.getValue(typeData, "config.html_postfix", typeData.config.postfix || '>'));
	}

    /**
     * The value of a tag is the content visible in the editor
     *
     * @param {String} typeName
     * @returns {Object}
     */
    getTagValue(typeName) {
        let tagContent = this.getTagContent(typeName);
        let value = (tagContent.prefix || '') + (tagContent.content || '') + (tagContent.postfix || '');
        value = value === '' ? '~NA~' : value;
        return value;
    }

    /**
     * Get the configured tag prefix, content, and postfix
     *
     * @param {string} typeName
     * @returns {Object}
     */
    getTagContent(typeName) {
        let typeData = this.editor.config.get('tagSet')[typeName];
        const content = {
            prefix: Utils.getValue(typeData, "config.html.prefix", Utils.getValue(typeData, "config.html_prefix", typeData.config.prefix)),
            content: Utils.getValue(typeData, "config.html.content", Utils.getValue(typeData, "config.html_content", typeData.config.content)),
            postfix: Utils.getValue(typeData, "config.html.postfix", Utils.getValue(typeData, "config.html_postfix", typeData.config.postfix))
        };
        return content;
    }

	newAttributeData(typeName) {
        let selectedText = '';
        const model = this.editor.model;
        if (!model.document.selection.isCollapsed) {
            selectedText = this.editor.data.stringify(model.getSelectedContent(model.document.selection));
            selectedText = selectedText.replace(/\s*data-tagid="[^"]*"/g, '');
        }

		return {
			'data-type' : typeName,
			'data-tagid' : generateUUID(),
            'data-value' : this.getTagValue(typeName),
            'data-new': true,
            'data-selected' : selectedText
		};
	}
}
