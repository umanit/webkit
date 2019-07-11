# Webkit

Etapes pour l'installation du Webkit UmanIT (juillet 2019).
En attendant la création d'un Bundle par le GT Back.

⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️

! Ce fichier est temporaire !

Nous allons remttre à jour tout le dossier pour avoir les bons éléments au même endroit.  
En attendant, voilà la procédure de base brute de décoffrage. 

TODO à venir : Mise en forme du guide de style en lui-même

⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️⚠️


## Mise en place de Webpack Encore
### Installer Webpack Encore sur le projet
Que le projet soit Symfony ou non , suivre la procédure :
[https://symfony.com/doc/current/frontend/encore/installation.html](https://symfony.com/doc/current/frontend/encore/installation.html)

### Création du dossier assets
A la racine du projet, créer le dossier ```assets``` à reprendre de ```umanit/css-starterkit``` sur Github.

### Réécriture du webpack.config.js
````
// webpack.config.js
const Encore = require('@symfony/webpack-encore');
let path = require('path');
// let styleguide = require('ruby-hologram-webpack-plugin');
let glob = require('glob');

/* *********************************************************************************************************************
 * Configuration générale * @todo : à l'initialisation du projet, renseigner ces letiables
 * ******************************************************************************************************************* */
// Chemin où se trouve le dossier assets du starterkit
const assetPath = './assets';
// Les assets compilés seront stockés ici
const outputPath = 'public/build';
// Path publique où le serveur web ira chercher les fichiers
const publicPath = '/build';

/* *********************************************************************************************************************
 * Fin de la configuration * ******************************************************************************************************************* */
Encore
// Les assets compilés seront stockés ici
  .setOutputPath(outputPath)

    // Path publique relatif à la racine web
  .setPublicPath(publicPath)

    // Purge le répertoire de build avant execution
  .cleanupOutputBeforeBuild()

    // Copie les assets statics (le répertoire de destination est déduit).
  .copyFiles([
        {
            from: './assets/img',
            to: 'images/[path][name].[ext]',
        },
        {
            from: './assets/fav',
            to: 'fav/[path][name].[ext]',
        },
        {
            from: './assets/fonts',
            to: 'fonts/[path][name].[ext]',
        }
    ])

    // Active SASS et Compass
  .enableSassLoader()

    .enablePostCssLoader()

    // allow legacy applications to use $/jQuery as a global letiable
  .autoProvidejQuery()

// Ajout des entrées JS dynamiquement
let entryArray = glob.sync(
    assetPath + '/js/**/*.js'
);

let name = '';
for (let x in entryArray) {
    name = entryArray[x].split('/').pop();
    Encore.addEntry(name.replace('.js', ''), entryArray[x]);
}

// When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
Encore
  .splitEntryChunks()

    // will require an extra script tag for runtime.js
 // but, you probably want this, unless you're building a single-page app  .enableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableSourceMaps()
    // enables hashed filenames (e.g. app.abc123.css)
  .enableVersioning(Encore.isProduction())

// export the final configuration
module.exports = Encore.getWebpackConfig();
````

## Création du guide de style dans le projet Symfony

### Création du Controller du guide de style

* Dans ```projet/config/routes/dev``` créer ```styleguide.yaml```
```
app.style_guide:
    path: /style-guide/{template}
    controller: App\Controller\StyleGuideController
 ```
* Dans le terminal, dans le projet :
``` docker-compose exec php bin/console make:controller ```
Cette commande crée le fichier ```StyleGuideController``` dans ```src/Controller```
Editer ce fichier avec :
```
<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class StyleGuideController extends AbstractController
{
  public function __invoke(string $template)
 {  return $this->render('style_guide/' . $template . '.html.twig');
 }}
```
### Le dossier et les fichiers du guide de style
Dans ```projet/templates```, créer le dossier ```style_guide```, puis structurer comme suit :
```
style_guide
	|_ partials
		|_ progress.html.twig
	|_ base.html.twig
	|_ index.html.twig
```
#### base.html.twig
```
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{% block title %}Welcome!{% endblock %}</title>
    {% block stylesheets %}
        {{ encore_entry_link_tags('app') }}
    {% endblock %}
</head>
<body>
{% block body %}{% endblock %}
{% block javascripts %}
    {{ encore_entry_script_tags('app') }}
{% endblock %}
</body>
</html>
```
#### index.html.twig
```
{% extends 'style_guide/base.html.twig' %}

{% block title %}ouais{% endblock %}

{% block body %}
    <table>
        {% include 'style_guide/partials/progress.html.twig' with {
 template: 'block',
 title: 'Block',
 tags: ['layout'],
 description: 'Block description',
 progress: 30
  }  %}
    </table>
{% endblock %}
```
Le ``` {% include %} ``` est à répéter autant de fois qu'il y a d'éléments ajoutés dans le dossier.

#### progress.html.twig
La ligne répétée à chacun de ses appels dans le ``` index.html.twig```.
```
<tr>
    <td width="40%">
        <a href="{{ path('app.style_guide', { template: template|default('index') }) }}">{{ title|default('Unnamed template')  }}</a>
        <p>
            {% for tag in tags|default([]) %}
                <small class="badge badge-warning">{{ tag|upper }}</small>
            {% endfor %}
        </p>
        <p>
            <small>{{ description|default('No description provided.') }}</small>
        </p>
    </td>
    <td width="60%">
        <div class="progress">
            <div class="progress-bar" style="width:{{ progress|default(0) }};">{{ progress|default(0) }}</div>
        </div>
    </td>
</tr>
```
## Réglages supplémentaires

### Installer Bootstrap 4 :
```docker-composer up node yarn add bootstrap@4.3.1```
Dans ```projet/scss/style.scss``` , penser à activer/désactiver/ajouter les lignes selon les besoins.

### Fichier postcss.config.js
En raison de la présence de ```.enablePostCssLoader()``` dans le fichier ```webpack.config.js``` il nous faut ajouter le fichier ```postcss.config.js``` à la racine du projet.

```
module.exports = {
  plugins: {
    // include whatever plugins you want
	// but make sure you install these via yarn or npm!
    // add browserslist config to package.json (see below)
    autoprefixer: {}
  }
}
```
### Installer les dépendances
Dans terminal, dans le projet :
```docker-compose exec node yarn install```

## Ajouter fichier .editorconfig
A placer à la racine du site
```# editorconfig.org
root = true

[*]
indent_style = space
indent_size = 2
end_of_line = lf
charset = utf-8
trim_trailing_whitespace = true
insert_final_newline = true

[*.{twig,js}]
indent_size = 2

[docker-compose.yml]
indent_size = 2

[Makefile]
indent_style = tab
```
