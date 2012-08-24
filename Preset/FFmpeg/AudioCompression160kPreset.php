<?php

namespace AC\Component\Transcoding\Preset\FFmpeg;

use AC\Component\Transcoding\Preset;

class AudioCompression160kPreset extends BasePreset
{
    protected $key = "ffmpeg.audio_compression_160k";
    protected $name = "Audio Compression 160k Preset";
    protected $description = "A ffmpeg preset that compresses an audio file to a bite rate of 160kb/s";
	protected $requiresOutputExtension = true;

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
            '-i' => '',
            '-ab' => '160k',
            '-o' => '',
        ));
    }
	public function getOutputExtension() {
		
	}
}
