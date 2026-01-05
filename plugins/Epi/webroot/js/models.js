/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import Utils from '/js/utils.js';
import {i18n}  from '/js/lingui.js';
import {BaseModel} from "/js/base.js";

// TODO: remove import, use events to request a select window
import {SelectWindow} from "/widgets/js/frames.js";

/**
 * Base class for document models
 *
 * The models (articles, sections, items, files, properties, annotations, links, types)
 * are instantiated in the parent document widget.
 *
 * Therefore, to access one model from another,
 * follow the doc property of a model.
 *
 */
class BaseDocumentModel extends BaseModel {

    constructor(documentWidget) {
        super(documentWidget);

        this.doc = documentWidget;
        this.rootTable = documentWidget ? this.doc.rootTable : undefined;
        this.rootId = documentWidget ? this.doc.rootId : undefined;
    }

    /**
     * Enable inputs within an element to make sure they are saved on form submission.
     *
     * @param {HTMLElement} element
     */
    enableInputs(element) {
        element.querySelectorAll('input, select, textarea').forEach(input => {
            input.disabled = false;
        });
    }

    /**
     * Disable inputs within an element to prevent them from being saved on form submission.
     *
     * @param {HTMLElement} element
     */
    disableInputs(element) {
        element.querySelectorAll('input, select, textarea').forEach(input => {
            input.disabled = true;
        });
    }
}

/**
 * Projects model
 *
 * @param options
 * @constructor
 */
export class ProjectsModel extends BaseDocumentModel {

}

/**
 * Articles model
 *
 * @param options
 * @constructor
 */
export class ArticlesModel extends BaseDocumentModel {

    /**
     * Get the article and all child nodes filtered by the targets configuration
     *
     * @param {Object} targets The targets configuration
     * @return {Array} A list of nodes with the keys
     *                 table, id, treeId, treeParent, treeLevel, labelName, and labelPath
     */
    getTree(targets) {
        let nodes = [];

        // Add the article if requested in targets
        const article = this.modelParent.widgetElement;
        if (targets && targets.articles && Array.isArray(targets.articles) && targets.articles.includes(article.dataset.rowType)) {
            nodes.push({
                table: article.dataset.rowTable,
                id: article.dataset.rowId,
                treeId: article.dataset.rowTable + "-" + article.dataset.rowId,
                treeParent: null,
                treeLevel: 0,
                labelName: "Article", // TODO: translate
                labelPath: "Article"  // TODO: translate
            });
        }

        // Add sections
        if (targets && targets.sections) {
            nodes.push(...this.doc.models.sections.getTree(targets, article));
        }

        // Add footnotes
        if (targets && targets.footnotes) {
            nodes.push(...this.doc.models.footnotes.getTree(targets, article));
        }

        return nodes;
    }

    /**
     * Get the current project ID
     *
     * TODO: move to the projects model
     *
     */
    getProjectId() {
        return Utils.getInputValue(
            this.modelParent.widgetElement.querySelector('.doc-content select[name=projects_id]')
        );
    }

}


/**
 * Sections model
 *
 * @param {DocumentWidget} modelParent
 * @constructor
 */
export class SectionsModel extends BaseDocumentModel {

    /**
     * Constructor
     *
     * @param {DocumentWidget} modelParent
     */
    constructor(modelParent) {
        super(modelParent);

        this.lastId = 0;
        this.sectionMenu = document.querySelector('.menu-sections');
        if (this.sectionMenu) {
            // Interface to the tree widget (TODO: refactor widgetScrollbox and create widgetTree)
            this.widgetTree = this.getWidget(this.sectionMenu, 'tree');

            // Add section listener
            const sideBar = this.sectionMenu.closest('.sidebar');
            this.listenEvent(sideBar, 'click', event => this.sectionMenuClick(event));
            this.listenEvent(sideBar, 'changed', event => this.sectionMenuClick(event));
            this.listenEvent(this.sectionMenu, 'epi:move:row', event => this.updatePositions());
            this.listenEvent(this.doc.widgetElement, 'epi:focus:section', event => this.view(event.target.dataset.rowId));
        }

        this.listenEvent(this.doc.widgetElement, 'change', event => this.onNameChanged(event));
    }

    /**
     * Generate a temporary section ID
     *
     * @return {string}
     * @private
     */
    _createId() {
        this.lastId += 1;
        return 'sections-int' + this.lastId;
    }

    /**
     * Get all sections and their child nodes filtered by the targets configuration
     *
     * // TODO: Can this be merged with the menu functions?
     *
     * @param {Object} targets The targets configuration
     * @param {HTMLElement} article The parent article
     * @return {Array} A list of nodes with the keys
     *                 table, id, treeId, treeParent, treeLevel, labelName and labelPath
     */
    getTree(targets, article) {
        let nodes = [];

        const sections = article.querySelectorAll('.doc-section');
        sections.forEach(section => {

            // Add the section if requested in targets
            if (targets.sections && Array.isArray(targets.sections) && targets.sections.includes(section.dataset.rowType)) {
                nodes.push({
                    table: section.dataset.rowTable,
                    id: section.dataset.rowId,
                    treeId: section.dataset.rowTable + "-" + section.dataset.rowId,
                    treeParent: section.dataset.rowParentId ?
                        section.dataset.rowTable + "-" + section.dataset.rowParentId :
                        article.dataset.rowTable + "-" + article.dataset.rowId,
                    treeLevel: parseInt(section.dataset.rowLevel || '0') + 1,
                    labelText: this.getPath(section),
                    labelName: this.getName(section),
                    labelPath: this.getTitle(section),
                    searchText: this.getPath(section)
                });
            }

            // Add sections
            if (targets.items) {
                nodes.push(...this.doc.models.items.getTree(targets, section));
            }
        });

        return nodes;
    }

    /**
     * Get all section elements
     *
     * @return {NodeList}
     */
    getAllSections() {
        return this.doc.widgetElement.querySelectorAll('.doc-section[data-row-table="sections"]');
    }

    /**
     * Get all menu item elements
     *
     * @return {NodeListOf<Element>}
     */
    getMenuItems() {
        return this.sectionMenu.querySelectorAll('.node:not(.item-removed)');
    }

    /**
     * Get the section element given a sidebar menu item
     *
     * @param {Element} menuItem The menu item
     * @returns {Element}
     */
    getSectionByMenuItem(menuItem) {
        if (!menuItem) {
            return undefined;
        }

        // When the menuItem is no menu item but the section
        if (menuItem.classList.contains('doc-section')) {
            return menuItem;
        }

        try {
            return this.modelParent.widgetElement.querySelector('#sections-' + menuItem.dataset.id);
        } catch {
            return undefined;
        }
    }

    getMenuItemBySection(section) {
        return this.getMenuItemById(section.dataset.rowId);
    }

    getMenuItemById(sectionId) {
        return this.sectionMenu.querySelector('[data-id="' + sectionId + '"]');
    }

    /**
     * Get the activated section
     * (activated by scrolling or mouse click)
     *
     * @returns {null|*|undefined}
     */
    getActiveSection() {
        if (App.scrollsync) {
            return App.scrollsync.activeSection;
        }
        return undefined;
    }

    getActiveMenuItem() {
        return this.sectionMenu.querySelector('.active');
    }

    getName(section) {
        return section.querySelector('.doc-section-name').dataset.rowName;
    }

    /**
     * Get the title of a section as configured in the section type
     *
     * May include the path, a prefix and a postfix
     *
     * @param {Element} section
     * @return {string}
     */
    getTitle(section) {
        return Utils.getElementText(section.querySelector('.doc-section-name [data-value="name"]'));
        // return Utils.getInputValue(section.querySelector('.doc-section-name [data-value="name"]'));
    }

    /**
     * Get the position of a section, optionally within the same parent or section type.
     *
     * @param {HTMLElement} section
     * @param {boolean} usePath
     * @param {boolean} useScope
     * @return {number}
     */
    getPosition(section, usePath = true, useScope = true) {
        let siblings = [];
        let prevSibling = section.previousElementSibling;
        while (prevSibling) {

            // Skip deleted sections
            let matches = prevSibling.classList.contains('doc-section') && !prevSibling.classList.contains('doc-section-removed');

            if (matches && usePath) {
                matches = prevSibling.dataset.rowParentId === section.dataset.rowParentId;
            }
            if (matches && useScope) {
                matches = prevSibling.dataset.rowType === section.dataset.rowType;
            }
            if (matches) {
                siblings.push(prevSibling);
            }
            prevSibling = prevSibling.previousElementSibling;
        }
        return siblings.length + 1;
    }

    /**
     * Get the joined names of the section and its ancestors
     *
     * @param {HTMLElement} section
     * @return {string}
     */
    getPath(section) {
        let ancestors = [this.getName(section)];

        try {

            // Only within the same table ('sections')
            const rowTable = section.dataset.rowTable;

            // Only when using the path is configured in the section type
            let sectionTypes = {};
            let sectionConfig = {};
            let usePath = false;
            if (rowTable === 'sections') {
                sectionTypes = Utils.getValue(this.modelParent.models.types._types, rowTable, {});
                sectionConfig = sectionTypes[section.dataset?.rowType] || this.getConfig(section);
                usePath = Utils.getValue(sectionConfig, 'config.name.path', false);
            }

            let prevSibling = section.previousElementSibling;
            while (usePath && prevSibling && (prevSibling.dataset.rowTable === rowTable)) {
                if (prevSibling.dataset.rowId === section.dataset.rowParentId) {
                    ancestors.push(this.getName(prevSibling));

                    section = prevSibling;
                    if (rowTable === 'sections') {
                        sectionConfig = sectionTypes[section.dataset?.rowType] || this.getConfig(section);
                        usePath = Utils.getValue(sectionConfig, 'config.name.path', false);
                    }
                }
                prevSibling = prevSibling.previousElementSibling;
            }

        } catch (error) {
            console.error("Error finding the path: ", error);
        }

        return ancestors.reverse().join('.');
    }

    /**
     * Get the section config from its json encoded data-row-config attribute
     *
     * @param {HTMLElement} section
     * @return {Object}
     */
    getConfig(section) {
        try {
            return JSON.parse(decodeURI(section.dataset.rowConfig));
        } catch (e) {
            return {};
        }
    }

    /**
     * Move sections to match the positions of the menu.
     * Then update data attributes and input values of the sections
     * (parent_id, preceding_id).
     * Then call updateNames() to update section names, numbers and sort numbers.
     *
     * @param {Event} event An event triggering the update.
     */
    updatePositions(event) {
        const treeWidget = this.widgetTree;

        // let sectionNumbers = {};
        let sortNumber = 0;

        let currentSection = this.modelParent.widgetElement.querySelector('.doc-section');
        this.sectionMenu.querySelectorAll('[data-list-itemof], .node').forEach(currentMenuItem => {
            if (currentSection && !currentMenuItem.classList.contains('fixed')) {
                if (currentSection.dataset.rowId !== currentMenuItem.dataset.id) {
                    const movedSection = this.modelParent.widgetElement
                        .querySelector('[data-row-id="' + currentMenuItem.dataset.id + '"]');
                    currentSection.before(movedSection);
                    currentSection = movedSection;
                }

                // Update the tree data fields of the section
                if (treeWidget) {

                    // Parent
                    const treeParent = treeWidget.treeGetParent(currentMenuItem);
                    currentSection.dataset.rowParentId = (treeParent && treeParent.dataset.id) ? treeParent.dataset.id : '';
                    Utils.setInputValue(
                        currentSection.querySelector('[data-row-field="parent_id"]'),
                        currentSection.dataset.rowParentId
                    );

                    // Preceding
                    const treePreceding = treeWidget.treeGetPrevSibling(currentMenuItem);
                    currentSection.dataset.rowPrecedingId = (treePreceding && treePreceding.dataset.id) ? treePreceding.dataset.id : '';
                    Utils.setInputValue(
                        currentSection.querySelector('[data-row-field="preceding_id"]'),
                        currentSection.dataset.rowPrecedingId
                    );

                    // Scoped section number
                    // sectionNumbers[currentSection.dataset.rowParentId] = sectionNumbers[currentSection.dataset.rowParentId] || {};
                    // const currentNumber = (sectionNumbers[currentSection.dataset.rowParentId][currentSection.dataset.rowType] || 0) + 1;
                    // sectionNumbers[currentSection.dataset.rowParentId][currentSection.dataset.rowType] = currentNumber;

                    // Utils.setInputValue(
                    //     currentSection.querySelector('[data-row-field="number"]'),
                    //     currentNumber
                    // );

                    // Level
                    //TODO: does not work?
                    currentSection.dataset.rowLevel = currentMenuItem.treeLevel;
                    Utils.removeClassByPrefix(currentSection, 'doc-section-level-');
                    currentSection.classList.add('doc-section-level-' + currentMenuItem.treeLevel);

                }

                // Running sort number
                // (For pipeline tasks the sortno is mapped to the number field, see EntityHelper taskStart)
                // TODO: move section inputs into doc-section-head and scope selector
                sortNumber += 1;

                Utils.setInputValue(
                    currentSection.querySelector('.doc-section-name [data-row-field="sortno"], :scope > [data-row-field="sortno"]'),
                    sortNumber
                );

                currentSection = currentSection.nextElementSibling;
            }
        });
        this.updateNames();

        if (App.scrollsync) {
            App.scrollsync.updateWidget();
            // restore the focus, which may have moved due to scroll sync operations
            App.scrollsync.scrollToSection(this.getActiveSection(), true);
        }
    }

    /**
     * Update all section names / numbers
     *
     * In the section type config, add a name setting, e.g.
     * name : {"number":"alphabetic", "prefix": "Teil "}
     *
     */
    updateNames() {
        this.getMenuItems().forEach(menuElement => {
            this.updateName(menuElement);
        });
    }

