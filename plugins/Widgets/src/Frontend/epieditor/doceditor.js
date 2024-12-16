/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import ClassicEditor from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';
import GeneralHtmlSupport from '@ckeditor/ckeditor5-html-support/src/generalhtmlsupport';
import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import Link from '@ckeditor/ckeditor5-link/src/link';
import SourceEditing from '@ckeditor/ckeditor5-source-editing/src/sourceediting';
import Image from '@ckeditor/ckeditor5-image/src/image';
import Heading from '@ckeditor/ckeditor5-heading/src/heading';
import Table from '@ckeditor/ckeditor5-table/src/table';
import TableToolbar from '@ckeditor/ckeditor5-table/src/tabletoolbar';
import Code from '@ckeditor/ckeditor5-basic-styles/src/code';
import CodeBlock from '@ckeditor/ckeditor5-code-block/src/codeblock';
import List from '@ckeditor/ckeditor5-list/src/list';
import RemoveFormat from '@ckeditor/ckeditor5-remove-format/src/removeformat';
import Style from '@ckeditor/ckeditor5-style/src/style';

import InsertImage from './doceditor-insertimage';
import SpecialCharacters from '@ckeditor/ckeditor5-special-characters/src/specialcharacters';
import SpecialCharactersEpi from './epi-specialcharacters-plugin';

export default class DocEditor extends ClassicEditor {

    static create(sourceElementOrData) {

        let config = {
            plugins: [
                Essentials, Paragraph, Link, Bold, Italic, Heading,
                Image, InsertImage,
                GeneralHtmlSupport, SourceEditing,
                Table, TableToolbar,
                Code, CodeBlock, List, Style,
                RemoveFormat, SpecialCharacters, SpecialCharactersEpi
            ],
            specialCharacters: {
                order: [
                    'Text',
                    'Latin',
                    'Mathematical',
                    'Currency',
                    'Arrows'
                ]
            },
            toolbar: ['heading', 'style', 'bold', 'italic', 'code', 'codeBlock', 'link', '|',
                'numberedList', 'bulletedList', '|',
                'insertTable', 'tableColumn', 'tableRow', 'insertImage', '|',
                'sourceEditing', 'removeFormat', '|',
                'specialCharacters'],

            // Tabellen werden nur im MD-Format gespeichert, wenn sie eine Header Row haben
            table: {
                defaultHeadings: {rows: 1, columns: 0}
            },
            heading: {
                options: [
                    {model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph'},
                    {model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1'},
                    {model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2'},
                    {model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3'},
                    {model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4'}
                ]
            },
            style: {
                definitions: [
                    {
                        name: 'Info box',
                        element: 'p',
                        classes: ['infobox']
                    },
                    {
                        name: 'Highlight',
                        element: 'span',
                        classes: ['highlight']
                    }
                ]
            },
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: false
                    }
                ]
            }
        };

        return super.create(sourceElementOrData, config);
    }
}
