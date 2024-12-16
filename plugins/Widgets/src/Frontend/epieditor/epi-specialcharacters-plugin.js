/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import Utils from '/js/utils.js';
import SpecialCharactersEssentials from '@ckeditor/ckeditor5-special-characters/src/specialcharactersessentials';
import SpecialCharacters from '@ckeditor/ckeditor5-special-characters/src/specialcharacters';

export default class SpecialCharactersEpi extends SpecialCharactersEssentials {
    constructor(editor) {
        super();

        this.editor = editor;
        this.specialCharacterSet = this.getCharSet();
        this.specialCharacterPanes = this.getCharSetPanes();

        for (const group in this.specialCharacterPanes) {
            if (this.specialCharacterPanes[group].length !== 0) {

                editor.plugins.get('SpecialCharacters').addItems(
                    group,
                    this.specialCharacterPanes[group],
                    {label: group}
                );

                this.editor.editing.view.document.on('keydown', (event, data) =>
                    this.insertWithShortcut(event, data));
            }
        }

        editor.plugins.get('SpecialCharacters').addItems('Greek', [
            { title: 'Alpha', character: 'Α' },
            { title: 'alpha', character: 'α' },
            { title: 'Beta', character: 'Β' },
            { title: 'beta', character: 'β' },
            { title: 'Gamma', character: 'Γ' },
            { title: 'gamma', character: 'γ' },
            { title: 'Delta', character: 'Δ' },
            { title: 'delta', character: 'δ' },
            { title: 'Epsilon', character: 'Ε' },
            { title: 'epsilon', character: 'ε' },
            { title: 'Zeta', character: 'Ζ' },
            { title: 'zeta', character: 'ζ' },
            { title: 'Eta', character: 'Η' },
            { title: 'eta', character: 'η' },
            { title: 'Theta', character: 'Θ' },
            { title: 'theta', character: 'θ' },
            { title: 'Iota', character: 'Ι' },
            { title: 'iota', character: 'ι' },
            { title: 'Kappa', character: 'Κ' },
            { title: 'kappa', character: 'κ' },
            { title: 'Lambda', character: 'Λ' },
            { title: 'lambda', character: 'λ' },
            { title: 'Mu', character: 'Μ' },
            { title: 'mu', character: 'μ' },
            { title: 'Nu', character: 'Ν' },
            { title: 'nu', character: 'ν' },
            { title: 'Xi', character: 'Ξ' },
            { title: 'xi', character: 'ξ' },
            { title: 'Omicron', character: 'Ο' },
            { title: 'omicron', character: 'ο' },
            { title: 'Pi', character: 'Π' },
            { title: 'pi', character: 'π' },
            { title: 'Rho', character: 'Ρ' },
            { title: 'rho', character: 'ρ' },
            { title: 'Sigma', character: 'Σ' },
            { title: 'sigma', character: 'σ' },
            { title: 'Tau', character: 'Τ' },
            { title: 'tau', character: 'τ' },
            { title: 'Upsilon', character: 'Υ' },
            { title: 'upsilon', character: 'υ' },
            { title: 'Phi', character: 'Φ' },
            { title: 'phi', character: 'φ' },
            { title: 'Chi', character: 'Χ' },
            { title: 'chi', character: 'χ' },
            { title: 'Psi', character: 'Ψ' },
            { title: 'psi', character: 'ψ' },
            { title: 'Omega', character: 'Ω' },
            { title: 'omega', character: 'ω' }
        ], { label: 'Greek' });

        editor.plugins.get('SpecialCharacters').addItems('Hebrew', [
            { title: 'Alef', character: 'א' },
            { title: 'Bet', character: 'ב' },
            { title: 'Gimel', character: 'ג' },
            { title: 'Dalet', character: 'ד' },
            { title: 'Hey', character: 'ה' },
            { title: 'Vav', character: 'ו' },
            { title: 'Zayin', character: 'ז' },
            { title: 'Het', character: 'ח' },
            { title: 'Tet', character: 'ט' },
            { title: 'Yod', character: 'י' },
            { title: 'Kaf', character: 'כ' },
            { title: 'Lamed', character: 'ל' },
            { title: 'Mem', character: 'מ' },
            { title: 'Nun', character: 'נ' },
            { title: 'Samekh', character: 'ס' },
            { title: 'Ayin', character: 'ע' },
            { title: 'Pe', character: 'פ' },
            { title: 'Tsadi', character: 'צ' },
            { title: 'Qof', character: 'ק' },
            { title: 'Resh', character: 'ר' },
            { title: 'Shin', character: 'ש' },
            { title: 'Tav', character: 'ת' }
        ], { label: 'Hebrew' });
    }

