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
        let data = {};
        data['database'] = taskConfig.database || '';
        data['task'] = taskConfig.task;
        data['prompts'] = taskConfig.prompts || 'default';
        data['tagname'] = taskConfig.tagname || '';
        data['itemtype'] = taskConfig.itemtype || '';
        data['multinomial'] = taskConfig.multinomial || '';

        if (taskConfig.input === 'article') {
            const articlesId = this.widgetElement.closest('[data-root-table="articles"]').dataset.rootId;
            data['record'] = 'articles-' + articlesId;
        }
        else if (taskConfig.input === 'item') {
            data['input'] = this.getItemContent();
        }
        else {
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
            this.result(response, taskConfig);
        }
        else if  (state !== 'PENDING') {
            this.error(i18n.t('An error occurred'), response, taskConfig);
        }
        else if (taskId) {
            let pollUrl = `${url}/${taskId}?task=${taskConfig.task}`;
            if (taskConfig.database) {
                pollUrl = pollUrl + `&database=${taskConfig.database || ''}`;
            }
            if (taskConfig.tagname) {
                pollUrl = pollUrl + `&tagname=${taskConfig.anno || ''}`;
            }
            if (taskConfig.itemtype) {
                pollUrl = pollUrl + `&itemtype=${taskConfig.itemtype || ''}`;
            }
            if (taskConfig.multinomial) {
                pollUrl = pollUrl + `&multinomial=${taskConfig.multinomial || ''}`;
            }

            setTimeout(() => {
                fetch(pollUrl, {
                    method: 'GET',
                    headers: {'Content-Type': 'application/json','Accept': 'application/json'},
                })
                    .then(response => response.json())
                    .then(response => this.poll(url, response, taskConfig))
                    .catch(response => this.error('An error occurred', response, taskConfig));
            }, this.delay);
        }
    }

    result(response, taskConfig) {
        if (!this.isPolling) {
            return;
        }

        this.success();

        // Results to be inserted into the source item (summarize & annotation tasks)
        const resultFields = taskConfig['fields'];
        const itemElement = this.widgetElement.closest('[data-row-table="items"]');
        if (itemElement && resultFields) {

            const responseData = {
                result: Utils.getValue(response, 'task.result.answers.0.llm_result'),
                state: Utils.getValue(response, 'task.state')
            };

            const data = {};
            data['table'] = itemElement.dataset.rowTable;
            data['id'] = itemElement.dataset.rowId;
            data['content'] = {};
            for (const fieldName in resultFields) {
                data['content'][fieldName] = Utils.getValue(responseData, resultFields[fieldName]);
            }
            this.emitEvent('epi:update:item', data);
        }

        // Results to be added as items
        const itemType = taskConfig['itemtype'];
        const sectionType = taskConfig['sectiontype'];
        if (itemType && sectionType) {
            const answers = Utils.getValue(response, 'task.result.answers');

            const itemContent = [];
            for (const idx in answers) {
                itemContent.push({
                    // 'content': Utils.getValue(answers[idx], 'llm_result'),
                    'properties_id': Utils.getValue(answers[idx], 'properties_id'),
                    'properties_label': Utils.getValue(answers[idx], 'properties_label'),
                    'value': Utils.getValue(answers[idx], 'value')
                });
            }

            const itemData = {
                'itemtype':  itemType,
                'sectiontype': sectionType,
                'items': itemContent
            }

            this.emitEvent('epi:import:item', itemData);
        }

        this.button.textContent = this.button.dataset.serviceLabel;
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

        const fieldElement = docElement.querySelector('[data-row-field="' + serviceConfig.input + '"] input');
        return Utils.getInputValue(fieldElement);
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
