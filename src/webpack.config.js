var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/static/')
    .setPublicPath('/static')
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
    .configureBabel(function(babelConfig) {
        babelConfig.plugins.push(['babel-plugin-root-import', {
            "rootPathPrefix": "@",
            "rootPathSuffix": "./src/"
        }]);
    })
    .addStyleEntry('css/styles', './src/Ui/styles/styles.sass')
    .addEntry('js/app', './src/Ui/js/app.js')
;

module.exports = Encore.getWebpackConfig();
