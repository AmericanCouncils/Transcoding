<?php

namespace AC\Transcoding\Preset\FFmpeg;

use AC\Transcoding\Preset;

class AudioCompression96kPreset extends BasePreset
{
    protected $key = "ffmpeg.audio_compression_96k";
    protected $name = "Audio Compression 96k Preset";
    protected $description = "A ffmpeg preset that compresses an audio file to a bite rate of 96kb/s";
    protected $requiresOutputExtension = true;

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
            '-ab' => '96k',
        ));
    }
}
