<?php

namespace AC\Component\Transcoding\Preset\Handbrake;

use AC\Component\Transcoding\Preset;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#ipodlow-rez
 */
class iPodPreset extends BasePreset
{
    protected $key = "handbrake.ipod";
    protected $name = "iPod Preset";
    protected $description = "HandBrake's low resolution settings for the iPod (5G and up). Optimized for great playback on the iPod screen, with smaller file size.";

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
            'video-library-encoder' => 'x264',
            'video-bitrate' => '700',
            'select-audio-tracks' => '1',
            'audio-encoder' => 'faac',
            'audio-bitrate' => '160',
            'surround-sound-downmixing' => 'dp12',
            'audio-samplerate' => 'Auto',
            'dynamic-range-compression' => '0.0',
            'format' => 'mp4',
            'mark-mp4-file-for-5.5g-ipods' => '',
            'max-width' => '320',
            'add-chapter-markers' => '',
            'advanced-encoder-options' =>  'level=30:bframes=0:weightp=0:cabac=0:ref=1:vbv-maxrate=768:vbv-bufsize=2000:analyse=all:me=umh:no-fast-pskip=1:subme=6:8x8dct=0:trellis=0',
        ));
    }

}
