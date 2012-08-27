<?php

namespace AC\Component\Transcoding\Preset\FFmpeg;

use AC\Component\Transcoding\Preset;

class AudioCompression32kPreset extends BasePreset
{
    protected $key = "ffmpeg.audio_compression_32k";
    protected $name = "Audio Compression 32k Preset";
    protected $description = "A ffmpeg preset that compresses an audio file to a bite rate of 32kb/s";
	protected $requiresOutputExtension = true;

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
            '-ab' => '32k',
        ));
    }
	public function getOutputExtension() {
		
	}
}
