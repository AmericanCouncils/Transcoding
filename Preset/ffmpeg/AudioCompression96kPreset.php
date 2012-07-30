<?php

namespace AC\Component\Transcoding\Preset\ffmpeg;

use AC\Component\Transcoding\Preset;

class AudioCompression96kPreset extends BasePreset
{
    protected $key = "ffmpeg.audio_compression_96k";
    protected $name = "Audio Compression 96k Preset";
    protected $description = "A ffmpeg preset that compresses an audio file to a bite rate of 96kb/s";

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
			'-i' => '',
			'-ab' => '96k',
			'-o' => '',
        ));
    }

}