import { formatter } from "@lingui/format-po";

module.exports = {
    locales: ["de_DE"],
    sourceLocale: "en",
    catalogs: [
        {
            path: "resources/locales/{locale}/js_messages",
            exclude: ["plugins/Widgets/webroot/js/*.min.js"],
            include: ["plugins/Widgets/webroot/js/*.js", "plugins/Epi/webroot/js/*.js"],
        },
    ],

    // Don't use generated IDs
    // TODO: use generated IDS. At the moment i18n._() doesn't transform the messages
    //       generated IDs, why?
    format: formatter({ explicitIdAsDefault: true })
};
