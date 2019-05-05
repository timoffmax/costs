require('laravel-mix-svg');
const mix = require('laravel-mix');
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


mix.js('resources/js/app.js', 'public/js');
mix.sass('resources/scss/app.scss', 'public/css');

if (mix.inProduction()) {
    mix.version();
}

if (!mix.inProduction()) {
    mix.sourceMaps();
}


mix.svg({
    assets: ['./resources/svg/'], // a list of directories to search svg images
    output: './resources/js/svg.js', // Where the craeted js file needs to go.
});

