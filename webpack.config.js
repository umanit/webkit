// webpack.config.js
let Encore = require('@symfony/webpack-encore');

let webpack = require('webpack');

let CopyWebpackPlugin = require('copy-webpack-plugin');

let path = require('path');

let assetPath = './assets';

let glob = require('glob');

Encore
// Path de build relatif au projet
    .setOutputPath('./build/')

    // Path publique relatif à la racine web
    .setPublicPath('/build')

    // Purge le répertoire de build avant execution
    .cleanupOutputBeforeBuild()

    // Copie les assets statics (le répertoire de destination est déduit).
    .addPlugin(
        new CopyWebpackPlugin([
            {context: assetPath, from: 'fonts/**/*'},
            {context: assetPath, from: 'img/**/*'},
            {context: assetPath, from: 'fav/**/*'},
        ])
    )

    // Création d'un fichier unique 'app.js' pour tous les scripts
    .createSharedEntry('app', [
        'jquery',
        'bootstrap',
    ])

    // Active SASS et Compass
    .enableSassLoader(function (options) {
        options.includePaths = [path.resolve(__dirname, "./node_modules/compass-mixins/lib")];
    })

    // allow legacy applications to use $/jQuery as a global letiable
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

    // Ajout un ash dans le nom du fichier
    .enableVersioning();

// Ajout des entrées js
// @todo trouver à faire ça nativement

let entryArray = glob.sync(assetPath + '/js/**/*.js');
let name = '';
for (let x in entryArray) {
    name = entryArray[x].split('/').pop();
    Encore.addEntry(name.replace('.js', ''), entryArray[x]);
}


// export the final configuration
module.exports = Encore.getWebpackConfig();

