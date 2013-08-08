<?php

namespace AC\Transcoding\Preset\FFmpeg;

use AC\Transcoding\Preset;

class AudioCompression256kPreset extends BasePreset
{
    protected $key = "ffmpeg.audio_compression_256k";
    protected $name = "Audio Compression 256k Preset";
    protected $description = "A ffmpeg preset that compresses an audio file to a bite rate of 256kb/s";
    protected $requiresOutputExtension = true;

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
            '-ab' => '256k',
        ));
    }
}
