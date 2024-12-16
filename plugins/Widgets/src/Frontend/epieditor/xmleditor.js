/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import DecoupledEditor from '@ckeditor/ckeditor5-editor-decoupled/src/decouplededitor';
import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import SourceEditing from '@ckeditor/ckeditor5-source-editing/src/sourceediting';
import SpecialCharacters from '@ckeditor/ckeditor5-special-characters/src/specialcharacters';
import DropdownView from '@ckeditor/ckeditor5-ui/src/dropdown/dropdownview';

import XmlButtons from './xmleditor-xmltag-plugin';
import SpecialCharactersEpi from './epi-specialcharacters-plugin';
import Utils from '/js/utils.js';
import EpiRemoveFormat from "./epi-remove-format-plugin";

export default class XmlEditor extends DecoupledEditor {

    /**
     * Create an XML editor
     *
     * @returns {Promise}
     */
    static create(sourceElement, tagSet) {

        // Define which toolbuttons are available
        // (how they look is processed in xmleditor-xmltag-plugin.js)
        let {tagGroups, toolbar} = XmlEditor.initToolButtons(tagSet);
        let {customCharacters, specialCharacterConfig} = XmlEditor.initCustomCharacters(tagSet);

        // TODO: source editing not working in decoupled editor inside div fields?
        //        toolbar.push('|');
        //        toolbar.push('sourceEditing');

        let config = {
            plugins: [
                Essentials,
                SourceEditing,
                XmlButtons,
                SpecialCharacters,
                SpecialCharactersEpi,
                EpiRemoveFormat
            ],
            specialCharacters: specialCharacterConfig,
            tagSet: tagSet,
            tagGroups: tagGroups,
            iconPath : "/img/icons.min.js", // Will be loaded by dynamic import
            specialCharacterSet: customCharacters,
            toolbar: {items: toolbar, shouldNotGroupWhenFull: true},
            fillEmptyBlocks: false,
            updateSourceElementOnDestroy: true,
            basicEntities: true,
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            },
            initialCaretPosition: Utils.getSelectionPosition(sourceElement)
        };

