<?php

namespace AC\Component\Transcoding\Preset\Handbrake;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\FileHandlerDefinition;

abstract class BasePreset extends Preset
{
<<<<<<< HEAD
    protected $requiredAdapter = 'handbrake';
=======
	protected $requiresOutputExtension = false;
    protected $requiredAdapter = 'ffmpeg';
>>>>>>> outputExtension revisions

    protected function buildInputDefinition()
    {
        return new FileHandlerDefinition(array(
            'requiredType' => 'file',
        ));
    }

    /**
     * Output should be a file with and .mp4 extension
     */
    protected function buildOutputDefinition()
    {
        return new FileHandlerDefinition(array(
            'requiredType' => 'file',
            'requiredExtension' => 'mp4',
            'inheritExtension' => false,
        ));
    }
}
