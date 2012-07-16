<?php
namespace AC\Component\Transcoding\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * An event fired when trying to resolve an unspecified output filepath.
 *
 * @package default
 * @author Evan Villemez
 */
class ResolveOutputFilepathEvent extends Event
{
    public function setOutputFilePath($path)
    {
        $this->path = $path;
        $this->stopPropagation();
    }

    public function getOutputFilePath()
    {
        return $this->path;
    }
}
