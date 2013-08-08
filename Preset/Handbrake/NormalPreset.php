<?php

namespace AC\Transcoding\Preset\Handbrake;

use AC\Transcoding\Preset;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#normal
 */
class NormalPreset extends BasePreset
{
    protected $key = "handbrake.normal";
    protected $name = "Normal Preset";
    protected $description = "HandBrake's normal, default settings. ";

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
            'video-library-encoder' => 'x264',
            'video-quality' => '20',
            'select-audio-tracks' => '1',
            'audio-encoder' => 'faac',
            'audio-bitrate' => '160',
            'surround-sound-downmixing' => 'dp12',
            'audio-samplerate' => 'Auto',
            'dynamic-range-compression' => '0.0',
            'format' => 'mp4',
            'strict-anamorphic-pixel-aspect-ratio' => '',
            'add-chapter-markers' => '',
            'advanced-encoder-options' => 'ref=2:bframes=2:subme=6:mixed-refs=0:weightb=0:8x8dct=0:trellis=0',
        ));
    }

}
