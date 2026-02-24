/*
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
 */

import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import DropdownView from '@ckeditor/ckeditor5-ui/src/dropdown/dropdownview';
import ToolbarView from '@ckeditor/ckeditor5-ui/src/toolbar/toolbarview';
import {EpiButtonView, EpiDropdownButtonView} from './epi-buttonview-plugin.js';
import {createDropdown} from '@ckeditor/ckeditor5-ui/src/dropdown/utils';

import clickOutsideHandler from '@ckeditor/ckeditor5-ui/src/bindings/clickoutsidehandler';
import {
    XmleditorXmltagEditing,
    XML_COMMAND,
    XML_BRACKET, XML_FORMAT, XML_TAG
} from './xmleditor-xmltag-editing';
import Utils from "/js/utils.js";

export default class XmlButtons extends Plugin {
    /**
     * @inheritDoc
     */
    static get requires() {
        return [XmleditorXmltagEditing];
    }

    /**
     * @inheritDoc
     */
    static get pluginName() {
        return 'XmlButtons';
    }

    async init() {
        const self = this;
        const editor = this.editor;
        const tagGroups = editor.config.get('tagGroups');
        const doConstrain = editor.config.get('doConstrain');

        // Load icons
        this.icons = {};
        const iconPath = editor.config.get('iconPath');
        if (iconPath) {
            const iconModule = await import(/*webpackIgnore: true*/iconPath);
            this.icons = iconModule.default || {};
        }

        // Create toolbutton components
        // (that automatically will be instantiated based on the toolbar config in xmleditor.js)
        Object.keys(tagGroups).forEach(function (groupName) {
            tagGroups[groupName]['children'].forEach(function (tagName) {
                editor.ui.componentFactory.add('xml' + tagName.capitalize(), locale => {
                    return self.createToolbarButton(editor, locale, tagName, groupName !== 'default', doConstrain);
                });
            });

            editor.ui.componentFactory.add('xmlGroup' + groupName.capitalize(), locale => {
                return self.createToolbarGroup(editor, locale, groupName);
            });

        });

        // Disable buttons when the editor loses focus, except when clicking on toolbar items
        //TODO: Apparently clicking sidebar action buttons (Save/Cancel) does not emit a blur event...?
        //      Maybe fix here.
        editor.editing.view.document.on('blur', (evt, data) => {
            if (!data.domEvent.relatedTarget) {
                editor.ui.view.toolbar.items._items.forEach(button => {
                    button.isEnabled = false;
                });
            }
        });

        // Enable buttons when the editor gains focus
        editor.editing.view.document.on('focus', () => {
            editor.ui.view.toolbar.items._items.forEach(button => {
                button.isEnabled = true;
            });
        });
    }

    /**
     * Create a dropdown toolbar group if the group contains more than one button, otherwise return a single button.
     *
     * See https://ckeditor.com/docs/ckeditor5/latest/examples/builds-custom/bottom-toolbar-editor.html
     *
     * @param {XmlEditor} editor
     * @param {Locale} locale
     * @param {string} groupName
     * @return {EpiButtonView|DropdownView}
     */
    createToolbarGroup(editor, locale, groupName) {
        const tagGroups = editor.config.get('tagGroups');
        const doConstrain = editor.config.get('doConstrain');

        // Output a single button if the group only contains one button
        const firstButton = tagGroups[groupName].children[0];
        if (tagGroups[groupName].children.length < 2) {
            return this.createToolbarButton(editor, locale, firstButton, false, doConstrain);
        }

        // Get group button
        let groupButtonData;
        if (tagGroups[groupName].button) {
            groupButtonData = editor.config.get('tagSet')[tagGroups[groupName].button];
        } else {
            groupButtonData = editor.config.get('tagSet')[firstButton];
        }

        const {caption, icon, symbol, font, style, shortcut} = this.getToolbarButtonConfig(groupButtonData)
        const groupCaption = groupName.capitalize();

        // Vertical toolbar which holds the buttons
        const toolbarView = new ToolbarView(locale);
        toolbarView.set({
            ariaLabel: groupCaption,
            label: groupCaption,
            isVertical: true
        });

        // Add buttons to the vertical toolbar
        const buttons = tagGroups[groupName].children.map(x => 'xml' + x.capitalize());
        toolbarView.fillFromConfig(
            buttons,
            editor.ui.componentFactory
        );

        // Create the dropdown button
        const dropdownView = createDropdown(locale, EpiDropdownButtonView);
        dropdownView.buttonView.set({
            label: groupCaption,
            withText: (symbol === undefined) && (icon === undefined),
            symbol: symbol,
            symbolStyle: style,
            icon: icon,
            class: font ? ('font_' + font + ' ck-reset_all-excluded') : undefined
        });

        // Set the vertical toolbar as panel of the dropdown view
        dropdownView.panelView.children.add(toolbarView);

        // Close the dropdown when a toolbutton is clicked
        toolbarView.children.first.children.delegate('execute').to(dropdownView);

        return dropdownView;
    }

