/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */
import { i18n } from "@lingui/core";

// Option 1: Generate the messages.js by calling npm extract and npm compile
// import { messages } from "./de_DE/js_messages.js";

// Options 2: Generate a po file using npm extract and load it directly
// import { messages } from "@lingui/loader!./de_DE/js_messages.po";

// Option 3: Asynchronously load the messages
const { messages } = await import(/* webpackChunkName: "de_DE" */"@lingui/loader!./de_DE/js_messages.po");

i18n.load("de_DE", messages);
i18n.activate("de_DE");

export {i18n};
