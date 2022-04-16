const mix = require('laravel-mix');

mix
  .js('resources/js/cache-evader.js', 'dist/js')
  .copyDirectory('dist', '../../public/vendor/statamic-cache-evader')
  .sourceMaps()
  .disableNotifications();
