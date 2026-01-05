/*
 * Service widget - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {BaseWidget} from '/js/base.js';
import Utils from '/js/utils.js';
import {i18n}  from '/js/lingui.js';
import {SelectWindow} from "/widgets/js/frames.js";

/**
 * Buttons to send data to a service and add the result to the property or article.
 *
 * Add a div with the css class widget-service-button and the following data attributes:
 * - data-service-name: The service name, e.g. 'summarize'.
 * - data-service-item: The ID of the item the widget belongs to
 * - data-service-data: The json encoded service configuration.
 *
 * For properties, the buttons are added to the target field (norm_data field).
 * For articles, buttons that use item data, are added to the source item that contains content to be sent to the service.
 *
 * Usually, the service name and service data is derived from the service configuration in a property or item.
 * The configuration includes the following fields:
 * - task: The task name, e.g. 'summarize'
 * - prompts: Optionally, the prompt template name to be used for the task. Empty by default.
 * - database: Database name
 * - input: The input type, either 'article' (the article id will be send to the service)
 *          or 'item' (the item content will be send).
 * - tagname: Optionally, for annotation tasks, the tag name. This should be a tag that is configured as links record.
 *            If you send a tag name to the services endpoint, the property type will be looked up from the configuration
 *            to assemble coding rules from the properties.
 * - itemtype: Optionally, for coding tasks, an item name. This determines what items are created by the service widget.
 *             The property used in the item will be looked up from the configuration.
 * - sectiontype: Optionally, for coding tasks, a section type. This determines the section where the item is inserted.
 * - fields: Optionally, for tasks that return results to be inserted into the source item.
 *           An object with field names as keys and service response fields as values.
 *           Example: `{ "content": "result", "value": "state"}`
 * - multinomial: Optionally, for multinomial tasks, a boolean value to indicate whether the task is multinomial or not.

 */
export class ServiceWidget extends BaseWidget {

    constructor(element, name, parent) {
        super(element, name, parent);

        this.delay = 1000;
        this.isPolling = false;
        this.progressBar = undefined;
    }

    initWidget() {
        const taskId = this.widgetElement.dataset.serviceTaskId;
        const taskState = this.widgetElement.dataset.serviceTaskState;
        if (taskId && (taskState === 'PENDING')) {
            this.refresh();
        }
    }

    showProgress() {
        if (!this.progressBar) {
            this.progressBar = this.widgetElement.querySelector('.widget-service-bar');
            if (!this.progressBar) {
                this.progressBar = Utils.spawnFromString(`<div class="widget-service-bar"></div>`);
                this.widgetElement.appendChild(this.progressBar);
            }

            $(this.progressBar).progressbar({
                disabled: false,
                value: false
            });
        }
    }

    refresh() {
        this.refreshUrl = this.widgetElement.dataset.serviceRefreshUrl;
        if (this.refreshUrl) {
            this.isPolling = true;
            this.showProgress();
            this.refreshTimeout = setTimeout(() => App.loadDataSnippets(this.refreshUrl, this.widgetElement), this.delay);
        }
    }
}