        return super.create(sourceElement, config);
    }

    static initToolButtons(tagSet) {

        // Group tool buttons
        let tagGroups = {};
        let toolbar = [];

        // Sort buttons
        let sortedTags =  Object.entries(tagSet);
        sortedTags.sort(function([aName,a],[bName,b]) {
            if (a.sortidx !== b.sortidx) {
                return a.sortidx - b.sortidx;
            } else {
                return a.sortno - b.sortno;
            }
        });

        for (const [tagName,tagValue] of sortedTags) {


                let toolbuttonConfig = Utils.getValue(tagValue, 'config.toolbutton');

                if (toolbuttonConfig === undefined) {
                    toolbuttonConfig = {'group': 'default', 'enable' : true, 'dropdown': false};
                } else if (typeof toolbuttonConfig === 'string') {
                    toolbuttonConfig = {'group': 'default', 'enable' : true, 'dropdown': false};
                } else if (typeof toolbuttonConfig === 'boolean') {
                    toolbuttonConfig = {'group' : 'default', 'enable' : toolbuttonConfig, 'dropdown': false}
                } else {
                    toolbuttonConfig.group ??= 'default';
                    toolbuttonConfig.enable ??= true;
                    toolbuttonConfig.dropdown ??= false;
                }

                if (toolbuttonConfig.dropdown) {
                    if (tagGroups[toolbuttonConfig.group] === undefined) {
                        tagGroups[toolbuttonConfig.group] = {'children' : [], 'button' : undefined};
                    }
                    tagGroups[toolbuttonConfig.group].button = tagName;
                }
                // The matched property is set in models.js::getTagSet()
                else if ((toolbuttonConfig.enable !== false) && tagValue.matched) {

                    // Add button or group to toolbar
                    const toolButton = (toolbuttonConfig.group === 'default') ?
                        'xml' + tagName.capitalize() :
                        'xmlGroup' + toolbuttonConfig.group.capitalize();

                    if (!toolbar.includes(toolButton)) {
                        toolbar.push(toolButton);
                    }

                    // Collect tagGroups (to create the buttons in xmleditor-xmltag-plugin.js)
                    if (tagGroups[toolbuttonConfig.group] === undefined) {
                        tagGroups[toolbuttonConfig.group] = {'children' : [], 'button' : undefined};
                    }
                    tagGroups[toolbuttonConfig.group].children.push(tagName);

                }

        }

        // Add special characters dropdown
        toolbar.push('specialCharacters');
        toolbar.push('removeFormat');

        return {tagGroups, toolbar};
    }

    /**
     * Add the custom character groups
     *
     * TODO: move to epi-specialcharacters-plugin.js
     *
     * @param tagSet
     * @return {{specialCharacterConfig: {order: string[]}, customCharacters: unknown[]}}
     */
    static initCustomCharacters(tagSet) {

        // Special Characters
        let customCharacters = Object.values(tagSet)
            .filter((tag) => tag.config.tag_type === 'character')
            .filter((tag) => tag.matched);

        const specialCharacterConfig = {
            order: [
                'Text',
                'Latin',
                'Greek',
                'Hebrew',
                'Mathematical',
                'Currency',
                'Arrows'
            ]
        };

        const customCharacterGroups = customCharacters.reduce((distinct, current) => {
            const currentGroup = current.config.pane;

            if (currentGroup && !distinct.includes(currentGroup)) {
                distinct.push(currentGroup)
            }

            return distinct;
        }, [])

        specialCharacterConfig.order.unshift(...customCharacterGroups)

        return {customCharacters, specialCharacterConfig};
    }

    /**
     * Emit custom events
     *
     * @param {string} name app:show:message, tag:create, tag:remove
     */
    emitEvent(name, data) {
        Utils.emitEvent(this.sourceElement, name, data, this);
    }

    /**
     * Force all toolbar dropdown panels to use northern positions rather than southern (editor default).
     * This will position them correctly relative to the toolbar at the bottom of the editing root.
     *
     * @private
     * @param {module:core/editor/editor~Editor} editor
     * @param {module:ui/toolbar/toolbarview~ToolbarView} toolbarView
     */
    overrideDropdownPositionsToNorth(editor, toolbarView) {

        if (!toolbarView) {
            return;
        }

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

        if (toolbarView instanceof DropdownView) {

            toolbarView.on('change:isOpen', () => {
                if (!toolbarView.isOpen) {
                    return;
                }

                toolbarView.panelView.position = DropdownView._getOptimalPosition({
                    element: toolbarView.panelView.element,
                    target: toolbarView.buttonView.element,
                    fitInViewport: true,
                    positions: panelPositions
                }).name;
            });
        }

        // if (toolbarView.items) {
        // 	for (const item of toolbarView.items) {
        // 		this.overrideDropdownPositionsToNorth(item);
        // 	}
        // }
        //
        // if (toolbarView.children) {
        // 	for (const item of toolbarView.children) {
        // 		this.overrideDropdownPositionsToNorth(item);
        // 	}
        // }

    }

    /**
     * Forces all toolbar items to display tooltips to the north.
     * This will position them correctly relative to the toolbar at the bottom of the editing root.
     *
     * @param {module:ui/toolbar/toolbarview~ToolbarView} toolbarView
     */
    overrideTooltipPositions(toolbarView) {
        if (toolbarView.buttonView) {
            toolbarView.buttonView.tooltipPosition = 'n';
        }

        //if (toolbarView.tooltipPosition) {
        toolbarView.tooltipPosition = 'n';
        //}

        // if (toolbarView.children) {
        // 	for (const view of toolbarView.children) {
        // 		this.overrideTooltipPositions(view);
        // 	}
        // }
        //
        //
        // if (toolbarView.children) {
        // 	for (const view of toolbarView.children) {
        // 		this.overrideTooltipPositions(view);
        // 	}
        // }
    }

}


