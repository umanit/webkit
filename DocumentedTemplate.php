<?php

/**
 * Class DocumentedTemplate
 * Outil de documentation des templates twigs
 *
 * @author tcaron@umanit.fr
 */
class DocumentedTemplate
{
    /* @var string $title */
    public $title;

    /* @var string $category */
    public $category;

    /* @var string $description */
    public $description;

    /* @var string $progressFront */
    public $progressFront;

    /* @var string $progressBack */
    public $progressBack;

    /* @var string $uri */
    public $uri;

    /* @var string $filePath */
    public $filePath;

    /* @var array $tags */
    public $tags = [];

    /**
     * DocumentedTemplate constructor.
     *
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->parseTemplate();
    }

    /**
     * Parse le template et set les propriétés de l'objet
     * @todo AGU mettre en place le Property Accessor de Symfo
     * @todo TCA voir à retirer ça de la classe modèle
     */
    private function parseTemplate()
    {
        $fileContent = file_get_contents($this->filePath);

        if (preg_match("/{#(.|\n)*#}/", $fileContent, $matches)) {
            if (preg_match_all("/@([A-Za-z\-]*): (.*)/", reset($matches), $commentStrings)) {
                // Set des propriétés
                foreach ($commentStrings[1] as $key => $property) {
                    $value = $commentStrings[2][$key];
                    // @todo AGU Property Accessor
                    $this->{'set'.ucfirst($property)}($value);
                }
            }
        }
    }

    /**
     * Récupère les fichiers d'un dossier récurcivement
     *
     * @see https://stackoverflow.com/questions/24783862/list-all-the-files-and-folders-in-a-directory-with-php-recursive-function#24784144
     *
     * @param string $directory
     * @param array  $results
     *
     * @return array
     */
    public static function parseDirectory(string $directory, array &$results = []): array
    {
        $files = scandir($directory);
        foreach ($files as $key => $value) {
            $path = realpath($directory.DIRECTORY_SEPARATOR.$value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                self::parseDirectory($path, $results);
            }
        }

        return $results;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @param string $category
     */
    public function setCategory(string $category)
    {
        $this->category = $category;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @param string $progressFront
     */
    public function setProgressFront(string $progressFront)
    {
        $this->progressFront = $progressFront;
    }

    /**
     * @param string $progressBack
     */
    public function setProgressBack(string $progressBack)
    {
        $this->progressBack = $progressBack;
    }

    /**
     * @param string $uri
     */
    public function setUri(string $uri)
    {
        $this->uri = $uri;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @param string $tags
     */
    public function setTags(string $tags)
    {
        $tags = explode(',', $tags);
        array_walk($tags, function ($item) {
            $this->tags[] = trim($item);
        });
    }

}



