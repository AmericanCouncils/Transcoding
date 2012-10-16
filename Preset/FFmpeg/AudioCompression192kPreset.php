<?php

namespace AC\Component\Transcoding\Preset\FFmpeg;

use AC\Component\Transcoding\Preset;

class AudioCompression192kPreset extends BasePreset
{
    protected $key = "ffmpeg.audio_compression_192k";
    protected $name = "Audio Compression 192k Preset";
    protected $description = "A ffmpeg preset that compresses an audio file to a bite rate of 192kb/s";
    protected $requiresOutputExtension = true;

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
            '-ab' => '192k',
        ));
    }
}
