/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

/**
 * Utility class
 */
class Utils {

    /**
     * Hide an element and remember the original display value
     * @param element
     */
    static hide(element) {
        if (!element) {
            return;
        }
        element.originalDisplay = window.getComputedStyle(element).getPropertyValue("display");
        element.style.display = 'none';
    }

    /**
     * Show an element and optionally set the text content
     *
     * @param {HTMLElement} element
     * @param {string} content
     */
    static show(element, content) {
        if (!element) {
            return;
        }

        // Set original display value
        if (element.style.display === 'none') {
            element.style.display = element.originalDisplay || 'block';
        }

        // If the original display value was 'none'
        if (element.style.display === 'none') {
            element.style.display = 'block';
        }

        if (content !== undefined) {
            element.textContent = content;
        }
    }

    static toggle(element, show=true) {
        if (show) {
            this.show(element);
        } else {
            this.hide(element);
        }
    }

    /**
     * Scroll an element to the top
     *
     * @param {HTMLElement} element The element to scroll into view
     * @param {HTMLElement} container The scrollable container. Leave empty zu use the element parent.
     */
    static scrollToTop(element, container) {
        if (!element) {
            return;
        }

        element.scrollIntoView(true);
    }

    /**
     * Calculate the total offset of an element relative to a specific container
     *
     * @param {HTMLElement} element
     * @param {HTMLElement} container
     * @return {{top: number, left: number}}
     */
    static getTotalOffset(element, container) {
        let totalOffsetTop = 0;
        let totalOffsetLeft = 0;
        let currentElement = element;

        while (currentElement && (currentElement !== container.offsetParent)) {
            totalOffsetTop += currentElement.offsetTop;
            totalOffsetLeft += currentElement.offsetLeft;
            currentElement = currentElement.offsetParent;
        }
        return { top: totalOffsetTop, left: totalOffsetLeft };
    }

    static getStickyElementHeight(container, selector) {

        const element = container.querySelector(selector);
        if (element && (container !== element)) {
            const position = window.getComputedStyle(element).position;
            if (position === 'sticky') {
                return element.offsetHeight;
            }
        }

        return 0;
    }

    /**
     * Scroll an element into view if not already visible
     *
     * @param {HTMLElement} element The element to scroll into view
     * @param {HTMLElement} container The scrollable container. Leave empty to use the element parent.
     * @param {boolean|string} centerIfNeeded Center the element within the container if it had to be scrolled into view.
     *                                        If 'x' or 'y', center only in that direction.
     * @param {boolean} dir Scroll direction. If 'x' only the horizontal position is adjusted, if 'y' only the vertical position.
     *                                        Set to true to scroll in both directions.
     */
    static scrollIntoViewIfNeeded(element, container, centerIfNeeded=true, dir = true) {

        const centerY = (centerIfNeeded === 'y') || (centerIfNeeded === true);
        const centerX = (centerIfNeeded === 'x') || (centerIfNeeded === true);
        const dirY = (dir === 'y') || (dir === true);
        const dirX = (dir === 'x') || (dir === true);

        if (!element || !Utils.isElementVisible(element)) {
            return;
        }
        container = container || element.parentNode;

        // const containerComputedStyle = window.getComputedStyle(container, null);
        // const containerBorderTopWidth = parseInt(containerComputedStyle.getPropertyValue('border-top-width'));
        // const containerBorderLeftWidth = parseInt(containerComputedStyle.getPropertyValue('border-left-width'));

        const elmBox = element.getBoundingClientRect();
        let conBox = container.getBoundingClientRect();

        // Reduce the box by the scrollbar height
        // Reduce the box by sticky table header heights
        const scrollBarHeight = container.offsetHeight - container.clientHeight;
        const stickyHeight = Utils.getStickyElementHeight(container, 'thead, li');
        conBox = new DOMRect(conBox.x, conBox.y + stickyHeight, conBox.width, conBox.height - scrollBarHeight - stickyHeight);

        const overTop = elmBox.top < conBox.top;
        const overBottom = elmBox.bottom > conBox.bottom;
        if (dirY) {

            let scrollOffset;
            if (overTop && !overBottom) {
                scrollOffset = (elmBox.top - conBox.top);
            } else if (overBottom && !overTop) {
                scrollOffset = (elmBox.bottom - conBox.bottom);
            }

            if (scrollOffset !== undefined) {
                if (centerY) {
                    let centerOffset =  (container.offsetHeight / 2) - (element.offsetHeight / 2);
                    centerOffset = overTop ? - centerOffset : + centerOffset;
                    scrollOffset = scrollOffset + centerOffset;
                }
                container.scrollTop = container.scrollTop + scrollOffset;
            }
        }

        const overLeft = elmBox.left < conBox.left;
        const overRight = elmBox.right > conBox.right;
        if (dirX) {
            let scrollOffset;
            if (overLeft && !overRight) {
                scrollOffset = elmBox.left - conBox.left;
            } else if (overRight && !overLeft) {
                scrollOffset = elmBox.right - conBox.right;
            }

            if (scrollOffset !== undefined) {
                if (centerX) {
                    let centerOffset = (container.offsetWidth / 2) - (element.offsetWidth / 2);
                    centerOffset = overLeft ? - centerOffset : + centerOffset;
                    scrollOffset = scrollOffset + centerOffset;
                }
                container.scrollLeft = container.scrollLeft + scrollOffset;
            }
        }

        // Safeguard in case manual calculation did not succeed
        // if ((overTop || overBottom || overLeft || overRight)) {
        //     element.scrollIntoView();
        // }
    }

