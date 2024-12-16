/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 * This is an enhanced version of @extralam's ajax queue.
 *
 */

/**
 * Ajax request queue
 *
 * Run ajax requests synchronously in a queue
 *
 *  Usage:
 *     let queue = new AjaxQueue();
 *
 *  Then add requests, optionally with a tag name:
 *     queue.add('test',{
 *         url: 'url',
 *         complete: function() {
 *             console.log('ajax completed');
 *         }
 *     });
 *
 *  Cancel tagged requests:
 *     queue.stop('test');
 *
 *   Cancel all requests:
 *     queue.stop();
 */
export class AjaxQueue {

    constructor() {
        this.pendingRequests = [];
        this.currentRequests = [];

        this.parallelRequests = 3;
        this.requesting = 0;
        this.debug = false;
    }

    /**
     * Add request to the queue
     *
     * @param {string} tag An identifier for the request that can be used to stop it later
     * @param {Object} request Options for the $.ajax() call
     * @param {Number} delay An optional delay in milliseconds before the request is started
     */
    add(tag, request, delay) {
        if (this.debug) console.log(`Add request to ${this.pendingRequests.length} pending requests while there are ${this.currentRequests.length} current requests.`);
        this.pendingRequests.push({name: tag, request: request, delay: delay});
        this.next();
    }

    /**
     * Stop all pending or current requests
     *
     * @param {string} tag Optional tag name to stop only requests with this tag. See add() how to define a tag.
     */
    stop(tag) {
        if (this.debug) console.log(`Stop ${this.pendingRequests.length} pending requests.`);

        for (let i = 0; i < this.pendingRequests.length; i++) {
            if ((tag === undefined) || (this.pendingRequests[i]['name'] === tag)) {
                this.pendingRequests.splice(i, 1);
                i--;
            }
        }

        if (this.debug) console.log(`Stop ${this.currentRequests.length} current requests.`);
        for (let i = 0; i < this.currentRequests.length; i++) {
            if ((tag === undefined) || (this.currentRequests[i]['name'] === tag)) {
                const currentRequest = this.currentRequests[i];
                this.currentRequests.splice(i, 1);
                i--;

                if (currentRequest.timeout) {
                    clearTimeout(currentRequest.timeout);
                }
                if (currentRequest.request && currentRequest.request.abort) {
                    currentRequest.request.abort();
                }
                this.next();
            }
        }
    }

    /**
     * Start next request from the queue
     */
    next() {
        const self = this;

        if (this.currentRequests.length >= this.parallelRequests) {
            return;
        }

        if (this.pendingRequests.length === 0) {
            return;
        }

        if (this.debug) console.log(`Get next of ${this.pendingRequests.length} pending requests`);
        const nextRequest = this.pendingRequests.splice(0, 1)[0];

        const request = nextRequest.request;
        const complete = request.complete;
        request.complete = function () {
            if (complete) {
                complete.apply(this, arguments);
            }

            const idx = self.currentRequests.indexOf(nextRequest);
            if (idx >= 0) {
                if (self.debug) console.log(`Complete and remove request ${idx + 1} from ${self.currentRequests.length} current requests.`);
                self.currentRequests.splice(idx, 1);
            }
            self.next();
        };


        // Delay request
        if (nextRequest.delay) {
            nextRequest.timeout = setTimeout(() => {
                nextRequest.request = $.ajax(request);
            }, nextRequest.delay);
        }
        // ...or start immediately
        else {
            nextRequest.request = $.ajax(request);
        }

        this.currentRequests.push(nextRequest);
    }
}
