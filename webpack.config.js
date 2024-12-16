const path = require('path');
const TerserWebpackPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");

module.exports = {
    entry: {
        app : [
            path.resolve(__dirname, 'htdocs/js/app.js')
        ]
    },
    mode: 'production',
    output: {
        filename: '[name].min.js',
        path: path.resolve(__dirname, 'htdocs/js'),
    },
    resolve: {
        alias: {
            '/js': path.resolve(__dirname, 'htdocs/js'),
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
