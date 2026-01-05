/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

/**
 * A utility class for working with cookies.
 */
export class CookiesManager {
    /**
     * Sets a cookie.
     * @param {string} name - The name of the cookie.
     * @param {string} value - The value of the cookie.
     * @param {number} [days] - The number of days until the cookie expires.
     */
    set(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = `; expires=${date.toUTCString()}`;
        }
        document.cookie = `${name}=${value}${expires}; path=/`;
    }

    /**
     * Gets the value of a cookie.
     * @param {string} name - The name of the cookie.
     * @returns {string|null} The value of the cookie, or null if it does not exist.
     */
    get(name) {
        const nameEQ = `${name}=`;
        const ca = document.cookie.split(";");
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === " ") {
                c = c.substring(1);
            }
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return null;
    }

    /**
     * Removes a cookie.
     * @param {string} name - The name of the cookie.
     */
    remove(name) {
        this.set(name, "", -1);
    }

    /**
     * Checks if a cookie exists.
     * @param {string} name - The name of the cookie.
     * @returns {boolean} True if the cookie exists, false otherwise.
     */
    exists(name) {
        return this.get(name)!== null;
    }

    /**
     * Returns all cookies as an object.
     * @returns {object} An object containing all cookies.
     */
    getAll() {
        const cookies = {};
        const ca = document.cookie.split(";");
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === " ") {
                c = c.substring(1);
            }
            const parts = c.split("=");
            cookies[parts[0]] = parts[1];
        }
        return cookies;
    }
}