export class ServiceButtonWidget extends ServiceWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        this.delay = 1000;
        this.messageBox = undefined;
        this.progressBar = undefined;

        this.button = this.widgetElement.querySelector('a.button');
        this.listenEvent(this.button, 'click', (event) => this.onButtonClick(event));
    }

    onButtonClick(event) {
        if (this.isPolling) {
            this.stop();
        } else {
            this.start();
        }
        event.preventDefault();
    }

    getItemContent() {
        const itemElement = this.widgetElement.closest('[data-row-table="items"]');
        if (!itemElement) {
            return;
        }
        const fieldElement = itemElement.querySelector('[data-row-field="content"]');
        if (!fieldElement) {
            return;
        }

        if (!this.emitEvent('epi:save:form', {}, true)) {
            return false;
        }

        return Utils.getInputValue(fieldElement.querySelector('input'));
    }

    start() {
        if (this.isPolling) {
            return;
        }
        this.isPolling = true;

        this.button.dataset.serviceLabel = this.button.textContent;
        this.button.textContent = i18n.t('Stop');
        this.progressShow();
        this.messageShow(i18n.t('Please stand by, weaving the words may take a minute.'));

        // Base configuration
        let taskConfig;
        try {
            taskConfig = JSON.parse(decodeURIComponent(this.widgetElement.dataset.serviceData));
        } catch (e) {
            this.error(i18n.t('Invalid service data'));
            return;
        }

        let url = '/services/' + taskConfig.service;

        // Input
        const data = this.getData(taskConfig);
        if (data === false) {
            this.error(i18n.t('Invalid service input'));
            return;
        }

        fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/json','Accept': 'application/json'},
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(response => this.poll(url, response, taskConfig))
            .catch(response => this.error(i18n.t('An error occurred'), response, taskConfig));
    }

    /**
     * Get the data to be sent to the service.
     *
     * @param {object} taskConfig
     * @param {Boolean} input Whether to include input data or only options.
     * @return {object}
     */
    getData(taskConfig, input = true) {
        let data = {};
        data['database'] = taskConfig.database || '';
        data['task'] = taskConfig.task;
        data['prompts'] = taskConfig.prompts || '';
        data['tagname'] =  Utils.getValue(taskConfig, 'target.tagname', '');
        data['itemtype'] = Utils.getValue(taskConfig, 'target.itemtype', '');
        data['multinomial'] = taskConfig.multinomial || '';

        if (input) {
            if (taskConfig.input === 'article') {
                const articlesId = this.widgetElement.closest('[data-root-table="articles"]').dataset.rootId;
                data['record'] = 'articles-' + articlesId;
            } else if (taskConfig.input === 'item') {
                data['input'] = this.getItemContent();
            } else {
                data = false;
            }
        }

        return data;
    }

    /**
     * Get the polling URL for the service.
     *
     * @param {string} url
     * @param {string} taskId
     * @param {object} taskConfig
     * @return {string}
     */
    getPollUrl(url, taskId, taskConfig) {
        let pollUrl = `${url}/${taskId}?task=${taskConfig.task}`;
        const data = this.getData(taskConfig);

        const keys = ['database', 'tagname', 'itemtype', 'multinomial'];
        keys.forEach(key => {
            if (data[key]) {
                pollUrl += `&${key}=${data[key]}`;
            }
        });

        return pollUrl;
    }

    /**
     * Update the results and keep polling
     *
     * @param {string} url
     * @param {Promise} response The JSON response
     * @param {Object} taskConfig The task configuration
     */
    poll(url, response, taskConfig) {
        if (!this.isPolling) return;

        const state = Utils.getValue(response,'task.state');
        const taskId = Utils.getValue(response,'task.task_id');

        if (state === 'SUCCESS') {
            this.showResult(response, taskConfig);
        }
        else if  (state !== 'PENDING') {
            this.error(i18n.t('An error occurred'), response, taskConfig);
        }
        else if (taskId) {
            const pollUrl = this.getPollUrl(url, taskId, taskConfig);
            setTimeout(() => {
                fetch(pollUrl, {
                    method: 'GET',
                    headers: {'Content-Type': 'application/json','Accept': 'application/json'},
                })
                    .then(response => response.json())
                    .then(response => this.poll(url, response, taskConfig))
                    .catch(error => this.error('An error occurred: ' + error.message));
            }, this.delay);
        }
    }

    showResult(response, taskConfig) {
        if (!this.isPolling) {
            return;
        }

        this.success();

        // Default target fields for summarize and annotation tasks
        let targetFields;
        let targetContainer;
        if (taskConfig.task === 'summarize') {
            targetFields = Utils.getValue(taskConfig, 'target.fields', {'content': 'llm_result', 'value': 'state'});
            targetContainer = 'item';
        }
        else if (taskConfig.task === 'annotate') {
            targetFields = Utils.getValue(taskConfig, 'target.fields', {'content': 'llm_result', 'value': 'state'});
            targetContainer = 'item';
        }
        else {
            targetFields = Utils.getValue(taskConfig, 'target.fields', {
                'properties_id': 'properties_id',
                'properties_label': 'properties_label',
                'value': 'value'
            });
            targetContainer = 'article';
        }

        // Get result data and mixin the task state
        const answers = Utils.getValue(response, 'task.result.answers');
        const items = [];
        for (const idx in answers) {
            let answer = answers[idx];
            answer['state'] =  Utils.getValue(response, 'task.state');
            let content = {};
            for (const fieldName in targetFields) {
                content[fieldName] = Utils.getValue(answer, targetFields[fieldName]);
            }
            items.push(content);
        }

        if (items.length === 0) {
            return;
        }

        // Results to be inserted into the source item (summarize & annotation tasks)
        targetContainer = Utils.getValue(taskConfig,'target.container', targetContainer);

        // TODO: Dry.

        // Update the target item (target is the source item)
        if (targetContainer === 'item') {
            const targetItem = this.widgetElement.closest('[data-row-table="items"]');

            if (targetItem) {
                const data = {};
                data['table'] = targetItem.dataset.rowTable;
                data['id'] = targetItem.dataset.rowId;
                data['content'] = items[0];
                this.emitEvent('epi:update:item', data);
            }
        }

        // Add or update items in the same section
        else if (targetContainer === 'section') {
            const targetSection = this.widgetElement.closest('[data-row-table="sections"]');

            const targetItemtype = Utils.getValue(taskConfig,'target.itemtype');
            const targetSectiontype = targetSection.dataset.rowType;

            if (targetItemtype && targetSectiontype) {
                const itemData = {
                    'itemtype': targetItemtype,
                    'sectiontype': targetSectiontype, // TODO: Add section ID?
                    'items': items
                }
                this.emitEvent('epi:import:item', itemData);
            }
        }

        // Add or update items in other sections
        else if (targetContainer === 'article') {
            const targetItemtype = Utils.getValue(taskConfig,'target.itemtype');
            const targetSectiontype = Utils.getValue(taskConfig,'target.sectiontype');

            if (targetItemtype && targetSectiontype) {
                const itemData = {
                    'itemtype': targetItemtype,
                    'sectiontype': targetSectiontype,
                    'items': items
                }
                this.emitEvent('epi:import:item', itemData);
            }
        }
    }

    progressShow() {
        if (!this.progressBar) {
            this.progressBar = this.widgetElement.querySelector('.widget-service-bar');
            if (!this.progressBar) {
                this.progressBar = Utils.spawnFromString(`<div class="widget-service-bar"></div>`);
                this.widgetElement.appendChild(this.progressBar);
            }

            $(this.progressBar).progressbar({
                disabled: false,
                value: false
            });
        }
        Utils.show(this.progressBar);
    }

    progressHide() {
        Utils.hide(this.progressBar);
        this.button.textContent = this.button.dataset.serviceLabel;
    }

    messageShow(msg) {
        if (!this.messageBox) {
            this.messageBox = this.widgetElement.querySelector('.widget-service-msg');
            if (!this.messageBox) {
                this.messageBox = Utils.spawnFromString(`<div class="widget-service-msg"></div>`);
                this.widgetElement.appendChild(this.messageBox);
            }
        }
        Utils.show(this.messageBox, msg);
    }

    messageHide() {
        Utils.hide(this.messageBox)
    }

    success() {
        this.isPolling = false;
        this.progressHide();
        this.messageHide();
        this.button.textContent = this.button.dataset.serviceLabel;
    }

    error(msg, response, taskConfig) {
        this.isPolling = false;
        this.progressHide();
        this.messageShow(msg)

        const error = Utils.getValue(response, 'status.message');
        if (error) {
            self.emitEvent('app:show:message', {'msg' : error, 'status': 'error'});
        }
    }

    stop()   {
        this.isPolling = false;
        this.progressHide();
        this.messageHide();
    }
}


