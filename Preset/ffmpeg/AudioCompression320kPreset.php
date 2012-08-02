<?php

namespace AC\Component\Transcoding\Preset\ffmpeg;

use AC\Component\Transcoding\Preset;

class AudioCompression320kPreset extends BasePreset
{
    protected $key = "ffmpeg.audio_compression_320k";
    protected $name = "Audio Compression 320k Preset";
    protected $description = "A ffmpeg preset that compresses an audio file to a bite rate of 320kb/s";
	protected $requiresOutputExtension = true;

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
			'-i' => '',
			'-ab' => '320k',
			'-o' => '',
        ));
    }

}