    /**
     * Update the section names and numbers
     *
     * In the section type config, add a name setting, e.g.
     * name : {"number":"alphabetic", "prefix": "Teil "}
     *
     * @param menuElement
     */
    updateName(menuElement) {
        const section = this.getSectionByMenuItem(menuElement);
        if (!section) {
            return;
        }

        this.modelParent.models.types.loadTypes('sections').then(
            (sectionTypes) => {

                // Published class
                const publishedSelect = section.querySelector('select.doc-section-published');
                if (publishedSelect) {

                    const publishedValue = Utils.getInputValue(publishedSelect, '');
                    const menuPublished = menuElement.querySelector('.tree-published');

                    Utils.removeClassByPrefix(menuPublished, 'tree-published-');
                    menuPublished.classList.add('tree-published-' + publishedValue);
                }

                // Step 1: Get numeric section number (number field in the database)
                const sectionConfig = sectionTypes[section.dataset?.rowType] || this.getConfig(section);
                let useNumber = Utils.getValue(sectionConfig, 'config.name.number');
                const usePath = Utils.getValue(sectionConfig, 'config.name.path', false);
                const useScope = Utils.getValue(sectionConfig, 'config.name.scoped', false);
                const sectionNumber = this.getPosition(section, usePath, useScope);
                const sortNumber = this.getPosition(section, false, false);

                // Step 2: Get section name (name field in the database)
                const customName = Utils.getInputValue(section.querySelector('[data-row-field="name"]'));

                let sectionName;
                if (useNumber !== undefined) {
                    useNumber = useNumber === 'alphabetic' ? 'alphabetic-upper' : useNumber;
                    sectionName = Utils.numberToString(sectionNumber, useNumber);
                } else if (customName !== undefined) {
                    //TODO: update when typing
                    sectionName = customName;
                }

                // Step 3: Update elements
                if (sectionName !== undefined) {

                    // Store in database
                    Utils.setInputValue(section.querySelector('[data-row-field="name"]'), sectionName);
                    section.querySelector('.doc-section-name').dataset.rowName = sectionName;

                    // Update menu item
                    const prefix = Utils.getValue(sectionConfig, 'config.name.prefix', '');
                    const postfix = Utils.getValue(sectionConfig, 'config.name.postfix', '');
                    const menuLabel = prefix + sectionName + postfix;
                    Utils.setElementContent(menuElement.querySelector('.tree-content a'), menuLabel);

                    // Update section name
                    const sectionTitle = prefix + this.getPath(section) + postfix;
                    Utils.setElementContent(
                        section.querySelector('.doc-section-name').querySelector('[data-value="name"]'),
                        sectionTitle
                    );

                    // Update indent
                    const indent = " ⬥ ".repeat(parseInt(section.dataset.rowLevel));
                    Utils.setElementContent(section.querySelector('.doc-section-indent'), indent);

                    // Update number
                    Utils.setInputValue(section.querySelector('.doc-section-name [data-row-field="number"], :scope > [data-row-field="number"]'), sectionNumber);
                }

                // Update number
                Utils.setInputValue(section.querySelector('.doc-section-name [data-row-field="sortno"], :scope > [data-row-field="sortno"]'), sortNumber);

            });
    }

    view(id) {
        if (id && App.scrollsync) {
            const section = document.getElementById('sections-' + id);
            App.switchbuttons.switchButton(section.querySelector('.doc-section-head'), true);
            App.scrollsync.scrollToSection(section, true);
        }
    }

    /**
     * Remove a section with all its descendants
     *
     * If the section belonging to the menu item has a data-row-field="deleted"
     * element, the items will be soft deleted.
     *
     * TODO: Set data-deleted to 1 and disable inputs for new sections, including all items.
     *
     * @param {HTMLElement} menuItem
     * @returns {boolean}
     */
    async delete(menuItem) {
        if (!menuItem) {
            return false;
        }

        if (!await App.confirmAction('Are you sure you want to delete the selected section?')) {
            return false;
        }

        // Delete the complete subtree
        const subtree = this.widgetTree.treeGetDescendants(menuItem);
        subtree.push(menuItem);

        const self = this;
        subtree.forEach(function (menuItem) {
            const section = self.getSectionByMenuItem(menuItem);
            if (section) {
                const notesContainer = self.doc.satellites.notes ? self.doc.satellites.notes.widgetElement : undefined;
                const note = notesContainer ?
                    notesContainer.querySelector('.doc-section-note[data-section-id="' + section.id + '"]') :
                    undefined;

                const softDelete = section.querySelector('[data-row-field="deleted"]');
                if (softDelete) {
                    Utils.setInputValue(softDelete, 1);

                    if (note) {
                        self.doc.models.footnotes.removeFrom(note);
                        note.classList.remove('active');
                        note.classList.add('doc-section-removed');
                    }

                    self.doc.models.footnotes.removeIn(section);
                    section.classList.add('doc-section-removed');

                    menuItem.classList.add('item-removed');
                } else {
                    if (note) {
                        self.doc.models.footnotes.removeFrom(note);
                        note.remove();
                    }

                    self.doc.models.footnotes.removeIn(section);
                    section.remove();

                    menuItem.remove();
                }
            }
        });

        this.widgetTree.treeUpdatePositions();
        this.updateNames();
        return true;
    }

    /**
     * Add the new section to the document
     *
     * Beware: Call updatePosition to push the section to the correct position.
     *
     * @param {HTMLElement} newSection A section element
     * @param {HTMLElement} precedingElement
     * @return {*}
     */
    add(newSection, precedingElement) {
        if (!precedingElement) {
            precedingElement = this.doc.widgetElement.querySelector('.doc-content');
        }

        // Move note to notes section
        const noteElement = newSection.querySelector('.doc-section-note');
        if (noteElement) {
            this.doc.satellites.notes.widgetElement.appendChild(noteElement);
            App.initWidgets(noteElement);
        }

        precedingElement.after(newSection);
        App.initWidgets(newSection);

        return newSection;
    }

    /**
     * Handle clicks in buttons in the section menu
     *
     * @param event
     */
    sectionMenuClick(event) {
        const menuItem = this.getActiveMenuItem();

        if (event.target.classList.contains('doc-section-add') && (event.type === 'changed')) {
            this.fetchNewSections(menuItem, false, event.detail.data.id);
        } else if (event.target.classList.contains('doc-section-remove')) {
            this.delete(menuItem);
        }
    }

    /**
     * Add new sections below to or inside another section
     *
     * @param {HTMLElement|undefined} referenceMenuItem The reference section menu item
     * @param {boolean} insert Boolean, whether to insert behind (false) or inside (true) the reference item
     * @param {string} type The sectiontype
     */
    fetchNewSections(referenceMenuItem, insert, type) {
        let url = this.sectionMenu.dataset.listAdd + '/' + type;
        url = new URL(url, App.baseUrl);

        // Parent ID
        const menuItemParent = !this.widgetTree ? undefined : this.widgetTree.treeGetParent(referenceMenuItem);
        if (referenceMenuItem && insert) {
            url.searchParams.set('parent_id', referenceMenuItem.dataset.id);
        } else if (!insert) {
            url.searchParams.set('parent_id', menuItemParent ? menuItemParent.dataset.id : null);
            if (referenceMenuItem) {
                url.searchParams.set('preceding_id', referenceMenuItem.dataset.id);
            }
        }

        const self = this;
        App.fetchHtml(
            url.toString(),
            function (data) {
                let firstNewMenuItem;

                const sections = Utils.spawnFromString(data, undefined, false).querySelectorAll('.doc-section');
                sections.forEach(function (newSection) {

                    // Create section menu item
                    const newMenuItem = self.createMenuItem(newSection, referenceMenuItem, insert);

                    // Add section
                    const referenceSection = self.getSectionByMenuItem(referenceMenuItem);
                    newSection = self.add(newSection, referenceSection);

                    // Move to correct position
                    self.updatePositions();

                    referenceMenuItem = newMenuItem;
                    firstNewMenuItem = firstNewMenuItem || newMenuItem;
                });


                if (firstNewMenuItem && App.scrollsync) {
                    App.scrollsync.activateLi(firstNewMenuItem);
                }

                // Can this be removed as it is called by updatePositions()?
                // self.updateNames();
            }
        );
    }

    /**
     * Add a new item to the section menu
     *
     * @param {HTMLElement} newSection The section for which a menu item should be created
     * @param {HTMLElement} menuItem The reference menu item or undefined
     * @param {boolean} insert If true, insert as a child of the reference menuItem, otherwise append as sibling.
     * @return {HTMLElement}
     */
    createMenuItem(newSection, menuItem, insert) {
        let newMenuItem = Utils.spawnFromTemplate(
            this.sectionMenu.querySelector('.template'),
            {
                id: newSection.dataset.rowId,
                parent_id: newSection.dataset.rowParentId,
                // level: newSection.dataset.rowLevel,
                // sectionname: newSection.querySelector('.doc-section-name').dataset.rowName
            }
        );

        if (newSection.dataset.rowParentId) {
            menuItem = this.getMenuItemById(newSection.dataset.rowParentId);
            insert = true;
        }

        if (insert) {
            newMenuItem = this.widgetTree.treeAppendChild(menuItem, newMenuItem);
        } else {
            newMenuItem = this.widgetTree.treeAppendAfter(menuItem, newMenuItem);
        }

        return newMenuItem;
    }

    /**
     * Name change handler
     *
     * @param event
     */
    onNameChanged(event) {

        // Published input
        const publishedSelect = event.target.closest('.doc-section-published select');

        if (publishedSelect) {
            const value = Utils.getInputValue(publishedSelect, '');

            const field = publishedSelect.closest('div.doc-section-published');
            Utils.removeClassByPrefix(field, 'doc-section-published-');
            field.classList.add('doc-section-published-' + value);

            const section = publishedSelect.closest('.doc-section');
            if (section) {
                const menuItem = this.getMenuItemBySection(section);
                this.updateName(menuItem);
            }
            return;
        }

        // Text input
        let inputText = event.target.closest('.doc-section-name [data-row-field="name"]');
        if (inputText) {
            const section = inputText.closest('.doc-section');
            const menuItem = this.getMenuItemBySection(section);
            this.updateName(menuItem);
            return;
        }

        // Select input
        const inputSelect = event.target.closest('.doc-section-name [data-row-field="properties_id"]');
        if (inputSelect) {
            const section = inputSelect.closest('.doc-section');

            if (section) {
                const propertiesId = Utils.getInputValue(inputSelect, '');
                const sectionName = inputSelect.options[inputSelect.selectedIndex].text;
                Utils.setInputValue(section.querySelector('[data-row-field="name"]'), sectionName);

                // TODO: implement generic autofill
                Utils.setInputValue(section.querySelector('[data-row-format="sectionname"] input'), propertiesId);

                const menuItem = this.getMenuItemBySection(section);
                this.updateName(menuItem);
            }
            return;
        }

    }
}

/**
 * Items model
 *
 * @param options
 * @constructor
 */
export class ItemsModel extends BaseDocumentModel {

    constructor(modelParent) {
        super(modelParent);
        this.lastId = 0;

        this.listenEvent(document, 'click', event => this.onClick(event), '.doc-item-remove, .doc-item-add');
        this.listenEvent(document, 'changed', event => this.onChanged(event));
        this.listenEvent(document, 'input', event => this.onInput(event));
        this.listenEvent(document, 'keydown', event => this.onKeyDown(event));
        this.listenEvent(document, 'epi:upload:files', event => this.onUploadFiles(event));
        this.listenEvent(document, 'epi:update:item', event => this.onUpdateItem(event));
        this.listenEvent(document, 'epi:import:item', event => this.onImportItem(event));
    }

    /**
     * Get all items within a section filtered by the targets configuration
     *
     * @param {Object} targets The targets configuration
     * @param {HTMLElement} section The parent section
     * @return {Array} A list of nodes with the keys
     *                 table, id, treeId, treeParent, treeLevel, labelName and labelPath
     */
    getTree(targets, section) {
        let nodes = [];

        const items = section.querySelectorAll('.doc-section-item');
        items.forEach(item => {

            // Add the item if requested in targets
            if (targets.items && Array.isArray(targets.items) && targets.items.includes(item.dataset.rowType)) {
                nodes.push({
                    table: item.dataset.rowTable,
                    id: item.dataset.rowId,
                    treeId: item.dataset.rowTable + "-" + item.dataset.rowId,
                    treeParent: section.dataset.rowId ? section.dataset.rowTable + "-" + section.dataset.rowId : null,
                    treeLevel: parseInt(section.dataset.rowLevel || '0') + 2,
                    labelText: `Item ${item.dataset.rowId}`, //TODO: this.getName(item),
                    labelName: `Item ${item.dataset.rowId}`, //TODO: this.getName(item),
                    labelPath: `Item ${item.dataset.rowId}`, //TODO: this.getName(item),
                });
            }
        });

        return nodes;
    }

    /**
     * Click item buttons
     *
     * @param event
     */
    async onClick(event) {
        const sourceInput = event.target;
        if (!this.doc.ownedByDocument(sourceInput)) {
            return;
        }

        // Remove button
        if (event.target.classList.contains('doc-item-remove')) {

            const item = event.target.closest('.doc-section-item');
            if (item) {
                event.preventDefault();
                event.stopPropagation();

                if (await App.confirmAction('Are you sure you want to delete the selected item?')) {
                    this.delete(item);
                }
            }
        }

        // Add button
        else if (event.target.classList.contains('doc-item-add')) {
            event.preventDefault();
            event.stopPropagation();

            const templateItem = event.target.closest('.doc-section-item');
            this.add(templateItem);
            if (event.target.dataset.itemsMax === '1') {
                templateItem.remove();
            }


        }
    }

    /**
     * Update inputs with dropdown data
     *
     * @param event
     */
    updateDropDown(event) {
        if (!event.target.classList.contains('widget-dropdown-selector')) {
            return;
        }

        const docField = event.target.closest('.doc-field');
        if (!docField) {
            return;
        }

        if (docField.dataset.rowFormat === 'property') {
            const inputId = docField.querySelector('input.input-reference-value');
            const inputText = docField.querySelector('input.input-reference-text');
            const inputType = docField.querySelector('input.input-reference-type');

            const inputName = inputId.getAttribute('name');

            const inputTextName = inputName.replace('properties_id', 'newproperty') + '[name]';
            inputText.setAttribute('name', inputTextName);

            if (inputType) {
                const inputTypeName = inputName.replace('properties_id', 'newproperty') + '[propertytype]';
                inputType.setAttribute('name', inputTypeName);
            }
        } else if ((docField.dataset.rowFormat === 'relation') || (docField.dataset.rowFormat === 'record')) {
            const valueInput = docField.querySelector('input.input-reference-value');
            if (valueInput) {
                const value = valueInput.value.split('-');

                const valueTab = value[0] || '';
                const valueId = value.slice(1).join('-');
                Utils.setInputValue(docField.querySelector('input[data-row-field=links_tab]'), valueTab);
                Utils.setInputValue(docField.querySelector('input[data-row-field=links_id]'), valueId);
            }
        }
    }

    /**
     * Changed property handler
     *
     * @param event
     */
    onChanged(event) {
        const sourceInput = event.target;
        if (!this.doc.ownedByDocument(sourceInput)) {
            return;
        }

        if (event.target.classList.contains('widget-dropdown-selector')) {
            this.updateDropDown(event);
        }
        this.onInput(event);
    }

