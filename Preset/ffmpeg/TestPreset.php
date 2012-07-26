<?php

namespace AC\Component\Transcoding\Preset\ffmpeg;

use AC\Component\Transcoding\Preset;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#classic
 */
class TestPreset extends Preset
{
    protected $key = "ffmpeg.test";
    protected $name = "Test Preset";
	protected $requiredAdapter = 'ffmpeg';
    protected $description = "A test ffmpeg preset";

    /**
     * Specify the options for this specific preset
     */
	 
	 ffmpeg -i source_video.avi -vn -ar 44100 -ac 2 -ab 192 -f mp3 sound.mp3
    public function configure()
    {
        $this->setOptions(array(
			'-i' => '',
            '-vn' => '',
			'-ar' => '44100',
			'-ac' => '2',
			'-ab' => '192',
            '-f' => 'mp3',
			'-o' => '',
        ));
    }

}
