<?php

namespace AC\Component\Transcoding\Preset\FFmpeg;

use AC\Component\Transcoding\Preset;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#classic
 */
class AviToAnimatedGifPreset extends BasePreset
{
    protected $key = "ffmpeg.avi_to_animated_gif";
    protected $name = "Avi To Animated Gif Preset";
    protected $description = "A test ffmpeg preset";

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
            '-i' => '',
            '-pix_fmt' => 'rgb24',
            '-o' => '',
        ));
    }

}
