const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('./src/Resources/public/')
    .setPublicPath('./')
    .setManifestKeyPrefix('bundles/edbadmin')
    .addEntry('app', './src/Resources/assets/app.js')
    .enableSassLoader()
    .enableSingleRuntimeChunk()
    .addExternals({
        jquery: '$',
        jquery: 'jQuery'
    })
;

module.exports = Encore.getWebpackConfig();