let mix = require("laravel-mix").mix;
let path = require('path');

mix.webpackConfig({
    externals: {
        '$': 'jQuery',
        'jquery': 'jQuery'
    }
});

mix.setPublicPath(
    path.resolve(__dirname, 'dist')
);

mix.js("src/js/main.js", "js/main.js");

mix.sass("src/scss/style.scss", "css/main.css");

mix.copy('src/images', 'dist/images');