    /**
     * Convert special characters from database into plugin-specific structure.
     *
     * @returns {array} Array with objects of special characters
     */
    getCharSet() {
        const config = Object.values(this.editor.config._config.specialCharacterSet || {});

        let charSet = [];
        config.forEach(char => {
            let title = char.caption;
            const character = Utils.getValue(char, "config.html.content", Utils.getValue(char, "config.html_content", char.config.content));
            const pane = char.config.pane || 'Custom';
            const shortcut = char.config.shortcut;
            if (shortcut) {
                title = title + ' (' + shortcut + ')';
            }
            charSet.push({title, character, pane, shortcut});
        });

        return charSet;
    }

    /**
     * Get character set panes from the current character set.
     * Organize characters into panes based on their assigned pane property in the character set.
     *
     * @returns {Object} An object where keys are pane names, and values are arrays of characters in each pane.
     */
    getCharSetPanes() {
        const charSet = this.getCharSet();
        return charSet.reduce((charGroups, char) => {
            const currentGroup = char.pane;

            if (currentGroup && !charGroups[currentGroup]) {
                charGroups[currentGroup] = [];
            }

            charGroups[currentGroup]?.push(char);

            return charGroups;
        }, {})
    }

    /**
     * Insert special character with preconfigured shortcut.
     *
     * @param {EventInfo} eventInfo CKEditor keydown event. Holds name and internal event information
     * @param {Event} data CKEditor DomEventData. Holds all classical event information
     */
    insertWithShortcut(eventInfo, event) {
        const shortcut = this.getShortcutFromEvent(event);
        const characterToInsert = this.specialCharacterSet.find(item => item.shortcut === shortcut);

        if (characterToInsert) {
            this.editor.model.change(writer => {
                writer.insertText(characterToInsert.character, this.editor.model.document.selection.getFirstPosition());
            });
            event.preventDefault();
        }
    }

    /**
     * Create complete shortcut by combining modifier keys and alphabet/number key.
     *
     * @param data CKEditor DomEventData. Holds all classical event information
     * @returns {string} Complete shortcut as string (for example "Alt+Shift+W")
     */
    getShortcutFromEvent(data) {
        let shortcut = this.getModifierKeys(data) || '';

        if (data.domEvent.key !== 'Alt' && data.domEvent.key !== 'Shift' && data.domEvent.key !== 'Control') {
            shortcut += data.domEvent.code === 'Space' ? 'Space' : data.domEvent.key.toUpperCase();
        }

        return shortcut;
    }

    /**
     * Get pressed modifier keys (Alt, Shift, Ctrl) on event.
     *
     * @param data CKEditor DomEventData. Holds all classical event information
     * @returns {string} Combined modifier keys as string (for example "Alt+Ctrl+")
     */
    getModifierKeys(data) {
        let modifiers = '';

        const modifierKeys = {
            Alt: data.altKey,
            Shift: data.shiftKey,
            Ctrl: data.ctrlKey || data.metaKey
        };

        for (const key of Object.keys(modifierKeys)) {
            if (modifierKeys[key]) {
                modifiers += key + '+';
            }
        }

        return modifiers;
    }
}
