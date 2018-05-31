<?php

namespace Umanit\Webkit;

/**
 * Exception levée en cas d'annotation non valide.
 *
 * @author Arthur Guigand <aguigand@umanit.fr>
 */
class NoSuchAnnotationException extends \Exception
{
    protected $message = 'Cette annotation n\'existe pas.';
}