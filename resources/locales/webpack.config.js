/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

'use strict';
const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
    // https://webpack.js.org/configuration/entry-context/
    entry: {
        lingui: path.resolve(__dirname, 'lingui.js')
    },

    // https://webpack.js.org/configuration/output/
    output: {
        path: path.resolve(__dirname, '../../htdocs/js'),
        filename: '[name].js',
        chunkFilename: '../msg/[name].js',
        library: {
            type: 'module'
        },
        publicPath: '/'
    },

    experiments: {
        outputModule: true
    },

    // Useful for debugging.
    //devtool: 'source-map',

    // By default webpack logs warnings if the bundle is bigger than 200kb.
    performance: { hints: "warning" },

    resolve: {
        alias: {
            '/msg': path.resolve(__dirname, '../msg')
        },
    },

    optimization: {
        minimizer: [new TerserPlugin({
            extractComments: false,
        })],
    }
};

