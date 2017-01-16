const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
 */

elixir((mix) => {
    mix.sass('app.scss')
       .webpack('app.js');

    // mix.copy(['../../../node_modules/izitoast/dist/css/iziToast.css','public/admin/css/style.css']);
    // mix.styles(
    // 	["../../../node_modules/izitoast/dist/css/iziToast.css",
    // 		"../../../node_modules/sweetalert/dist/sweetalert.css"
    // 	]
    // 	,'public/admin/css/app.css');
});