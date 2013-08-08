<?php

namespace AC\Transcoding\Preset\FFmpeg;

use AC\Transcoding\Preset;
use AC\Transcoding\FileHandlerDefinition;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#classic
 */
class SoundFromVideoPreset extends BasePreset
{
    protected $key = "ffmpeg.sound_from_video";
    protected $name = "Sound from Video Preset";
    protected $description = "A ffmpeg preset that takes the audio from a video and transmits it to an audio file";

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
            '-vn' => '',
            '-ar' => '44100',
            '-ac' => '2',
            '-ab' => '192',
            '-f' => 'mp3',
        ));
    }

    protected function buildOutputDefinition()
    {
        return new FileHandlerDefinition(array(
            'requiredType' => 'file',
            'requiredExtension' => 'mp3',
            'inheritInputExtension' => false,
        ));
    }
}
