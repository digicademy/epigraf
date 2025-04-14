/*
 * Document widgets - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import Utils from '/js/utils.js';
import {i18n}  from '/js/lingui.js';
import {BaseDocument, BaseWidget, BaseForm, ModelEntity, ModelField} from "/js/base.js";
import {DetachedWindow, DetachedTab} from "./frames.js";
import {
    ProjectsModel,
    ArticlesModel,
    SectionsModel,
    ItemsModel,
    AnnotationsModel,
    FootnotesModel,
    LinksModel,
    FilesModel,
    PropertiesModel,
    AttributesModel,
    TypesModel
} from '/epi/js/models.js';

/**
 * Base class for the document widget and its satellites
 *
 * @property {DocumentWidget} doc The document this part belongs to.
 * @property {string} rootTable
 * @property {string} rootId
 */
class BaseDocumentPart extends BaseForm {

    constructor(element, name, parent) {
        super(element, name, parent);

        this.doc = parent || this;
        this.rootTable = parent ? this.doc.rootTable : element.dataset.rootTable;
        this.rootId = parent ? this.doc.rootId : element.dataset.rootId;

        if (this.widgetElement) {
            // Listen dropdowns that load link targets
            this.listenEvent(this.widgetElement, 'epi:load:dropdown', event => this.onLoadDropdown(event));
        }
    }

    onClearWidgets(container) {
        if (container && this.widgetElement && (container !== this.widgetElement) && container.contains(this.widgetElement)) {
            if (this.toolbarContainer) {
                this.toolbarContainer.innerHTML = '';
            }
        }
        super.onClearWidgets(container);
    }

    /**
     * Get the field name, table name, table ID etc. for a given element
     *
     * @param {HTMLElement} elm The element to get the field data from
     * @returns {ModelField} Field object with the keys field, table, id, type, rootTable, rootId, deleted
     * @private
     */
    getFieldData(elm) {
       return new ModelField(elm);
    }

    /**
     * Get the table name and table ID for a given element
     *
     * @param {HTMLElement} elm The element to get the entity data from
     * @returns {ModelEntity} Entity object with the keys table, id, type, rootTable, rootId, deleted
     * @private
     */
    getEntityData(elm) {
        return new ModelEntity(elm);
    }

    /**
     * Populate the dropdown pane with a section list
     *
     * @param event
     */
    onLoadDropdown(event) {
        if (event.target.closest('.doc-fieldname-links')) {

            // TODO: find selected item and pass to getTargetList()
            let fieldData = this.getFieldData(event.target);
            this.doc.models.types.loadTypes(fieldData.table).then(
                (types) => {
                    let targets = this.doc.models.types.getFieldConfig(
                        types,
                        fieldData.type,
                        fieldData.field,
                        'targets'
                    );
                    const pane = event.detail.data.pane;
                    pane.replaceChildren(this.getTargetList(targets));
                }
            );
        }
    }
}

/**
 * Root document widget
 *
 * Add the class 'widget-document' to a div containing sections.
 * Each section can contain items and annotations.
 * The annotations are linked to footnotes and links.
 *
 * The DocumentWidget will instantiate models:
 * - ArticlesModel
 * - SectionsModel
 * - ItemsModel
 * - FilesModel
 * - PropertiesModel
 * - AnnotationsModel
 * - FootnotesModel
 * - LinksModel
 * - TypesModel
 *
 * The DocumentWidget will instantiate satellites:
 * - Footnotes
 * - Notes
 *
 */
export class DocumentWidget extends BaseDocumentPart {

    /**
     * The models of the document, instantiated in the constructor by initModels().
     *
     * Models are the central station for accessing and manipulating article data.
     * They provide methods to interact with the data in the DOM,
     * but despite their name don't store data on their own.
     *
     * @type {{
     *         types: TypesModel,
     *         projects: ProjectsModel,
     *         articles: ArticlesModel,
     *         sections: SectionsModel,
     *         items: ItemsModel,
     *         properties: PropertiesModel,
     *         files: FilesModel,
     *         annotations: AnnotationsModel,
     *         links: LinksModel,
     *         footnotes: FootnotesSatellite,
     *         attributes: AttributesModel
     *       }}
     * @public
     */
    models = {};

