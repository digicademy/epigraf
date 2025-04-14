/*
 * Filter widget - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {BaseWidget} from '/js/base.js';
import Utils from "/js/utils.js";

/**
 * AJAX based search box and properties filter
 *
 * Usage:
 *
 * Create a form with the class widget-filter.
 * Create filter inputs with the class widget-filter-item.
 * Create a result table with the class recordlist.
 *
 * All those elements need a data-filter-group attribute with a shared value.
 *
 * Create a class with the following functions:
 *
 * - getUrlParams should return an object with query parameters,
 *   it will be called to assemble the URL for an AJAX request
 *   when results need to be updated
 *
 * - updateFilter will be called after the params of other filter classes
 *   have changed. You can use it to update the filter, when other criteria changed.
 *   Use this.coordinator.getUrlParams() to get parameters from other filters.
 *
 * - updateWidget will be called a) after all filters were instantiated and
 *   b) after new data was loaded
 *
 * Create an instance of your class and pass the filter coordinator in the constructor.
 *
 *  Call this.coordinator.updateResults(self) if your filter input changed,
 *  this will assemble a new query URL, fetch the data, update the page
 *  and finally call updateFilter of all other filter objects.
 *  You can pass a second parameter (scope) to updateResults that will handed over
 *  to the updateFilter functions.
 *
 * See the classes FilterProjects, FilterProperties, FilterSelector and FilterSearchBar for existing implementations-
 *
 * // TODO: Maybe remove "event" param for functions that don't expect it?
 */
