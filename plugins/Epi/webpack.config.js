const path = require('path');
const TerserWebpackPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");

module.exports = {
    entry: {
        models : [
            path.resolve(__dirname, 'webroot/js/models.js')
        ]
    },
    mode: 'production',
    output: {
        filename: '[name].min.js',
        path: path.resolve(__dirname, 'webroot/js'),
    },
    resolve: {
        alias: {
            '/js': path.resolve(__dirname, '../../htdocs/js'),
            '/widgets/js': path.resolve(__dirname,  '../Widgets/webroot/js'),
            '/epi/js': path.resolve(__dirname, '../Epi/webroot/js')
        },
    },
    module: {
        rules: [
            {
                test: /\.svg$/,
                type: 'asset/inline'
            },
            {
                test: /\.png$/,
                type: 'asset/inline'
            },
            {
                test: /\.css$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    "css-loader",
                    "postcss-loader"

                ],
            }
        ]
    },

    optimization: {
        minimize: true,
        minimizer: [
            new TerserWebpackPlugin({
                extractComments: false,
                terserOptions: {
                    format: {
                        comments: false,
                    },
                },
            }),
            new CssMinimizerPlugin(),
        ],
    },
};
