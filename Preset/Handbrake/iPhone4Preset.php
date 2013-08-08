<?php

namespace AC\Transcoding\Preset\Handbrake;

use AC\Transcoding\Preset;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#iphone4
 */
class iPhone4Preset extends BasePreset
{
    protected $key = "handbrake.iphone4";
    protected $name = "iPhone 4 Preset";
    protected $description = "HandBrake's preset for the iPhone 4 is optimized for viewing on its 960x480 display. ";

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
            'select-audio-tracks' => '1',
            'audio-encoder' => 'faac',
            'audio-bitrate' => '160',
            'surround-sound-downmixing' => 'dp12',
            'audio-samplerate' => 'Auto',
            'dynamic-range-compression' => '0.0',
            'format' => 'mp4',
            'use-64-bit-mp4-files' => '',
            'max-width' => '960',
            'loose-anamorphic-pixel-aspect-ratio' => '',
            'add-chapter-markers' => '',
        ));
    }

}
