<?php

namespace AC\Component\Transcoding\Preset\ffmpeg;

use AC\Component\Transcoding\Preset;

class AudioCompression32kPreset extends BasePreset
{
    protected $key = "ffmpeg.audio_compression_32k";
    protected $name = "Audio Compression 32k Preset";
    protected $description = "A ffmpeg preset that compresses an audio file to a bite rate of 32kb/s";

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
			'-i' => '',
			'-ab' => '32k',
			'-o' => '',
        ));
    }

}