    /**
     * The satellites of the document, instantiated in the constructor by initSatellites().
     * Satellites are widgets that are not nested in the document markup,
     * they are positioned outside the document markup, e.g. footnotes or notes.
     *
     * @type {{
     *     footnotes: DocumentSatellite,
     *     notes : DocumentSatellite
     * }}
     */
    satellites = {};

    constructor(doc) {
        super(doc, 'document', undefined);

        this.models = {};
        this.dataBindings = {};

        this.editMode = doc.dataset.editMode === '1';
        this.isSaving = false;
        this.toolbarContainer = null;

        // Create models
        this.initModels();
        this.initSatellites();
        this.initToolbar();

        // Focus listeners
        this.listenEvent(document,'focusin',(event) => this.onFocusChanged(event));
        this.listenEvent(document,'focusout',(event) => this.onFocusChanged(event));
        this.listenEvent(document, 'epi:focus:entity', (event) => this.onFocusEntity(event));

        // Detach listener
        this.listenEvent(document,'click',(event) => this.onDetachClick(event),'.button-section-detach, button.doc-item-more');
    }

    /**
     * Init models
     *
     * The models are the central station for accessing and manipulating
     * article data. They provide methods to find and choose values.
     *
     */
    initModels() {
        this.models.types = new TypesModel(this);
        this.models.projects = new ProjectsModel(this);
        this.models.articles = new ArticlesModel(this);
        this.models.sections = new SectionsModel(this);
        this.models.items = new ItemsModel(this);
        this.models.properties = new PropertiesModel(this);
        this.models.files = new FilesModel(this);
        this.models.annotations = new AnnotationsModel(this);

        // TODO: implement links, footnotes and attributes as submodels of the AnnotationsModel
        this.models.links = new LinksModel(this);
        this.models.footnotes = new FootnotesModel(this);
        this.models.attributes = new AttributesModel(this);

        // Listen input updates
        this.listenBindings();
    }


    /**
     * Init satellites
     *
     * Satellites are widgets that are not nested in the document markup.
     *
     */
    initSatellites() {
        // Footnotes
        const footnotesElement = document.querySelector(
            '.doc-article-footnotes' +
            '[data-root-table="' + this.rootTable + '"]' +
            '[data-root-id="' + this.rootId + '"]'
        );

        if (footnotesElement) {
            new DocumentSatellite(this, footnotesElement, 'footnotes');
        }

        // Notes
        const notesElement = document.querySelector(
            '.doc-article-notes' +
            '[data-root-table="' + this.rootTable + '"]' +
            '[data-root-id="' + this.rootId + '"]'
        );

        if (notesElement) {
            new DocumentSatellite(this, notesElement, 'notes');
        }
    }

    /**
     * Create a toolbar container if not present
     *
     * @param {boolean} show
     */
    initToolbar(show = false) {

        // TODO: Where to place the toolbar best? Between main menu and content-wrapper?
        // TODO: Conceptualize layout functions. Don't we have a method to get the doc container?
        if (!this.toolbarContainer) {
            const contentWrapper = this.widgetElement.closest('.sidebar, .content, .widget-tabsheets-sheet');
            // this.toolbarContainer = document.querySelector('.content-toolbar');
            if (contentWrapper) {
                // const headerSection = document.querySelector('.actions-main');
                // TODO: Conceptualize layout functions. Don't we have a method to get container elements such as header or title bars?

                this.toolbarContainer = contentWrapper.querySelector('.content-toolbar');
                const headerSection = contentWrapper.querySelector('.content-header, .sidebar-title, .frame-title');
                if (!this.toolbarContainer && headerSection) {
                    this.toolbarContainer = document.createElement('div');
                    this.toolbarContainer.classList.add('content-toolbar');
                    headerSection.after(this.toolbarContainer);
                }
            }
        }
        // Show toolbar
        if (show && this.toolbarContainer) {
            this.toolbarContainer.classList.add('empty');
            this.toolbarContainer.classList.add('active');
        }
    }

    /**
     * Get the field name, table name, table ID etc. and the model for a given element
     *
     * @param {HTMLElement} elm The element to get the field data from
     * @returns {ModelField} Field object with the keys field, table, id, type, rootTable, rootId, deleted, model
     * @private
     */
    getFieldData(elm) {
        const fieldData = super.getFieldData(elm);
        if (fieldData.table && this.models[fieldData.table]) {
            fieldData.model = this.models[fieldData.table];
        }

        return fieldData;
    }

