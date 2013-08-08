<?php

namespace AC\Transcoding\Preset\Handbrake;

use AC\Transcoding\Preset;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#atv2
 */
class AppleTV2Preset extends BasePreset
{
    protected $key = "handbrake.apple_tv2";
    protected $name = "Apple TV 2 Preset";
    protected $description = "HandBrake's preset for the Apple TV (2nd gen) is optimized for viewing on its 1280x720 display.";

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
            'video-library-encoder' => 'x264',
            'video-quality' => '20',
            'video-framerate' => '29.97',
            'peak-limited-frame-control-rate' => '',
            'select-audio-tracks' => '1,1',
            'audio-encoder' => 'faac,copy:ac3',
            'audio-bitrate' => '160,160',
            'surround-sound-downmixing' => 'dp12,auto',
            'audio-samplerate' => 'Auto,Auto',
            'dynamic-range-compression' => '0.0,0.0',
            'format' => 'mp4',
            'use-64-bit-mp4-files' => '',
            'max-width' => '1280',
            'loose-anamorphic-pixel-aspect-ratio' => '',
            'add-chapter-markers' => '',
        ));
    }

}
