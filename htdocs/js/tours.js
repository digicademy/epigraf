/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {BaseWidget} from '/js/base.js';

/**
 * Tour through the application
 *
 */
export class Tours extends BaseWidget {

    constructor(element, name, parent) {
        super(element, name, parent);
        this.driver = null;
    }

    /**
     * Widget initialization: Start tours after all widgets are ready.
     */
    initWidget() {
        const tour = App.user.settings.get('tours', 'start', false);
        if (tour && App.getEndpoint() === 'epi.articles.index') {

            // TODO: async load JS and CSS
            this.driver = window.driver.js.driver;

            this.startArticlesTour();
        }
    }

    startArticlesTour() {

        // TODO: load tour data asynchronously

        const driverObj = this.driver({
            showProgress: true,
            steps: [
                {
                    element: '[data-list-itemof=epi_articles]',
                    popover: {
                        title: 'Show article preview',
                        description: 'Click on an article to view the content in the sidebar.',
                        side: "bottom",
                        align: 'start'
                    }
                },
                {
                    element: '.sidebar-right',
                    popover: {
                        title: 'Inspect article',
                        description: 'The sidebar shows a preview of the article content.',
                        side: "left",
                        align: 'center'
                    },
                    onHighlightStarted: function(element, step, options) {
                        document.querySelector('[data-list-itemof=epi_articles]').click();
                    }
                },
                {
                    element: '.sidebar-right button.role-open',
                    popover: {
                        title: 'Open article',
                        description: 'Open the article in a new tabsheet.',
                        side: "top",
                        align: 'center'
                    }
                },

            ]
        });

        driverObj.drive();
    }
}