    /**
     * Creates a section widget from a section element or returns an existing one
     *
     * @param {HTMLElement} elm The section element
     * @returns {SectionWidget} The section widget
     * @private
     */
    getSectionWidget(elm) {
        let widget = this.getWidget(elm, 'section', false);
        if (!widget) {
            widget = new SectionWidget(elm, 'section', this);
        }
        return widget;
    }


    /**
     * Creates an item widget from an item element or returns an existing one
     *
     * @param {HTMLElement} elm The item element
     * @returns {ItemWidget} The item widget
     * @private
     */
    getItemWidget(elm) {
        let widget = this.getWidget(elm, 'item', false);
        if (!widget) {
            widget = new ItemWidget(elm, 'item', this);
        }
        return widget;
    }

    /**
     * Focus the fist editor in the document and show the toolbar
     *
     */
    focusEditor() {
        const toolbarActive = this.toolbarContainer && this.toolbarContainer.classList.contains('active');

        if (this.editMode && toolbarActive) {
            let xmlEditors = this.widgetElement.querySelectorAll('.widget-xmleditor');
            let visibleEditors = [...xmlEditors].filter((elm) => !(elm.clientHeight === 0 || elm.clientWidth === 0));
            let firstEditor = (visibleEditors.length > 0) ? this.getWidget(visibleEditors[0], 'xmleditor', false) : null;

            if (firstEditor) {
                firstEditor.activateEditor(false);
            }
        }
    }

    /**
     * Show the CKEditor toolbar
     *
     * @param {Element} toolbar The toolbar element (editor.ui.view.toolbar.element)
     */
    attachToolbar(toolbar) {
        if (this.toolbarContainer) {
            // Remove old toolbar
            this.toolbarContainer.innerHTML = '';

            // Append the toolbar
            this.toolbarContainer.appendChild(toolbar);
            this.toolbarContainer.classList.add('active');
            this.toolbarContainer.classList.remove('empty');
        }
    }

    /**
     * Get or loose focus
     *
     * @param {Event} event Leave empty to focus
     */
    onFocusChanged(event) {
        const sectionElement = event.target.closest('.doc-section');
        if (sectionElement && (event.type === 'focusout')) {
            sectionElement.dataset.dirty = 'true';
            Utils.emitEvent(sectionElement,'epi:save:form');
        }

        if (sectionElement && (event.type === 'focusin')) {
            const sectionWidget = this.getSectionWidget(sectionElement);
            sectionWidget.setFocus(event.type !== 'focusout');
        }
    }

    onFocusEntity(event) {
        if (event.detail.data.id === this.getEntityId()) {
            this.focusWidget();
        }
    }

    /**
     * Get or loose focus
     *
     * @param {Event} event Leave empty to focus
     * @return {boolean} Always false
     */
    onDetachClick(event) {
        if (!this.ownedByDocument(event.target)) {
            return;
        }

        let detachElement;
        let sourceElement;
        let sourceWidget;

        if (event.target.matches('.button-section-detach')) {
            sourceElement = event.target.closest('.doc-section');
            sourceWidget = this.getSectionWidget(sourceElement);
            detachElement = document.querySelector('#' + event.target.dataset.target);
        } else if (event.target.matches('button.doc-item-more')) {
            sourceElement = event.target.closest('.doc-section-item');
            sourceWidget = this.getItemWidget(sourceElement);
            detachElement = sourceElement;
        }

        let target = event.target.closest('.widget-content-pane-main') ? 'tab' : 'popup';
        if (detachElement && sourceWidget) {
            sourceWidget.detach(detachElement, target);
        }

        event.preventDefault();
        return false;
    }