    /**
     * Handle input events
     * - For item changes, fire the epi:change:item event
     *
     * @param event
     */
    onInput(event) {
        const sourceInput = event.target;
        if (!this.doc.ownedByDocument(sourceInput)) {
            return;
        }

        const item = event.target.closest('.doc-section-item');
        this.emitEvent(item, 'epi:change:item',{}, false);
    }

    /**
     * Handle key press events
     * - Enter: Update the item position according to the sort number field
     *
     * @param {Event} event
     */
    onKeyDown(event) {
        if (event.keyCode === 13) {

            const sourceInput = event.target;
            if (!this.doc.ownedByDocument(sourceInput)) {
                return;
            }

            const item = event.target.closest('[data-row-table="items"]');
            this.moveItem(item);
        }
    }

    /**
     * Handle file import events
     *
     * Item import can be triggered by the epi:import:file event.
     *
     * @param event
     */
    onUploadFiles(event) {
        const eventData = event.detail.data;
        if (!eventData || !eventData.listName || !eventData.items) {
            return;
        }

        const folderButton = this.doc.findInDocument('[data-target-list="' + eventData.listName + '"]');
        if (!folderButton) {
            return;
        }

        const templateItem = folderButton.closest('.doc-section-item');
        if (!templateItem) {
            return;
        }

        const existingValues = Array.from(
            templateItem.closest('.doc-section-content').querySelectorAll('.doc-section-item:not([data-deleted]) input[data-itemtype="file"]'),
         input => (
             Utils.extractFileName(input.value || '').toLowerCase()
            )
        );

        const items = eventData.items;
        items.forEach(item => {
            if (!existingValues.includes(item.fileName.toLowerCase())) {
                this.add(templateItem, item, false);
            }
        });
    }

    /**
     * Handle item import events
     *
     * TODO: merge with onUploadFiles()
     *
     * Item import can be triggered by the epi:import:item event.
     * Used for automated coding by the LLM service.
     *
     * The event data has to contain the following properties:
     * - sectiontype
     * - itemtype
     * - items
     *
     * @param event
     */
    onImportItem(event) {

        const eventData = event.detail.data;
        if (!eventData || !eventData.sectiontype || !eventData.itemtype || !eventData.items) {
            return;
        }

        if (eventData.items.length === 0) {
            return;
        }

        const sectionElement = this.doc.findInDocument(
            '[data-row-table="sections"][data-row-type="' + eventData.sectiontype + '"] '
        );
        if (!sectionElement) {
            return;
        }

        // Add multiple non-existing items
        const addButton= sectionElement.querySelector('.doc-section-item .doc-field-add-' + eventData.itemtype);
        if (addButton) {

            const templateItem = addButton.closest('.doc-section-item');
            if (!templateItem) {
                return;
            }

            eventData.items.forEach(item => {
                this.add(templateItem, item, false);
            });

            return;
        }

        // Update a single existing item
        const targetItem = sectionElement.querySelector('[data-row-table="items"][data-row-type="' + eventData.itemtype + '"]');
        if (targetItem) {
            const data = {};
            data['table'] = targetItem.dataset.rowTable;
            data['id'] = targetItem.dataset.rowId;
            data['content'] = eventData.items[0];
            this.emitEvent(targetItem, 'epi:update:item', data);
        }

    }

    onUpdateItem(event) {
        const eventData = event.detail.data;
        if (!eventData || !eventData.table || !eventData.id || !eventData.content) {
            return;
        }

        const item = document.querySelector('[data-row-table="' + eventData.table + '"][data-row-id="' + eventData.id + '"]');
        if (this.doc.ownedByDocument(item)) {
            for (const fieldName in  eventData.content) {

                let fieldElement = item.querySelector( `[data-row-field="${fieldName}"]`);
                if (!fieldElement && (fieldName === 'properties_id')) {
                    fieldElement = item.querySelector(`[data-row-field="property"] input.input-reference-value`);
                }
                else if (!fieldElement && (fieldName === 'properties_label')) {
                    fieldElement = item.querySelector( `[data-row-field="property"] input.input-reference-text`);
                }
                this.setContent(fieldElement, eventData.content[fieldName]);
            }
        }


    }


    /**
     * Delete an item
     *
     * @param {HTMLElement }item The item element to delete
     * @fires {epi:remove:item}
     */
    delete(item) {
        if (item.classList.contains('doc-section-item-first')) {
            const next = Utils.getNextVisibleSibling(item);
            if (next) {
                next.classList.add('doc-section-item-first');
            }
        }

        this.doc.models.annotations.removeFrom(item);

        const fieldDeleted= item.querySelector('input[data-row-field=deleted]');
        if (fieldDeleted) {
            Utils.setInputValue(fieldDeleted, 1);
            item.dataset.deleted = '1';
            Utils.removeInputConstraints(item);
            if (item.dataset.new === '1') {
                this.disableInputs(item);
            }
        } else {
            item.remove();
        }

        // Renumber all sortno fields
        this.updateSortnumbers(item.parentElement);

        // Fire event (and observe it for example in the map widget)
        const event = new Event('epi:remove:item', {bubbles: true, cancelable: false});
        item.dispatchEvent(event);
    }

    /**
     * Move an item to its position
     *
     * @param {HTMLElement} element The item element to move a new position. It needs to have the property data-row-table="items.
     */
    moveItem(element) {
        if (!element) {
            return;
        }

        // const itemContainer = element.closest('.doc-section-groups');
        const newPosition = this.doc.getFieldData(element).rowNumber;
        let curPosition;

        // Move up
        let hasMoved = false;
        let siblingItem = element;
        while (siblingItem = Utils.getPrevVisibleSibling(siblingItem, '[data-row-table="items"]')) {
            if (siblingItem.dataset.rowType !== element.dataset.rowType) {
                break;
            }

            curPosition = this.doc.getFieldData(siblingItem).rowNumber;
            if (curPosition < newPosition) {
                break;
            } else {
                siblingItem.classList.remove('doc-section-item-first');
                siblingItem.insertAdjacentElement('beforebegin', element);
                hasMoved = true;
            }
        }

        // Move down
        if (!hasMoved) {
            siblingItem = element;
            while (siblingItem = Utils.getNextVisibleSibling(siblingItem, '[data-row-table="items"]')) {
                if (siblingItem.dataset.rowType !== element.dataset.rowType) {
                    break;
                }
                curPosition = this.doc.getFieldData(siblingItem).rowNumber;
                if (curPosition > newPosition) {
                    break;
                } else {
                    siblingItem.classList.remove('doc-section-item-first');
                    siblingItem.insertAdjacentElement('afterend', element);
                    hasMoved = true;
                }
            }
        }

        // Renumber all sortno fields
        this.updateSortnumbers(element.parentElement);

        // Update class of first item
        element.classList.remove('doc-section-item-first');
        const firstOfType = element.parentElement.querySelector('[data-row-type="' + element.dataset.rowType + '"]:not([data-deleted="1"]):not([data-preview="1"])');
        if (firstOfType) {
            firstOfType.classList.add('doc-section-item-first');
        }
    }

    /**
     * Update the sort numbers of all items in a section
     *
     * @param {HTMLElement} itemGroup The parent element of the items where sortno fields should be updated
     */
    updateSortnumbers(itemGroup) {
        const items = itemGroup.querySelectorAll('[data-row-table="items"]:not([data-deleted="1"]):not([data-preview="1"])');
        items.forEach((item, index) => {
            Utils.setInputValue(item.querySelector('input[data-row-field="sortno"], [data-row-field="sortno"] input'), index+1);
        });

        itemGroup.classList.toggle('doc-section-groups-empty',items.length === 0);
    }

    setContent(fieldElement, content) {
        if (!fieldElement) {
            return;
        }

        // Update input
        const inputElement = Utils.querySelectorAndSelf(fieldElement, 'input');
        Utils.setInputValue(inputElement, content || '');

        // Update XMLEditor
        const xmlEditorWidget = this.getWidget(fieldElement.querySelector('.doc-field-content'), 'xmleditor');
        if (xmlEditorWidget) {
            xmlEditorWidget.setContent(content);
        }

    }

    /**
     * Create a new item based on a template
     *
     * Fires the itemAdded event.
     *
     * @param {HTMLElement} templateItem An item element with curly bracket placeholders
     *                                   for id, itemsId and sectionsId
     * @param {Object} itemData The new data, including the fields fileName, property and content
     * @param {boolean} focusItem If true, the first input of the new item will be focused
     */
    add(templateItem, itemData, focusItem = true) {

        const section = templateItem.closest('.doc-section, [data-container-table="sections"]');
        if (!section) {
            return;
        }

        const sectionId = section.dataset.rowId || section.dataset.containerId;
        const itemId = this._createId();

        let item = Utils.spawnFromTemplate(
            templateItem.querySelector('.template-doc-section-item'),
            {
                "id": itemId,
                "sections-id": sectionId,
                'rootTable': section.dataset.rootTable,
                'rootId': section.dataset.rootId
            }
        );
        if (!item) {
            console.log('Template not found');
        }

        item.dataset.new = '1';

        // Import content from the ImportFilesWidget and the ServiceButtonWidget
        // TODO: refactor to a more abstract method, not specific to files and content and properties
        if (itemData && itemData.fileName) {
            const fieldName = 'file';
            const fileInput = item.querySelector(`.doc-fieldname-${fieldName} input[data-itemtype="file"]`);
            if (fileInput) {
                // const basePath = Utils.getDataValue(item.closest('[data-file-basepath]'),'fileBasepath');
                const defaultPath = Utils.getDataValue(templateItem.closest('[data-file-defaultpath]'),'fileDefaultpath')
                const filePath = defaultPath + '/' + itemData.fileName;
                Utils.setInputValue(fileInput, filePath);
                fileInput.dataset.path = defaultPath;
            }
        }

        if (itemData && itemData.properties_id) {
            Utils.setInputValue(
                item.querySelector(`.doc-fieldname-property input.input-reference-value`),
                itemData['properties_id']
            );
            Utils.setInputValue(
                item.querySelector(`.doc-fieldname-property input.input-reference-text`),
                itemData['properties_label']
            );
        }

        if (itemData && itemData.content) {
            const fieldName = 'content';
            const inputElm = item.querySelector(`.doc-fieldname-${fieldName} input`);
            if (inputElm) {
                Utils.setInputValue(inputElm, itemData[fieldName]);
            }
        }

        if (itemData && itemData.value) {
            Utils.setInputValue(
                item.querySelector(`.doc-fieldname-value input`),
                itemData['value']
            );
        }

        templateItem.before(item);
        let newItem = templateItem.previousElementSibling;
        const itemGroup = newItem.parentElement;

        // Renumber all sortno fields
        this.updateSortnumbers(itemGroup);

        // Update css classes
        if (templateItem.classList.contains('doc-section-item-first')) {
            templateItem.classList.remove('doc-section-item-first');
            newItem.classList.add('doc-section-item-first');
        }

        // Bind events
        App.initWidgets(newItem);

        // Focus first input (and open dropdown)
        if (focusItem) {
            const inputSelector = 'div[data-row-field]:not([data-row-field="sortno"]) input:not([type="hidden"]), '
                + 'div[data-row-field]:not([data-row-field="sortno"]) select';
            const firstInput = newItem.querySelector(inputSelector);
            if (firstInput) {
                firstInput.focus();
                firstInput.click();
            }
        }

        // Fire event (and observe it for example in the map widget)
        Utils.emitEvent(newItem, 'epi:add:item',{}, false);
    }

    _createId() {
        this.lastId += 1;
        return 'items-int' + this.lastId;
    }
}


/**
 * Files model
 *
 * @param options
 * @constructor
 */
export class FilesModel extends BaseDocumentModel {

    constructor(modelParent) {
        super(modelParent);

        this.listenEvent(document, 'click', event => this.click(event));
    }

    /**
     * Click field handler
     *
     * @param event
     */
    click(event) {
        const field = event.target.closest('.doc-fieldname-file');
        const frame = event.target.closest('.frame-content');
        if (!frame && field) {
            event.preventDefault();
            event.stopPropagation();

            this.show(field);
        }
    }


    /**
     * Show the file
     *
     * @param elm A property field
     */
    show(elm) {
        const elmContent = elm.querySelector('.doc-field-content');
        if (elmContent) {
            this.view(elmContent.dataset.path, elmContent.dataset.fileName);
        }
    }

    /**
     * Open a property view window
     *
     * @param id
     */
    view(filepath, filename) {
        if (!filepath || !filename) {
            return;
        }

        let url = App.databaseUrl + 'files/view?root=root&path=' + filepath + '&filename=' + filename;

        App.openDetails(url, {
            title: "File",
            ajaxButtons: ['submit'],
            external: true
        });
    }
}

/**
 * Property chooser
 *
 * @param options
 * @constructor
 */
export class PropertiesModel extends BaseDocumentModel {

    constructor(modelParent) {
        super(modelParent);

        this.listenEvent(document, 'click', event => this.clickProperty(event));
    }

    /**
     * Click property handler
     *
     * @param event
     */
    clickProperty(event) {
        const field = event.target.closest('.doc-fieldname-property');
        const frame = event.target.closest('.frame-content');
        const dropdown = field ? field.querySelector('.widget-dropdown-selector') : undefined;

        if (field && !frame && !dropdown) {
            event.preventDefault();
            event.stopPropagation();

            const elmContent = field.querySelector('[data-row-field=properties_id]');
            const fieldData = this.doc.getFieldData(elmContent);
            const propertiesId = elmContent ? elmContent.dataset.rowValue : undefined;

            if (fieldData.field && propertiesId) {
                this.view(propertiesId);
            }
        }
    }

    /**
     * Open a property view window
     *
     * @param {String} id The property ID as string
     */
    view(id) {
        if (!id) {
            return;
        }

        const url = App.databaseUrl + 'properties/view/' + id;
        App.openDetails(url, {
            title: "Property",
            ajaxButtons: ['submit'],
            external: true
        });
    }
}

/**
 *  Manages the links and the footnotes model
 *
 *  // TODO: create tag model and split into tags (=inside text) / annotations (=database records)
 *
 * @param modelParent
 * @constructor
 */
export class AnnotationsModel extends BaseDocumentModel {

