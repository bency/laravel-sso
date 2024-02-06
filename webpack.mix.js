const mix = require('laravel-mix');

mix.setPublicPath('public')
  .js('resources/js/app.js', 'public')
  .options({
    terser: {
      terserOptions: {
        compress: {
          drop_console: true,
        },
      },
    },
  })
  .version();
