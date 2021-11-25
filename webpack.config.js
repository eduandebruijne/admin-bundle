const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('./src/Resources/public/')
    .setPublicPath('./')
    .setManifestKeyPrefix('bundles/edbadmin')
    .addEntry('app', './src/Resources/assets/app.js')
    .enableSassLoader()
    .enableSingleRuntimeChunk()
    .autoProvidejQuery()
    .autoProvideVariables({
        'test': require('jquery')
    })
;

module.exports = Encore.getWebpackConfig();