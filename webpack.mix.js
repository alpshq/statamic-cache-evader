const mix = require('laravel-mix');

mix
  .js('resources/js/cache-evader.js', 'dist/js')
  .disableNotifications();
