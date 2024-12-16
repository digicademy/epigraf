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
const webpack = require('webpack');
const TerserWebpackPlugin = require('terser-webpack-plugin');


module.exports = {
    // https://webpack.js.org/configuration/entry-context/
    entry: {
        icons: path.resolve(__dirname, 'icons.js')
    },

    // https://webpack.js.org/configuration/output/
    output: {
        path: path.resolve(__dirname, '../../htdocs/img'),
        filename: '[name].min.js',
        library: {
            type: 'module'
        }
    },

    experiments: {
        outputModule: true
    },

    module: {
        rules: [
            {
                test: /\.svg$/,
                use: ['raw-loader']
            }
        ]
    },

    // Useful for debugging.
    //devtool: 'source-map',

    // By default webpack logs warnings if the bundle is bigger than 200kb.
    performance: { hints: "warning" }
};