export class FilterWidget extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        // Init vars
        element.widgetFilter = this;
        this.widgetElement = element;
        this.filterGroup = element.dataset.filterGroup;

        // Array of filter objects
        // Each filter widget should call this.coordinator.addFilter() in its constructor
        this.filters = [];
    }

    initWidget() {
        let hasSortWidget = false;
        this.getFilterWidgetElements().forEach(elm => {
                if (elm.filterCoordinator) {
                    return;
                }
                if (elm.classList.contains('widget-filter-item-searchbar')) {
                    new FilterSearchBar(this, elm);
                }

                if (elm.classList.contains('widget-filter-item-selector')) {
                    new FilterSelector(this, elm);
                }

                if (elm.classList.contains('widget-filter-item-sort')) {
                    new FilterSort(this, elm);
                    hasSortWidget = true;
                }

                if (elm.classList.contains('widget-filter-item-template')) {
                    new FilterTemplate(this, elm);
                }

                if (elm.classList.contains('widget-filter-item-map')) {
                    new FilterMap(this, elm);
                }
            }
        );

        // Default sort filter (table columns)
        if (!hasSortWidget) {
            new FilterSort(this, this.widgetElement);
        }

        // Bind reset button
        this.listenEvent(document,'click', event => this.resetFilterClick(event));
    }

    /**
     * Add current filter to filter list.
     *
     * Each filter widget should call this.coordinator.addFilter() in its constructor
     *
     * @param filterObject Current filter object
     */
    addFilter(filterObject) {
        this.filters.push(filterObject);
        filterObject.coordinator = this;
        filterObject.widgetElement.filterCoordinator = this;
    }

    resetFilterClick(event) {
        const selector = '.widget-filter-item-reset[data-filter-group=' + this.filterGroup + ']';
        if (!event.target.closest(selector)) {
            return;
        }

        this.clearResults();
        event.preventDefault();
    }

    /**
     * Get all query parameters that result from filter operations
     *
     * @param {boolean} dataParams Whether to strip navigation params and only keep data params
     * @returns {Object} Object with all filter params
     */
    getUrlParams(dataParams=false) {
        let data = {};
        const filters = this.getFilterWidgets();
        filters.forEach(filter => {
            // Run callback and merge result into data
            if (typeof filter.getUrlParams === 'function') {
                try {
                    data = {...data, ...filter.getUrlParams()};
                } catch {
                    console.log("Filter not ready yet");
                }
            }
        });

        if (dataParams) {
            delete data.save;
            delete data.load;
            delete data.sort;
            delete data.direction;
            delete data.columns;
        }

        return data;
    }

    /**
     * Get URL path
     *
     * @returns {string} Path
     */
    getPath() {
        let path = '';
        const filters = this.getFilterWidgets();
        filters.forEach(filter => {
            if (typeof filter.getPath === 'function') {
                const segment = filter.getPath();
                if (segment !== undefined) {
                    path = segment;
                }
            }
        });

        return path;
    }

    /**
     * Get result table.
     *
     * @returns {HTMLTableElement} Result table
     */
    getResultTable() {
        return document.querySelector('.recordlist[data-filter-group=' + this.filterGroup + ']');
    }

    /**
     * Get filter widgets.
     *
     * @returns {NodeList|null} Filter widgets
     */
    getFilterWidgetElements() {
        return document.querySelectorAll('.widget-filter-item[data-filter-group=' + this.filterGroup + ']');
    }

    /**
     * Get all filter widgets of the filter group
     *
     * Merges this.filters with all filters attached elements' filterWidget property
     *
     * @return {array}
     */
    getFilterWidgets() {
        let filters = this.filters;

        const elements = document.querySelectorAll('.widget-filter-item[data-filter-group=' + this.filterGroup + ']');
        elements.forEach((filterElement) => {filters = [...filters, ...filterElement.filterWidgets || []]});

        return filters;
    }

    /**
     * After data was loaded, widgets may be replaced and
     * event bindings may need updates
     *
     * @param scope
     */
    updateWidget(scope) {
        const filters = this.getFilterWidgets();

        filters.forEach(filter => {
            if (typeof filter.updateWidget === 'function') {
                filter.updateWidget(scope);
            }
        });
    }

    /**
     * Load results based on article and property search options.
     *
     * @param source The filter that called the method
     * @param {boolean} clear Whether all other parameters should be cleared
     */
    updateResults(source, clear=false) {
        App.ajaxQueue.stop();
        // TODO: jquery in ajax call, fix later

        // Collect URL components
        let data = clear ? {'save':true} : this.getUrlParams();
        const segment = this.getPath();

        // Build URL
        const form = $(this.widgetElement);
        let action = form.attr('data-action');
        action = !action ? form.attr('action') : action;

        let url = action.split('?')[0];
        url += segment ? '/' + segment : '';
        let params = {...data};
        //params['show'] = 'content,searchbar';
        url += '?' + jQuery.param(params);

        // History URL
        let historyUrl = action.split('?')[0];
        historyUrl += segment ? '/' + segment : '';
        historyUrl += '?' + jQuery.param(data);

        // Form URL
        form.attr('action', historyUrl);

        // Load data
        this.loadData(url, historyUrl);

        // Allow filters to update
        this.emitEvent('epi:update:filter',{source:source});

        // Run callback for all other filters
        this.filters.forEach(filter => {
            if (typeof filter.updateFilter === 'function') {
                filter.updateFilter(source);
            }
        });
    }

    loadData(url, historyUrl) {
        this.pushHistory = window.history.pushState && historyUrl && !this.isInFrame();
        const container = this.getFrame(false);

        App.ajaxQueue.add('',
            {
                type: 'GET',
                url: url,
                dataType: 'html',
                beforeSend: xhr => {
                    App.showLoader();
                },
                success: (data, textStatus, xhr) => {
                    if (this.pushHistory) {
                        window.history.pushState(historyUrl, "Epigraf - search results", historyUrl);
                    }

                    // TODO: handle empty result when seeking, old result should stay unchanged?
                    // const html = Utils.spawnFromString(data);
                    // html.querySelector(['data-list-seek']);

                    App.replaceDataSnippets(data, container);
                    this.updateWidget();
                },
                error: (xhr, textStatus, errorThrown) => {
                    // If aborted (see above, https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/abort)
                    if ((xhr.readyState === XMLHttpRequest.UNSENT) && (xhr.status === 0)) {
                        return;
                    }
                    let msg = Utils.getValue(xhr.responseJSON,'error.message');
                    msg = msg ? (errorThrown + ': ' + msg) : errorThrown;
                    App.showMessage(msg, textStatus);
                },
                complete: (xhr, textStatus) => {
                    App.hideLoader();
                }
            }
        );
    }

    clearResults() {
        App.ajaxQueue.stop();

        // Build URL
        const form = $(this.widgetElement);
        let action = form.attr('data-action');
        action = !action ? form.attr('action') : action;
        const segment = this.getPath();

        let url = action.split('?')[0];
        url += segment ? '/' + segment : '';
        let params = {'save': true}; //,'show':'content,searchbar'
        url += '?' + jQuery.param(params);

        if (!this.isInFrame()) {
            App.ajaxQueue.stop();
            window.location = url;
        } else {
            const frameWidget = this.getFrame();
            frameWidget.loadUrl(url, frameWidget.options);
        }
    }
}

/**
 * Base class for filter items
 *
 */
class FilterItemWidget extends BaseWidget {

    constructor(element, name, parent) {
        super(element, name, parent);

        this.filterGroup = element.dataset.filterGroup;
        this.coordinator = this.getCoordinator(); //TODO: use this.parent from the BaseWidget ?

        // Add to the filterWidgets property
        element.filterWidgets = element.filterWidgets || [];
        element.filterWidgets.push(this);
    }

