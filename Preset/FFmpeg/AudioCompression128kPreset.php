<?php

namespace AC\Component\Transcoding\Preset\FFmpeg;

use AC\Component\Transcoding\Preset;

class AudioCompression128kPreset extends BasePreset
{
    protected $key = "ffmpeg.audio_compression_128k";
    protected $name = "Audio Compression 128k Preset";
    protected $description = "A ffmpeg preset that compresses an audio file to a bite rate of 128kb/s";
	protected $requiresOutputExtension = true;

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
            '-ab' => '128k',
        ));
    }
	public function getOutputExtension() {
		
	}
}
