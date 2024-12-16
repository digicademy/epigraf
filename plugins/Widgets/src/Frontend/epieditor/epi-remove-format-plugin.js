/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import XmleditorRemoveFormatCommand from "./xmleditor-remove-format-command";
import {EpiButtonView} from "./epi-buttonview-plugin";

export default class EpiRemoveFormat extends Plugin {
    constructor(editor) {
        super(editor);

        this.editor = editor;
        this.COMMAND = 'remove_format'

        this.editor.commands.add(this.COMMAND, new XmleditorRemoveFormatCommand(this.editor));

        editor.ui.componentFactory.add('removeFormat', locale => {
            const button = new EpiButtonView(locale);
            const command = editor.commands.get(this.COMMAND);
            const t = editor.t;

            let toolButtonConfig = {
                name: 'Remove format',
                label: t( 'Remove format' ),
                symbol: '\uf12d', // eraser
                symbolFont: 'awesome',
                keystroke: 'ctrl+delete',
                withKeystroke: false,
                withText: false,
                tooltip: true
            };

            button.set( toolButtonConfig);

            button.on( 'execute', () => {
                editor.execute(this.COMMAND);
                editor.editing.view.focus();
            } );

            button.bind( 'isOn', 'isEnabled' ).to( command, 'value', 'isEnabled' );

            return button;
        });
    }
}
