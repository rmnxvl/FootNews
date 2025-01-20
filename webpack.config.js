const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // ⚡️ Point d'entrée de l'application
    .addEntry('app', './assets/app.js')

    // 🧹 Nettoyer le dossier build avant chaque build
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enablePostCssLoader()

    // 📦 Gérer les images (logo, icônes, etc.)
    .copyFiles({
        from: './assets/Images',           // Dossier source
        to: 'images/[path][name].[ext]',   // Dossier cible dans public/build/images
    })

    // 🔧 Configurer Babel
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })
;

module.exports = Encore.getWebpackConfig();