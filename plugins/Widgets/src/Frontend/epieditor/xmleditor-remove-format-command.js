/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import { Command } from 'ckeditor5/src/core';
import {XML_FORMAT} from "./xmleditor-xmltag-editing";

export default class XmleditorRemoveFormatCommand extends Command {
    constructor(editor) {
        super(editor);
    }

    execute(args) {
        const editingPlugin = this.editor.plugins.get('XmlTagEditing');
        for (const range of this.editor.model.document.selection.getRanges()) {
            if (range.isCollapsed) {
                editingPlugin.removeTag(this.editor, range.start.parent);
            } else {
                this.editor.model.change(writer => {
                    // console.log(range);

                    // Flat ranges are defined as ranges with the same start and end parent
                    for (const flatRange of range.getMinimalFlatRanges()) {
                        // console.log(flatRange);

                        // First remove contained formats
                        for (const item of flatRange.getItems({ignoreElementEnd: true, shallow: true})) {
                            if (item.is('element', XML_FORMAT)) {
                                writer.move(writer.createRangeIn(item), item, 'after');
                                writer.remove(item);
                            }
                        }

                        // Then remove overlapping formats
                        if (flatRange.start.parent.is('element', XML_FORMAT)) {
                            // Complete tag is selected
                            if (flatRange.start.isAtStart && flatRange.end.isAtEnd) {
                                writer.move(flatRange, flatRange.start.parent, 'after');
                                writer.remove(flatRange.start.parent);
                            }
                            // Selection up to the end of the tag
                            else if (flatRange.end.isAtEnd) {
                                writer.move(flatRange, flatRange.end.parent, 'after');
                            }
                            // Selection from start of the tag
                            else if (flatRange.start.isAtStart) {
                                writer.move(flatRange, flatRange.start.parent, 'before');
                            }
                        }
                    }
                });
            }
        }
    }

    refresh() {
        // TODO: Update this
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
}
