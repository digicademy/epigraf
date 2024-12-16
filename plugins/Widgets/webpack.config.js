const path = require('path');
const TerserWebpackPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");

module.exports = [{
    entry: {
        widgets : [
            path.resolve(__dirname, 'webroot/js/widgets.js'),
            path.resolve(__dirname, 'webroot/css/widgets.css'),
        ],
        // 'leaflet-bundle' : [
        //     path.resolve(__dirname, 'webroot/js/leaflet/leaflet-bundle.js'),
        // ]
    },
    mode: 'production',
    output: {
        filename: '[name].min.js',
        path: path.resolve(__dirname, 'webroot/js')

    },
    // externals: {
    //     'leaflet-bundle': ['L']
    // },
    resolve: {
        alias: {
            '/js': path.resolve(__dirname, '../../htdocs/js'),
            '/msg': path.resolve(__dirname, '../../htdocs/msg'),
            '/widgets/js': path.resolve(__dirname,  '../Widgets/webroot/js'),
            '/epi/js': path.resolve(__dirname, '../Epi/webroot/js')
        },
    },
    plugins: [new MiniCssExtractPlugin(
        {
            filename: '../css/[name].min.css'
        }
    )],
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
}];
