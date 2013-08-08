<?php

namespace AC\Transcoding\Preset\Handbrake;

use AC\Transcoding\Preset;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#ipodhigh-rez
 */
class iPodLegacyPreset extends BasePreset
{
    protected $key = "handbrake.ipod_legacy";
    protected $name = "iPod Legacy Preset";
    protected $description = "HandBrake's high resolution settings for older 5 and 5.5G iPods. Good video quality, great for viewing on a TV using your iPod. This is the iPod High-Rez preset from 0.9.2. ";

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
            'video-library-encoder' => 'x264',
            'video-bitrate' => '1500',
            'select-audio-tracks' => '1',
            'audio-encoder' => 'faac',
            'audio-bitrate' => '160',
            'surround-sound-downmixing' => 'dp12',
            'audio-samplerate' => 'Auto',
            'dynamic-range-compression' => '0.0',
            'format' => 'mp4',
            'mark-mp4-file-for-5.5g-ipods' => '',
            'max-width' => '640',
            'add-chapter-markers' => '',
            'advanced-encoder-options' =>  'level=30:bframes=0:weightp=0:cabac=0:ref=1:vbv-maxrate=1500:vbv-bufsize=2000:analyse=all:me=umh:no-fast-pskip=1:psy-rd=0,0:subme=6:8x8dct=0:trellis=0',
        ));
    }

}
