<?php

namespace AC\Transcoding\Event;

use Symfony\Component\EventDispatcher\Event;
use AC\Transcoding\File;

/**
 * Fired any time a file operation has taken place.
 *
 * @package Transcoding
 * @author Evan Villemez
 */
class FileEvent extends Event
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }
}
