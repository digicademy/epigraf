/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import env from '@ckeditor/ckeditor5-utils/src/env';
import XmleditorRemoveFormatCommand from "./xmleditor-remove-format-command";
import {EpiButtonView} from "./epi-buttonview-plugin";

export default class EpiRemoveFormat extends Plugin {

    static get pluginName() {
        return 'EpiRemoveFormat';
    }

    init() {
        const editor = this.editor;
        this.COMMAND = 'remove_format';

        editor.commands.add(this.COMMAND, new XmleditorRemoveFormatCommand(editor));
        const command = editor.commands.get(this.COMMAND);

        // Register the keystroke.
        // High priority: Ctrl+Delete is otherwise consumed by the
        // Delete plugin (delete word forward) before the keystroke handler sees it.
        editor.keystrokes.set('Ctrl+Delete', (keyEvtData, cancel) => {
            if (command.isEnabled) {
                editor.execute(this.COMMAND);
            }
            // Always cancel to prevent the word deletion, which is the default action of CkEditor
            cancel();
        }, {priority: 'high'});

        const keystrokeLabel = env.isMac ? '⌘⌦' : 'Ctrl+Delete';

        editor.ui.componentFactory.add('removeFormat', locale => {
            const button = new EpiButtonView(locale);
            const t = editor.t;

            button.set({
                name: 'Remove format',
                label: t('Remove format'),
                symbol: '\uf12d', // eraser
                symbolFont: 'awesome',
                keystroke: 'Ctrl+Delete', // display only
                withKeystroke: false,
                withText: false,
                tooltip: `${t('Remove format')} (${keystrokeLabel})`
            });

            button.on('execute', () => {
                editor.execute(this.COMMAND);
                editor.editing.view.focus();
            });

            button.bind('isOn', 'isEnabled').to(command, 'value', 'isEnabled');

            return button;
        });
    }
}