    constructor(modelParent) {
        super(modelParent);
        this.lastRowId = 0;

        this.listenEvent(document, 'mouseover', event => this.hoverAnno(event), '[data-tagid] .xml_bracket_open, [data-tagid] .xml_bracket_close , .xml_text[data-tagid], .xml_format[data-tagid], [data-from-tagid]');
        this.listenEvent(document, 'mouseout', event => this.unhoverAnno(event), '[data-tagid], [data-from-tagid]');
        this.listenEvent(document, 'click', event => this.onClickAnno(event));
        this.listenEvent(document, 'epi:toggle:switch', event => (this.updatePositions(event.target.closest('.doc-section-links'))));

        this.updatePositions();
    }

    _createRowId() {
        this.lastRowId += 1;
        return 'links-int' + this.lastRowId;
    }

    /**
     * Get tag and link elements from click event
     *
     * @param event
     * @returns {(*|Element)[]}
     * @private
     */
    _getAnnoByEvent(event) {
        let anno = event.target.closest('.doc-section-link');
        let tag;
        let tagid;
        let sectionElement;

        // Check if tag was clicked
        if (!anno) {
            // Get annotation
            tag = event.target.closest('.xml_text, .xml_bracket_open, .xml_bracket_close');
            if (tag) {
                let target = tag.closest('[data-tagid]');
                tagid = target.dataset.tagid;
                sectionElement = this._getSection(target);
                if (sectionElement) {
                    anno = sectionElement.querySelector('.doc-section-link[data-from-tagid="' + tagid + '"]');
                }
            }
        } else {

            // Get tag
            tagid = anno.dataset.fromTagid;
            sectionElement = this._getSection(anno);
            if (sectionElement) {
                tag = sectionElement.querySelector('[data-tagid="' + tagid + '"]');
            }
        }

        return [anno, tag];
    }


    _getAnnoByTagId(tagId, linkContainer) {
        let link;
        if (!linkContainer) {
            link = document.querySelector('.doc-section-links [data-from-tagid="' + tagId + '"]');
        } else {
            link = linkContainer.querySelector('[data-from-tagid="' + tagId + '"]');
        }
        return link;
    }

    /**
     * Get all annotations with the same tag id
     *
     * @param {string} tagId The tag id
     * @param {object} linkContainer The annotation container (next to the content) or undefined to search the whole document
     * @return {NodeListOf<Element>}
     * @private
     */
    _getAnnosByTagId(tagId, linkContainer) {
        let links;
        if (!linkContainer) {
            links = document.querySelectorAll('.doc-section-links [data-from-tagid="' + tagId + '"]');
        } else {
            links = linkContainer.querySelectorAll('[data-from-tagid="' + tagId + '"]');
        }
        return links;
    }

    /**
     * Return the annotation element by a tag element
     *
     * @param tag An element inside of a XMLEditor widget
     * @param linkContainer
     * @returns {Element|undefined}
     * @private
     */
    _getAnnoByTag(tag, linkContainer) {
        return this._getAnnoByTagId(tag.dataset.tagid, linkContainer);
    }

    /**
     * Return the link container of a section
     *
     * @param elm An element inside the section
     * @returns {Element|undefined}
     */
    _getAnnoContainer(elm) {
        let sectionContent = elm.closest('.doc-section-content, .doc-section-note');
        return sectionContent ? sectionContent.querySelector('.doc-section-links') : undefined;
    }

    /**
     * Return the parent section element
     *
     * @param elm An element inside the section
     * @returns {Element|undefined}
     */
    _getSection(elm) {
        return elm.closest('.doc-section-content, .doc-section-note');
    }

    /**
     * Add hover effect
     *
     * @param event
     * @returns {boolean}
     */
    hoverAnno(event) {
        this.unhoverAnno();

        let target = event.target;
        let id = target.dataset.tagid || target.dataset.fromTagid;

        if (!id) {
            target = target.closest('[data-tagid], [data-from-tagid]');
            id = target.dataset.tagid || target.dataset.fromTagid;
        }

        // Set title (= hover text) and tag-hover class
        document.querySelectorAll('.doc-section-link[data-from-tagid="' + id + '"]').forEach(
            function (elm) {
                elm.classList.add('tag-hover');
                const title = Utils.getElementText(elm.querySelector('.doc-section-link-text'));
                document.querySelectorAll('.doc-section-content [data-tagid="' + id + '"]').forEach(
                    function (tag) {
                        tag.classList.add('tag-hover');
                        tag.title = title;
                    }
                );
            }
        );

        event.preventDefault();
        return false;
    }

    /**
     * Remove hover effect
     *
     * @param event
     * @returns {boolean}
     */
    unhoverAnno(event) {
        document.querySelectorAll('.tag-hover').forEach(
            function (elm) {
                elm.classList.remove('tag-hover');
            }
        );

        if (event) {
            event.preventDefault();
        }
        return false;
    }

    /**
     * Reorder annotations to match tag order
     *
     * @param {HTMLElement} section
     * @param {HTMLElement} linkContainer
     */
    orderAnnos(section, linkContainer) {
        const tags = Array.from(section.querySelectorAll('[data-tagid]'));
        let currentLink = linkContainer.querySelector(['data-from-tagid']);

        tags.forEach(tag => {
            const tagid = tag.dataset.tagid;
            const expectedLinks = linkContainer.querySelectorAll('[data-from-tagid="' + tagid + '"]');
            if (!expectedLinks) {
                return;
            }

            for (const expectedLink of expectedLinks) {
                // Remove any spacer before the expected link
                if (expectedLink.previousElementSibling && expectedLink.previousElementSibling.classList.contains('spacer')) {
                    linkContainer.removeChild(expectedLink.previousElementSibling);
                }

                if (expectedLink !== currentLink) {
                    linkContainer.insertBefore(expectedLink, currentLink);
                }
                currentLink = expectedLink.nextElementSibling;
                while (currentLink && currentLink.classList.contains('spacer')) {
                    currentLink = currentLink.nextElementSibling;
                }
            }
        });
    }

    /**
     * Order and shift annotations
     *
     * @param {HTMLElement} linkContainer If empty, all link containers will be updated
     */
    updatePositions(linkContainer) {
        if (!linkContainer) {
            const linkContainers = document.querySelectorAll('.doc-section-links');
            linkContainers.forEach((linkContainer) => {
                this.updatePositions(linkContainer);
                this.initResizeObservers(linkContainer);
            });
            return;
        }

        if (!linkContainer.classList.contains('doc-section-links')) {
            return;
        }

        const section = linkContainer.closest('.doc-section');
        if (section) {
            this.arrangeAnnos(section, linkContainer);
        }

    }

    initResizeObservers(linkContainer) {
        if (!linkContainer.classList.contains('doc-section-links')) {
            return;
        }
        const section = linkContainer.closest('.doc-section');
        if (!section) {
            return;
        }

        this.doc.listenResize(section, entries => {
            for (let entry of entries) {
                const cbSection = entry.target.closest('.doc-section');
                if (cbSection) {
                    const cbLinkContainer = cbSection.querySelector('.doc-section-links');
                    if (cbLinkContainer) {
                        this.updatePositions(cbLinkContainer);                         }
                }
            }
        });
    }

    /**
     * Align annotations in linkContainer with the corresponding tags in section
     *
     * @param {HTMLElement} section The section element containing the tags and annotations
     * @param {HTMLElement} annoContainer The element containing annotation elements
     */
    arrangeAnnos(section, annoContainer) {
        annoContainer.style.position = 'relative';

        const tagsContainer = section.querySelector('.doc-section-content');
        if (!tagsContainer) {
            return;
        }

        const zeroTop = annoContainer.getBoundingClientRect().top;
        const zeroLeft = annoContainer.getBoundingClientRect().left;

        // Start positioning after buttons
        const buttons = annoContainer.querySelectorAll('button');
        let minTop = 0;
        if (buttons.length > 0) {
            const lastButton  = buttons[buttons.length - 1];
            minTop = lastButton.getBoundingClientRect().bottom - zeroTop;
        }

        const tags = section.querySelectorAll('[data-tagid]');

        const marginLeft = 2;
        const marginTop = 2;

        // Collect annotations
        const deltaTop = 10;
        let currentTop = -1;
        let currentBottom = -marginTop;
        let currentLeft = 0;
        let currentRight = 0;
        let targetRight = 0;
        tags.forEach((tag) => {
            const annos = annoContainer.querySelectorAll('[data-from-tagid="' + tag.dataset.tagid + '"]');
            if (annos.length) {
                let targetTop = Math.max(tag.getBoundingClientRect().top - zeroTop, minTop);
                targetTop = deltaTop * Math.floor(targetTop / deltaTop);
                annos.forEach((anno) => {
                    if (Utils.isElementVisible(anno)) {
                        if (targetTop < currentBottom) {
                            currentLeft = currentRight + marginLeft;
                            targetRight = currentLeft + marginLeft + anno.scrollWidth;
                            if (targetRight > annoContainer.clientWidth) {
                                currentTop = currentBottom + marginTop;
                                currentLeft = 0;
                            }
                        } else {
                            currentTop = targetTop;
                            currentLeft = 0;
                        }
                        anno.style.position = 'absolute';
                        anno.style.top = currentTop + 'px';
                        anno.style.left = currentLeft + 'px';
                        currentRight = anno.getBoundingClientRect().right - zeroLeft;
                        currentBottom = anno.getBoundingClientRect().bottom - zeroTop;
                    }
                });
            }
        });
    }

    /**
     * Show annotation properties in the sidebar
     *
     * @param anno {HTMLElement} The annotation element
     */
    show(anno) {

        // Get element
        let toId = anno.dataset.toId;
        let toTab = anno.dataset.toTab;
        let tagId = anno.dataset.fromTagid;
        let tagName = anno.dataset.fromTagname;

        this.doc.models.types.loadTypes().then((types) => {
                let tagConfig = types.links[tagName] || types.footnotes[tagName] || {};
                let targets = Utils.getValue(tagConfig, 'config.fields.to.targets', {});

                if ((toTab === 'articles') && toId) {
                    this.doc.view(toTab + '-' + toId);
                } else if ((toTab === 'sections') && toId) {
                    this.doc.models.sections.view(toId);
                } else if ((toTab === 'properties') && toId) {
                    this.doc.models.properties.view(toId);
                } else if ((tagConfig.scope === 'footnotes') && tagId) {
                    this.doc.models.footnotes.view(tagId);
                }
            }
        );
    }

    /**
     * Edit the annotation properties
     *
     * Called by onClickAnno
     *
     * @param {HTMLElement} annoElement The annotation element (div.doc-section-link inside div.doc-section-links)
     * @param {boolean} isNew Whether the annotation is new, in which case the popup window is only shown for properties.
     */
    edit(annoElement, isNew = true) {

        const tagId = annoElement.dataset.fromTagid;
        const tagSection = this.doc.models.annotations._getSection(annoElement);
        const tagElement = this.doc.models.tags.getTagByTagId(tagId, tagSection);

        if (tagElement) {
            this.modelParent.models.tags.editAnnoOrTag(tagElement, isNew);
        } else {
            this.modelParent.models.tags.editAnnoOrTag(annoElement, isNew);
        }
    }

    /**
     * Remove an annotation
     *
     * @param {HTMLElement} widget The xml editor widget element containing the tag
     * @param {string} tagId The tag ID
     * @return {HTMLElement}
     */
    delete(widget, tagId) {
        const annoContainer = this._getAnnoContainer(widget);
        let annoElements = annoContainer.querySelectorAll('[data-from-tagid="' + tagId + '"]');

        for (const annoElement of annoElements) {
            this.removeAnno(annoElement);
        }

        if (annoContainer) {
            const hasAnnos = annoContainer.querySelector('.doc-section-link:not([data-deleted="1"])');
            annoContainer.classList.toggle('doc-section-links-empty', !hasAnnos);
        }

        return annoElements;
    }

    /**
     * Remove an annotation
     *
     * @param {HTMLElement} annoElement The annotation element
     */
    removeAnno(annoElement) {
        if (annoElement && (annoElement.dataset.deleted !== '1')) {
            Utils.setInputValue(annoElement.querySelector('input[data-row-field=deleted]'), 1);
            annoElement.dataset.deleted = '1';
            if (annoElement.dataset.new) {
                this.disableInputs(annoElement);
            }

            if (annoElement.dataset.rowTable === 'footnotes') {
                this.doc.models.footnotes.delete(annoElement);
            }
        }
    }

    /**
     * Revive an element deleted by removeAnno
     *
     * @param {HTMLElement} annoElement The annotation element
     */
    reviveAnno(annoElement) {
        if (annoElement && (annoElement.dataset.deleted === '1')) {
            Utils.setInputValue(annoElement.querySelector('input[data-row-field=deleted]'), 0);
            annoElement.dataset.deleted = '0';
            if (annoElement.dataset.new) {
                this.enableInputs(annoElement);
            }
        }
    }

    /**
     * Remove a tag
     *
     * @param {HTMLElement} tagElement The tag element within a xml editor widget
     * @return {HTMLElement}
     */
    removeTag(tagElement) {
        const tagContainer = this.modelParent.models.tags.getTagContainer(tagElement);
        const tagEditor = this.doc.getWidget(tagContainer, 'xmleditor', false);
        if (tagEditor) {
            tagEditor.removeTag(tagElement);
        }
    }

    /**
     * Remove all annotations connected to the element
     * Used when deleting a note, item or footnote
     *
     * @param {HTMLElement} element
     */
    removeFrom(element) {
        element.querySelectorAll('[data-tagid]')
            .forEach((tag) => this.delete(tag, tag.dataset.tagid));
    }

    /**
     * Called after inserting footnotes to update the tags
     *
     * @param {HTMLElement} widget The xml editor widget element containing the tag
     * @param {HTMLElement} anno The annotation element inside div.doc-section-links
     * @param {Object} values The values object passed to updateAnnoOrTag
     * @param {Object} typeData The type entity data
     */
    updateCounter(widget, anno, values, typeData) {

        if (widget.ckeditorInstance) {
            this.doc.models.tags.updateAnnoOrTag(anno, values, false, typeData);
        } else {
            const tagId = anno.dataset.fromTagid;
            const tagType = anno.dataset.fromTagname;
            const tagElement = this.modelParent.models.tags.getTagByTagId(tagId, widget);
            if (tagElement) {
                const value = values[tagType] || {};
                tagElement.textContent = value.label || '*';
            }
        }
    }