    /**
     * Find the filter coordinator element
     * and return the filter widget
     *
     * @return {*}
     */
    getCoordinator() {
        const filterElement = document.querySelector('.widget-filter[data-filter-group=' + this.filterGroup + ']');

        if (!filterElement) {
            return;
        }

        filterElement.widgets = filterElement.widgets || {};
        return filterElement.widgets.filter;
    }

    /**
     * Interface to parent filter component.
     *
     * @param event
     */
    updateResults(event) {
        this.coordinator.updateResults(this);
    }

    /**
     * Get the parameters managed by the filter
     * Override in derived filter classes
     *
     * @return {*}
     */
    getUrlParams() {
        return {};
    }


    /**
     * Get the path managed by the filter
     * Override in derived filter classes
     *
     * @return {string}
     */
    getUrlPath() {

    }
}


/**
 * Fixed parameters filter
 *
 * Adds fixed parameters to the URL, e.g. the scope of properties.
 * Add the classes 'widget-filter-item' and 'widget-filter-item-fixed' to an element of your choice.
 * Add the data-filter-group attribute to the filter group name.
 * Set the data-filter-path property to the fixed URL path value, i.e. the scope.
 *
 */
class FilterFixed extends FilterItemWidget {

    /**
     * Return the params to the filter coordinator.
     *
     * If a parameter name is set, the value is returned in getUrlParams().
     *
     * @returns {{}}
     */
    getPath() {
        if (this.widgetElement && this.widgetElement.dataset) {
            const scope = this.widgetElement.dataset.filterPath;
            return scope;
        }
    }
}


class FilterColumns extends FilterItemWidget {

    /**
     * Initialize widget and bind event listeners.
     *
     */
    constructor(element, name, parent) {
        super(element, name, parent);
    }

    initWidget() {
        const resultWidget = this.coordinator.getResultTable();
        if (!resultWidget || !resultWidget.classList.contains('widget-table')) {
            return;
        }

        this.button = resultWidget.querySelector('.widget-dropdown-toggle.button-settings');
        this.pane = this.button ? this.button.widgetDropdown.pane : undefined;

        if ((!this.button) || (!this.pane)) {
            return;
        }

        if (this.pane.classList.contains('widget-initialized')) {
            return;
        }

        // Bind checkbox click events
        this.listenEvent(this.pane,'click',  event => this.clickCheckbox(event))

        // Bind reset button event
        this.listenEvent(this.pane,'click', event => this.resetSelection(event));

        this.pane.classList.add('widget-initialized');
    }

    /**
     * Called after click on column checkbox.
     *
     * @param event Click
     */
    clickCheckbox(event) {
        if (!event.target.closest('input')) {
            return;
        }
        clearTimeout(this.inputTimeout);
        this.inputTimeout = setTimeout(() => this.updateResults(), App.settings.timeout * 2);
    }

    /**
     * Reset column selection to default.
     *
     * @param event Click
     */
    resetSelection(event) {
        if (!event.target.closest('.selector-reset')) {
            return;
        }

        Array.from(this.pane.querySelectorAll('input:checked')).forEach(item => {
            item.checked = false;
        });

        this.updateResults();
    }

    /**
     * Interface to parent filter component.
     *
     * @returns {Object} List of selected columns
     */
    getUrlParams() {
        // Column selector
        this.selector = this.pane.querySelector('.selector-columns');

        const selectedColumns = this.selector.querySelectorAll('input[type="checkbox"]:checked');
        const columnLists = Array.from(selectedColumns)
            .map(column => column.closest('[data-value]').dataset.value)
            .join(',');

        return {'columns': columnLists, 'save': true};
    }
}


/**
 * Search box widget
 *
 * //TODO: derive from FilterItemWidget
 *
 * @param coordinator
 * @param widgetElement
 * @constructor
 */
class FilterSearchBar {
    constructor(coordinator, widgetElement) {
        // Init vars
        this.coordinator = coordinator;
        this.inputTimeout = null;
        this.widgetElement = widgetElement;

        // Init elements and bind events
        if (!this.widgetElement) {
            return false;
        }

        this.coordinator.addFilter(this);

        // overwrite default
        this.prefix = this.widgetElement ? (this.widgetElement.dataset.filterPrefix || '') : '';
        this.paramTerm = this.widgetElement ? (this.widgetElement.dataset.filterParam || 'term') : '';
        this.paramField = this.widgetElement ? (this.widgetElement.dataset.filterField || 'field') : '';

        // Search box
        this.searchBox = this.widgetElement.querySelector('.search-term');
        if (this.searchBox) {
            const searchBoxEvents = ['input', 'paste', 'propertychange'];
            searchBoxEvents.forEach(eventType => {
                this.searchBox.addEventListener(eventType, event => {
                    this.articlesTermChanged(event);
                });
            });
        }

        this.searchField = this.widgetElement.querySelector('.search-field');
        if (this.searchField) {
            this.searchField.addEventListener('change', event => this.articlesTermChanged());
            this.searchField.closest('form').addEventListener('submit', event => {
                this.selectorsChanged(event);
                event.preventDefault();
            });
        }
    }