    /**
     * Create a toolbar button
     *
     * @param {XmlEditor} editor
     * @param {Locale} locale
     * @param {string} typeName
     * @param {boolean} isInGroup True if tag belongs to group, false otherwise
     * @param {boolean} doConstrain Whether to constrain allowed root tags and nested tags based on the configuration.
     *                              Without constrains, all tags configured for the field are allowed in any position.
     * @returns {EpiButtonView}
     */
    createToolbarButton(editor, locale, typeName, isInGroup = true, doConstrain = true) {
        const typeData = editor.config.get('tagSet')[typeName];
        const {caption, icon, symbol, font, style, shortcut} = this.getToolbarButtonConfig(typeData)

        // Display options of the button
        let toolButtonConfig = {
            name: typeData.name,
            label: caption,
            symbol: symbol,
            symbolFont: font,
            symbolStyle: style,
            icon: icon,

            keystroke: shortcut,
            withKeystroke: true,
            withText: true,
            tooltip: shortcut
        };

        // Toolbutton styling depending on the group status
        toolButtonConfig.withKeystroke = isInGroup;
        toolButtonConfig.tooltip = caption + (shortcut ? (' [' + shortcut) + ']' : '');

        // Disable text of toolbuttons if symbol or icon are defined
        toolButtonConfig.withText = ((symbol === undefined) && (icon === undefined));

        const toolButton = new EpiButtonView(locale);
        toolButton.set(toolButtonConfig);

        // Callback executed once the tool button is clicked.
        toolButton.on('execute', (evt, data) => {
            editor.execute(XML_COMMAND, {'data-type': typeName});
            editor.editing.view.focus();
        });

        // Shortcut
        if (shortcut) {
            editor.keystrokes.set(shortcut, (keyEvtData, cancel) => {
                if (toolButton.isEnabled) {
                    editor.execute(XML_COMMAND, {'data-type': typeName, 'initiator': 'shortcut'});
                    cancel();
                    editor.editing.view.focus();
                }
            });
        }

        // Update visibility on selection change
        if (doConstrain) {
            editor.model.document.selection.on('change:range', () => this.updateVisibility(editor, toolButton, typeData));
            this.updateVisibility(editor, toolButton, typeData);
        }

        return toolButton;
    }

    getCurrentTagType(editor) {
        const selection = editor.model.document.selection;
        const position = selection.getFirstPosition();
        if (position) {
            let parent = position.parent;
            while (parent && ![XML_BRACKET, XML_FORMAT, XML_TAG].includes(parent.name)) {
                parent = parent.parent;
            }
            if (parent) {
                return parent.getAttribute('data-type');
            }
        }
    }

    updateVisibility(editor, toolButton, typeData) {

        if (!editor.model ||! editor.model.document) {
            return;
        }
        editor.model.change(writer => {
            const parentType = this.getCurrentTagType(editor);
            if (!parentType) {
                toolButton.isEnabled = typeData.isAllowedRoot;
            }
            else if (typeData && typeData.allowedParents) {
                toolButton.isEnabled = typeData.allowedParents.includes(parentType);
            }
            else {
                toolButton.isEnabled = false;
            }
        });
    }

    /**
     * Return toolbar button configuration of normal and dropdown button
     * (caption, icon, unicode symbol, font, shortcut)
     * if values are set in button type configuration.
     *
     * // TODO: Use config of dropdown button if this is a dropdown child
     *
     * @param typeData Type configuration object
     * @returns {{caption: String, icon: SVGElement, symbol: String, font: 'awesome' | undefined, style: String, shortcut: String}}
     */
    getToolbarButtonConfig(typeData) {
        let toolButtonConfig = Utils.getValue(typeData, 'config.toolbutton');

        const iconName = Utils.getValue(toolButtonConfig, 'icon', typeData.norm_iri);
        const icon = iconName ? Utils.getValue(this.icons, iconName) : undefined;

        if (toolButtonConfig === undefined) {
            toolButtonConfig = {'symbol': icon ? undefined : typeData.name};
        } else if (typeof toolButtonConfig === 'string') {
            toolButtonConfig = {'symbol': toolButtonConfig};
        } else if (typeof toolbuttonConfig === 'boolean') {
            toolButtonConfig = {'symbol': icon ? typeData.name : undefined};
        }

        let symbol = Utils.getValue(toolButtonConfig, 'symbol');
        const font = Utils.getValue(toolButtonConfig, 'font');
        const style = Utils.getValue(toolButtonConfig, 'style');

        const shortcut = Utils.getValue(typeData, 'config.shortcut');
        const caption = typeData.caption || '';

        return {caption, icon, symbol, font, style, shortcut};
    }

    getPanelPositions(editor) {
        const {
            south, north, southEast, southWest, northEast, northWest,
            southMiddleEast, southMiddleWest, northMiddleEast, northMiddleWest
        } = DropdownView.defaultPanelPositions;

        let panelPositions;

        if (editor.locale.uiLanguageDirection !== 'rtl') {
            panelPositions = [
                northEast, northWest, northMiddleEast, northMiddleWest, north,
                southEast, southWest, southMiddleEast, southMiddleWest, south
            ];
        } else {
            panelPositions = [
                northWest, northEast, northMiddleWest, northMiddleEast, north,
                southWest, southEast, southMiddleWest, southMiddleEast, south
            ];
        }

        return panelPositions;
    }
}
