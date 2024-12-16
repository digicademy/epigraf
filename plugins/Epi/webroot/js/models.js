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
 * (articles, sections, items, files, properties, annotations, links, types)
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

    /**
     * Open an article choose window for external links
     *
     * //TODO: move to the links or annotation model
     *
     * @param anno {HTMLElement} The annotation element
     * @param callback {function} Called when the value was changed
     * @param isNew {boolean} Whether the annotation was just created
     */
    editAnno(anno, callback, isNew) {

        const {selected, targets} = this.modelParent.models.links.getTargets(anno);

        // TODO: do not transfer JSON, convert to explicit keys,
        //       e.g. targets_articles=epi-articles&targets_sections=inscription,inscriptionpart
        let url = App.databaseUrl
            + 'articles/index'
            + '?template=choose&show=content,searchbar'
            + '&columns=signature,name, project_signature'
            + '&targets='+ JSON.stringify(targets);

        const project_id = this.getProjectId();
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
                        'tab': valueTab,
                        'id': valueId,
                        'label': element.dataset.label,
                    }
                )
            }
        };

        new SelectWindow(options);
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
                    labelPath: this.getTitle(section)
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

    getPosition(section, scoped = true) {
        let siblings = [];
        var prevSibling = section.previousElementSibling;
        while (prevSibling) {
            const inScope = !scoped || (
                (prevSibling.dataset.rowParentId === section.dataset.rowParentId) &&
                (prevSibling.dataset.rowType === section.dataset.rowType)
            );

            if (inScope && prevSibling.classList.contains('doc-section') && !prevSibling.classList.contains('doc-section-removed')) {
                siblings.push(prevSibling);
            }
            prevSibling = prevSibling.previousElementSibling;
        }
        return siblings.length + 1;
    }

    /**
     * Get the joined names of the section and its ancestors
     *
     * @param {Element} section
     * @return {string}
     */
    getPath(section) {
        let ancestors = [this.getName(section)];
        const rowTable = section.dataset.rowTable;

        let prevSibling = section.previousElementSibling;
        while (prevSibling && (prevSibling.dataset.rowTable === rowTable)) {
            if (prevSibling.dataset.rowId === section.dataset.rowParentId) {
                section = prevSibling;
                ancestors.push(this.getName(prevSibling));
            }
            prevSibling = prevSibling.previousElementSibling;
        }
        return ancestors.reverse().join('.');
    }

    /**
     * Get the section config from its json encoded data-row-config attribute
     *
     * @param {HTMLElement} section
     * @return {{}|any}
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
     * (parent_id, preceding_id, sectionnumber, number).
     * Then update section names;
     *
     * @param {Event} event An event triggering the update.
     */
    updatePositions(event) {
        const treeWidget = this.widgetTree;

        let sectionNumbers = {};
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

                // Update the data fields of the section
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
                    sectionNumbers[currentSection.dataset.rowParentId] = sectionNumbers[currentSection.dataset.rowParentId] || {};
                    const currentNumber = (sectionNumbers[currentSection.dataset.rowParentId][currentSection.dataset.rowType] || 0) + 1;
                    sectionNumbers[currentSection.dataset.rowParentId][currentSection.dataset.rowType] = currentNumber;

                    Utils.setInputValue(
                        currentSection.querySelector('[data-row-field="number"]'),
                        currentNumber
                    );

                    // Sort number
                    // For sections this is the running section number
                    // For pipeline tasks the sortno is mapped to the number field, see EntityHelper taskStart
                    // TODO: move section inputs into doc-section-head and scope selector
                    sortNumber += 1;
                    Utils.setInputValue(
                        currentSection.querySelector('[data-row-field="sortno"]'),
                        sortNumber
                    );

                    // Level
                    //TODO: does not work?
                    currentSection.dataset.rowLevel = currentMenuItem.treeLevel;
                    Utils.removeClassByPrefix(currentSection, 'doc-section-level-');
                    currentSection.classList.add('doc-section-level-' + currentMenuItem.treeLevel);

                }

                currentSection = currentSection.nextElementSibling;
            }
        });

        if (App.scrollsync) {
            App.scrollsync.updateWidget();
        }

        // Before refactored from scrollsync
        // const activateItem = event ? event.detail.data.row : undefined;
        // if (activateItem) {
        //     this.activateLi(activateItem);
        // }

        this.updateNames();
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

                // Step 1: Get numeric section number (sectionnumber in the database)
                const sectionConfig = sectionTypes[section.dataset?.rowType] || this.getConfig(section);
                let useNumber = Utils.getValue(sectionConfig, 'config.name.number');
                const usePath =
                    Utils.getValue(sectionConfig, 'config.name.path', false) |
                    Utils.getValue(sectionConfig, 'config.name.scoped', false);
                const sectionNumber = this.getPosition(section, usePath);

                // Step 2: Get section name (sectionname in the database)
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

                    // Update section title
                    const sectionTitle = prefix + (usePath ? this.getPath(section) : sectionName) + postfix;
                    Utils.setElementContent(
                        section.querySelector('.doc-section-name').querySelector('[data-value="name"]'),
                        sectionTitle
                    );

                    // Update indent
                    const indent = " ⬥ ".repeat(parseInt(section.dataset.rowLevel));
                    Utils.setElementContent(section.querySelector('.doc-section-indent'), indent);

                    // Update section number
                    Utils.setInputValue(section.querySelector('.doc-section-name [data-row-field="sortno"]'), sectionNumber);
                }

            });

        return;
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

                self.updateNames();
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
            event.preventDefault();
            event.stopPropagation();

            const item = event.target.closest('.doc-section-item');
            if (item) {
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

    /**
     * Create a new item based on a template
     *
     * Fires the itemAdded event.
     *
     * @param {HTMLElement} templateItem An item element with curly bracket placeholders
     *                                   for id, itemsId and sectionsId
     */
    add(templateItem) {

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
        const inputSelector = 'div[data-row-field]:not([data-row-field="sortno"]) input:not([type="hidden"]), '
            + 'div[data-row-field]:not([data-row-field="sortno"]) select';
        const firstInput = newItem.querySelector(inputSelector);
        if (firstInput) {
            firstInput.focus();
            firstInput.click();
        }

        // Fire event (and observe it for example in the map widget)
        const event = new Event('epi:add:item', {bubbles: true, cancelable: false});
        newItem.dispatchEvent(event);
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
            const propertiesId = elmContent? elmContent.dataset.rowValue : undefined;

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

    /**
     * Open a property choose window
     *
     * @param anno {HTMLElement} The annotation element
     * @param callback {function} Called when the value was changed
     * @param isNew {boolean} Whether the annotation was just created
     */
    editAnno(anno, callback, isNew) {
        // @var targets A list of allowed property types
        // @var selected The currently selected ID
        const {selected, targets} = this.modelParent.models.links.getTargets(anno);
        const propertytype = Utils.getValue(targets, 'properties.0');
        const selectedId = ((selected.id !== undefined) && (!selected.new)) ? selected.id : undefined;
        if (propertytype === undefined) {
            return;
        }

        let url = App.databaseUrl + 'properties/index/' + propertytype + '?template=input&show=content';
        url = (selectedId !== undefined) ? url + '&seek=' + selectedId : url;

        if (selected.new && (selected.value !== undefined)) {
            url = url + '&append=1&find=' + selected.value;
        }

        // Allow empty or not
        let tagConfig = this.modelParent.models.links.getConfig(anno);
        const required = Utils.getValue(tagConfig,'config.fields.to.required', true);
        if (!required) {
            url = url + '&empty=1';
        }

        if (Utils.getValue(tagConfig,'config.fields.to.append', false)) {
            url = url + '&append=1';
        }

        // External manage URL
        let urlManage = App.databaseUrl + 'properties/index/' + propertytype;
        urlManage = (selectedId !== undefined) ? urlManage + '?seek=' + selectedId : urlManage;

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
                    'tab': 'properties',
                    'id': value.value,
                    'type': value.type,
                    'new': (value.new || false) == true, // Will be implicitly converted to boolean by '=='
                    'label': value.label || value.caption || '',
                }
            )
        };

        new SelectWindow(options);
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

        //TODO: replace jquery
        $('body').on('mouseout', '[data-tagid], [data-from-tagid]', event => this.unhoverAnno(event));
        $('body').on('mouseover', '[data-tagid], [data-from-tagid]', event => this.hoverAnno(event));

        this.listenEvent(document, 'click', event => this.onClickAnno(event));
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
                sectionElement = this._getAnnoSection(target);
                if (sectionElement) {
                    anno = sectionElement.querySelector('.doc-section-link[data-from-tagid="' + tagid + '"]');
                }
            }
        } else {

            // Get tag
            tagid = anno.dataset.fromTagid;
            sectionElement = this._getAnnoSection(anno);
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
    _getAnnoSection(elm) {
        return elm.closest('.doc-section-content, .doc-section-note');
    }

    /**
     * Return the xml editor widget element of a tag
     *
     * @param {HTMLElement} tagElement An element inside the widget
     * @returns {Element|undefined}
     */
    _getTagContainer(tagElement) {
        return tagElement ? tagElement.closest('.widget-xmleditor') : undefined;
    }

    /**
     * Return the tag inside the XML editor
     *
     * @param tagId The tag id
     * @param {HTMLElement} widget The CKEditor editor (div.widget-xmleditor).
     * @protected
     */
    _getTagByTagId(tagId, widget) {
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
     * Add hover effect
     *
     * @param event
     * @returns {boolean}
     */
    hoverAnno(event) {
        this.unhoverAnno();

        let target = event.currentTarget;
        let id = target.dataset.tagid || target.dataset.fromTagid;

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
     * Show annotation properties in the sidebar
     *
     * @param anno {HTMLElement} The annotation element
     */
    show(anno) {

        // Get element
        let toId = anno.dataset.toId;
        let toTab = anno.dataset.toTab;
        let tagId = anno.dataset.fromTagid;
        let tagType = anno.dataset.rowType;

        this.doc.models.types.loadTypes().then((types) => {
                types.links = types.links || {};
                types.footnotes = types.footnotes || {};
                let tagConfig = types.links[tagType] || types.footnotes[tagType] || {};

                let targets = Utils.getValue(tagConfig, 'config.fields.to.targets', {});

                if (targets.articles && (toTab === 'articles') && toId) {
                    this.doc.view(toTab + '-' + toId);
                } else if (targets.sections && (toTab === 'sections') && toId) {
                    this.doc.models.sections.view(toId);
                } else if (targets.properties && (toTab === 'properties') && toId) {
                    this.doc.models.properties.view(toId);
                } else if ((tagConfig.scope === 'footnotes') && tagId) {
                    this.doc.models.footnotes.view(tagId);
                }
            }
        );
    }

    /**
     * Add an annotation next to the content field
     *
     * @param {Element} widget
     * @param {string} tagId
     * @param {string} typeName
     * @param {string} scope footnotes or links
     * @param {Object} tagSet
     * @returns The new annotation element
     */
    add(widget, tagId, typeName, scope, tagSet) {
        const annoContainer = this._getAnnoContainer(widget);
        if (!annoContainer) {
            console.log('Annotation container not found.');
            return;
        }

        annoContainer.classList.remove('doc-section-links-empty');

        let fieldData = this.modelParent.getFieldData(widget);

        let annoElement = this._getAnnoByTagId(tagId, annoContainer);
        if (!annoElement) {
            let caption = Utils.getValue(tagSet, typeName + '.caption', 'UNDEFINED');

            const annoId = this._createRowId();
            const newLink = this.doc.spawnFromTemplate(
                'template-annotation-' + scope,
                {
                    scope: scope,
                    idx: annoId,
                    id: annoId,
                    // id: "",
                    type: typeName,

                    rootId: fieldData.rootId,
                    rootTab: fieldData.rootTab,
                    fromId: fieldData.id,
                    fromTab: fieldData.table,
                    fromField: fieldData.field,
                    fromTagid: tagId,
                    fromTagname: typeName,

                    // Only for links, not for footnotes
                    toId: "",
                    toTab: "",
                    toValue: caption,

                    deleted: 0
                }
            );

            if (newLink) {
                annoContainer.appendChild(newLink);
                annoElement = this._getAnnoByTagId(tagId, annoContainer);
                annoElement.dataset.new = '1';
            }
        } else {
            if (fieldData.deleted !== '1') {
                Utils.setInputValue(annoElement.querySelector('input[data-row-field=deleted]'), 0);
                annoElement.dataset.deleted = '0';
                this.enableInputs(annoElement);
            }
        }

        return annoElement;
    }

    /**
     * Edit the annotation properties
     * - links to properties, sections, footnotes, articles
     * - footnote content
     * - attributes
     *
     * Called by onClickAnno and onCreateAnno
     *
     * @param {HTMLElement} anno The annotation element (div.doc-section-link inside div.doc-section-links)
     * @param {boolean} isNew Whether the annotation is new, in which case the popup window is only shown for properties.
     */
    edit(anno, isNew = true) {

        this.doc.models.types.loadTypes().then(
            (types) => {
                if (!types.links && !types.footnotes) {
                    return;
                }
                types.links = types.links || {};
                types.footnotes = types.footnotes || {};

                // Get element
                const tagName = anno.dataset.rowType;
                const tagConfig = types.links[tagName] || types.footnotes[tagName];

                const isFootnote = types.footnotes && (types.footnotes[tagName] !== undefined);
                const toFormat = Utils.getValue(tagConfig, 'config.fields.to.format');
                const toTargets = Object.keys(Utils.getValue(tagConfig, 'config.fields.to.targets', {}));
                const hasAttributes = Utils.getValue(tagConfig, 'config.attributes') !== undefined;

                // Properties
                let model;
                if (isFootnote) {
                    model = this.doc.models.footnotes;
                }

                else if (toTargets.includes('properties')) {
                    model = this.doc.models.properties;
                }

                // External links
                else if (toFormat === 'record') {
                    model = this.doc.models.articles;
                }

                // Internal links
                else if (toFormat === 'relation') {
                    model = this.doc.models.links;
                }

                // Tag attributes
                else if (hasAttributes || !isNew) {
                    model = this.doc.models.attributes;
                }

                if (model) {
                    model.editAnno(
                        anno,
                        (value) => this.updateAnno(anno, value, true),
                        isNew
                    );
                }
            }
        );

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
        let annoElement = annoContainer.querySelector('[data-from-tagid="' + tagId + '"]');
        this.removeAnno(annoElement);

        if (annoContainer) {
            const hasAnnos = annoContainer.querySelector('.doc-section-link:not([data-deleted="1"])');
            annoContainer.classList.toggle('doc-section-links-empty', !hasAnnos);
        }

        return annoElement;
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
     * Remove a tag
     *
     * @param {HTMLElement} tagElement The tag element within a xml editor widget
     * @return {HTMLElement}
     */
    removeTag(tagElement) {
        const tagContainer = this._getTagContainer(tagElement);
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
     * Called by edit to update the annotation and the tag
     * after annotation properties have been edited
     *
     * @param {HTMLElement} anno The annotation element (div.doc-section-link inside div.doc-section-links)
     * @param {Object|string|null} value The value, either null or an object with the keys
     *              - label The text that will be displayed in the annotation and as textContent of the tag
     *              - tab Set the to_tab property of the link (table name, e.g. properties)
     *              - id Set the to_id property of the link (ID)
     *              - further attributes will be added as data-attr-* attributes
     *              If the value is a string, it will be parsed as JSON and converted to an object.
     *              If the value is null, the tag will be removed.
     * @param {boolean} focus Whether the element should get focus
     */
    updateAnno(anno, value, focus) {
        // Convert to JSON
        if (typeof value === 'string') {
            try {
                value = JSON.parse(decodeURI(value));
            } catch (e) {
                console.log(e.toString());
                value = {};
            }
        }

        // Remove annotation
        if (value === null) {
            this.removeAnno(anno);
        }

        // Update annotation
        else {
            if (value.label !== undefined) {
                anno.querySelector('span').textContent = value.label;
            }

            if (value.tab !== undefined) {
                anno.dataset.toTab = value.tab;
                Utils.setInputValue(anno.querySelector('input[data-row-field=to_tab]'), value.tab);
            }

            if (value.id !== undefined) {
                anno.dataset.toId = value.id;
                Utils.setInputValue(anno.querySelector('input[data-row-field=to_id]'), value.id);
            }

            this.addProperty(anno, value);
        }

        // Update tag
        const tagId = anno.dataset.fromTagid;
        const tag = this._getTagByTagId(tagId, this._getAnnoSection(anno));
        const tagContainer = this._getTagContainer(tag);
        const tagEditor = this.doc.getWidget(tagContainer, 'xmleditor', false);

        if (tagEditor) {
            tagEditor.updateTag(tagId, value, true);
        }

    }

    /**
     * Called after inserting footnotes to update the tags
     *
     * @param widget
     * @param anno The annotation element inside of div.doc-section-links
     * @param value The value, either null or an object with the keys
     *              - label
     *              - tab and id.
     *              If the value is a string, it will be parsed as JSON.
     *              If the value is null, the tag should be removed
     */
    updateCounter(widget, anno, value) {

        if (widget.ckeditorInstance) {
            this.updateAnno(anno, value, false);
        } else {
            const tagId = anno.dataset.fromTagid;
            const tagElement = this._getTagByTagId(tagId, widget);
            if (tagElement) {
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
            // TODO: use isInFrame()
            const frame = event.target.closest('.frame-content');

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

    /**
     * Will be called from the editor everytime an element is created.
     * Makes sure the annotation is created in the link container
     * and creates the footnote.
     *
     * @param {HTMLElement} widget The CkEditor (div.widget-xmleditor)
     * @param {string} tagId ID of the tag
     * @param {string} typeName The link or footnote type
     * @param {boolean} isNew Whether the element was created by the user using the toolbar
     * @returns Promise
     */
    onCreateAnno(widget, tagId, typeName, isNew = false) {

        return this.doc.models.types.getTagSet(widget, false).then(
            (tagSet) => {

                const scope = Utils.getValue(tagSet, typeName + '.scope');

                let anno = this.add(widget, tagId, typeName, scope, tagSet);
                if (scope === 'footnotes') {
                    this.modelParent.models.footnotes.add(widget, tagId, typeName, tagSet);
                }

                if (isNew && anno) {
                    this.edit(anno, isNew);
                }

                return anno;
            }
        );

    }

    /**
     * Will be called from the editor everytime an element is deleted.
     * Hides the link in the link container.
     *
     * @param widget
     * @param tagId
     */
    onRemoveAnno(widget, tagId) {
        this.delete(widget, tagId);
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
                        labelPath: i18n.t('Footnote') + ' ' + Utils.querySelectorText(item, '.doc-footnote-number')
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
        return this.doc.models.annotations._getTagByTagId(tagId);
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

                        const widget = self.doc.models.annotations._getTagContainer(elm);
                        const anno = self.doc.models.annotations._getAnnoByTag(elm);

                        const label = Utils.numberToString(counter, Utils.getValue(typeData, 'config.fields.name.counter', 'numeric'));
                        const value = {label: label, tab: anno.dataset.toTab, id: anno.dataset.toId};

                        // Update tag and annotation
                        self.doc.models.annotations.updateCounter(widget, anno, value);

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
     * @param widget
     * @param tagId
     * @param typeName
     * @param tagSet
     * @return {HTMLElement}
     */
    add(widget, tagId, typeName, tagSet) {

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

            //TODO: make configurable whether the current text is copied to the segment
            // const tagElement = this.doc.models.annotations._getTagByTagId(tagId, widget);
            //const segment = tagElement ? tagElement.textContent : '';
            const segment = '';

            const annoId = annoElement.dataset.rowId || this.doc.models.annotations._createRowId();
            const newFootnote = this.doc.satellites.footnotes.spawnFromTemplate(
                'template-footnote-' + typeName,
                {
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
                    content: '',
                    segment: segment
                }
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
     * Show footnote editor
     *
     * @param anno {HTMLElement} The annotation element
     * @param callback {function} Called when the value was changed
     * @param isNew {boolean} Whether the annotation was just created
     */
    editAnno(anno, callback, isNew) {
        if (anno) {
            this.edit(anno.dataset.fromTagid);
        }
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
     * Get selected link data and possible targets from the config
     *
     * @param {HTMLElement} link The annotation
     * @return {string, Object, Array}  The tag ID, selected id and tab, target list
     */
    getTargets(link) {
        const selected = {
            id: link.dataset.toId,
            tab: link.dataset.toTab
        };

        const isNew = Utils.isTrue(link.dataset.toNew);
        if (isNew) {
            selected.new = true;
            selected.value = Utils.getInputValue(link.querySelector('[data-row-field="to_value"]'));
            selected.type = Utils.getInputValue(link.querySelector('[data-row-field="to_type"]'));
        }

        let tagId = link.dataset.fromTagid;
        let tagName = link.dataset.rowType;

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

        return {tagId, selected, targets};
    }

    /**
     * Get tag config of the link type
     *
     * @param {HTMLElement} anno The annotation
     * @return {Object} The types config
     */
    getConfig(anno) {
        const tagName = anno.dataset.rowType;
        const types = this.modelParent.models.types._types;
        if (types.links || types.footnotes) {
            return types.links[tagName] || types.footnotes[tagName];
        }
    }

    /**
     * Open a choose window for internal links to sections and footnotes
     *
     * @param anno {HTMLElement} The annotation element
     * @param callback {function} Called when the value was changed
     * @param isNew {boolean} Whether the annotation was just created
     */
    editAnno(anno, callback, isNew) {
        const {selected, targets} = this.modelParent.models.links.getTargets(anno);
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
                        'tab': valueTab,
                        'id': valueId,
                        'label': value.label || value.caption || '',
                    });
                }
            }
        };

        new SelectWindow(options);
    }
}


/**
 *  Manages tag attributes
 *
 * @param modelParent
 * @constructor
 */
export class AttributesModel extends BaseDocumentModel {

    /**
     * Open a window to set tag attributes or to remove the tag
     *
     * //TODO: split up the function
     *
     * @param {HTMLElement} anno The annotation element
     * @param {function} setValue Called to change the value of the annotation and the tag.
     *                            First parameter is the new value or null to remove annotation and tag.
     * @param {boolean} isNew Whether the annotation was just created
     */
    editAnno(anno, setValue, isNew) {
        const self = this;
        this.doc.models.types.loadTypes().then(
            (types) => {
                const tagName = anno.dataset.rowType;

                types.links = types.links || {};
                types.footnotes = types.footnotes || {};
                const tagConfig = types.links[tagName] || types.footnotes[tagName];

                const tagId = anno.dataset.fromTagid;
                const tag = this.doc.models.annotations._getTagByTagId(tagId, this.doc.models.annotations._getAnnoSection(anno));
                const container = this.doc.models.annotations._getTagContainer(tag);
                const editorWidget = this.doc.getWidget(container, 'xmleditor', false);

                if (editorWidget && tag && container) {
                    editorWidget.createEditor().then(editor => {
                            // Get attributes from xml editor
                            let tagAttributes = {};

                            // TODO: move calls to plugins.get('XmlTagEditing') into editors.js
                            const editing = editor.plugins.get('XmlTagEditing');
                            if (Utils.getValue(tagConfig, 'config.attributes', false)) {
                                tagAttributes = editing.tagGetModelAttributes(editor, tagId);
                            }

                            self.openDialog(tagConfig, setValue, isNew, container, tagAttributes);
                        });
                } else {
                    self.openDialog(tagConfig,  setValue, isNew);
                }
            });
    }

    /**
     * Open a popup to edit the attributes or remove the annotation
     *
     * @param {object} tagConfig
     * @param {function} setValue
     * @param {boolean} isNew
     * @param {Element|undefined} widget
     * @param {object} tagAttributes
     */
    openDialog(tagConfig, setValue, isNew, widget, tagAttributes={}) {
        const self = this;
        const caption = Utils.getValue(tagConfig, 'caption') || '';
        let options = {};

        // Create inputs in popup
        const content = document.createElement('div');
        content.classList.add('doc-attributes');

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

        const tagAttributesConfig = Utils.getValue(tagConfig, 'config.attributes');

        if (tagAttributesConfig) {
            self.createInputs(content, tagAttributesConfig, tagAttributes);

            buttons.apply = {
                'text': 'Apply',
                'handler': (dialog) => {

                    let value = {};
                    const widgets = dialog.widgetElement.querySelectorAll('input[type=text], input[type=checkbox], select');
                    let valid = true;
                    if (widgets) {
                        widgets.forEach((elm) => {
                            valid = valid && (!elm.classList.contains('widget-validate') || elm.reportValidity());
                            if (elm.getAttribute('type') === 'checkbox') {

                                value[elm.name] = elm.checked ? elm.value : (elm.dataset.default || '');
                            } else {
                                value[elm.name] = elm.value;
                            }
                        });
                    }

                    if (valid) {
                        setValue(value);
                        dialog.closeWindow();
                        if (widget) {
                            widget.focus();
                        }
                    }
                }
            };

        } else if (!isNew) {
            content.textContent = 'Do you want to remove the annotation?';
        }

        options = {
            title: caption,
            height: Utils.getValue(tagConfig, 'config.attributes') ? 400 : 150,
            width: 400,
            focus: true,
            dialogButtons: buttons
        };

        App.openPopup(content, options);
    }

    createInputs(content, tagConfig, tagAttributes) {
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
            if (inputType === 'select') {
                input = document.createElement('select');
                input.setAttribute('name', attrKey);

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
                input.setAttribute('name', attrKey);
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
                input.setAttribute('name', attrKey);

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

    // TODO: move to utils.js
    groupTypes(data) {
        let grouped = {};

        data = data.constructor === 'Array' ? data : Object.values(data);
        for (const item of data) {
            grouped[item.scope] = grouped[item.scope] || {};
            grouped[item.scope][item.name] = item;
        }
        return grouped;
    }

    /**
     * Fetch types
     *
     * @param scope
     * @returns Promise
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
                        App.databaseUrl + 'types.json?limit=1000&modes=default',
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