    /**
     * Called by App.filter.updateWidget.
     *
     */
    updateWidget(scope) {
        return true;
    }

    /**
     * Interface to parent filter component.
     *
     * @returns {Object} Search term and search field
     */
    getUrlParams() {
        const data = {};

        const prefix = this.prefix;
        data[prefix + this.paramTerm] = this.getSearchTerm();
        data[prefix + this.paramField] = this.getSearchField();

        return data;
    }

    /** Interface to parent filter component.
     *
     * @param source The filter that triggered the update
     */
    updateFilter(source) {
        clearTimeout(this.inputTimeout);
    }

    /**
     * Called after all input events in search bar (input, paste, propertychange) and after search field was changed.
     */
    articlesTermChanged() {
        clearTimeout(this.inputTimeout);
        this.inputTimeout = setTimeout(() => {
            this.coordinator.updateResults(this);
        }, App.settings.timeout);
    }

    /**
     * Called on form submit event.
     */
    selectorsChanged() {
        this.coordinator.updateResults(this);
    }

    /**
     * Get articles search term.
     *
     * @returns {string} Search term
     */
    getSearchTerm() {
        let term = this.searchBox ? this.searchBox.value : undefined;
        if (term !== undefined) {
            term = term.trim();
        }
        return term;
    }

    /**
     * Get articles search field.
     *
     * @returns {string} Search field (type, e.g. status, signature, title, ...)
     */
    getSearchField() {
        let field = this.searchField ? this.searchField.value : undefined;
        if (field !== undefined) {
            field = field.trim();
        }
        return field;
    }
}

/**
 * Selector widget
 *
 * //TODO: derive from FilterItemWidget
 *
 * @param coordinator
 * @param widgetElement
 */
class FilterSelector {
    constructor(coordinator, widgetElement) {
        // Init vars
        this.coordinator = coordinator;
        this.widgetElement = widgetElement;
        this.dropdown = null;

        // From which attribute will the value be retrieved in checkbox lists, e.h. data-value or data-id?
        this.widgetCheckboxlist = null;
        this.valueAttribute = 'value';
        this.clearParameters = widgetElement.dataset.filterClear;
        this.param = widgetElement.dataset.filterParam;

        if (!this.initWidgets()) {
            console.log("Error initializing selector widget.");
            return false;
        }

        this.coordinator.addFilter(this);
    }

    /**
     * Initialize widget and bind event listeners.
     *
     * @returns {boolean} False if no dropdown or checkbox list exists
     */
    initWidgets() {
        // For filters in the table header, find the old widget element after the table was updated
        // TODO: more elegant way?
        const tableElement = this.coordinator.getResultTable();
        const newWidgetElement = tableElement ? tableElement.querySelector('[data-filter-param="' + this.param + '"]') : undefined;
        if (newWidgetElement) {
            this.widgetElement = newWidgetElement;
        }

        const newDropdown = this.widgetElement ? this.widgetElement.querySelector('.widget-dropdown-selector') : undefined;
        if (newDropdown && (newDropdown !== this.dropdown)) {
            this.dropdown = newDropdown;
            this.dropdown.addEventListener('changed', event => this.updateResults(event));
            return true
        }

        const newCheckboxList = this.widgetElement ? this.widgetElement.querySelector('.widget-checkboxlist') : undefined;
        if (this.widgetCheckboxlist && (newCheckboxList !== this.widgetCheckboxlist)) {
            this.widgetCheckboxlist = newCheckboxList;
            this.widgetCheckboxlist.addEventListener('change', event => this.updateResults(event));
            return true;
        }

        return false;
    }

    /**
     * Called by App.filter.updateWidget.
     *
     * After the table was reloaded, the button is replaced,
     * therefore rebind events.
     *
     * @param scope
     * @returns {boolean}
     */
    updateWidget(scope) {
        return this.initWidgets();
    }

    /**
     * Load results.
     */
    updateResults() {
        this.coordinator.updateResults(this, this.clearParameters);
    }

