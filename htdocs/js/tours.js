/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import Utils from '/js/utils.js';
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

    addClickHandler(element, driverObj) {
        if (element && element.tourListener) {
            element.removeEventListener('click', element.tourListener);
        }

        if (element) {
            element.tourListener = () => driverObj.moveNext();
            element.addEventListener('click', element.tourListener);
        }
    }

    removeClickHandler(element) {
        if (element && element.tourListener) {
            element.removeEventListener('click', element.tourListener);
        }
        element.tourListener = null;
    }

    startArticlesTour() {

        const self = this;
        const driverObj = this.driver({
            // TODO: Fix z index
            // onDestroyStarted: async () => {
            //     if (!driverObj.hasNextStep() || await App.confirmAction("Are you sure to exit the tour?")) {
            //         driverObj.destroy();
            //     }
            // },
            showProgress: true,
            steps: [
                {
                    element: '.sidebar-left',
                    popover: {
                        title: 'Left sidebar',
                        side: "right",
                        description: 'Use filters in the left sidebar to select a specific project.',
                        align: 'start'
                    }
                },
                {
                    element: '[data-toggle=select-propertytype-pane]',
                    popover: {
                        title: 'Filters',
                        side: "left",
                        description: 'Klick the plus button to see more filter options.',
                        align: 'start'
                    },
                    onHighlightStarted: function(element, step, options) {
                        self.addClickHandler(element, driverObj);
                    },
                    onDeselected: function(element, step, options) {
                        self.removeClickHandler(element);
                    }
                },
                {
                    element: '#select-propertytype-pane',
                    popover: {
                        title: 'Filters',
                        side: "right",
                        description: 'Select one of the category systems to show all properties in the sidebar.Then, you can filter articles that use one of the properties.',
                        align: 'start'
                    },
                    onHighlightStarted: function(element, step, options) {
                        if (!Utils.isElementVisible(element)) {
                            document.querySelector('[data-toggle=select-propertytype-pane]').click();
                        }
                        self.addClickHandler(element, driverObj);
                    },
                    onDeselected: function(element, step, options) {
                        self.removeClickHandler(element);
                        if (Utils.isElementVisible(element)) {
                            document.querySelector('[data-toggle=select-propertytype-pane]').click();
                        }
                    }
                },

                {
                    element: '[data-list-itemof=epi_articles]',
                    popover: {
                        title: 'Show article preview',
                        description: 'Click on an article to view the content in the sidebar.',
                        side: "bottom",
                        align: 'start'
                    },onHighlightStarted: function(element, step, options) {
                        self.addClickHandler(element, driverObj);
                    },
                    onDeselected: function(element, step, options) {
                        self.removeClickHandler(element);
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
                    onHighlightStarted: async function(element, step, options) {
                        if (!Utils.isElementVisible(element)) {
                            document.querySelector('[data-list-itemof=epi_articles]').click();
                        }
                        try {
                            await Utils.waitFor('.sidebar-right', 3000);
                        } catch (e) {
                            // Handle sidebar not appearing, maybe skip or report error
                        }
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

