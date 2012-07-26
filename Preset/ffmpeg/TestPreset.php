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