    /**
     * Return the params to the filter coordinator.
     *
     * If no parameter name is set, the value is returned in getPath().
     *
     * @returns {Object} Selected options of all FilterSelectors on page.
     */
    getUrlParams() {
        const data = {};

        if (this.param) {
            let selected = '';
            if (this.dropdown) {
                selected = (this.dropdown && this.dropdown.widgetDropdownSelector) ?
                    this.dropdown.widgetDropdownSelector.getValue() : '';
            } else if (this.widgetCheckboxlist) {
                const inputs = Array.from(this.widgetCheckboxlist.querySelectorAll('input:checked'));
                selected = inputs.map(item => {
                        return item.closest('[data-' + this.valueAttribute + ']').dataset[this.valueAttribute];
                    }
                ).join(',');

            }

            data[this.param] = selected;
        }

        return data;
    }

    /**
     * Return the params to the filter coordinator.
     *
     * If a parameter name is set, the value is returned in getUrlParams().
     *
     * @returns {{}}
     */
    getPath() {
        let selected;

        if (!this.param) {
            selected = (this.dropdown && this.dropdown.widgetDropdownSelector) ?
                this.dropdown.widgetDropdownSelector.getValue() : '';
        }
        return selected;
    }
}

/**
 * Base class of the property filter
 */
class FilterFacetsBase extends FilterItemWidget {

    /**
     * Initialize widget and bind event listeners.
     *
     */
    constructor(element, name, parent) {
        super(element, name, parent);
        this.filterParameter = undefined;
        this.flagsParameter = undefined;
    }

    initWidget() {
          if (!this.coordinator) {
            return;
        }

        // Other filters were updated...
        this.listenEvent(
            this.coordinator.widgetElement,
            'epi:update:filter',
            (event) => this.onFilterUpdated(event)
        );

        // Search field content changed...
        this.listenEvent(
            this.widgetElement.querySelector('.widget-filter-facets-term'),
            'input',
            (event) => this.onTermChanged(event)
        );

        // Reset button clicked...
        this.listenEvent(
            this.widgetElement.querySelector('.widget-filter-facets-reset'),
            'click',
            (event) => this.onResetClicked(event)
        );

        // Descendants, invert, or all checkbox changed...
        this.listenEvent(
            this.widgetElement.querySelector('.widget-filter-facets-options'),
            'input',
            (event) => this.onFlagsChanged(event),
            'input'
        );

        // Node clicked...
        this.listenEvent(
            this.widgetElement,
            'change',
            (event) => this.onNodeChanged(event)
        );

        this.loadFacets();
        this.updateTabCounter();
    }

    /**
     * Reload properties
     *
     * @param event
     */
    onFilterUpdated(event) {
        if (event.detail.data.source !== this) {
            this.loadFacets();
        }
    }

    /**
     * Fired when a checkbox for selection modifications changed
     *
     * @param {Event} event
     */
    onFlagsChanged(event) {
        const widgetFilterFacetsOptions = event.target.closest('.widget-filter-facets-options');
        if (!widgetFilterFacetsOptions) {
            return;
        }
        clearTimeout(this.inputTimeout);

        const flags = [...widgetFilterFacetsOptions.querySelectorAll('input:checked')]
            .map((node) => node.value);

        this.widgetElement.dataset.filterFlags = flags.join(',');
        this.coordinator.updateResults(this);
    }

    /**
     * Fired on search term changes
     *
     * @param {Event} event
     */
    onTermChanged(event) {
        clearTimeout(this.inputTimeout);
        if (event.keyCode === 13) {
            event.preventDefault();
        }
        this.inputTimeout = setTimeout( () => this.loadFacets(), App.settings.timeout);
    }

    onResetClicked(event) {
        this.widgetElement.querySelectorAll('input.property:checked').forEach((node) => node.checked = false);
        this.widgetElement.dataset.filterSelected = '';
        this.updateTabCounter();
        this.coordinator.updateResults(this);
    }

    /**
     * Fired when a node is (de)selected
     *
     * @param {Event} event
     */
    onNodeChanged(event) {
        const widgetFilterFacetResults = event.target.closest('.widget-filter-facets-results')
        if (!widgetFilterFacetResults) {
            return;
        }
        clearTimeout(this.inputTimeout);

        const selected = [...widgetFilterFacetResults.querySelectorAll('input.property:checked')]
            .map((node) => node.value);

        this.widgetElement.dataset.filterSelected = selected.join(',');
        this.updateTabCounter();
        this.emitEvent('epi:load:facets');

        this.inputTimeout = setTimeout( () => this.coordinator.updateResults(this), App.settings.timeout);
    }

