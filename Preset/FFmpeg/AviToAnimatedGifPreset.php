<?php

namespace AC\Component\Transcoding\Preset\FFmpeg;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\FileHandlerDefinition;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#classic
 */
class AviToAnimatedGifPreset extends BasePreset
{
    protected $key = "ffmpeg.avi_to_animated_gif";
    protected $name = "Avi To Animated Gif Preset";
    protected $description = "A test ffmpeg preset";

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
            '-pix_fmt' => 'rgb24',
        ));
    }

    protected function buildOutputDefinition()
    {
        return new FileHandlerDefinition(array(
            'requiredType' => 'file',
            'requiredExtension' => 'gif',
            'inheritInputExtension' => false,
        ));
    }

    protected function buildInputDefinition()
    {
        return new FileHandlerDefinition(array(
            'requiredType' => 'file',
            'requiredExtension' => 'avi',
        ));
    }

}
