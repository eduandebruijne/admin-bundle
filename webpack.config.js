const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './src/Resources/public/source/main.js')
;

module.exports = Encore.getWebpackConfig();