    clearWidget() {
        this.emitEvent('epi:close:facets');
        super.clearWidget();
        const allCheckbox = this.widgetElement.querySelector('.widget-filter-facets-all input');
        const inverseCheckbox = this.widgetElement.querySelector('.widget-filter-facets-inverse input');
        if (this.widgetElement.dataset.filterSelected
            || (allCheckbox && allCheckbox.checked)
            || (inverseCheckbox && inverseCheckbox.checked)) {
            this.coordinator.updateResults();
        }
    }

    updateTabCounter() {
        const selectedValue = this.widgetElement.dataset.filterSelected;
        const selectedCount = selectedValue ? selectedValue.toString().split(',').length : 0;
        const countPostfix = selectedCount > 0 ? ' (' + selectedCount + ')' : '';
        const caption = (this.widgetElement.dataset.filterCaption || '') + countPostfix;

        const tabSheet = this.widgetElement.closest('.widget-tabsheets-sheet');
        const tabName = tabSheet ? tabSheet.dataset.tabsheetId : undefined;

        const tabsWidget = this.getWidget(this.widgetElement, 'tabsheets');
        if (tabsWidget) {
            tabsWidget.setTitle(tabName, caption);
        }

    }

    showLoader() {
        const tree = this.widgetElement.querySelector('.widget-filter-facets-results');
        if (tree) {
            const loader = Utils.spawnFromString('<div class="loader" data-snippet="rows"></div>');
            tree.replaceChildren(loader);
        }
    }

    hideLoader() {
        const loader = this.widgetElement.querySelector('.loader');
        if (loader) {
            loader.remove();
        }
    }

    /**
     * Load properties
     */
    loadFacets() {
        if (!this.coordinator) {
            return;
        }

        App.ajaxQueue.stop('filter-facets-' + this.filterParameter.replace('.','-'));

        let articleParams = this.getArticleParams();
        let facetParams = this.getFacetParams();

        const data = $.extend({}, facetParams, articleParams);
        data.template = 'select';

        const action = this.widgetElement.dataset.filterAction.split('?')[0];
        const url = App.databaseUrl + action + '?' + jQuery.param(data);

        let self = this;
        App.ajaxQueue.add('filter-facets-' + this.filterParameter.replace('.','-'),
            {
                type: 'GET',
                url: url,
                dataType: 'html',
                beforeSend: function (xhr) {
                    self.showLoader();
                },
                success: function (data, textStatus, xhr) {
                    App.replaceDataSnippets(data, self.widgetElement);
                    self.emitEvent('epi:load:facets');
                },
                error: function (xhr, textStatus, errorThrown) {
                    // If aborted (see above, https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/abort)
                    if ((xhr.readyState === XMLHttpRequest.UNSENT) && (xhr.status === 0)) {
                        return;
                    }

                    App.showMessage(errorThrown, textStatus);
                },
                complete: function (xhr, textStatus) {
                    self.hideLoader();
                }
            }
        );
    }

    /**
     * Get the selected facets
     *
     * Override in child classes.
     *
     * @return {{}} An object with the property type name as key and items as value.
     *              Each item is an object with the id and label of the property.
     */
    getFacets() {
        return {};
    }

    /**
     * Get properties search value
     *
     * @returns {string}
     */
    getFilterTerm() {
        const input = this.widgetElement.querySelector('input.widget-filter-facets-term');
        let term = input ? input.value : undefined;
        if (term !== undefined) {
            term = term.trim();
        }
        return term;
    }

    /** Get article parameters
     *
     * @return {Object}
     */
    getArticleParams() {
        // TODO: only get params with the article prefix (filter out irrelevant params)
        let articleParams = this.coordinator.getUrlParams(true);
        delete articleParams[this.filterParameter];
        delete articleParams[this.flagsParameter];
        return articleParams;
    }

    /**
     * Return parameters to select articles
     *
     * @returns {Object}
     */
    getUrlParams() {
        let params = {};
        params[this.filterParameter] = this.widgetElement.dataset.filterSelected;
        if (this.flagsParameter && this.widgetElement.dataset.filterFlags) {
            params[this.flagsParameter] = this.widgetElement.dataset.filterFlags;
        }
        return params;
    }

    /**
     * Get facet parameters
     *
     * Overwrite in derived classes
     *
     * @return {{}}
     */
    getFacetParams() {
        return {}
    }
}

class FilterProperties extends FilterFacetsBase {

    colors = [
        '#E69F00',
        '#56B4E9',
        '#009E73',
        '#bb3fc3',
        '#0072B2'
    ];

    /**
     * Initialize widget and bind event listeners.
     *
     * // TODO: revise parameters,
     *          replace properties.{propertytype} and descent.{propertytype} by
     *          properties.{propertytype}.ids and properties.{propertytype}.descent
     */
    constructor(element, name, parent) {
        super(element, name, parent);
        let propertytype = this.widgetElement.dataset.filterPropertytype;
        this.filterParameter = 'properties.' + propertytype + '.selected';
        this.flagsParameter = 'properties.' + propertytype + '.flags';
    }