    /**
     * Init autofill bindings
     */
    listenBindings() {
        if (!this.models || !this.models.types || !this.getFieldData) {
            return;
        }

        // Init data bindings
        this.dataBindings = {};
        this.models.types.loadTypes().then((types) => {
            this.widgetElement.querySelectorAll('input[data-autofill]').forEach((element) => {
                let fieldData = this.getFieldData(element);

                const autofillConfig = this.doc.models.types.getFieldConfig(
                    types[fieldData.table],
                    fieldData.type,
                    fieldData.field,
                    'autofill'
                );

                if (autofillConfig && autofillConfig.source) {
                    let source = autofillConfig.source.split('.');
                    source = source.length < 2 ? [fieldData.table + '['+fieldData.type+']', source[0]] : source;
                    source = source.join('.');

                    if (!this.dataBindings.hasOwnProperty(source)) {
                        this.dataBindings[source] = [];
                    }
                    this.dataBindings[source].push({targetElement: element, config: autofillConfig});
                }
            });
        });

        // Listen input updates
        this.listenEvent(document, 'input', event => this.onInput(event));
    }

    /**
     * Handle inputs events for autofill and for sorting items
     *
     * @param event {InputEvent} Input event
     */
    onInput(event) {
        const sourceInput = event.target;
        if (!this.ownedByDocument(sourceInput)) {
            return;
        }

        // Emit widget change event
        const entity= this.widgetElement.classList.contains('widget-entity');
        if (entity) {
            this.emitEvent('epi:change:entity', {source: sourceInput});
        }


        // Set dirty flag and remove autofill flag on the source input
        if (sourceInput.dataset.autofill) {
            delete sourceInput.dataset.autofill;
        }
        sourceInput.dataset.dirty = true;
        this.autofillSource(sourceInput);
    }

    /**
     * Check whether values from a source input should be transferred to target inputs
     *
     * @param {HTMLElement} sourceInput
     */
    autofillSource(sourceInput) {
        // Fill target input
        const fieldData = this.getFieldData(sourceInput);
        if (!sourceInput.bindProperty) {
            sourceInput.bindProperty = fieldData.table + '[' + fieldData.type + '].' + fieldData.field;
        }

        if (fieldData.rowNumber !== 1) {
            return;
        }

        const dataBindings = this.dataBindings[sourceInput.bindProperty];
        if (dataBindings) {
            dataBindings.forEach((binding) => {
                this.autofillValue(binding.targetElement, sourceInput, binding.config);
            });
        }
    }

    /**
     * Transfer the value from a source to a target input,
     * optionally processed by steps defined in the config.
     *
     * @param {HTMLInputElement} targetInput
     * @param {HTMLInputElement} sourceInput
     * @param {Object} config
     */
    autofillValue(targetInput, sourceInput, config) {
        if (!sourceInput || !targetInput) {
            return;
        }

        if (targetInput.dataset.autofill !== '1') {
            return;
        }

        let value = sourceInput.value;

        const pathSeparator = config.pathSeparator || ' › ';

        if (config.process && Array.isArray(config.process)) {
            for (var i = 0; i < config.process.length; i++) {
                let step = config.process[i];

                // Get config
                let stepConfig = typeof step === "object" ? step : {};
                step = stepConfig.method || step;

                if (step === 'path') {
                    const parentInput = sourceInput.closest('.doc-section').querySelector('input[name=parent_id-text]');
                    if (parentInput && parentInput.value !== '') {
                        value = parentInput.value + pathSeparator + value;
                    }
                }
                else if (step === 'number') {
                    value = value.toLowerCase().trim();
                    value = Utils.extractNumber(value);
                }
                else if (step === 'sortkey') {
                    value = value.toLowerCase().trim();
                    value = Utils.replaceUmlauts(value);
                    value = Utils.removeSpecialCharacters(value);
                    value = Utils.collapseWhitespace(value);
                    value = Utils.prefixNumbersWithZero(value, stepConfig?.width ?? 5);
                }
                else if (step === 'irifragment') {
                    value = value.toLowerCase().trim();
                    value = Utils.replaceUmlauts(value);
                    value = Utils.removeSpecialCharacters(value);
                    value = Utils.collapseWhitespace(value);
                    value = Utils.replaceSpacesWithHyphens(value)
                }
            }
        }

        targetInput.value = value;
        this.autofillSource(targetInput);
    }

