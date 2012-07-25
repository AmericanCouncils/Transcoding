<?php

namespace AC\Component\Transcoding\Preset\Handbrake;

use AC\Component\Transcoding\Preset;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#appletv-old
 */
class AppleTVLegacyPreset extends BasePreset
{
    protected $key = "handbrake.apple_tv_legacy";
    protected $name = "Apple TV Legacy Preset";
    protected $description = "HandBrake's deprecated settings for the AppleTV, including Dolby Digital 5.1 AC3 sound. Provides a good balance between quality and file size, and optimizes performance. This is the AppleTV preset from HandBrake 0.9.2, and while it is offered as a service to legacy users, it is no longer supported. ";

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
            'video-library-encoder' => 'x264',
            'video-bitrate' => '2500',
            'select-audio-tracks' => '1,1',
            'audio-encoder' => 'faac,copy:ac3',
            'audio-bitrate' => '160,160',
            'surround-sound-downmixing' => 'dp12,auto',
            'audio-samplerate' => 'Auto,Auto',
            'dynamic-range-compression' => '0.0,0.0',
            'format' => 'mp4',
            'use-64-bit-mp4-files' => '',
            'strict-anamorphic-pixel-aspect-ratio' => '',
            'add-chapter-markers' => '',
            'advanced-encoder-options' =>  'ref=1:b-pyramid=none:weightp=0:subme=5:me=umh:no-fast-pskip=1:cabac=0:weightb=0:8x8dct=0:trellis=0',
        ));
    }

}