    /**
     * Add a new property to the annotation
     *
     * @param {HTMLElement} anno The annotation element
     * @param {Object} value The value passed from the dropdown through editAnno()
     */
    addProperty(anno, value) {

        if (value.new) {
            const toInput = anno.querySelector('input[data-row-field=to_id]');
            let toValueInput = toInput.cloneNode();
            toValueInput.dataset.rowField = "to_value";
            toValueInput.value = value.label;
            toValueInput.name = toInput.name.replace('[to_id]', '[to_value]');
            anno.appendChild(toValueInput);

            let toTypeInput = toInput.cloneNode();
            toTypeInput.dataset.rowField = "to_type";
            toTypeInput.value = value.type;
            toTypeInput.name = toInput.name.replace('[to_id]', '[to_type]');
            anno.appendChild(toTypeInput);

            anno.dataset.toNew = '1';
        } else {
            const toValueInput = anno.querySelector('input[data-row-field=to_value]');
            if (toValueInput) {
                toValueInput.remove()
            }
            const toTypeInput = anno.querySelector('input[data-row-field=to_type]');
            if (toTypeInput) {
                toTypeInput.remove()
            }
            anno.removeAttribute('data-to-new');
        }
    }

    /**
     * Click link & tag handler
     *
     * @param event
     */
    onClickAnno(event) {

        const [anno, tag] = this._getAnnoByEvent(event);

        if (anno) {
            event.preventDefault();
            event.stopPropagation();

            // Open editor
            if (this.doc.editMode) {
                return this.edit(anno, false);
            }
            // Show data in sidebar
            else {
                this.show(anno);
            }
        }
    }

}

/**
 * Footnote handling
 *
 * Footnotes are rendered in the sidebar, outside of the document.
 * That's why it is called a satellite of the document.
 *
 * @param {DocumentWidget} modelParent
 * @constructor
 */
export class FootnotesModel extends BaseDocumentModel {

    constructor(modelParent) {
        super(modelParent);
        this.listenEvent(document, 'click', event => this.onClick(event));
    }

    /**
     * Get all footnotes within a document filtered by the targets configuration
     *
     * @param {Object} targets The targets configuration
     * @param {HTMLElement} doc The parent document
     * @return {Array} A list of nodes with the keys
     *                 table, id, treeId, treeParent, treeLevel, labelName and labelPath
     */
    getTree(targets, doc) {
        let nodes = [];

        const container = this.doc.satellites.footnotes.widgetElement;
        if (container) {
            container.querySelectorAll('.doc-footnote').forEach(item => {

                // Add the item if requested in targets
                if (targets.footnotes && Array.isArray(targets.footnotes) && targets.footnotes.includes(item.dataset.rowType)) {
                    nodes.push({
                        table: item.dataset.rowTable,
                        id: item.dataset.rowId,
                        treeId: item.dataset.rowTable + "-" + item.dataset.rowId,
                        treeParent: null,
                        // treeParent: section.dataset.rowId ? section.dataset.rowTable + "-" + section.dataset.rowId : null,
                        treeLevel: 0,
                        labelText: Utils.querySelectorText(item, '.doc-footnote-number'),
                        labelName: i18n.t('Footnote') + ' ' + Utils.querySelectorText(item, '.doc-footnote-number'),
                        labelPath: i18n.t('Footnote') + ' ' + Utils.querySelectorText(item, '.doc-footnote-number'),
                        searchText: Utils.querySelectorText(item, '.doc-footnote-number')
                    });
                }
            });
        }

        return nodes;
    }

    /**
     * Get all footnote annotations by type in the order of the document
     *
     * Iterates all sections, gets all corresponding section containers,
     * and finally all footnote elements in XMLEditor widgets.
     *
     * @param {String} typeName
     * @return {Array} A list of footnote elements
     */
    getAllFootnotes(typeName) {
        let footnotesList = [];
        this.doc.models.sections.getAllSections().forEach((section) => {
            const sectionId = section.dataset.rowId;
            if (sectionId) {

                // Get normal and detached section containers
                const sectionContainers = document.querySelectorAll(
            '[data-row-table="sections"][data-row-id="' + sectionId + '"], ' +
                    '[data-container-table="sections"][data-container-id="' + sectionId + '"]'
                );

                // TODO: get footnotes in detached items
                sectionContainers.forEach((sectionContainer) => {
                    const sectionFootnotes = sectionContainer.querySelectorAll('.widget-xmleditor [data-type=' + typeName + ']');
                    footnotesList =  [...footnotesList].concat([...sectionFootnotes]);
                });
            }
        });
        return footnotesList;
    }

    /**
     * Get the footnote element from the bottom of the doc
     *
     * @param tagId
     * @return {HTMLElement}
     */
    getFootnoteElement(tagId) {
        const selector = '[data-tagid="' + tagId + '"]';
        const container = this.doc.satellites.footnotes.widgetElement;
        return container ? container.querySelector(selector) : undefined;
    }

    /**
     * Get the footnote annotation of the content that contains the footnote
     *
     * @param tagId
     * @return {*}
     */
    getFootnoteAnno(tagId) {
        return this.doc.models.annotations._getAnnoByTagId(tagId);
    }

    /**
     * Get the footnote annotation of the content that contains the footnote
     *
     * @param tagId
     * @return {*}
     */
    getFootnoteTag(tagId) {
        return this.modelParent.models.tags.getTagByTagId(tagId);
    }

    /**
     * Update the footnote numbers after footnotes have been added or deleted
     *
     * // TODO: update in XML fields as well
     *
     * @param {string} typeName The footnote type (from_tagname) to update
     */
    updateNumbers(typeName) {
        // Call for all types
        if (typeName === undefined) {
            // TODO: get all footnote types and call updateNumbers for each type
            return;
        }

        const self = this;
        this.doc.models.types.loadTypes('footnotes').then(
            (types) => {
                const typeData = types[typeName] || {};

                // Update footnotes of the type
                const footnotes = this.getAllFootnotes(typeName);

                let counter = 0;
                footnotes.forEach(
                    function (elm) {
                        // Count up
                        counter += 1;

                        const widget = self.doc.models.tags.getTagContainer(elm);
                        const anno = self.doc.models.annotations._getAnnoByTag(elm);

                        const label = Utils.numberToString(counter, Utils.getValue(typeData, 'config.fields.name.counter', 'numeric'));
                        const value = {label: label, tab: anno.dataset.toTab, id: anno.dataset.toId};

                        // Update tag and annotation
                        self.doc.models.annotations.updateCounter(widget, anno, { typeName: value}, typeData);

                        // Move annotation to bottom
                        anno.parentElement.appendChild(anno);

                        // Update footnote, move footnote to bottom
                        const footnote = self.getFootnoteElement(elm.dataset.tagid);
                        Utils.setElementText(footnote.querySelector('.doc-footnote-number'), value.label);
                        Utils.setInputValue(footnote.querySelector('[data-row-field="name"]'), value.label);
                        footnote.parentElement.appendChild(footnote);
                    }
                );

            });

        // TODO: move unmatched footnotes to bottom
    }

    /**
     * Show the footnote in the sidebar
     *
     * @param tagId
     */
    view(tagId) {
        this.doc.satellites.footnotes.view();
        const footnote = this.getFootnoteElement(tagId);
        if (footnote) {
            const section = footnote.closest('.doc-section');
            App.switchbuttons.switchButton(section.querySelector('.doc-section-head'), true);
            // Utils.emitEvent(section, 'epi:focus:section', {}, this);

            const container = footnote.closest('.widget-tabsheets-sheets');
            Utils.scrollIntoViewIfNeeded(footnote, container);
        }
    }

    edit(tagId) {
        this.doc.satellites.footnotes.view();
        const footnote = this.getFootnoteElement(tagId);
        if (footnote) {
            const section = footnote.closest('.doc-section');
            App.switchbuttons.switchButton(section.querySelector('.doc-section-head'), true);

            const container = footnote.closest('.widget-tabsheets-sheets');
            Utils.scrollIntoViewIfNeeded(footnote, container);

            const editorWidget = App.findWidget(footnote, 'xmleditor');
            if (editorWidget) {
                editorWidget.activateEditor();
            }
        }
    }


    /**
     * Add a footnote to the document, including the annotation
     *
     * @param {HTMLElement} widget The CkEditor (div.widget-xmleditor)
     * @param {object} tagAttributes The attributes of the created element, including:
     *                               - data-tagid The tag id
     *                               - data-type The link or footnote type
     *                               - data-new Whether the element was created by the user using the toolbar
     * @param {Object} tagSet
     * @return {HTMLElement}
     */
    add(widget, tagAttributes, tagSet) {
        const tagId = tagAttributes['data-tagid'];
        const typeName = tagAttributes['data-type'];

        const annoContainer = this.doc.models.annotations._getAnnoContainer(widget);
        const annoElement = this.doc.models.annotations._getAnnoByTagId(tagId, annoContainer);
        let fieldData = this.doc.getFieldData(widget);

        let footnoteElement = this.getFootnoteElement(tagId);
        if (!footnoteElement) {
            const footnoteContainer = this.doc.satellites.footnotes.widgetElement
                .querySelector('.doc-section-footnotes[data-row-type="' + typeName + '"] .doc-section-content .doc-footnotes');
            if (!footnoteContainer) {
                App.showMessage('The footnote container for ' + typeName + ' is missing, check the article configuration.');
                return footnoteElement;
            }

            let caption = Utils.getValue(
                tagSet, typeName + '.config.html.content',
                Utils.getValue(tagSet, typeName + '.config.html_content',
                    Utils.getValue(tagSet, typeName + '.config.content', '*')
                )
            );

            // const tagElement = this.doc.models.annotations.getTagByTagId(tagId, widget);
            // const segment = tagElement ? tagElement.textContent : '';

            const annoId = annoElement.dataset.rowId || this.doc.models.annotations._createRowId();
            let footnoteContent = {
                idx: annoId,
                id: annoId,
                // id: "",
                deleted: 0,
                type: typeName,

                rootId: fieldData.rootId,
                rootTab: fieldData.rootTab,
                fromId: fieldData.id,
                fromTab: fieldData.table,
                fromField: fieldData.field,
                fromTagid: tagId,
                fromTagname: typeName,

                displayname: caption,
            };

            // Add content
            for (const fieldName of ['content', 'segment']) {

                footnoteContent[fieldName] = '';

                // Auto fill from selected text
                let autoFill = Utils.getValue(tagSet, typeName + '.config.fields.' + fieldName+ '.autofill', false);
                if (autoFill) {

                    if (typeof autoFill !== 'object') {
                        autoFill = { enabled: autoFill };
                    }
                    footnoteContent[fieldName] = tagAttributes['data-selected'] || '';

                    if (autoFill.wrap && (footnoteContent[fieldName] !== '')) {
                        const wrapType = autoFill.wrap;
                        const wrapConfig = Utils.getValue(tagSet, wrapType + '.config');
                        if (wrapConfig && (wrapConfig.tag_type === 'format')) {
                            const wrapTag = Utils.getValue(wrapConfig, 'html.tag', 'span');
                            footnoteContent[fieldName] =
                                '<' + wrapTag + ' class="xml_tag xml_format xml_tag_' + wrapType + '" data-type="' + wrapType+ '">' +
                                footnoteContent[fieldName] +
                                '</' + wrapTag + '>';
                        }
                    }
                    if (autoFill.postfix) {
                        footnoteContent[fieldName] = footnoteContent[fieldName] + autoFill.postfix;
                    }
                    if (autoFill.prefix) {
                        footnoteContent[fieldName] = autoFill.prefix + footnoteContent[fieldName];
                    }
                }
            }

            // Spawn footnote
            const newFootnote = this.doc.satellites.footnotes.spawnFromTemplate(
                'template-footnote-' + typeName,
                footnoteContent
            );

            if (!newFootnote) {
                App.showMessage('The footnote template for ' + typeName + ' is missing, check the article configuration.');
                return footnoteElement;
            }

            footnoteContainer.appendChild(newFootnote);
            footnoteElement = this.getFootnoteElement(tagId);
            footnoteElement.dataset.new = '1';

            // Numbering
            this.updateNumbers(typeName);

            // Bind events
            App.initWidgets(footnoteElement);

        } else if (footnoteElement) {
            Utils.setInputValue(footnoteElement.querySelector('input[data-row-field=deleted]'), 0);
            footnoteElement.dataset.deleted = '0';
            this.enableInputs(footnoteElement);
        }

        return footnoteElement;
    }

    /**
     * Remove all footnotes with annotations in the container
     * Used when deleting a section
     *
     * @param {HTMLElement} container
     */
    removeIn(container) {
        container.querySelectorAll('.doc-section-link[data-row-table="footnotes"]')
            .forEach((annotation) => this.delete(annotation));
    }

    /**
     * Remove all footnotes connected to the element
     * Used when deleting a note or item
     *
     * @param {HTMLElement} element
     */
    removeFrom(element) {
        element.querySelectorAll('[data-tagid]')
            .forEach((tag) => this.delete(tag.dataset.tagid));
    }

    /**
     * Delete the footnote element
     *
     * @param {Element|string} annoElement The annotation element that links the footnote or the tagid
     * @returns {undefined}
     */
    delete(annoElement) {
        const tagId = typeof annoElement === 'string' ? annoElement : annoElement.dataset.fromTagid;
        const footnoteElement = this.getFootnoteElement(tagId);

        if (footnoteElement && (footnoteElement.dataset.deleted !== '1')) {
            // Remove annotations in the footnote
            this.doc.models.annotations.removeFrom(footnoteElement);

            // Remove footnote element
            footnoteElement.dataset.deleted = '1';
            Utils.setInputValue(footnoteElement.querySelector('input[data-row-field=deleted]'), 1);
            if (footnoteElement.dataset.new === '1') {
                this.disableInputs(footnoteElement);
            }

            // Remove footnote tag
            const footnoteTag = this.getFootnoteTag(tagId);
            if (footnoteTag) {
                this.doc.models.annotations.removeTag(footnoteTag);
            }

            // Remove footnote annotation
            const footnoteAnno = this.getFootnoteAnno(tagId);
            if (footnoteAnno) {
                this.doc.models.annotations.removeAnno(footnoteAnno);
            }

            // Update footnote numbers
            const footnoteType = footnoteElement.dataset.rowType;
            this.updateNumbers(footnoteType);
        }
    }

    /**
     * Handle clicks on footnote numbers and remove buttons
     *
     * @param event
     */
    async onClick(event) {

        // Remove button
        if (event.target.classList.contains('doc-item-remove')) {

            const footnote = event.target.closest('.doc-footnote');
            if (footnote) {
                event.preventDefault();
                event.stopPropagation();

                if (await App.confirmAction('Are you sure you want to delete the selected footnote?')) {
                    this.delete(footnote.dataset.tagid);
                }
            }
        }

        // Jump to footnote
        if (event.target.classList.contains('doc-footnote-number')) {

            const footnote = event.target.closest('.doc-footnote');
            const footnoteId = footnote ? footnote.dataset.tagid : undefined;
            const footnoteTag = this.getFootnoteTag(footnoteId);

            if (footnoteTag) {
                const container = footnoteTag.closest('.content-content');
                const section = footnoteTag.closest('.doc-section');

                // TODO: use events or sections model?
                App.switchbuttons.switchButton(section.querySelector('.doc-section-head'), true);
                Utils.scrollIntoViewIfNeeded(footnoteTag, container);
            }
        }
    }
}