    /**
     * Check whether the element is owned by the document or its satellites
     *
     * @param {HTMLElement} element
     * @return {boolean}
     */
    ownedByDocument(element) {
        if (!element) {
            return false;
        }

        // Check the mother document
        if (this.widgetElement && this.widgetElement.contains(element)) {
            return true;
        }

        // Iterate satellites and check if the element is owned by one of them
        for (let satellite in this.satellites) {
            if (this.satellites[satellite].widgetElement && this.satellites[satellite].widgetElement.contains(element)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find an element by selector in the document or its satellites
     *
     * @param {string} selector
     * @return {HTMLElement|undefined}
     */
    findInDocument(selector) {
        if (!selector) {
            return;
        }

        // Check the mother document
        let element = this.widgetElement.querySelector(selector);

        // Iterate satellites
        if (!element) {
            for (let satellite in this.satellites) {
                if (this.satellites[satellite].widgetElement) {
                    let elementInSatellite = this.satellites[satellite].widgetElement.querySelector(selector)
                    if (elementInSatellite) {
                        return elementInSatellite;
                    }
                }
            }
        }

        return element;
    }

    /**
     * Get all nodes of the document, filtered by type
     *
     * @param {Object} targets The targets configuration from the types table to filter
     * @return {Array} Array of nodes with the properties "table", "id" and "label"
     */
    getTree(targets) {
        return this.models.articles.getTree(targets);
    }

    /**
     * Create a table with link targets inside the document
     * (article, sections, items, footnotes)
     *
     * @param {Object} targets The targets configuration from the types table
     * @param {Object} selected The currently selected value (an object with keys id and tab)
     * @return {HTMLTableElement}
     */
    getTargetList(targets, selected) {
        // Get nodes
        // TODO: clarify when
        //  labelText (rendered in xml content),
        //  labelPath (rendered in lists), and
        //  labelName (rendered in trees) are used
        const nodeTemplate =
            '<li class="node item item-nochildren{selected} item-level-0"' +
            ' data-list-itemof="targets" data-value="{table}-{id}" data-search-text="{searchText}" data-label="{labelText}">' +
            // ' <div class="tree-indent tree-indent-leaf"></div>' +
            ' <div class="tree-content"> ' +
            ' <label class="text" title="{labelPath}" data-label="{labelText}">{labelPath}</label>' +
            ' </div>' +
            '</li>';

        // TODO: add indents / tree formatting
        let targetNodes = this.getTree(targets);
        const selectedTabId = selected ? (selected.tab + '-' + selected.id) : undefined;
        targetNodes = targetNodes.map(node => {
            if (selectedTabId && ((node.table + '-' + node.id) === selectedTabId)) {
                node.selected = ' row-selected selected';
            } else {
                node.selected = '';
            }
            return nodeTemplate.formatUnicorn(node)
        }).join('');

        // Spawn table
        return Utils.spawnFromString(
            '<ul class="widget-tree widget-tree-fixed" data-list-name="targets">'
            + targetNodes
            + '</ul>'
        );
    }

    /**
     * Show an article in the sidebar
     *
     * @param id
     */
    view(id, table) {

        // Split compound ids (e.g. articles-234)
        if (Utils.isString(id)) {
            const idArray = id.split('-');
            table = idArray[0];
            id = idArray[1];
        }

        if (table === 'articles') {
            const url = App.databaseUrl + 'articles/view/' + id;

            App.openDetails(url, {
                title: "Article",
                ajaxButtons: ['submit'],
                external: true
            });
        }
    }
}


/**
 * Section in a document
 *
 * Section widgets are not instantiated automatically for all section elements,
 * they come into live when requested by DocumentWidget::getSectionWidget().
 *
 * The widget handles the detach-button of sections.
 *
 * @param {HTMLElement} element
 * @param {string} name
 * @param {DocumentWidget} parent
 */
export class SectionWidget extends BaseDocument {
    constructor(element, name, parent) {
        super(element, name, parent);
        this.isDetached = false;
        this.popup = undefined;
    }

    initWidget(event) {

    }

    /**
     * Open section content in a popup window
     *
     * @param {Element} element The section content element to show
     * @param {String} target Where to show the section content: 'popup' or 'tab'
     */
    detach(element, target='popup') {
        // Toggle detached mode
        if (this.isDetached) {
            if (this.popup) {
                this.popup.closeWindow();
            }
            return;
        }
        this.isDetached = true;

        // Disable section switch
        const sectionHead = this.widgetElement.querySelector('.doc-section-head');
        if (App.switchbuttons) {
            App.switchbuttons.switchButton(sectionHead, true);
        }
        sectionHead.classList.toggle('widget-switch', false);

        // Hide section switch button
        const contentButton = this.widgetElement.querySelector('.doc-section-head .button-section-content');
        Utils.hide(contentButton);

        // Init satellite
        new DocumentSatellite(this.modelParent, element, this.widgetElement.id);

        // Show tab
        if (target === 'tab') {

            const tabsheetsWidget = App.findWidget(App.sidebarright.widgetElement,'tabsheets');
            if (!tabsheetsWidget) {
                return;
            }

            this.popup = new DetachedTab(element, tabsheetsWidget, {
                title: this.modelParent.models.sections.getTitle(this.widgetElement),
                onClose: (popup) => this.onRetach(element),
                dialogButtons: {
                    close: {
                        text: i18n.t('Apply'),
                        handler: (dialog) => tabsheetsWidget.removeTab('more')
                    }
                }
            });

        }
        // Show popup
        else {
            this.popup = new DetachedWindow(element, {
                title: this.modelParent.models.sections.getTitle(this.widgetElement),
                collapsable: true,
                onClose: () => this.onRetach(element)
            });
        }
    }

    onRetach(element) {
        if (!this.isDetached) {
            return;
        }
        this.isDetached = false;

        // Enable section switch
        const sectionHead = this.widgetElement.querySelector('.doc-section-head');
        sectionHead.classList.toggle('widget-switch', true);

        const contentButton = this.widgetElement.querySelector('.doc-section-head .button-section-content');
        Utils.show(contentButton);

        // Remove satellite
        delete this.modelParent.satellites[this.widgetElement.id];

        this.popup = undefined;
    }
}


/**
 * Item in a document
 *
 * Item widgets are not instantiated automatically for all item elements,
 * they come into live when requested by DocumentWidget::getItemWidget().
 *
 * The widget handles the more-button of items.
 *
 * @param {HTMLElement} element
 * @param {string} name
 * @param {DocumentWidget} parent
 */
export class ItemWidget extends BaseDocument {
    constructor(element, name, parent) {
        super(element, name, parent);
        this.isDetached = false;
        this.doc = parent;

        this.popup;
        this.wrapper;
        this.satellite;
    }

    initWidget(event) {

    }

    /**
     * Open item content in a popup window
     *
     * @param {Element} element The item element to show
     * @param {String} target Where to show the item: 'popup' or 'tab'
     */
    detach(element, target = 'popup') {
        // Toggle detached mode
        if (this.isDetached) {
            if (this.popup) {
                this.popup.closeWindow();
            }
            return;
        }
        this.isDetached = true;

        this.satellite = new DocumentSatellite(this.doc, element, this.widgetElement.id);


        // Show tab
        if (target === 'tab') {

            const tabsheetsWidget = App.findWidget(App.sidebarright.widgetElement,'tabsheets');
            if (!tabsheetsWidget) {
                return;
            }

            this.popup = new DetachedTab(element, tabsheetsWidget, {
                title: Utils.getElementText(element.querySelector('.doc-field-itemtype'), 'Item'),
                onLoad: (popup) => this.onLoad(popup, element),
                onClose: (popup) => this.onRetach(popup, element),
                dialogButtons: {
                    close: {
                        text: this.editMode ? i18n.t('Apply') : i18n.t('Close'),
                        handler: (dialog) => tabsheetsWidget.removeTab('more')
                    }
                }
            });

        }

        // Show popup
        else {
            this.popup = new DetachedWindow(element, {
                title: Utils.getElementText(element.querySelector('.doc-field-itemtype'), 'Item'),
                collapsable: true,
                onLoad: (popup) => this.onLoad(popup, element),
                onClose: (popup) => this.onRetach(popup, element)
            });
        }
    }

    /**
     * Prepare detached element (after it was detached)
     *
     * @param {DetachedWindow} popup
     * @param {HTMLElement} element
     */
    onLoad(popup, element) {

        // Init satellite
        this.wrapper = Utils.spawnFromString('<div class="doc-section-content frame-detached"></div>');
        this.wrapper.classList.toggle('widget-document-edit', this.doc.editMode);
        element.parentElement.appendChild(this.wrapper);
        const sectionStack = Utils.spawnFromString('<div class="doc-section-stack"></div>');
        this.wrapper.appendChild(sectionStack);
        sectionStack.appendChild(element);

        // Init annotation container
        if (this.doc.models.annotations) {
            const sourceLinks = this.doc.models.annotations._getAnnoContainer(popup.placeholder);

            if (sourceLinks) {
                const sectionLinks = document.createElement('div');
                sectionLinks.className = sourceLinks.className;
                this.wrapper.appendChild(sectionLinks);

                // Add toggle button
                let toggleButton = sourceLinks.querySelector('button.button-links-toggle');
                if (toggleButton) {
                    sectionLinks.appendChild(toggleButton.cloneNode(true));
                }

                // Move annotation elements
                sourceLinks
                    .querySelectorAll('.doc-section-link[data-from-tab="' + element.dataset.rowTable + '"][data-from-id="' + element.dataset.rowId + '"]')
                    .forEach((link) => sectionLinks.appendChild(link));

                sectionLinks.classList.toggle('doc-section-links-hidden',!toggleButton.classList.contains('widget-switch-active'));
                sectionLinks.classList.toggle('doc-section-links-empty',!sectionLinks.querySelector('.doc-section-link'));

            }
        }

        // Add captions and placeholder content
        const entityData = this.doc.getEntityData(element);
        popup.placeholder.classList.add('doc-section-item');
        if (element.classList.contains('doc-section-item-first')) {
            popup.placeholder.classList.add('doc-section-item-first');
        }

        this.doc.models.types.loadTypes(entityData.table).then(
            (types) => {

                element.querySelectorAll('.doc-field').forEach((fieldElement) => {

                    // Set placeholder content
                    const placeholderText = Utils.getRenderedText(fieldElement);
                    const placeholderContent = Utils.spawnFromString(`<div>${placeholderText}</div>`);
                    placeholderContent.className = fieldElement.className;
                    popup.placeholder.appendChild(placeholderContent);

                    // Wrap field
                    let contentElement = fieldElement.querySelector('.doc-field-content');
                    if (!contentElement) {
                        contentElement = Utils.spawnFromString(`<div class="doc-field-content"></div>`);
                        contentElement.replaceChildren(...fieldElement.children);
                        fieldElement.appendChild(contentElement);
                    }

                    // Set caption in popup
                    const captionText = this.doc.models.types.getFieldConfig(
                        types,
                        entityData.type,
                        fieldElement.dataset.rowField,
                        'caption'
                    );

                    const captionElement = Utils.spawnFromString(`<span class="doc-field-caption">${captionText}</span>`);
                    contentElement.parentElement.insertBefore(captionElement, contentElement);
                });
            }
        );
    }

    /**
     * Restore detached element (after it was placed back)
     *
     * @param {DetachedWindow} popup
     * @param {HTMLElement} element
     */
    onRetach(popup, element) {
        if (!this.isDetached) {
            return;
        }
        this.isDetached = false;

        // Remove captions
        element.querySelectorAll('.doc-field').forEach((fieldElement) => {
            const captionElement = fieldElement.querySelector('.doc-field-caption');
            captionElement.remove();
        });

        // Move annotation elements back
        const sourceLinks = this.doc.models.annotations._getAnnoContainer(element);
        if (sourceLinks) {
            this.wrapper
                .querySelectorAll('.doc-section-link')
                .forEach((link) => sourceLinks.appendChild(link));

            //let toggleButton = sourceLinks.querySelector('button.button-links-toggle');
            //sourceLinks.classList.toggle('doc-section-links-hidden',!toggleButton.classList.contains('widget-switch-active'));
            sourceLinks.classList.toggle('doc-section-links-empty',!sourceLinks.querySelector('.doc-section-link'));
        }

        // Remove satellite
        this.satellite.clear();
        delete this.modelParent.satellites[this.widgetElement.id];

        this.popup = undefined;
    }
}

export class FieldGroupWidget extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);
        this.listenEvent(document, 'epi:focus:widgets', (event) => this.onFocusWidgets(event));
        this.listenEvent(document, 'input', event => this.onInput(event));
    }

