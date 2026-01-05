/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import imageIcon from '@ckeditor/ckeditor5-core/theme/icons/image.svg';
import ButtonView from '@ckeditor/ckeditor5-ui/src/button/buttonview';
import Utils from '/js/utils.js';

/**
 * Select an image
 *
 * The plugin emits the 'epi:select' event on the editor element.
 * You need to listen to the event and open a modal popup to choose a value.
 *
 * The event carries options for the SelectWindow widget,
 * including the onSelect callback function.
 *
 */
export default class InsertImage extends Plugin {
    init() {
        const editor = this.editor;
        editor.ui.componentFactory.add( 'insertImage', locale => {
            const view = new ButtonView( locale );

            view.set( {
                label: 'Insert image',
                icon: imageIcon,
                tooltip: true
            } );

            // Callback executed once the image is clicked.
            view.on('execute', () => {

                // Assemble the URL
                const database = Utils.getDataValue(editor.sourceElement.closest('[data-database]'),'database');
                const baseUrl = database ? App.databaseUrl : App.baseUrl;
                let url = new URL('files/select', baseUrl);
                url.searchParams.set('download', '0');

                let basePath = Utils.getDataValue(editor.sourceElement.closest('[data-file-basepath]'),'fileBasepath');
                const defaultPath = Utils.getDataValue(editor.sourceElement.closest('[data-file-defaultpath]'),'fileDefaultpath')

                if (basePath) {
                    url.searchParams.set('path', basePath);
                    url.searchParams.set('basepath', basePath);
                }

                if (defaultPath) {
                    basePath = basePath ? basePath + '/' : '';
                    url.searchParams.set('path', basePath + defaultPath);
                }

                url = url.toString();

                // Popup options
                const windowOptions = {
                    url: url,
                    itemtype: 'file',
                    ajaxButtons: 'exclusive',
                    buttonSelect: false,
                    selectOnClick: true,
                    onSelect: (element) => {
                        if (element.dataset.url) {
                            // Create an image element
                            editor.model.change(writer => {
                                const imageElement = writer.createElement('imageBlock', {
                                    src: element.dataset.url
                                });
                                // Insert the image in the current selection location.
                                editor.model.insertContent(imageElement, editor.model.document.selection);
                            });
                        }
                    }
                };

                Utils.emitEvent(editor.sourceElement, 'epi:select', windowOptions, this.editor, true);
            } );

            return view;
        });
    }
}

