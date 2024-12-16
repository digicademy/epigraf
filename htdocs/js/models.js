/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */
import Utils from '/js/utils.js';
import {AjaxQueue} from '/js/requests.js';

/**
 * User settings model
 */
export class UsersModel {

    constructor() {
        this.user = null;
        this.role = null;
        this.scope = null;

        this.settings = new ConfigModel('user');
        this.session = new ConfigModel('session');
        this.activeTimeout = 300000; // 5 minutes

        this.initEvents();
    }

    /**
     * Init events
     */
    initEvents() {
        if (!document.querySelector('body').classList.contains("userrole_guest")) {
            setTimeout((ev) => this.updateActive(ev), this.activeTimeout);
        }
    }

    /**
     * Assign user data passed from the server to the user model
     *
     * @param {Object} values
     */
    load(values) {
        if (values && values.settings) {
            this.settings.settings = values.settings;
            delete values.settings;
        }

        if (values && values.session) {
            this.session.settings = values.session;
            delete values.session;
        }

        Object.assign(this, values);

        this.settings.readonly = ((this.role || 'guest') === 'guest');
        this.session.readonly = ((this.role || 'guest') === 'guest');
    }

    /**
     * Get the preferred color mode (light or dark) and store it in the user session
     * (not really needed by now, because the theme is selected using a conditional import in default.css.
     *  just for demonstration purposes)
     */
    storeTheme() {
        if (!window.matchMedia) {
            return;
        }

        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            this.session.save('ui', 'theme', 'dark');
        } else {
            this.session.save('ui', 'theme', 'light');
        }
    }


    /**
     * Signal the server that the user is still online.
     * Keeps the login active.
     *
     * @return {Promise<void>}
     */
    async updateActive() {
        let url = '/users/login.json';
        const token = Utils.getParameterByName('token');
        if (token) {
            url = url + '?token=' + token;
        }

        try {
            const response = await fetch(url);
            const data = await response.json();

            if (!data.success) {
                App.user.showLogin();
            }
        } catch (error) {
            App.showMessage('Could not connect to Epigraf: ' + error);
        }

        setTimeout((ev) => this.updateActive(ev), this.activeTimeout);
    }

    /**
     * Show a login popup
     *
     * ### Options
     * onLogin: Function to call after the login was successful
     *
     * @param {Object} options Options passed to the popup
     */
    showLogin(options = {}) {
        options =  Object.assign(
            {
                title:'Login',
                modal:true,
                name: 'app.login',
                ajaxButtons: true,
                height: 400,
                width: 400
            },
            options
        );

        // Call onLogin() callback after login succeded
        options.onLoad = function (popupWidget, popupData, popupOptions) {
            const userElement = Utils.querySelectorAndSelf(
                Utils.spawnFromString(popupData,undefined, false),
                '[data-row-table="users"][data-row-id]'
            );

            if (userElement && userElement.dataset.rowId) {
                popupWidget.closeWindow();
                if (options.onLogin) {
                    options.onLogin();
                }
            }
        }

        App.openPopup('/users/login?redirect=/users/view/me', options);
    }
}

/**
 * Manage settings
 *
 * @param {String} storage The storage engine, 'user' or 'session'.
 */
export class ConfigModel {

    constructor(storage = 'user') {

        this.settings = {};
        this.settingsQueue = {};
        this.readonly = true;
        this.storage = storage;

        this.ajaxQueue = new AjaxQueue();
   }

    /**
     * Get user settings
     *
     * User settings are passed from PHP to JS in the HTML template
     *  // TODO: Load async, dont' pass to HTML template
     *
     * @param {String} scope The scope of the settings, e.g. 'columns'.
     * @param {String} key The setting key, e.g. the model name 'epi.articles'.
     * @param {*} defaultValue - Default value if setting is not found
     * @return {*|null}
     */
    get(scope, key, defaultValue) {
        let value;
        if (this.settings && scope && !key) {
            value = this.settings[scope];
        }
        else if (this.settings && scope && this.settings[scope] && key) {
            value = this.settings[scope][key];
        }
        if (value === undefined) {
            return defaultValue;
        } else {
            return value;
        }
    }

    /**
     * Update the user settings and send them to the server
     *
     * The value has to be an object, because it is merged with the existing settings.
     * Sending settings is delayed by 1 second to prevent too many requests.
     *
     * @param {String} scope The scope of the settings, e.g. 'columns' or 'ui'.
     * @param {String} key A unique key for the setting
     * @param {Object} value The new value. It has to be an object.
     * @param {boolean} merge Whether to merge the new value with the existing settings or to replace them
     */
    save(scope, key, value, merge= true) {
        if (this.readonly) {
            return;
        }

        // Update settings
        this.settings = Utils.setValue(this.settings, scope + '.' + key, value, merge);

        // Collect and merge settings until they are transfered
        // TODO: What if consecutive calls have different scopes or keys? Only the last one will be saved. Fix.

        const url = '/users/settings/' + scope + '/' + key + '?storage=' + this.storage;
        this.settingsQueue[url] = {...(this.settingsQueue[url] || {}), ...value};

        const request = {
            type: merge ? 'PATCH' : 'PUT',
            url: url,
            data: this.settingsQueue[url],
            dataType: 'json',
            success: ()=> this.settingsQueue[url] = {}
        };

        this.ajaxQueue.stop(url);
        this.ajaxQueue.add(url, request, 1000);
    }

}
