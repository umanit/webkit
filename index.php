<?php
/**
 * Ajouter une page avec l'avancement
 * 1. Se positionner dans l'élément `.container`
 * 2. Créer un lien de la forme `<a href="#" data-progress="[integer]">Page</a>`
 * 3. Trier par ordre alphabétique
 */

// Programme de chargement de Twig
require_once 'vendor/autoload.php';
require_once 'DocumentedTemplate.php';

CONST PATH = 'templates';

$loader = new Twig_Loader_Filesystem(PATH);
$twig   = new Twig_Environment($loader, [
    'auto_reload' => true,
    'debug'       => true,
]);

// Cache buster pour l'intégration statique basé sur le checksum des css/js générés
$assetVersions = [
    // 'css' => sha1_file(__DIR__.'/build/css/style.css'),
    // 'js'  => sha1_file(__DIR__.'/build/js/app.js'),
];

// Simulation de la fonction `asset()` de Bolt
$twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($assetVersions) {
    $assetName    = explode('.', $asset);
    $assetVersion = $assetVersions[end($assetName)];

    return sprintf('/theme/adn-ouest/%s?v'.$assetVersion, ltrim($asset, '/'));
}));

// Pour accéder à un template, remplir le paramètre t dans l'URL par le nom du template (sans extension) voulu.
if (isset($_GET['template'])) {
    $templateName = $_GET['template'];
    $directory    = $_GET['directory'] ?? '';

    try {
        print $twig->render($directory.'/'.$templateName.'.html.twig');
    } catch (Twig_Error_Loader $e) {
        print $twig->render('404.html.twig', [
            'error_text' => 'Le template <code>'.$templateName.'</code> n\'existe pas.</br>
		     Stacktrace : </br><code>'.$e.'</code>',
        ]);
    }
} else {
    // Récupération des templates
    $files = DocumentedTemplate::parseDirectory(reset($loader->getPaths()));

    // Création des DocumentTemplate
    $categories = [];
    foreach ($files as $filePath) {
        $template = new DocumentedTemplate($filePath);
        $uri = str_replace(
            '.html.twig',
            '',
            str_replace(__DIR__.'/'.PATH, '', $filePath)
        );

        $template->setUri($uri);
        $categories[strtolower($template->category)][] = $template;
    }

    print $twig->render('index-webkit.html.twig', [ 'categories' => $categories]);
}



