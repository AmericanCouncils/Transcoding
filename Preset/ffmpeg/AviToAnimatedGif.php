<?php

namespace AC\Component\Transcoding\Preset\ffmpeg;

use AC\Component\Transcoding\Preset;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#classic
 */
class AviToAnimateGifPreset extends BasePreset
{
    protected $key = "ffmpeg.avi_to_animated_gif";
    protected $name = "Test Preset";
    protected $description = "A test ffmpeg preset";

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
			'-i' => '',
			'-o' => '',
        ));
    }

}