    getFacetParams() {
        return {
            selected: this.widgetElement.dataset.filterSelected,
            term: this.getFilterTerm(),
            field: 'lemma',
            references: 0
        };
    }

    /**
     * Get the selected facets
     *
     * Override in child classes.
     *
     * @return {{}} An object with the label, color and id indexed by property id.
     */
    getFacets() {
        let data = {};

        // Clear legend color
        this.widgetElement.querySelectorAll('.node .tree-meta').forEach(
            (elm) => {
                elm.style.backgroundColor = '';
                elm.style.color = '';
            }
        );

        let colorIdx = 0;
        const checked = [...this.widgetElement.querySelectorAll('input.property:checked')];
        for (let elm of checked) {

            const node = elm.closest('.node');
            if (node) {
                const nodeColor = this.colors[colorIdx % this.colors.length];

                // Set data
                const label = elm.closest('label');
                if (label) {
                    data[elm.value] = {
                        label: label.textContent,
                        color: nodeColor,
                        id: elm.value
                    };
                }

                // Set legend color
                const metaElm = node.querySelector('.tree-meta');
                if (metaElm) {
                    metaElm.style.backgroundColor = nodeColor;
                    metaElm.style.color = 'white';
                }

                colorIdx++;
            }
        }
        return data;
    }

}

class FilterProjects extends FilterFacetsBase {

    /**
     * Initialize widget and bind event listeners.
     *
     */
    constructor(element, name, parent) {
        super(element, name, parent);
        this.filterParameter =  'articles.projects';
    }

    getFacetParams() {
        return {
            selected: this.widgetElement.dataset.filterSelected,
            term: this.getFilterTerm(),
            field: 'name'
        };
    }
}

/**
 * Sort widget
 *
 * //TODO: derive from FilterItemWidget
 * @param coordinator
 * @param widgetElement Attach to
 * @constructor
 */
class FilterSort {
    constructor(coordinator, widgetElement) {
        // Init vars
        this.coordinator = coordinator;
        this.widgetElement = widgetElement;

        // Init elements and bind events
        if (!this.widgetElement) {
            return false;
        }

        this.coordinator.addFilter(this);

        // overwrite default
        this.prefix = this.widgetElement ? (this.widgetElement.dataset.filterPrefix || '') : '';

        // Sort selector
        this.sortSelector = this.widgetElement.querySelector('select.widget-filter-item-sort-field');
        if (this.sortSelector) {
            this.sortSelector.addEventListener('change', event => this.selectorsChanged(event));
        }
    }

    /**
     * Called by App.filter.updateWidget.
     *
     * @param scope
     * @returns {boolean}
     */
    updateWidget(scope) {
        if (this.sortSelector) {
            try {
                const sortKey = this.coordinator.getResultTable().dataset.sortkey;
                this.sortSelector.value = sortKey;
            } catch {
                console.log("Sort key is missing");
            }
        }

        return true;
    }

    /**
     * Interface to parent filter component.
     *
     * @returns {Object} Object with filter params
     */
    getUrlParams() {
        const data = {};

        // TODO: use prefix in paginator
        const prefix = this.prefix;
        data[prefix + 'sort'] = this.getSortField();
        data[prefix + 'direction'] = this.getSortDirection();

        return data;
    }

    /**
     * Interface to parent filter component.
     *
     * @param source The filter that triggered the update
     */
    updateFilter(source) {
    }

    /**
     * Called after selector was changed.
     */
    selectorsChanged() {
        this.coordinator.updateResults(this);
    }

    /**
     * Get sort field.
     *
     * @returns {String} Column name that is current sort key
     */
    getSortField() {
        if (this.sortSelector) {
            return this.sortSelector.value;
        } else {
            const table = this.coordinator.getResultTable();
            return table ? table.dataset.sortkey : '';
        }
    }

    /**
     * Get sort direction (ascending or descending).
     *
     * @returns {String} Sort direction ("asc" or "desc")
     */
    getSortDirection() {
        if (this.sortSelector) {
            return 'asc';
        } else {
            const table = this.coordinator.getResultTable();
            return table ? table.dataset.sortdir : 'asc';
        }
    }
}

/**
 * Adds the template, mode, and lanes parameter to queries
 *
 * //TODO: derive from FilterItemWidget
 * @param coordinator
 * @param widgetElement
 * @constructor
 */