/**
 * Link handling.
 *
 * Links are a type of annotation handled by the annotation model.
 *
 * @param options
 * @constructor
 */
export class LinksModel extends BaseDocumentModel {

    /**
     * Get selected link data
     *
     * TODO: refactor, move a level up?
     *
     * The link data is an object with the following keys:
     * - id   The target row ID
     * - tab  The target table name
     * - type The target row type
     * - label The target caption
     * - new  Whether this links to a row not yet created (invivo properties)
     *
     * @param {HTMLElement} link The annotation
     * @return {Object}  The link data
     */
    getAttributes(link) {

        // TODO: rename id to toId and tab to toTab
        const selected = {
            id: link.dataset.toId,
            tab: link.dataset.toTab,
            type: link.dataset.toType,
            label : undefined,
            new : undefined
        };

        selected.new = Utils.isTrue(link.dataset.toNew);
        if (selected.new) {
            selected.label = Utils.getInputValue(link.querySelector('[data-row-field="to_value"]'));
            selected.type = Utils.getInputValue(link.querySelector('[data-row-field="to_type"]'));
        } else if (link.dataset.toId) {
            selected.label = Utils.getElementText(link.querySelector('.doc-section-link-text'));
        }

        return selected;
    }

    /**
     * Get selected link data and possible targets from the config
     *
     * TODO: refactor, move a level up?
     *
     * @param {HTMLElement} link The annotation
     * @return {Array}  Target list
     */
    getTargets(anno) {

        let tagName = anno.dataset.fromTagname;
        let targets = [];

        const types = this.modelParent.models.types._types;
        if (types.links || types.footnotes) {
            let tagConfig = types.links[tagName] || types.footnotes[tagName];
            if (types.footnotes && (types.footnotes[tagName] !== undefined)) {
                targets = {'footnotes' : [tagName]};
            } else {
                targets = Utils.getValue(tagConfig, 'config.fields.to.targets', {});
            }
        }

        return targets;
    }

    /**
     * Get tag config of the link type
     *
     * @param {HTMLElement} anno The annotation
     * @return {Object} The types config
     */
    getConfig(anno) {
        const tagName = anno.dataset.fromTagname;
        const types = this.modelParent.models.types._types;
        if (types.links || types.footnotes) {
            return types.links[tagName] || types.footnotes[tagName];
        }
    }

    /**
     * Add an annotation next to the content field
     *
     * May be a links or a footnotes annotation.
     *
     * @param {String} annoType
     * @param {object} tagAttributes The attributes of the created element, including:
     *                               - data-tagid The tag id
     *                               - data-type The link or footnote type
     *                               - data-new Whether the element was created by the user using the toolbar
     * @param {Object} tagSet
     * @param {Object} fieldData
     * @returns The new link element
     */
    add(annoType, tagAttributes, tagSet, fieldData) {

        const tagId = tagAttributes['data-tagid'];
        const tagType = tagAttributes['data-type'];
        const scope = Utils.getValue(tagSet, tagType + '.scope');
        const tagGroup = Utils.getValue(tagSet, tagType + '.config.group');

        const linkConfig = Utils.getValue(tagSet, annoType + '.config');

        // Molecular annotations have attributes prefixed with the property type
        let toType = Utils.getValue(linkConfig,'fields.to.targets.properties.0');

        let toValue;
        let toTab;
        let toId;

        if (toType) {
            const attrPrefix = 'data-link-' + toType;
            toValue = Utils.getValue(tagAttributes, attrPrefix + '-value', '');
            toTab = Utils.getValue(tagAttributes, attrPrefix + '-tab', '');
            toId = Utils.getValue(tagAttributes, attrPrefix + '-id', '');
        }

        // Atomic annotations have attributes prefixed with 'data-target-';
        else {
            const attrPrefix = 'data-target';
            toValue = Utils.getValue(tagAttributes, attrPrefix + '-value', '');
            toTab = Utils.getValue(tagAttributes, attrPrefix + '-tab', '');
            toId = Utils.getValue(tagAttributes, attrPrefix + '-id', '');
            toType = Utils.getValue(tagAttributes, attrPrefix + '-type', '');
        }

        // Fallback
        if (!toValue) {
            toValue = Utils.getValue(tagSet, annoType + '.caption', 'UNDEFINED');
            // toValue = Utils.getValue(
            //     tagAttributes, 'data-value',
            //     Utils.getValue(tagSet, annoType + '.caption', 'UNDEFINED')
            // );
        }

        const annoId = this.doc.models.annotations._createRowId();
        const annoElement = this.doc.spawnFromTemplate(
            'template-annotation-' + scope,
            {
                scope: scope,
                idx: annoId,
                id: annoId,
                group: tagGroup,
                // id: "",
                // type: linkType,

                rootId: fieldData.rootId,
                rootTab: fieldData.rootTab,
                fromId: fieldData.id,
                fromTab: fieldData.table,
                fromField: fieldData.field,
                fromTagid: tagId,
                fromTagname: tagType,

                // Only for links, not for footnotes
                toId: toId,
                toTab: toTab,
                toValue: toValue,
                toType: toType,

                deleted: 0
            }
        );

        return annoElement;
    }

}

/**
 *  Manages tag attributes
 *
 * @param modelParent
 * @constructor
 */
export class TagsModel extends BaseDocumentModel {

    /**
     * Get annotation attributes
     *
     * @param {HTMLElement} linkContainer
     * @param {string} tagId
     * @return {Object}
     */
    getLinkAttributes(linkContainer, tagId, tagConfig) {
        const self = this;
        let attributes = {};

        const annos = linkContainer.querySelectorAll('[data-from-tagid="' + tagId + '"]');
        for (const anno of annos) {
            const annoType = anno.dataset.toType;
            const annoDeleted = anno.dataset.deleted === '1';
            if (annoType && !annoDeleted) {
                attributes['data-links-' + annoType] = self.modelParent.models.links.getAttributes(anno);
            }
        }

        return attributes;
    }

    /**
     * Get tag attributes
     *
     * @param {Object} editor The ckeditor instance
     * @param {string} tagId
     * @param {Object} tagConfig
     * @return {Object}
     */
    getTagAttributes(editor, tagId, tagConfig) {

        // TODO: move calls to plugins.get('XmlTagEditing') into editors.js
        let tagAttributes = {};
        if (Utils.getValue(tagConfig, 'config.attributes', false)) {
            const editing = editor.plugins.get('XmlTagEditing');
            tagAttributes = editing.tagGetModelAttributes(editor, tagId);
        }
        return tagAttributes;
    }

    /**
     * Return the tag inside the XML editor
     *
     * @param tagId The tag id
     * @param {HTMLElement} widget The CKEditor editor (div.widget-xmleditor).
     * @return {HTMLElement} The tag
     * @protected
     */
    getTagByTagId(tagId, widget) {
        let tagElement;

        if (!widget) {
            tagElement = this.modelParent.widgetElement.querySelector(
                '.widget-xmleditor [data-tagid="' + tagId + '"], ' +
                '.doc-field-content [data-tagid="' + tagId + '"]'
            );
        } else {
            tagElement = widget.querySelector('[data-tagid="' + tagId + '"]');
        }

        return tagElement;
    }


    /**
     * Return the xml editor widget element of a tag
     *
     * @param {HTMLElement} tagElement An element inside the widget
     * @returns {Element|undefined}
     */
    getTagContainer(tagElement) {
        return tagElement ? tagElement.closest('.widget-xmleditor') : undefined;
    }


    /**
     * Create tag handler
     *
     * Called from the editor everytime an element is created.
     * Creates annotations in the link container.
     * Creates footnotes.
     *
     * @param {HTMLElement} widget The CkEditor (div.widget-xmleditor)
     * @param {object} tagAttributes The attributes of the created element, including:
     *                               - data-tagid The tag id
     *                               - data-type The link or footnote type
     *                               - data-new Whether the element was created by the user using the toolbar
     * @returns Promise
     */
    onCreateTag(widget, tagAttributes) {
        return this.doc.models.types.getTagSet(widget, false).then(
            (tagSet) => {
                const annoModel = this.modelParent.models.annotations;
                const linksModel = this.modelParent.models.links;
                const footnotesModel = this.modelParent.models.footnotes;

                const tagId = tagAttributes['data-tagid'];
                const tagType = tagAttributes['data-type'];
                const scope = Utils.getValue(tagSet, tagType + '.scope');

                // Add annotations
                const annoContainer = annoModel._getAnnoContainer(widget);
                if (!annoContainer) {
                    console.log('Annotation container not found.');
                    return;
                }
                annoContainer.classList.remove('doc-section-links-empty');

                // Get annotation types
                // A molecular tag config can be linked to multiple annotations in its attributes
                const tagAttributesConfig = Utils.getValue(tagSet, tagType + '.config.attributes', {});
                let annoTypes = Object.values(tagAttributesConfig)
                    .filter(entry => entry.input === "link")
                    .map(entry => entry.type);
                const isMolecule = annoTypes.length > 0;

                // An atomic tag config is linked to one annotation
                if (!isMolecule) {
                    annoTypes = [tagType];
                }

                // Update annotations (get existing or create new)
                let annoElements = annoModel._getAnnosByTagId(tagId, annoContainer);
                let leftoverElements = [];
                if (isMolecule) {
                    annoElements = Utils.groupElements(annoElements, 'toType');
                    leftoverElements = Utils.getValue(annoElements, '',leftoverElements);
                }

                let fieldData = this.modelParent.getFieldData(widget);
                annoTypes.forEach((annoType, annoIdx) => {

                    let annoElement;
                    if (isMolecule) {
                        const propertyType =  Utils.getValue(tagSet, annoType + '.config.fields.to.targets.properties.0');
                        annoElement = Utils.getValue(annoElements, propertyType + '.0');

                        // Recylce annos
                        if ((!annoElement) && (leftoverElements.length > 0)) {
                            annoElement = leftoverElements.pop();
                            annoElement.dataset.toType = propertyType;
                        }
                    } else {
                        annoElement = annoElements[0];
                    }

                    if (!annoElement) {
                        annoElement = linksModel.add(annoType, tagAttributes, tagSet, fieldData);
                        if (annoElement) {
                            annoContainer.appendChild(annoElement);
                            annoElement.dataset.new = '1';
                        }
                    } else {
                        if (fieldData.deleted !== '1') {
                            Utils.setInputValue(annoElement.querySelector('input[data-row-field=deleted]'), 0);
                            annoElement.dataset.deleted = '0';
                            this.enableInputs(annoElement);
                        }
                    }
                });

                // Update footnotes
                if (scope === 'footnotes') {
                    footnotesModel.add(widget, tagAttributes, tagSet);
                }

                // Show editor
                const isNew = tagAttributes['data-new'];
                const tagElement = this.getTagByTagId(tagId, widget);
                if (isNew && (tagElement)) {
                    annoModel.updatePositions(annoContainer);
                    this.editAnnoOrTag(tagElement, isNew);
                }
            }
        );
    }

    /**
     * Will be called from the editor everytime an element is deleted.
     * Hides the link in the link container.
     *
     * @param {HTMLElement} widget The CkEditor (div.widget-xmleditor)
     * @param {object} tagAttributes The attributes of the created element, including:
     *                              - data-tagid The tag id
     *                              - data-type The link or footnote type
     */
    onRemoveTag(widget, tagAttributes) {
        const tagId = tagAttributes['data-tagid'];
        this.doc.models.annotations.delete(widget, tagId);
    }


    /**
     * Edit the annotation properties
     * - links to properties, sections, footnotes, articles
     * - footnote content
     * - attributes
     *
     * Called by onClickAnno and onCreateTag
     *
     * @param {HTMLElement} annoOrTag The annotation or tag element
     * @param {boolean} isNew Whether the annotation is new, in which case the popup window is only shown for properties.
     */
    editAnnoOrTag(annoOrTag, isNew = true) {

        this.doc.models.types.loadTypes().then(
            (types) => {

                // Get type and config
                const annoOrTagType = annoOrTag.dataset.type ?
                    annoOrTag.dataset.type :  // From tag
                    annoOrTag.dataset.fromTagname;  // From anno

                const typeData = types.links[annoOrTagType] || types.footnotes[annoOrTagType];

                // Determine target model
                const isFootnote = types.footnotes && (types.footnotes[annoOrTagType] !== undefined);
                const toFormat = Utils.getValue(typeData, 'config.fields.to.format');
                const toTargets = Object.keys(Utils.getValue(typeData, 'config.fields.to.targets', {}));
                const hasAttributes = Utils.getValue(typeData, 'config.attributes') !== undefined;

                // Footnotes
                let method;
                if (isFootnote) {
                    method = this.editFootnote;
                }

                // Properties
                else if (toTargets.includes('properties')) {
                    method = this.editProperty;
                }

                // External links
                else if (toFormat === 'record') {
                    method = this.editRecord;
                }

                // Internal links
                else if (toFormat === 'relation') {
                    method = this.editRelation;
                }

                // Tag attributes (including molecule annotations)
                else if (hasAttributes || !isNew) {
                    method = this.editAttributes;
                }

                if (method) {
                    method.call(
                        this,
                        annoOrTag,
                        (value) => this.updateAnnoOrTag(annoOrTag, value, true, typeData),
                        isNew
                    );
                }
            }
        );
    }


