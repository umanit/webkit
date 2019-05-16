<?php

use Umanit\Webkit\DocumentedTemplate;

CONST PATH = 'templates';

$loader = new Twig_Loader_Filesystem([PATH, 'src/views']);
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
$twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
    return sprintf('/%s', ltrim($asset, '/'));
}));

// Simulation de la fonction 'trans' de Symfony
$twig->addFilter(new \Twig_SimpleFilter('trans', function ($str) {
    return $str;
}));

// Basic routing if a route is provided
$path = explode('/', trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), '/'));
if ($path[0] !== '') {
    $templateName = array_pop($path);

    $directory = '';
    foreach ($path as $dir) {
        $directory .= $dir.'/';
    }

    try {
        print $twig->render($directory.'/'.$templateName.'.html.twig');
    } catch (Twig_Error_Loader $e) {
        print $twig->render('404.html.twig', [
            'error_text' => sprintf(
                "<code>%s</code> template does not exist.</br> Stacktrace: </br><code>%s</code>",
                $templateName,
                $e
            ),
        ]);
    }
} else { // Display template listing if no route is provided
    $paths = $loader->getPaths();

    $files = DocumentedTemplate::parseDirectory(reset($paths));

    // DocumentTemplate creation
    $categories = [];
    $absPath = str_replace('src', PATH, __DIR__);
    foreach ($files as $filePath) {
        $arrayPath = explode('/', trim($filePath, '/'));
        $prefix = substr(array_pop($arrayPath), 0, 1);
        $extension = substr($filePath, -4, 4);
        $isBaseTemplate = strstr($filePath, 'base') !== false;
        // Ignore files starting with "_", base template and not a twig files
        if ( $prefix !== '_' && $extension === 'twig' && !$isBaseTemplate) {
            $template = new DocumentedTemplate($filePath);
            $uri      = str_replace(
                '.html.twig',
                '',
                str_replace($absPath, '', $filePath)
            );

            $template->setUri($uri);
            $categories[ucfirst($template->category)][] = $template;
        }
    }

    print $twig->render('index-webkit.html.twig', ['categories' => $categories]);
}