    /**
     * Handle inputs events for autofill and for sorting items
     *
     * @param event {InputEvent} Input event
     */
    onInput(event) {
        const sourceInput = event.target;
        if (!this.widgetElement.contains(sourceInput)) {
            return;
        }

        this.clearGroups();
        this.highlightGroup(event.target);
    }

    /**
     * Called on widget focus changes. Updates group highlighting.
     *
     * @param {CustomEvent} event epi:focus:widgets
     */
    onFocusWidgets(event) {
        this.clearGroups();

        if (!event.detail.data.focus) {
            return;
        }

        if (!event.detail.data.sender === this.widgetElement) {
            return;
        }

        this.highlightGroup(event.target);
    }

    clearGroups() {
        document.querySelectorAll('.doc-section-item-highlight').forEach(element => element.classList.remove('doc-section-item-highlight'));
    }

    highlightGroup(element) {
        const item = element.closest('.doc-section-item');
        if (!item) {
            return;
        }
        const groupInput = item.querySelector('.doc-fieldname-itemgroup input');
        if (!groupInput) {
            return;
        }
        const groupValue = Utils.getInputValue(groupInput);
        if (groupValue === '') {
            return;
        }
        const section = item.closest('.doc-section');
        if (!section) {
            return;
        }
        section.querySelectorAll('.doc-section-item').forEach(element => {
            const elementGroupInput = element.querySelector('.doc-fieldname-itemgroup input');
            if (elementGroupInput && Utils.getInputValue(elementGroupInput) === groupValue) {
                element.classList.add('doc-section-item-highlight');
            }
        });
    }
}