class FilterTemplate {
    constructor(coordinator, widgetElement) {
        // Init vars
        this.coordinator = coordinator;
        this.widgetElement = widgetElement;
        this.template = 'table';
        this.mode = null;
        this.lanes = null;

        if (!this.initWidgets()) {
            return false;
        }

        this.coordinator.addFilter(this);
    }

    /**
     * Initialize widgets.
     *
     * @returns {boolean} False if widgetElement does not exist
     */
    initWidgets() {
        if (!this.widgetElement) {
            return false;
        }

        this.mode = this.widgetElement.dataset.filterMode;
        this.template = this.widgetElement.dataset.filterTemplate;
        this.lanes = this.widgetElement.dataset.filterLanes;

        return true;
    }

    /**
     * Called by App.filter.updateWidget.
     *
     * @param scope
     * @returns {boolean} False if widgetElement does not exist
     */
    updateWidget(scope) {
        return this.initWidgets();
    }

    /**
     * Interface to parent filter component.
     *
     * @returns {Object} Object that contains template and name of active filter pane
     */
    getUrlParams() {
        const params = {'template': this.template};

        if (this.mode) {
            params.mode = this.mode;
        }

        // Determine the lane from the active properties panel
        let lane = this.lanes;
        this.coordinator.filters.forEach(filter => {
            if (filter.widgetElement && filter.getActivePanel) {
                const activePanel = filter.getActivePanel();
                if (activePanel) {
                    lane = activePanel.dataset.segment || '';
                }

            }
        });

        if (lane) {
            params.lanes = lane;
        }

        return params;
    }

    /**
     * Interface to parent filter component.
     *
     * @param event
     */
    updateResults(event) {
        this.coordinator.updateResults(this);
    }
}

/**
 * Map filter
 * //TODO: derive from FilterItemWidget
 */
class FilterMap extends FilterItemWidget {
    constructor(coordinator, widgetElement) {
        super(widgetElement, 'filtermap', coordinator);

        // Init vars
        this.coordinator = coordinator;
        this.widgetElement = widgetElement;
        this.inputTimeout = null;
        this.widgetMap = null;

        if (!this.initWidgets()) {
            return false;
        }
        this.coordinator.addFilter(this);
        this.listenEvent(document,'epi:load:facets', event => this.onLoadFacets(event));
        this.listenEvent(document,'epi:close:facets', event => this.onCloseFacets(event));
    }

    /**
     * Nothing special, just update the class widget-initialized.
     *
     * @returns {boolean} False if widgetElement does not exist
     */
    initWidgets() {
        if (!this.widgetElement) {
            return false;
        }

        this.widgetMap = this.getWidget(this.widgetElement, 'map');
        if (this.widgetMap !== undefined) {
            this.widgetMap.updateMarkers();
        }

        if (this.widgetElement.classList.contains('widget-initialized')) {
            return false;
        }
        this.widgetElement.classList.add('widget-initialized');

        return true;
    }

    onLoadFacets(event) {
        if (!this.widgetMap || !this.widgetElement || !event.detail.sender) {
            return;
        }

        const facetWidget = event.detail.sender;

        if (facetWidget.widgetElement.classList.contains('widget-filter-item-properties')) {
            const data = facetWidget.getFacets();
            this.widgetMap.updateColors(data);
        }
    }

    onCloseFacets(event) {
        if (!this.widgetMap || !this.widgetElement || !event.detail.sender) {
            return;
        }
        this.widgetMap.updateColors();
    }


        /**
     * Called by App.filter.updateWidget.
     * After the data was reloaded (by snippet replacement), update the map
     *
     * @param scope
     * @returns {boolean} False if widgetElement does not exist
     */

    updateWidget(scope) {
        return this.initWidgets();
    }

    /**
     * Called by App.filter after filter conditions changed
     *
     * @param source Current filter
     * @returns {boolean} False if source param is current filter
     */
    updateFilter(source) {
        if (source === this) {
            return false;
        }
    }

    /**
     * Interface to parent filter component.
     *
     * @returns {Object} Object that contains map filter options for URL
     */
    getUrlParams() {
        if (!this.widgetMap) {
            return {};
        }
        const mapCenter = this.widgetMap.getCenter();
        return {'template': 'map', 'lat': mapCenter.lat, 'lng': mapCenter.lng, 'sort': 'distance', 'direction': 'asc'};
    }
}

/**
 * Register widget classes in the app
 */
window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['filter'] = FilterWidget;
window.App.widgetClasses['filter-item-columns'] = FilterColumns;
window.App.widgetClasses['filter-item-properties'] = FilterProperties;
window.App.widgetClasses['filter-item-projects'] = FilterProjects;
window.App.widgetClasses['filter-item-fixed'] = FilterFixed;
