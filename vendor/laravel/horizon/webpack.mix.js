const mix = require('laravel-mix');
const webpack = require('webpack');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .options({
        terser: {
            terserOptions: {
                compress: {
                    drop_console: true,
                }
            }
        }
    })
    .setPublicPath('public')
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .copy('resources/img', 'public/img')
    .sourceMaps()
    .copy('public', '../../laravelhorizon/public/vendor/horizon')
    // .copy('public', '../app/public/vendor/horizon')
    .version();


mix.webpackConfig({
    plugins: [
        new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/)
    ],
    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.runtime.esm.js'
        }
    }
});