/**
 * Satellite handling
 *
 * Footnotes and Notes are rendered in the sidebar, outside the document.
 * That's why the footnote and notes areas are called a satellite of the document.
 *
 * @param {DocumentWidget} docWidget The parent document widget
 * @param {Element} satelliteElement The satellite element
 * @param {string} satelliteName The satellite name is used
 *                 to store the satellite in the satellites property
 *                 of the parent document and to identify the tabsheet.
 * @constructor
 */
class DocumentSatellite extends BaseDocumentPart {

    constructor(docWidget, satelliteElement, satelliteName) {
        super(satelliteElement, satelliteName, docWidget);
        this.satelliteName = satelliteName;
        this.modelParent.satellites[satelliteName] = this;

        // Set root and container of detached element
        const entityData = this.getEntityData(satelliteElement);

        satelliteElement.dataset.rootTable = satelliteElement.dataset.rootTable || entityData.rootTable;
        satelliteElement.dataset.rootId = satelliteElement.dataset.rootId || entityData.rootId;

        satelliteElement.dataset.containerTable = satelliteElement.dataset.containerTable || entityData.table;
        satelliteElement.dataset.containerId = satelliteElement.dataset.containerId || entityData.id;

        satelliteElement.classList.toggle('widget-document-edit', docWidget.editMode);

        this.initForm();
    }

