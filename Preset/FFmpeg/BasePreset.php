<?php

namespace AC\Transcoding\Preset\FFmpeg;

use AC\Transcoding\Preset;
use AC\Transcoding\FileHandlerDefinition;

abstract class BasePreset extends Preset
{
    protected $requiredAdapter = 'ffmpeg';

    protected function buildInputDefinition()
    {
        return new FileHandlerDefinition(array(
            'requiredType' => 'file',
        ));
    }

    protected function buildOutputDefinition()
    {
        return new FileHandlerDefinition(array(
            'requiredType' => 'file',
            'inheritInputExtension' => false,
        ));
    }
}
