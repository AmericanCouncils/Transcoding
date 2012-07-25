<?php

namespace AC\Component\Transcoding\Preset\Handbrake;

use AC\Component\Transcoding\Preset;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#iphone-old
 */
class iPhoneLegacyPreset extends BasePreset
{
    protected $key = "handbrake.iphone_legacy";
    protected $name = "iPhone Legacy Preset";
    protected $description = "HandBrake's deprecated settings for the iPhone and iPod Touch. This is the iPhone preset from HandBrake 0.9.2, and while it is offered as a service to legacy users, it is no longer supported. ";

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
            'video-library-encoder' => 'x264',
            'video-bitrate' => '960',
            'selectaudio-tracks' => '1',
            'audio-encoder' => 'faac',
            'audio-bitrate' => '128',
            'surround-sound-downmixing' => 'dp12',
            'audio-samplerate' => 'Auto',
            'dynamic-range-compression' => '0.0',
            'format' => 'mp4',
            'mark-mp4-file-for-5.5g-ipods' => '',
            'max-width' => '480',
            'add-chapter-markers' => '',
            'advanced-encoder-options' =>  'level=30:cabac=0:ref=1:analyse=all:me=umh:no-fast-pskip=1:psy-rd=0,0:bframes=0:weightp=0:subme=6:8x8dct=0:trellis=0',
        ));
    }

}