export class ReconcileButtonWidget extends ServiceWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        this.button = this.widgetElement.querySelector('a.button');
        this.listenEvent(this.button, 'click', (event) => this.onButtonClick(event));
        //this.input_toggle.addEventListener('keydown', event => this.onKeyDown(event));
    }

    onButtonClick(event) {
        let serviceConfig;
        try {
            serviceConfig = JSON.parse(decodeURIComponent(this.widgetElement.dataset.serviceData));
        } catch (e) {
            this.error(i18n.t('Invalid service data'));
            return;
        }
        this.openPopup(serviceConfig);
        event.preventDefault();
    }

    getTerm(serviceConfig) {
        const docElement = this.widgetElement.closest('.widget-document');
        if (!docElement) {
            return;
        }

        const inputFieldName = serviceConfig.input;
        if (inputFieldName === 'path') {
            const ancestorElement = docElement.querySelector('[data-row-field="parent_id"] input');
            let pathValue = Utils.getInputValue(ancestorElement);
            const lemmaElement = docElement.querySelector('[data-row-field="lemma"] input');
            let lemmaValue = Utils.getInputValue(lemmaElement);
            return [pathValue, lemmaValue].filter(Boolean).join(', ');

        } else {
            const fieldElement = docElement.querySelector('[data-row-field="' + inputFieldName + '"] input');
            return Utils.getInputValue(fieldElement);
        }
    }

    getTargetData() {
        const docElement = this.widgetElement.closest('.widget-document');
        if (!docElement) {
            return;
        }

        const fieldElement = docElement.querySelector('[data-row-field=norm_data] textarea');
        return Utils.getInputValue(fieldElement);
    }

    setTargetData(value) {
        const docElement = this.widgetElement.closest('.widget-document');
        if (!docElement) {
            return;
        }

        const fieldElement = docElement.querySelector('[data-row-field=norm_data] textarea');
        return Utils.setInputValue(fieldElement, value);
    }

    openPopup(serviceConfig) {

        let url = '/services/reconcile?q='
            + encodeURIComponent(this.getTerm(serviceConfig))
            + '&provider=' + serviceConfig['provider']
            + '&preview=1';

        if (serviceConfig['type']) {
            url += '&type=' + serviceConfig['type'];
        }

        const options = {
            title: "Reconcile",
            height: 500,
            width: 600,

            focus: true,
            url: url,

            selectOnClick: true,
            onSelect: (button) => {
                this.applyValue(button.dataset.value);
            }
        };

        new SelectWindow(options);
    }

    applyValue(value) {
        let normData = this.getTargetData();
        const prefix = Utils.getPrefix(value);

        normData = normData.split('\n').filter(line =>
            line !== '' && !line.startsWith(prefix + ':')
        );
        normData.push(value);
        normData = normData.join('\n');

        this.setTargetData(normData);
    }

}


/**
 * Register widget classes in the app
 */
window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['service'] = ServiceWidget;
window.App.widgetClasses['service-button'] = ServiceButtonWidget;
window.App.widgetClasses['reconcile-button'] = ReconcileButtonWidget;