    /**
     * Get a query parameter from the URL
     *
     *     //TODO: can this be replaced by the URL() feature?
     *     //      See https://developer.mozilla.org/en-US/docs/Web/API/URL/URL
     *
     * @param {string} name The query parameter name
     * @param {string} url The URL to search in. Leave empty to use the current page URL.
     * @return {string|null}
     */
    static getParameterByName(name, url) {
        if (!url) {
            url = window.location.href;
        }
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

    /**
     * Get nested property
     * (will be replaced by chaining operator as soon as we can use ES11)
     *
     * @param {Object} obj Object containing the data
     * @param {string|array} key A dot sperated path to the value inside the data object.
     *                           Alternatively, an array of keys. Each key will be tested
     *                           until a value different from undefined is found.
     * @param {*} def Default value if the key is not present in the data
     * @returns {*} Extracted value (if key exists) or undefined
     */
    static getValue(obj, key, def = undefined) {

        // Key is an array of keys, try each of them
        if (key && Array.isArray(key)) {
            for (let keyItem of key) {
                const value = this.getValue(obj, keyItem);
                if (value !== undefined) {
                    return value;
                }
            }
            return def;
        }

        // If key is a string, get the value
        try {
            const value = key.split('.').reduce((o, x) => {
                if (typeof o == 'undefined' || o === null || x === '') {
                    return o;
                } else if (o.constructor === Array) {
                    return o[parseInt(x)];
                } else {
                    return o[x];
                }
            }, obj);

            return (typeof value == 'undefined') ? def : value;
        } catch {
            return def;
        }
    }

    /**
     * Set nested property
     *
     * Translates input names such as 'sections[0][name]' to nested objects
     * and dot separated paths such as 'sections.0.name' to nested objects.
     *
     * @param {Object} data The data object
     * @param {string} key A dot separated path to the value inside the data object or a square bracket path.
     * @param {*} value The new value
     * @param {boolean} merge If true, the value is merged with the existing object instead of replacing the value
     * @returns {Object} The updated data object
     */
    static setValue(data, key, value, merge=false) {
        if (typeof key === 'string') {
            key = key.replace(/\]/g, '').split(/[\[\.]/);
        }
        if (key.length > 1) {
            if (!data.hasOwnProperty(key[0])) {
                data[key[0]] = {};
            } else if (typeof data[key[0]] !== 'object') {
                data[key[0]] = {};
            }

            Utils.setValue(data[key[0]], key.slice(1), value, merge);
        } else {

            if (merge &&
                (typeof data[key[0]] === 'object') &&
                (data[key[0]] !== null) &&
                (typeof value === 'object') &&
                (value !== null)
            ) {
                // Merge the existing object with the new value
                data[key[0]] = Object.assign({}, data[key[0]], value);
            } else {
                // Otherwise, replace the value
                data[key[0]] = value;
            }
        }

        return data;
    }

    // static setValue(obj, path, value, merge = false) {
    //     if (typeof key === 'string') {
    //       path = path.replace(/\]/g, '').split(/[\[\.]/);
    //     }
    //
    //     // path = path.split('.');
    //     let current = obj;
    //
    //     for (var i=0; i < (path.length - 1); i++) {
    //         let key = path[i];
    //         if(!current.hasOwnProperty(key)){
    //             current[key] = {};
    //         }
    //         current = current[key];
    //     }
    //
    //     if (path.length > 0) {
    //         let key = path[path.length - 1];
    //
    //         // Check if the current value is an object and merge if needed
    //         if (merge &&
    //             (typeof current[key] === 'object') &&
    //             (current[key] !== null) &&
    //             (typeof value === 'object') &&
    //             (value !== null)
    //         ) {
    //             // Merge the existing object with the new value
    //             current[key] = Object.assign({}, current[key], value);
    //         } else {
    //             // Otherwise, replace the value
    //             current[key] = value;
    //         }
    //
    //     }
    //     return obj;
    // }

    /**
     * Parse HTML content and extract the text content of the first matched snippet.
     *
     * @param {string} data The HTML content to parse.
     * @param {string} name The name of the snippet to extract.
     * @return {string} The text content of the first matched snippet, or an empty string if not found or an error occurs.
     */
    static extractSnippetText(data, name) {
        try {
            data = new DOMParser().parseFromString(data, 'text/html');
            const snippet = data.querySelector('[data-snippet="' + name + '"]');
            if (snippet) {
                return snippet.textContent;
            }
        } catch {
            return '';
        }
        return '';
    }

    /**
     * Wrap element with another element.
     *
     * @param {HTMLElement} target Element that should be wrapped
     * @param {HTMLElement} wrapper Wrapper element for target element
     * @returns {HTMLElement} Wrapped target
     */
    static wrapAll(target, wrapper) {
        // The spread operator ... is used to convert the live collection to a static list
        [...target.childNodes].forEach(child => wrapper.appendChild(child));
        target.appendChild(wrapper);
        return wrapper;
    };

    /**
     * Create nodes from a HTML string.
     *
     * @param {string} html The HTML string to parse.
     * @param {object} data Object with values to replace in the template (curly bracket placeholders).
     * @param {boolean} first If true, returns the first child element, otherwise the document fragment
     * @return {HTMLElement|DocumentFragment} Dom nodes The created DOM nodes.
     */
    static spawnFromString(html, data, first = true) {
        const template = document.createElement('template');
        template.innerHTML = html.trim().formatUnicorn(data);
        if (first) {
            return template.content.firstElementChild;
        } else {
            return template.content;
        }
    }

    /**
     * Create an element from a template and unicorn format it.
     *
     * @param {HTMLTemplateElement} elm The template element.
     * @param {object} data Object with values to replace in the template (curly bracket placeholders).
     * @param {boolean} [first=true] If true, returns the first child element, otherwise the document fragment.
     * @return {HTMLElement|DocumentFragment} The created DOM nodes.
     */
    static spawnFromTemplate(elm, data, first = true) {
        if (!elm) {
            return undefined;
        }
        return Utils.spawnFromString(elm.text, data, first);
    }

    /**
     * Check whether one child matches a selector.
     *
     * @param {HTMLElement} elm Element that is checked for given child
     * @param {string} selector CSS selector to check element for
     * @returns {boolean} True if child is matched, false if otherwise
     */
    static childMatches(elm, selector) {
        return [].some.call(elm.children, e => e.matches(selector));
    };

    /**
     * Savely set the value of an input element.
     *
     * @param {HTMLElement } element The input element.
     * @param value
     */
    static setInputValue(element, value) {
        if (element) {
            if (element.value !== value) {
                element.value = value;
                element.disabled = false;
                element.dataset.dirty = true;
            }
        }
    }

    static getInputValue(element, defaultValue) {
        return element ? element.value : defaultValue;
    }

    /**
     * Remove `min` and `max` constraints from input elements within a HTML element.
     * Used on item delete actions to disable default constraint checking on these inputs later when the form is saved.
     *
     * This is important because constraint parameters can change dynamically (for example the heraldry grid size)
     * and the default hint behaviour (`value does not match constraints`) can not be applied to deleted items
     * (since `display: none`), and therefore an error would be thrown.
     *
     * // TODO: Create `restoreInputConstraints` when adding restore behaviour for deleted items.
     *
     * @param {HTMLElement} element The HTML element containing the input elements with constraints to be removed.
     * */
    static removeInputConstraints(element) {
        /**
         * The collection of input elements within the given HTML element.
         * @type {NodeListOf<HTMLInputElement>}
         */
        const inputElms = element.querySelectorAll('input');
        inputElms.forEach(elm => {
            elm.removeAttribute('min');
            elm.removeAttribute('max');
        });
    }


    /**
     * Get a data value
     *
     * @param {HTMLElement} element
     * @param {string} key
     * @param {string} defaultValue
     * @return {string}
     */
    static getDataValue(element, key, defaultValue) {
        if (element) {
            return element.dataset[key] || defaultValue;
        } else {
            return defaultValue;
        }
    }

    /**
     * Change the inner HTML of an element
     *
     * @param {Element} element The element or undefined
     * @param {string} value HTML content
     */
    static setElementContent(element, value) {
        if (element) {
            element.innerHTML = value;
        }
    }

    /**
     * Change the text content of an element
     *
     * @param {Element} element The element or undefined
     * @param {string} value Text content
     */
    static setElementText(element, value) {
        if (element) {
            element.textContent = value;
        }
    }

    /**
     * Get the text content of an element
     *
     * @param {Element} element The element or undefined
     * @param {String} defaultValue The default value to return if the element is not provided
     * @return {string} Text content
     */
    static getElementText(element,defaultValue) {
        if (element) {
            return element.textContent;
        } else {
            return defaultValue;
        }
    }

    /**
     * Get the rendered text of an element by concatenating all text nodes and input values
     *
     * @param {Element} element
     * @param {string} defaultValue The default value to return if the element is not provided
     * @return {string}
     */
    static getRenderedText(element, defaultValue = '') {
        // Return the defaultValue if the element is not provided
        if (!element) {
            return defaultValue;
        }

        let result = '';

        // Function to recursively process child nodes
        function processNode(node) {
            switch (node.nodeType) {
                case Node.TEXT_NODE:
                    result += node.nodeValue.trim();
                    break;
                case Node.ELEMENT_NODE:
                    // Ignore button and hidden input elements, ignore dropdown panes
                    if (node.tagName === 'BUTTON' || (node.tagName === 'INPUT' && node.type === 'hidden')) {
                        result += '';
                    }
                    else if (node.classList.contains('widget-dropdown-pane')) {
                        result += '';
                    } else if (node.tagName === 'SELECT') {
                        const selectedOption = node.options[node.selectedIndex];
                        if (selectedOption) {
                            result += selectedOption.text.trim();
                        }
                    } else if (node.tagName === 'INPUT') {
                        result += node.value;
                    } else {
                        // Recursively process all child nodes for other elements
                        for (const child of node.childNodes) {
                            processNode(child);
                        }
                    }
                    break;
                default:
                    break; // Ignore other node types
            }
        }

        // Start processing the provided element
        processNode(element);

        return result || defaultValue;
    }

    /**
     * Extract elements from containers and remove the containers from the DOM
     *
     * @param {HTMLElement} data
     * @param {string} containerSelector
     * @param {string} elementSelector
     * @return {array}
     */
    static extractElements(data, containerSelector, elementSelector) {
        let results = [];
        data.querySelectorAll(containerSelector)
            .forEach((snippet) => {
                results.push(...Array.from(snippet.querySelectorAll(elementSelector)));
                snippet.remove();
            });

        return results;
    }

    /**
     * Set the class to the element and remove it from all others
     *
     * @param {string} className
     * @param {HTMLElement} needle
     * @param {NodeList} haystack
     */
    static toggleClass(className, needle, haystack) {
        haystack.forEach(elm => elm.classList.remove(className));
        if (needle) {
            needle.classList.add(className);
        }
    }

    /**
     * Remove all classes with the given prefix from the element
     *
     * @param {HTMLElement} elm
     * @param {string} prefix
     */
    static removeClassByPrefix(elm, prefix) {
        let newClassList = [];

        elm.classList.forEach(className => {
            if (className.indexOf(prefix) !== 0 ) {
                newClassList.push(className);
            }
        });

        elm.className = newClassList.join(' ');
    }

    /**
     * Get the name of a class without its prefix
     *
     * @param {Element} elm
     * @param {string} prefix
     */
    static getClassValue(elm, prefix) {
        if (!elm) {
            return;
        }

        for (let className of elm.classList) {
            if (className.indexOf(prefix) === 0 ) {
                return className.substring(prefix.length);
            }
        }
    }

    /**
     * Replace reserved characters with named entities for HTML and XML attribute values
     *
     * @param {string} value
     * @return {string}
     */
    static encodeHtmlAttribute(value) {
        if (!(typeof value === 'string')) {
            return value;
        }

        return value
            .replace(/&/g, "&amp;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
    }

    /**
     * Replace named entities with reserved characters for HTML and XML attribute values
     *
     * @param {string} value
     * @return {string}
     */
    static decodeHtmlAttribute(value) {
        if (!(typeof value === 'string')) {
            return value;
        }

        return value
            .replace(/&gt;/g, ">")
            .replace(/&lt;/g, "<")
            .replace(/&#39;/g, "'")
            .replace(/&quot;/g, "\"")
            .replace(/&amp;/g, "&");
    }


    /**
     * Convert numbers to letters
     *
     * @thanks https://stackoverflow.com/a/75643566
     * @param {integer} num Input number
     * @param {boolean} lower Upper or lower case?
     * @param {integer|string} alphabet_start The first letter of the target alphabet
     * @param {integer} alphabet_length The length of the target alphabet
     * @return {string}
     */
    static numberToLetters(num, lower = true, alphabet_start = 65, alphabet_length = 26) {
        alphabet_start = typeof alphabet_start === 'string' ? alphabet_start.charCodeAt(0) : alphabet_start;

        let result = num <= 0 ? '' :
            Utils.numberToLetters(Math.floor((num - 1) / alphabet_length), lower, alphabet_start, alphabet_length) +
            String.fromCharCode((num - 1) % alphabet_length + alphabet_start);

        return lower ? result.toLowerCase() : result.toUpperCase();
    }

    /**
     * Convert numbers to roman numbers
     *
     * @param {integer} number Input number
     * @param {boolean} lower Upper or lower case?
     * @return {string}
     */
    static numberToRoman(number, lower = true) {
        const map = {
            'M': 1000, 'CM': 900, 'D': 500, 'CD': 400, 'C': 100, 'XC': 90, 'L': 50,
            'XL': 40, 'X': 10, 'IX': 9, 'V': 5, 'IV': 4, 'I': 1
        };
        let result = '';

        while (number > 0) {
            for (const roman in map) {
                if (number >= map[roman]) {
                    number -= map[roman];
                    result += roman;
                    break;
                }
            }
        }

        return lower ? result.toLowerCase() : result;
    }

    /**
     * Convert letters to numbers
     *
     * @thanks https://stackoverflow.com/a/75643566
     * @param {number} abbr
     * @return {number}
     */
    static abbrNum(abbr) {
        return abbr.toUpperCase().split("").reduce((acc, val) => acc * 26 + val.charCodeAt(0) - 96, 0);
    }

    /**
     * Convert numbers to different number systems
     *
     * @thanks https://stackoverflow.com/a/75643566
     * @param {number} number Input number
     * @param {string} format alphabetic | alphabetic-lower | alphabetic-alpha |
     *                        roman | roman-lower | roman-upper
     * @return {string}
     */
    static numberToString(number, format) {
        switch (format) {
            case 'alphabetic':
            case 'alphabetic-lower':
                return Utils.numberToLetters(number, true);
            case 'alphabetic-upper':
                return Utils.numberToLetters(number, false);
            case 'roman':
            case 'roman-lower':
                return Utils.numberToRoman(number, true);
            case 'roman-upper':
                return Utils.numberToRoman(number, false);
            case 'greek':
            case 'greek-lower':
                return Utils.numberToLetters(number, true, 'Α', 24);
            case 'greek-upper':
                return Utils.numberToLetters(number, false, 'Α', 24);
            default:
                return String(number);
        }
    }

    /**
     * Modulo operator (positive result for negative inputs).
     *
     * @param {number} n
     * @param {number} m
     * @returns {number}
     */
    static mod(n, m) {
        return ((n % m) + m) % m;
    }

    /**
     * Evaluate numbers (0,1) and strings ("false","true") to boolean
     *
     * @param {*} value The value to evaluate
     * @returns {boolean}
     */
    static isTrue(value) {
        if (typeof value === 'string') {
            value = value.toLowerCase();
        }

        return value === 1 || value === "1" || value === true || value === "true";
    }

    /**
     * Check if the given value is a string.
     *
     * @param {*} obj The value to check.
     * @returns {boolean} Returns true if the value is a string, false otherwise.
     */
    static isString(obj) {
        return (Object.prototype.toString.call(obj) === '[object String]');
    }

    /**
     * Check if element is visible on page by using "display" CSS property.
     *
     * @param {HTMLElement} elm Element to check for visibility
     * @returns {boolean} True if element is visible, false if otherwise
     */
    static isElementVisible(elm) {
        return window.getComputedStyle(elm).getPropertyValue('display') !== 'none';
    }

    /**
     * Check whether a scrollbox element is visible on page.
     *
     * @param {HTMLElement} elm Element that should be checked
     * @param {HTMLElement} scrollContainer Scrollbox container that contains given element
     * @returns {boolean} True if element is visible in viewport, false if otherwise
     */
    static isVisible(elm, scrollContainer) {
        const {bottom, height, top} = elm.getBoundingClientRect();
        const containerRect = scrollContainer.getBoundingClientRect();

        return top <= containerRect.top ? containerRect.top - top <= height : bottom - containerRect.bottom <= height;
    }

    /**
     * Find the nearest positioned ancestor of an element.
     *
     * The nearest positioned ancestor is the closest ancestor element that has a position value other than static.
     * It is the reference element for absolutely positioned children.
     *
     * @param {HTMLElement} element
     * @return {HTMLElement|null}
     */
    static nearestPositionedAncestor(element) {
        let currentElement = element.parentElement;
        while (currentElement) {
            const position = window.getComputedStyle(currentElement).position;

            if (position === 'relative' || position === 'absolute' || position === 'fixed' || position === 'sticky') {
                return currentElement;
            }
            currentElement = currentElement.parentElement;
        }

        return null;
    }

    /**
     * Find the previous sibling that matches a selector.
     *
     * See https://gomakethings.com/finding-the-next-and-previous-sibling-elements-that-match-a-selector-with-vanilla-js/
     *
     * @param {HTMLElement} elm Element to find the previous sibling for
     * @param {string} selector CSS selector
     * @return {HTMLElement} Previous sibling
     */
    static getPrevSibling(elm, selector) {
        let sibling = elm.previousElementSibling;
        while (sibling) {
            if (sibling.matches(selector))
                return sibling;
            sibling = sibling.previousElementSibling;
        }
    }

    /**
     * Find the next sibling that matches a selector.
     *
     * See https://gomakethings.com/finding-the-next-and-previous-sibling-elements-that-match-a-selector-with-vanilla-js/
     *
     * @param {HTMLElement} elm Element to find the next sibling for
     * @param {string} selector CSS selector
     * @return {HTMLElement} Next sibling
     */
    static getNextSibling(elm, selector) {
        if (!elm) {
            return;
        }

        let sibling = elm.nextElementSibling;
        while (sibling) {
            if (sibling.matches(selector))
                return sibling;
            sibling = sibling.nextElementSibling;
        }
    }

    /**
     * Find the next visible sibling that matches a selector.
     *
     * @param {HTMLElement} elm Element to find the next visible sibling for
     * @param {string} selector Optionally, find the next visible sibling matching a selector
     * @return {HTMLElement} Next visible sibling
     */
    static getNextVisibleSibling(elm, selector) {
        if (!elm) {
            return undefined;
        }

        let sibling = elm.nextElementSibling;
        while (sibling && (!Utils.isElementVisible(sibling) || (selector && !sibling.matches(selector)))) {
            sibling = sibling.nextElementSibling;
        }

        if (sibling && Utils.isElementVisible(sibling) && (!selector || sibling.matches(selector))) {
            return sibling;
        }
    }

    /**
     * Find the previous visible sibling that matches a selector.
     *
     * @param {HTMLElement} elm Element to find the previous visible sibling for
     * @param {string} selector Optionally, find the previous visible sibling matching a selector
     * @return {HTMLElement} Previous visible sibling
     */
    static getPrevVisibleSibling(elm, selector) {
        if (!elm) {
            return undefined;
        }

        let sibling = elm.previousElementSibling;
        while (sibling && (!Utils.isElementVisible(sibling) || (selector && !sibling.matches(selector)))) {
            sibling = sibling.previousElementSibling;
        }

        if (sibling && Utils.isElementVisible(sibling) && (!selector || sibling.matches(selector))) {
            return sibling;
        }
    }

    /**
     * Check if the bottom of an element is above the container viewport bottom (for example a scrollbox).
     *
     * @param {HTMLElement} elm Element in the container
     * @param {HTMLElement} container Container, for example a scrollbox
     * @returns {boolean} True if bottom of element is above the container viewport bottom, false if otherwise
     */
    static bottomAboveViewport(elm, container) {
        const rectElement = elm.getBoundingClientRect();
        const rectContainer = container.getBoundingClientRect();
        return rectElement.bottom < rectContainer.bottom;
    }

    /**
     * Check if the top of an element is below the container viewport top (for example a scrollbox).
     *
     * @param {HTMLElement} elm Element in the container
     * @param {HTMLElement} container Container, for example a scrollbox
     * @returns {boolean} True if bottom of element is below the container viewport top, false if otherwise
     */
    static topBelowViewport(elm, container) {
        const rectElement = elm.getBoundingClientRect();
        const rectContainer = container.getBoundingClientRect();
        return rectElement.top > rectContainer.top;
    }

    /**
     * Return the last matched element
     *
     * @param {HTMLElement} elm
     * @param {string} selector
     * @return {HTMLElement}
     */
    static querySelectorLast(elm, selector) {
        return [...elm.querySelectorAll(selector)].at(-1);
    }

    /**
     * Return the first matched ancestor or self.
     *
     * @param {HTMLElement|Array} elm The element from which to start the query or a list of elements
     * @param {string} selector A CSS selector to match elements against.
     * @returns {HTMLElement} The first matched ancestor or self element, or null if none is found.
     */
    static querySelectorAndSelf(elm, selector) {
        if (Array.isArray(elm)) {
            for (let i = 0; i < elm.length; i++) {
                const result = Utils.querySelectorAndSelf(elm[i], selector);
                if (result) {
                    return result;
                }
            }
        } else {
            if (elm.matches && elm.matches(selector)) {
                return elm;
            }
            return elm.querySelector(selector);
        }
    }

    /**
     * Return an array containing the element itself and the closest ancestor that matches the provided CSS selector.
     *
     * @param {HTMLElement} elm The element from which to start the query.
     * @param {string} selector A CSS selector to match elements against.
     * @returns {HTMLElement[]} An array containing the element itself and the closest ancestor,
     *                          or an empty array if none is found.
     */
    static querySelectorSelfAndContainer(elm, selector) {
        return [
            elm,
            elm === document ? null : elm.closest(selector)
        ].filter(el => (el !== null) && (el !== document) && el.matches(selector));
    }

    /**
     * Return an array of elements including the given element and all its descendants that match the provided CSS selector.
     *
     * @param {HTMLElement} elm The element from which to start the query.
     * @param {string} selector A CSS selector to match elements against.
     * @returns {HTMLElement[]} An array of elements matching the selector, including the initial element.
     */
    static querySelectorAllAndSelf(elm, selector) {
        return [elm, ...elm.querySelectorAll(selector)].filter(
            el => (!(el instanceof DocumentFragment)) && (el !== document) && el.matches(selector)
        );
    }

    /**
     * Return an array of elements that includes the given element, its descendants matching the provided CSS selector,
     * and the closest ancestor that matches the selector.
     *
     * @param {HTMLElement|null} elm The element from which to start the query, or null if there's no initial element.
     * @param {string} selector A CSS selector to match elements against.
     * @returns {HTMLElement[]} An array of elements matching the selector,
     *                          including the initial element and the closest ancestor.
     */
    static querySelectorAllAndSelfAndContainer(elm,selector) {
        const descendants = elm ? elm.querySelectorAll(selector) : [];
        const container = (elm && (elm !== document)) ? elm.closest(selector) : null;
        return [
            elm,
            ...descendants,
            container
        ].filter(el => (el !== null) && (el !== document) && el.matches(selector));
    }

    /**
     * Get the text content of an element
     *
     * @param {HTMLElement} elm The element from which to get the text content.
     * @param {string} selector A CSS selector to select an element within "elm".
     * @param {*} [def=''] The default value to return if no matching element is found.
     * @returns {string} The text content of the selected element or the default value if not found.
     */
    static querySelectorText(elm, selector, def='') {
        elm = elm.querySelector(selector);
        return elm ? elm.textContent : def;
    }

    /**
     * Get the value of a specific data attribute from an element.
     *
     * @param {HTMLElement} elm The element from which to get the data attribute value.
     * @param {string} selector A CSS selector to select an element within "elm".
     * @param {string} dataKey The name of the data attribute to retrieve.
     * @param {*} [def=''] The default value to return if no matching element or data attribute is found.
     * @returns {string} The value of the specified data attribute or the default value if not found.
     */
    static querySelectorData(elm, selector, dataKey, def='') {
        elm = elm.querySelector(selector);
        return elm ? elm.dataset[dataKey] : def;
    }

    /**
     * Observe an event, optionally for child elements matching a selector.
     *
     * @param {HTMLElement} element Element to observe.
     * @param {string} eventName Event name.
     * @param {function} eventHandler The event handler.
     * @param {string} selector Optionally, only fire if the selector matches the current target.
     */
    static listenEvent(element, eventName, eventHandler, selector) {

        if (!element) {
            return;
        }

        // Internal handler that checks the selector
        const handler = (event) => {
            if (event.defaultPrevented) {
                return;
            }
            if ((selector === undefined) || event.target.matches(selector)) {
                return eventHandler(event);
            }
        };

        // Keep track in the eventListeners property
        if (!element.eventListeners) {
            element.eventListeners = {};
        }

        if (!element.eventListeners[eventName]) {
            element.eventListeners[eventName] = {};
        }

        if (!element.eventListeners[eventName][selector]) {
            element.eventListeners[eventName][selector] = [];
        }

        element.eventListeners[eventName][selector].push({
            source: eventHandler,
            target: handler
        });

        return element.addEventListener(eventName, handler);
    }

    /**
     * Stop observing an event that was previously observed using listenEvent().
     *
     * @param {HTMLElement} element The observed element.
     * @param {string} eventName The event name used in listenEvent().
     * @param {function} eventHandler The event handler used in listenEvent().
     * @param {string} selector The selector used in listenEvent().
     */
    static unlistenEvent(element, eventName, eventHandler, selector) {
        if (!element) {
            return;
        }

        if (
            element.eventListeners &&
            element.eventListeners[eventName] &&
            element.eventListeners[eventName][selector]
        ) {

            // Find matching items
            const listeners = element.eventListeners[eventName][selector];
            let i = listeners.length;
            while (i--) {
                const listener = listeners[i];
                if (listener.source === eventHandler) {
                    element.removeEventListener(eventName, listener.target);
                    listeners.splice(i, 1);
                }
            }
        }
    }

    /**
     * Emit a custom event
     *
     * @param {HTMLElement} element
     * @param {string} name
     * @param {Object} data
     * @param {Object} sender The sender of the event, e.g. a BaseWidget instance or CkEditor instance
     * @param {boolean} cancelable
     * @return {boolean}
     */
    static emitEvent(element, name, data, sender, cancelable=false) {
        if (!element) {
            return;
        }

        let event = new CustomEvent(
            name,
            {
                bubbles: true,
                cancelable: cancelable,
                detail: {
                    data: data,
                    sender: sender
                }
            }
        );

        return element.dispatchEvent(event);
    }



    // Function to get the caret position
    static getSelectionPosition(element) {
        if (window.getSelection) {
            const selection = window.getSelection();
            if (selection.rangeCount > 0) {
                const range = selection.getRangeAt(0);
                return range.startOffset;
            }
        }
        return 0;
    }

    // Function to set the caret position
    static setSelectionPosition(element, position) {
        const range = document.createRange();
        const selection = window.getSelection();
        range.setStart(element.childNodes[0], position); // You may need to adjust this based on your DOM structure
        range.collapse(true);
        selection.removeAllRanges();
        selection.addRange(range);
    }

    /**
     * Calculate the tile X-coordinate from a longitude value.
     * This function is used to convert a longitude value to the corresponding tile X-coordinate for a given zoom level.
     * @see {@link https://wiki.openstreetmap.org/wiki/Slippy_map_tilenames#ECMAScript_.28JavaScript.2FActionScript.2C_etc..29 Wiki}
     * for more information about the calculation.
     *
     * @param {number} lon The longitude value to convert.
     * @param {number} zoom The zoom level for which to calculate the tile X-coordinate.
     * @return {number} The tile X-coordinate.
     */
    static lon2tile(lon, zoom) {
        return (Math.floor((lon + 180) / 360 * Math.pow(2, zoom)));
    }

    /**
     * Calculate the tile Y-coordinate from a latitude value.
     * This function is used to convert a latitude value to the corresponding tile Y-coordinate for a given zoom level.
     * @see {@link https://wiki.openstreetmap.org/wiki/Slippy_map_tilenames#ECMAScript_.28JavaScript.2FActionScript.2C_etc..29 Wiki}
     * for more information about the calculation.
     *
     * @param {number} lat The latitude value to convert.
     * @param {number} zoom The zoom level for which to calculate the tile Y-coordinate.
     * @return {number} The tile Y-coordinate.
     */
    static lat2tile(lat, zoom) {
        return (Math.floor((1 - Math.log(Math.tan(lat * Math.PI / 180) + 1 / Math.cos(lat * Math.PI / 180)) / Math.PI) / 2 * Math.pow(2, zoom)));
    }

    /**
     * Calculate the longitude value from a tile X-coordinate.
     * This function is used to convert a tile X-coordinate back to the corresponding longitude value for a given zoom level.
     * @see {@link https://wiki.openstreetmap.org/wiki/Slippy_map_tilenames#ECMAScript_.28JavaScript.2FActionScript.2C_etc..29 Wiki}
     * for more information about the calculation.
     *
     * @param {number} x The tile X-coordinate to convert.
     * @param {number} z The zoom level for which to calculate the longitude value.
     * @return {number} The longitude value.
     */
    static tile2long(x, z) {
        return (x / Math.pow(2, z) * 360 - 180);
    }

    /**
     * Calculate the latitude value from a tile Y-coordinate.
     * This function is used to convert a tile Y-coordinate back to the corresponding latitude value for a given zoom level.
     * @see {@link https://wiki.openstreetmap.org/wiki/Slippy_map_tilenames#ECMAScript_.28JavaScript.2FActionScript.2C_etc..29 Wiki}
     * for more information about the calculation.
     *
     * @param {number} y The tile Y-coordinate to convert.
     * @param {number} z The zoom level for which to calculate the latitude value.
     * @returns {number} The latitude value.
     */
    static tile2lat(y, z) {
        const n = Math.PI - 2 * Math.PI * y / Math.pow(2, z);
        return (180 / Math.PI * Math.atan(0.5 * (Math.exp(n) - Math.exp(-n))));
    }


    /**
     * Replace multiple whitspaces with a single whitespace
     *
     * @param {string} value The input string that may contain whitespaces to be collapsed
     * @returns {string} The string with whitespaces collapsed
     */
    static collapseWhitespace(value) {
        return value.replace(/ +/g, ' ');
    }


    /**
     * Prefix all numbers inside a string with a "0".
     *
     * Example:  "no. 23" becomes "no. 023"
     *
     * @param {string} value Input string
     * @returns {string} Output string with prefixed numbers
     */
    static prefixNumbersWithZero(value, width = 1) {
        return value.replace(/\d+/g, match => match.padStart(width, '0'));
    }

    /**
     * Replace all whitespaces in a string with hyphens
     *
     * @param {string} value The input string that may contain whitespaces to be replaced
     * @returns {string} The string with whitespaces replaced
     */
    static replaceSpacesWithHyphens(value) {
        return value.replace(/\s+/g, '-');
    }

    /**
     * Remove characters other than small letters (a-z), numbers (0-9), hyphen (-), underscore (_) and tilde (~) from a string.
     *
     * @param {string} value The input string that may contain characters to be removed.
     * @returns {string} The string with invalid characters removed.
     */
    static removeSpecialCharacters(value) {
        return value.replace(/[^ ~a-z0-9\-_]/g, '');
    }

    /**
     * Replace umlauts with their long form.
     *
     * @param {string} value The input string that may contain umlauts.
     * @returns {string} The string with umlauts replaced.
     */
    static replaceUmlauts(value) {
        return value
            .replace(/ä/g, 'ae')
            .replace(/ö/g, 'oe')
            .replace(/ü/g, 'ue')
            .replace(/Ä/g, 'Ae')
            .replace(/Ö/g, 'Oe')
            .replace(/Ü/g, 'Ue')
            .replace(/ß/g, 'sz');
    }

    /**
     * Extract a number from a string.
     *
     * @param {string} value The input string that contains the number and additional content.
     * @returns {string} The first number found in the string or an empty string if no number is found.
     */
    static extractNumber(value) {
        value = value.match(/\d+/);
        return value ? value[0] : '';
    }

    static extractFileName(value) {
        if (!value) {
            return;
        }

        const parts = value.split(/[/\\]/);
        return parts.pop();
    }

    static parseFormData(formData) {
        const data = {};
        for (const [key, value] of formData.entries()) {
            Utils.setValue(data, key, value);
        }
        return data;
    }

    /**
     * Generate the URL for get requests of a form element
     *
     * @param {HTMLFormElement} formElement
     * @param {string} baseUrl
     * @return {string}
     */
    static formToUrl(formElement, baseUrl) {
        let url = new URL(formElement.getAttribute('action'), baseUrl);
        const formParams =  new URLSearchParams(new FormData(formElement));
        let urlParams = url.searchParams;
        urlParams =
            new URLSearchParams({
                ...Object.fromEntries(urlParams),
                ...Object.fromEntries(formParams)
            });
        urlParams = urlParams.toString();

        url.search = urlParams;
        return url.toString();
    }

    /**
     * Convert a string to an object
     *
     * @param {string|object} input The input string. If an object, returns the object without modification.
     * @param {string} key The key within the object to store the string value
     * @return {object}
     */
    static toObject(input, key = 'data') {
        // Check if input is an object and not an array
        if (typeof input === 'object') {
            return input;
        } else if (typeof input === 'string') {
            const data = {};
            data[key] = input;
            return data;
        } else {
            throw new Error("Input must be an object or a string");
        }
    }

    /**
     * Split a comma separated string value to an array
     *
     * Returns an empty array for empty strings.
     *
     * @param {string} input
     * @param {string} separator
     * @return {[]}
     */
    static splitString(input, separator = ',') {
        if (!input) {
            return [];
        }

        if (input === '') {
            return [];
        }

        return input.split(separator);
    }

    /**
     * Get the prefix of a string
     *
     * @param {string} value The input string
     * @param {string} separator The character that separates the prefix from the rest of the string
     * @param {string} defaultPrefix The default value if no prefix is found
     * @return {string}
     */
    static getPrefix(value, separator = ':', defaultPrefix = '') {
        const pos = value.indexOf(separator);
        return (pos !== -1) ? value.substring(0, pos) : defaultPrefix;
    }
}

export default Utils;


/** Polyfills **/


/**
 * add capitalize function to strings
 */

if (!String.prototype.capitalize) {
    String.prototype.capitalize = function() {
        return this.charAt(0).toUpperCase() + this.slice(1);
    };
}

/**
 * jQuery reverse method
 */

$.fn.reverse = [].reverse;

/**
 *  Replace placeholders with data
 *
 *  Replaces all placeholders in curly brackets by the corresponding value in the data object.
 *  Placeholders followed by "|attr" will be preprocessed to safely insert them in HTML attributes.
 *
 * See https://stackoverflow.com/questions/55538149/how-to-create-template-or-placeholder-in-html-and-add-them-dynamically-to-the-bo
 *
 * @param {Object} data Object with values to replace in the template (curly bracket placeholders).
 * @returns {string}
 */

if (!String.prototype.formatUnicorn) {
    String.prototype.formatUnicorn = function () {
        "use strict";
        let str = this.toString();
        if (arguments.length) {
            let t = typeof arguments[0];
            let key;
            let args = ("string" === t || "number" === t) ?
                Array.prototype.slice.call(arguments)
                : arguments[0];

            for (key in args) {
                let plainRegex = new RegExp("\\{" + key + "\\}", "gi");
                str = str.replace(plainRegex, args[key]);

                let attrRegex = new RegExp("\\{" + key + "\\|attr\\}", "gi");
                str = str.replace(attrRegex, Utils.encodeHtmlAttribute(args[key]));
            }
        }

        return str;
    };
}