    /**
     * Called by edit to update the annotation and the tag
     * after annotation properties have been edited
     *
     * @param {HTMLElement} tagOrAnno The tag (inside ckeditor) or annotation element (div.doc-section-link inside div.doc-section-links)
     * @param {Object|string|null} values The values object (to update the annotation and tag) or null (to remove the annotation and tag).
     *              In the values object,  keys starting with 'attr-' will be set on the tag.
     *              All other keys will be treated as link values.
     *              Each link value is an object with the following keys:
     *              - label The text that will be displayed in the annotation and as text content of the tag
     *                      All labels from the link values will be joined.
     *              - tab The target tab of the link (e.g. properties)
     *              - type The to_type property of the link
     *              - id The target ID of the link

     *              If the values object is a string, it will be parsed as JSON and converted to an object.

     * @param {boolean} focus Whether the element should get focus
     * @param {Object} typeData The type entity data
     */
    updateAnnoOrTag(tagOrAnno, values, focus, typeData) {
        // Convert to JSON
        if (typeof values === 'string') {
            try {
                values = JSON.parse(decodeURI(values));
            } catch (e) {
                console.log(e.toString());
                values = {};
            }
        }

        // Get tag elements
        const tagId = (tagOrAnno.dataset.fromTagid) ?
            tagOrAnno.dataset.fromTagid :
            tagOrAnno.dataset.tagid;

        const tag = (tagOrAnno.dataset.fromTagid) ?
            this.getTagByTagId(tagId, this.doc.models.annotations._getSection(tagOrAnno)) :
            tagOrAnno;

        const tagContainer = this.getTagContainer(tag);

        // Get annotations
        const annoContainer = this.modelParent.models.annotations._getAnnoContainer(tagOrAnno);
        const annos = this.modelParent.models.annotations._getAnnosByTagId(tagId, annoContainer);
        let tagAttributes = {};

        // Remove annotation
        if (values === null) {
            for (const anno of annos) {
                this.doc.models.annotations.removeAnno(anno);
            }
            tagAttributes = null;
        }

        // Update annotation
        else {

            let annoIdx = 0;
            let labels = [];
            for (const [key, value] of Object.entries(values)) {

                if (key.startsWith('attr-')) {
                    tagAttributes[key.slice(5)] = value; // removes "attr-"
                }
                else {
                    // Tag
                    if (value.label !== undefined) {
                        labels.push(value.label)
                    }

                    // Anno
                    let anno = annos[annoIdx];
                    if (anno) {
                        if (value.label !== undefined) {
                            let label = value.label;
                            if ((label === '') && typeData) {
                                label = typeData.caption || '';
                            }

                            anno.querySelector('span').textContent = label;
                            anno.title = label;
                        }

                        if (value.tab !== undefined) {
                            anno.dataset.toTab = value.tab;
                            Utils.setInputValue(anno.querySelector('input[data-row-field=to_tab]'), value.tab);
                        }

                        if ((value.type !== undefined) && (value.type !== "undefined")) {
                            anno.dataset.toType = value.type;
                            Utils.setInputValue(anno.querySelector('input[data-row-field=to_type]'), value.type);
                        }

                        if (value.id !== undefined) {
                            anno.dataset.toId = value.id;
                            Utils.setInputValue(anno.querySelector('input[data-row-field=to_id]'), value.id);
                        }

                        this.doc.models.annotations.reviveAnno(anno);

                        this.modelParent.models.annotations.addProperty(anno, value);
                        annoIdx += 1;
                    }
                }

            }

            // Remove obsolete annos
            for (let i = annoIdx; i < annos.length; i++) {
                this.doc.models.annotations.removeAnno(annos[i]);
            }

            // Set tag title
            if (labels.length > 0) {
                tagAttributes['label'] = labels.join(' / ');
            }
        }

        // Update tag attributes
        const tagEditor = this.doc.getWidget(tagContainer, 'xmleditor', false);

        if (tagEditor) {
            tagEditor.updateTag(tagId, tagAttributes, true);
        }

    }

    /**
     * Open a window to set tag attributes or to remove the tag
     *
     * //TODO: split up the function
     *
     * @param {HTMLElement} annoOrTag The annotation element or the tag
     * @param {function} setValue Called to change the value of the annotation and the tag.
     *                            First parameter is the new value or null to remove annotation and tag.
     * @param {boolean} isNew Whether the annotation was just created
     */
    editAttributes(annoOrTag, setValue, isNew) {
        const self = this;

        // Get the tag (it contains all data, atomic and molecular
        let tagId;
        let tagElement;
        if (annoOrTag.classList.contains('doc-section-link')) {
            tagId = annoOrTag.dataset.fromTagid;
            tagElement = this.getTagByTagId(tagId, this.doc.models.annotations._getSection(annoOrTag));
        } else {
            tagId = annoOrTag.dataset.tagid;
            tagElement = annoOrTag;
        }
        const tagContainer = this.getTagContainer(tagElement);
        const editorWidget = this.doc.getWidget(tagContainer, 'xmleditor', false);


        this.doc.models.types.loadTypes().then(
            (types) => {

                if (editorWidget && tagElement && tagContainer) {
                    editorWidget.createEditor().then(editor => {
                            const tagType = tagElement.dataset.type;
                            const tagConfig = types.links[tagType] || types.footnotes[tagType];
                            const linkContainer = this.modelParent.models.annotations._getAnnoContainer(annoOrTag);

                            const tagAttributes = self.getTagAttributes(editor, tagId, tagConfig);
                            const linkAttributes = self.getLinkAttributes(linkContainer, tagId, tagConfig);
                            const moleculeAttributes = {...tagAttributes, ...linkAttributes};

                            self.openAttributesDialog(tagConfig, setValue, isNew, tagContainer, moleculeAttributes);
                        });
                }

                // Why? Broken annotations with missing tags?
                else if (annoOrTag.dataset.rowType) {
                    const annoType = annoOrTag.dataset.rowType;
                    const annoConfig = types.links[annoType] || types.footnotes[annoType];
                    self.openAttributesDialog(annoConfig,  setValue, isNew);
                }
            });
    }


    /**
     * Show footnote editor
     *
     * @param annoOrTag {HTMLElement} The annotation element
     * @param callback {function} Called when the value was changed
     * @param isNew {boolean} Whether the annotation was just created
     */
    editFootnote(annoOrTag, callback, isNew) {
        if (annoOrTag) {
            this.doc.models.footnotes.edit(annoOrTag.dataset.tagid);
        }
    }

    /**
     * Open a property choose window
     *
     * @param annoOrTag {HTMLElement} The annotation or tag element
     * @param callback {function} Called when the value was changed
     * @param isNew {boolean} Whether the annotation was just created
     */
    editProperty(annoOrTag, callback, isNew) {

        let anno = annoOrTag;
        if (!anno.classList.contains('doc-section-link')) {
            anno = this.modelParent.models.annotations._getAnnoByTag(
                annoOrTag,
                this.doc.models.annotations._getAnnoContainer(annoOrTag)
            )
            if (!anno) {
                return
            }
        }

        // @var selected The currently selected ID
        const selected = this.modelParent.models.links.getAttributes(anno);
        const selectedId = ((selected.id !== undefined) && (!selected.new)) ? selected.id : undefined;

        // @var targets A list of allowed property types
        const targets = this.modelParent.models.links.getTargets(anno);

        const propertytype = Utils.getValue(targets, 'properties.0');
        if (propertytype === undefined) {
            return;
        }

        let url = App.databaseUrl + 'properties/index/' + propertytype + '?template=input&show=content';
        url = (selectedId !== undefined) ? url + '&seek=' + selectedId : url;

        if (selected.new && (selected.value !== undefined)) {
            url = url + '&append=1&find=' + selected.value;
        }

        // Get anno configuration
        let tagConfig = this.modelParent.models.links.getConfig(anno);

        const limitProperties = Utils.getValue(tagConfig, 'config.fields.to.limit');
        if ((limitProperties === 'article') && (this.rootTable === 'articles') && this.rootId) {
            url = url + '&articles.field=id&articles.term=' + this.rootId;
        }
        if ((limitProperties === 'project')  && this.doc.models.articles){
            const projectId = this.doc.models.articles.getProjectId();
            if (projectId) {
                url = url + '&projects=' + projectId;
            }
        }


        // Allow empty or not
        const required = Utils.getValue(tagConfig,'config.fields.to.required', true);
        if (!required) {
            url = url + '&empty=1';
        }

        if (Utils.getValue(tagConfig,'config.fields.to.append', false)) {
            url = url + '&append=1';
        }

        // We don't need to manage fields here, as we have the external manage button in the popup
        // if (Utils.getValue(tagConfig,'config.fields.to.manage', false)) {
        //     url = url + '&manage=1';
        // }

        // External manage URL
        let urlManage = App.databaseUrl + 'properties/index/' + propertytype;
        urlManage = (selectedId !== undefined) ? (urlManage + '?seek=' + selectedId) : urlManage;

        const options = {
            title: "Select property",
            height: 500,
            width: 600,

            focus: true,
            openDropdown: true,

            url: url,
            external: urlManage,
            selected: selected,
            required : required,

            removeOnCancel: isNew,
            buttonRemove: !isNew,

            onRemove: () => callback(null),
            onSelect: (value) => callback(
                value === null ? null : {
                    propertytype : {
                        'tab': 'properties',
                        'id': value.value,
                        'type': value.type,
                        'new': (value.new || false) == true, // Will be implicitly converted to boolean by '=='
                        'label': value.label || value.caption || ''
                    }
                }
            )
        };

        new SelectWindow(options);
    }

    /**
     * Open an article choose window for external links
     *
     * //TODO: move to the links or annotation model
     *
     * @param {HTMLElement} annoOrTag The annotation element
     * @param {function} callback Called when the value was changed
     * @param {boolean} isNew Whether the annotation was just created
     */
    editRecord(annoOrTag, callback, isNew) {
        let anno = annoOrTag;
        if (!anno.classList.contains('doc-section-link')) {
            anno = this.modelParent.models.annotations._getAnnoByTag(
                annoOrTag,
                this.doc.models.annotations._getAnnoContainer(annoOrTag)
            )
            if (!anno) {
                return
            }
        }

        const targets = this.modelParent.models.links.getTargets(anno);

        // TODO: do not transfer JSON, convert to explicit keys,
        //       e.g. targets_articles=epi-articles&targets_sections=inscription,inscriptionpart
        let url = App.databaseUrl
            + 'articles/index'
            + '?template=choose&show=content,searchbar'
            + '&columns=signature,name, project_signature'
            + '&targets='+ JSON.stringify(targets);

        const project_id = this.modelParent.models.articles.getProjectId();
        if (project_id) {
            url = url + '&projects=' + project_id;
        }

        // TODO: select the selected option
        //url = (selected.id !== undefined) ? url + '/' + selected.id : url;

        const options = {
            title: "External link",
            //selected: selected.id,
            height: 600,
            width: 600,
            focus: true,
            url: url,

            removeOnCancel: isNew,
            buttonRemove: !isNew,
            buttonSelect: true,
            selectOnClick: false,

            onRemove: () => callback(null),
            onSelect: (element) => {
                const value = element.dataset.value.split('-', 2);
                const valueTab = value.length > 1 ? value[0] : 'articles';
                const valueId = value.length > 1 ? value[1] : value[0];

                callback(
                    !element ? null : {
                        'record' : {
                            'tab': valueTab,
                            'id': valueId,
                            'label': element.dataset.label
                        }
                    }
                )
            }
        };

        new SelectWindow(options);
    }

    /**
     * Open a choose window for internal links to sections and footnotes
     *
     * @param anno {HTMLElement} The annotation element
     * @param callback {function} Called when the value was changed
     * @param isNew {boolean} Whether the annotation was just created
     */
    editRelation(annoOrTag, callback, isNew) {

        let anno = annoOrTag;
        if (!anno.classList.contains('doc-section-link')) {
            anno = this.modelParent.models.annotations._getAnnoByTag(
                annoOrTag,
                this.doc.models.annotations._getAnnoContainer(annoOrTag)
            )
            if (!anno) {
                return
            }
        }

        const selected = this.modelParent.models.links.getAttributes(anno);
        const targets = this.modelParent.models.links.getTargets(anno);

        const targetList = this.doc.getTargetList(targets, selected);

        // TODO: add styling (hover effect)
        let windowContent = Utils.spawnFromString(
            '<div class="content-main widget-scrollbox">' +
            '<div class="input reference">' +
            '<div class="widget-dropdown-selector widget-dropdown-selector-frame">' +
            '<input type="text" name="value-text" value="" title="" autocomplete="off" class="input-reference-text" data-label="" data-oldvalue="">' +
            '<input type="hidden" name="value" class="input-reference-value">' +
            '<div class="widget-dropdown-pane widget-scrollbox widget-dropdown-pane-frame">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>'
        );

        windowContent.querySelector('.widget-dropdown-pane').appendChild(targetList);

        const options = {
            title: "Select target",

            selected: selected.id + '-' + selected.tab,
            element: windowContent,

            height: 400,
            width: 300,
            focus: true,

            removeOnCancel: isNew,
            buttonRemove: !isNew,
            openDropdown: true,

            onRemove: () => callback(null),
            onSelect: (value) => {
                if (!value) {
                    callback(null);
                } else {
                    const valueParts = value.value.split('-', 2);
                    const valueTab = valueParts.length > 1 ? valueParts[0] : undefined;
                    const valueId = valueParts.length > 1 ? valueParts[1] : valueParts[0];
                    callback({
                        'relation' : {
                            'tab': valueTab,
                            'id': valueId,
                            'label': value.label || value.caption || ''
                        }
                    });
                }
            }
        };

        new SelectWindow(options);
    }

    /**
     * Open a popup to edit the attributes or remove the annotation
     *
     * @param {object} tagConfig
     * @param {function} setValue
     * @param {boolean} isNew
     * @param {Element|undefined} widget
     * @param {object} moleculeAttributes An object with properties prefixed by 'tag' and 'links' that hold the respective attributes
     */
    openAttributesDialog(tagConfig, setValue, isNew, widget, moleculeAttributes={}) {
        const self = this;
        const caption = Utils.getValue(tagConfig, 'caption') || '';
        let options = {};

        // Create inputs in popup
        const tagAttributesConfig = Utils.getValue(tagConfig, 'config.attributes');
        const content = self.createAttributesInputs(tagAttributesConfig, moleculeAttributes);
        if (!tagAttributesConfig && !isNew) {
            content.textContent = 'Do you want to remove the annotation?';
        }

        // Buttons
        let buttons = {};
        buttons.cancel = {
            'text': 'Cancel',
            'handler': (dialog) => {
                if (isNew) {
                    setValue(null);
                }
                dialog.closeWindow();
                if (widget) {
                    widget.focus();
                }
            }
        };

        if (!isNew) {
            buttons.remove = {
                'text': 'Remove',
                'handler': (dialog) => {
                    setValue(null);
                    dialog.closeWindow();
                    if (widget) {
                        widget.focus();
                    }
                }
            };
        }

        if (tagAttributesConfig) {
            buttons.apply = {
                'text': 'Apply',
                'handler': (dialog) => {
                    const {value, valid} = self.getAttributesInputValues(dialog);
                    if (!valid) {
                        return;
                    }
                    setValue(value);
                    dialog.closeWindow();
                    if (widget) {
                        widget.focus();
                    }
                }
            };

        }

        options = {
            title: caption,
            height: Utils.getValue(tagConfig, 'config.attributes') ? 450 : 150,
            width: 400,
            focus: true,
            dialogButtons: buttons
        };

        App.openPopup(content, options);
    }