    /**
     * Clear the satellite
     */
    clear() {
        this.widgetElement.classList.remove('widget-document-edit');
    }

    /**
     * Set the form ID of all detached inputs to the form ID of the parent document
     *
     */
    initForm() {
        const form = this.modelParent.widgetElement.querySelector('form');
        if (form) {
            const formId = form.getAttribute('id');
            this.widgetElement
                .querySelectorAll('input, textarea, button, select')
                .forEach(element => element.setAttribute('form', formId));
        }
    }

    /**
     * Create a table with link targets inside the document
     * (article, sections, items, footnotes)
     *
     * @param {Object} targets The targets configuration from the types table
     * @param {Object} selected The currently selected value (an object with keys id and tab)
     * @return {HTMLTableElement}
     */
    getTargetList(targets, selected) {
        return this.modelParent.getTargetList(targets, selected);
    }

    /**
     * Activate tabsheet in the sidebar
     */
    view() {
        App.activateTabsheet(this.satelliteName);
    }
}


/**
 * Base class for the entity forms and views
 *
 * The form is only attached if a document is not already present
 *
 */
class EntityWidget extends BaseForm {
    constructor(element, name, parent) {
        super(element, name, parent);
    }
}

window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['document'] = DocumentWidget;
window.App.widgetClasses['entity'] = EntityWidget;
window.App.widgetClasses['fieldgroup'] = FieldGroupWidget;

