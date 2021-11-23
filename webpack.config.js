const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('./src/Resources/public/')
    .setPublicPath('./')
    .setManifestKeyPrefix('bundles/edbadmin')
    .addEntry('app', './src/Resources/assets/js/app.js')
    .addEntry('style', './src/Resources/assets/js/style.js')
    .enableSassLoader()
    .enableSingleRuntimeChunk()
;

module.exports = Encore.getWebpackConfig();