    /**
     * Create a div containing all configured inputs
     *
     * @param {Object} tagConfig
     * @param {Object} tagAttributes
     * @return {HTMLDivElement}
     */
    createAttributesInputs(tagConfig, tagAttributes) {
        const content = document.createElement('div');
        content.classList.add('doc-attributes');

        if (!tagConfig) {
            return content;
        }

        for (const [attrKey, attrConfig] of Object.entries(tagConfig)) {

            const fieldWrapper = Utils.spawnFromString('<div class="doc-content-element"></div>');
            content.append(fieldWrapper);

            // Label
            const label = Utils.spawnFromString('<label class="doc-content-fieldname"></label>');
            label.textContent = attrConfig['caption'] || '';
            label.setAttribute('for', attrKey);
            fieldWrapper.appendChild(label);

            // Help
            const inputHelp = attrConfig['title'];
            if (inputHelp !== undefined) {
                const helpElement = Utils.spawnFromString('<div class="doc-content-help widget-tooltip">?</div>');
                helpElement.setAttribute('title', inputHelp);
                fieldWrapper.append(helpElement);
            }

            // Input
            const inputWrapper = Utils.spawnFromString('<div class="doc-content-content"></div>');
            fieldWrapper.append(inputWrapper);

            // Convert array to object
            let inputOptions = attrConfig['values'];
            if (Array.isArray(inputOptions)) {
                inputOptions = inputOptions.reduce((acc, currentValue, currentIndex) => {
                    acc[currentIndex.toString()] = currentValue;
                    return acc;
                }, {});
            }

            // Determine input type
            let inputType = attrConfig['input'];

            if ((inputType === undefined) && (typeof inputOptions === 'object')) {
                inputType = 'select';
            } else if (inputType === undefined) {
                inputType = 'text';
            }

            let input;

            // Links to properties (molecular annotation)
            if (inputType === 'link') {
                // Molecular annotations:
                // - They carry the name of the link config in the type property of each attribute config
                // - The tagAttributes array contains both, tag attributes and link attributes.
                //   Link attributes are named by the property type (TODO: not good!)
                const annoType = Utils.getValue(attrConfig, 'type');
                const types = this.modelParent.models.types._types;
                const annoConfig = types.links[annoType] || types.footnotes[annoType];

                const propertytype = Utils.getValue(annoConfig, 'config.fields.to.targets.properties.0');
                const linksAttribute = 'data-links-' + propertytype;

                if (propertytype !== undefined) {
                    const selected = tagAttributes[linksAttribute] || {};
                    const paneId = 'dropdown-pane-properties-' + propertytype;
                    let paneElm = document.getElementById(paneId);
                    if (paneElm) {
                        paneElm.remove();
                        paneElm = undefined;
                    }

                    // TODO: Make dry, see other property selectors
                    let url = App.databaseUrl + 'properties/index/' + propertytype + '?template=choose&references=0&show=content';

                    const limitProperties = Utils.getValue(annoConfig, 'config.fields.to.limit');
                    if ((limitProperties === 'article') && (this.rootTable === 'articles') && this.rootId) {
                        url = url + '&articles.field=id&articles.term=' + this.rootId;
                    }
                    if ((limitProperties === 'project')  && this.doc.models.articles){
                        const projectId = this.doc.models.articles.getProjectId();
                        if (projectId) {
                            url = url + '&projects=' + projectId;
                        }
                    }

                    if (Utils.getValue(annoConfig,'config.fields.to.append', false)) {
                        url = url + '&append=1';
                    }

                    if (Utils.getValue(annoConfig,'config.fields.to.manage', false)) {
                        url = url + '&manage=1';
                    }

                    let selectedId;
                    if  ((selected.id !== undefined) && (!selected.new)) {
                        url = url + '&seek=' + selected.id;
                        selectedId = selected.id;
                    }

                    const inputData = {
                        id: paneId,
                        url: url,
                        table: 'properties',
                        type: propertytype,
                        name: 'links-' + attrKey,
                        label: selected.label || '',
                        value: selectedId || ''
                    };
                    input = this.doc.spawnFromTemplate('template-widget-dropdown-selector', inputData);
                    inputWrapper.appendChild(input);
                }
            }
            else if (inputType === 'select') {
                input = document.createElement('select');
                input.setAttribute('name', 'attr-' + attrKey);

                for (let inputKey in inputOptions) {
                    if (inputOptions.hasOwnProperty(inputKey)) {
                        let inputOptionElm = document.createElement("option");
                        inputOptionElm.value = inputKey;
                        inputOptionElm.text = inputOptions[inputKey];
                        input.appendChild(inputOptionElm);
                    }
                }

                input.value = tagAttributes[ 'data-attr-' + attrKey] || '';
                inputWrapper.appendChild(input);

            } else if (inputType === 'checkbox') {
                if (typeof inputOptions !== 'object') {
                    inputOptions = inputOptions || attrConfig['caption'] || attrKey;
                    inputOptions = {"0": inputOptions, "1": inputOptions};
                }

                const inputLabel = document.createElement('label');
                inputLabel.textContent = ' ' + (Object.values(inputOptions)[1] || attrConfig['caption'] || attrKey);

                input = document.createElement('input');
                input.setAttribute('type', 'checkbox');
                input.setAttribute('name', 'attr-' + attrKey);
                input.setAttribute('value', Object.keys(inputOptions)[1] || '1');
                input.dataset.default = Object.keys(inputOptions)[0] || '0';
                inputLabel.prepend(input);

                // Check or not
                const attrValue = tagAttributes['data-attr-' + attrKey] || '';
                input.checked = (attrValue === (Object.keys(inputOptions)[1] || '1'));

                inputWrapper.appendChild(inputLabel);

            } else {
                input = document.createElement('input');
                input.setAttribute('type', 'text');
                input.setAttribute('name', 'attr-' + attrKey);

                const inputPattern = attrConfig['values'];
                if ((inputPattern !== undefined) && (typeof inputPattern === 'string')) {
                    input.setAttribute('pattern', inputPattern);
                    input.classList.add('widget-validate')
                }

                input.value = tagAttributes['data-attr-' + attrKey] || '';

                inputWrapper.appendChild(input);
            }


            if (inputHelp !== undefined) {
                input.setAttribute('title', inputHelp);
            }
        }

        return content;
    }

    /**
     * Get values from the inputs
     *
     * @param  dialog
     * @return {{valid: boolean, value: {}}}
     */
    getAttributesInputValues(dialog) {
        let value = {};
        const widgets = dialog.widgetElement.querySelectorAll('input[type=text], input[type=checkbox], select');
        let valid = true;
        if (widgets) {
            widgets.forEach((elm) => {
                valid = valid && (!elm.classList.contains('widget-validate') || elm.reportValidity());

                // Links
                if (elm.classList.contains('input-reference-text')) {
                    const elmId = Utils.getNextSibling(elm,'.input-reference-value');
                    const elmReference = elm.closest('.input.reference');

                    const selected = {
                        id : elmId.value,
                        tab:  elmReference.dataset.referenceTab,
                        type: elmReference.dataset.referenceType,
                        label : elm.value
                    };
                    value[elmId.name] = selected;
                }

                // Tag attributes
                else if (elm) {
                    if (elm.getAttribute('type') === 'checkbox') {
                        value[elm.name] = elm.checked ? elm.value : (elm.dataset.default || '');
                    } else {
                        value[elm.name] = elm.value;
                    }
                }
            });
        }
        return {value: value, valid: valid};
    }
}

/**
 * Types model
 *
 * @param options
 * @constructor
 */
export class TypesModel extends BaseDocumentModel {

    constructor(modelParent) {
        super(modelParent);
        this._types = null;
        this._isLoading = false;
    }

    /**
     * Group types data by scope
     * TODO do merging in PHP via URL parameter, remove merging code here
     *
     * @param {Object} data type configuration
     * @return {Object}
     */
    groupTypes(data) {
        let grouped = {};

        data = data.constructor === 'Array' ? data : Object.values(data);
        // collect all versions
        for (const item of data) {
            grouped[item.scope] ??= {};
            grouped[item.scope][item.name] ??= [];
            grouped[item.scope][item.name].push(item);
        }

        // merge, assume that items are sorted (e.g. sort=sortno)
        // fields like 'category' or 'caption' are joined
        for (const scope of Object.keys(grouped)) {
            grouped[scope] ??= {}; // empty as fallback/default
            for (const [name, value] of Object.entries(grouped[scope])) {
                grouped[scope][name] = Utils.replaceRecursive(true, ...value);
            }
        }

        return grouped;
    }

    /**
     * Fetch types
     *
     * @param {string} scope The scope to load types for, e.g. 'links', 'footnotes'.
     *                       Leave empty to get all types grouped by scope.
     * @returns {Promise} A promise resolving to a types object.
     */
    async loadTypes(scope) {
        scope = scope || '';

        // Wait for previous requests
        function sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms))
        }

        while (this._isLoading) {
            await sleep(5);
        }

        const self = this;
        return new Promise(function (resolve, reject) {

                if (!App.databaseUrl) {
                    resolve({});
                }
                else if (self._types === null) {

                    self._isLoading = true;

                    // TODO: fetch paginated and only used types
                    // TODO: merge config modes (default, preview, code)
                    App.fetch(
                        App.databaseUrl + 'types.json?sort=sortno&limit=1000&modes=default',
                        function (data) {
                            if (data.status === 'success') {
                                self._types = self.groupTypes(data.types || {});
                                self._isLoading = false;
                                resolve(Utils.getValue(self._types, scope));
                            } else {
                                self._isLoading = false;
                                reject(data);
                            }
                        }
                    );

                } else {
                    resolve(Utils.getValue(self._types, scope));
                }
            }
        );
    }

    /**
     * Get a list of annotation groups.
     *
     * The group configurations contain the following keys:
     * - caption: The group caption. If a type without tag_type or with tag_type set to 'group' is found,
     *            the type name is used. Otherwise, the group name is used.
     *
     * @param {string[]} scopes The scopes, a list of 'links' and/or 'footnotes'.
     * @param {string[]} excludeTagTypes Exclude these tag types, e.g. ['character'].
     * @return {Promise} A promise resolving to an object of group configurations keyed by group name.
     */
    async loadAnnoConfig(scopes = ['links', 'footnotes'], excludeTagTypes = ['character']) {
        return new Promise( (resolve, reject) => {
            this.loadTypes().then(types => {
                const groups = {};

                for (const scope of scopes) {
                    const scopedTypes = types[scope] || {};
                    Object.entries(scopedTypes).forEach(([typeName, type]) => {
                        let tagType = type.config?.tag_type || '';
                        tagType = tagType === 'group' ? '' : tagType; // group as alias for empty tag_type
                        if (!excludeTagTypes.includes(tagType)) {
                            const groupName = type.config?.group || '';
                            if (groupName) {
                                const groupConfig = groups[groupName] || {};
                                groupConfig.caption = !tagType ? type.caption : (groupConfig.caption || groupName);
                                groups[groupName] = groupConfig;
                            }
                        }
                    });
                }

                // Sort groups by key (group name)
                const sortedGroups = {};
                Object.keys(groups).sort().forEach(key => {
                    sortedGroups[key] = groups[key];
                });

                resolve(sortedGroups);
            });
        });
    }

    /**
     * Get the field config from the type data.
     *
     * Supports nested fields with dot notation.
     *
     * @param {Object} typeData The type data as loaded by loadTypes()
     * @param {String} typeName The typeName, if the typeData is not yet focused on a specific type
     * @param {String} fieldName A fieldname or a nested key (e.g. 'file_metadata.licence')
     * @param {String} valueName If not undefined, return the value of this key
     * @param {*} defaultValue The default value if the field or key is not found
     * @return {*}
     */
    getFieldConfig(typeData, typeName, fieldName, valueName, defaultValue) {
        if (!fieldName) {
            return defaultValue;
        }

        fieldName = fieldName.split('.');

        let fieldConfig;
        if (fieldName.length === 1) {
            fieldConfig = Utils.getValue(typeData,typeName + '.config.fields.' + fieldName[0]);
        } else {
            fieldName = [fieldName[0], fieldName.splice(1,fieldName.length).join('.')];
            fieldConfig = Utils.getValue(typeData,typeName + '.config.fields.' + fieldName[0] + '.keys.' + fieldName[1]);
        }

        if (!valueName) {
            return fieldConfig || defaultValue;
        }

        return Utils.getValue(fieldConfig, valueName, defaultValue);
    }

    /**
     * Return the tagset from the config for a given item field
     *
     * @param {HTMLElement} elm
     * @param {boolean} filter Only return configured tags
     * @returns Promise
     */
    getTagSet(elm, filter = true) {

        let fieldData = this.doc.getFieldData(elm);

        return this.loadTypes().then(
            (types) => {

                // XML tags
                let tagSet = Object.entries({...types.links, ...types.footnotes});

                // Extract field config from the item
                let fieldConfig = [];
                if (fieldData.field && fieldData.type && fieldData.table) {
                    const typeKey = fieldData.table + '.' + fieldData.type + '.config' +
                        '.fields' + '.' + fieldData.field + '.types';
                    fieldConfig = Utils.getValue(types, typeKey, []);
                }

                // Disable toolbuttons for unconfigured tags
                // @deprectated: remove tag.category as criterion, use config.group
                tagSet = tagSet.map(([tagName, tag]) => {
                    tag.matched = false;

                    let selectors = [
                        tag.name,
                        tag.scope + '.' + tag.name,
                        tag.category,
                        tag.scope + '.' + tag.category
                    ];

                    const tagGroup = Utils.getValue(tag,'config.group');
                    if (tagGroup) {
                        selectors.push(tagGroup);
                        selectors.push(tag.scope + '.' + tagGroup);
                    }

                    for (let i = 0; i < selectors.length; i++) {
                        tag.sortidx = fieldConfig.indexOf(selectors[i]);
                        if (tag.sortidx > -1) {
                            tag.matched = true;
                            break;
                        }
                    }

                    return [tagName, tag];
                });

                // Remove unconfigured tags
                if (filter) {
                    tagSet = tagSet.filter(([tagName, tag]) => tag.matched);
                }

                tagSet = Object.fromEntries(tagSet);
                return tagSet;
            }
        );
    }
}
