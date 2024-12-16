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
// const webpack = require('webpack');

const {bundler, styles} = require('@ckeditor/ckeditor5-dev-utils');
const TerserWebpackPlugin = require('terser-webpack-plugin');


module.exports = {
    // https://webpack.js.org/configuration/entry-context/
    entry: {
        epieditor: path.resolve(__dirname, 'epieditor', 'epieditor.js')
    },

    // https://webpack.js.org/configuration/output/
    output: {
        path: path.resolve(__dirname, '../../webroot/js'),
        filename: '[name].min.js',
        library: "EpiEditor",
        libraryTarget: "var"
    },

    resolve: {
        alias: {
            '/js/widgets': path.resolve(__dirname, '../../../../plugins/Widgets/webroot/js'),
            '/js': path.resolve(__dirname, '../../../../htdocs/js')
        }
    },

    optimization: {
        minimizer: [
            new TerserWebpackPlugin({
                terserOptions: {
                    output: {
                        // Preserve CKEditor 5 license comments.
                        comments: /^!/
                    }
                },
                extractComments: false
            })
        ]
    },


    // plugins: [
    //     new CKEditorWebpackPlugin({
    //         // UI language. Language codes follow the https://en.wikipedia.org/wiki/ISO_639-1 format.
    //         // When changing the built-in language, remember to also change it in the editor's configuration (src/ckeditor.js).
    //         language: 'en',
    //         addMainLanguageTranslationsToAllAssets: true
    //     })
    // ],

    module: {
        rules: [
            {
                test: /\.svg$/,
                use: ['raw-loader']
            },
            {
                test: /\.css$/,
                use: [
                    {
                        loader: 'style-loader',
                        options: {
                            injectType: 'singletonStyleTag',
                            attributes: {
                                'data-cke': true
                            }
                        }
                    },
                    {
                        loader: 'css-loader'
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            postcssOptions: styles.getPostCssConfig({
                                themeImporter: {
                                    themePath: require.resolve('@ckeditor/ckeditor5-theme-lark')
                                },
                                minify: true
                            })
                        }
                    },
                ]
            }
        ]
    },

    // Useful for debugging.
    //devtool: 'source-map',

    // By default webpack logs warnings if the bundle is bigger than 200kb.
    performance: { hints: "warning" }
};

