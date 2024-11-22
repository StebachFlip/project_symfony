const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .enableVersioning()  // Ceci active la gestion des versions (hash)
    .enableSourceMaps(!Encore.isProduction())
    .enableSassLoader()
    .enableVueLoader()
    .enableSingleRuntimeChunk() // Active le chunk unique pour le runtime
    .enableVersioning();

module.exports = Encore.getWebpackConfig();
