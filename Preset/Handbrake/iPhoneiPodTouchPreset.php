<?php

namespace AC\Component\Transcoding\Preset\Handbrake;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\FileHandlerDefinition;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#iphone
 */
class iPhoneiPodTouchPreset extends BasePreset {
	protected $key = "handbrake.iphone_ipod_touch";
	protected $name = "iPhone and iPod Touch Preset";
	protected $description = "HandBrake's settings for all iPhones and iPod Touches going back to the original iPhone 2G. ";
	
	/**
	 * Specify the options for this specific preset
	 */
	public function configure() {
		$this->setOptions(array(
			'video-library-encoder' => 'x264',
			'video-quality' => '20',
			'selectaudio-tracks' => '1',
			'audio-encoder' => 'faac',
			'audio-bitrate' => '128',
			'surround-sound-downmixing' => 'dp12',
			'audio-samplerate' => 'Auto',
			'dynamic-range-compression' => '0.0',
			'format' => 'mp4',
			'max-width' => '480',
			'add-chapter-markers' => '',
			'advanced-encoder-options' =>  'cabac=0:ref=2:me=umh:bframes=0:weightp=0:subme=6:8x8dct=0:trellis=0',
		));
	}
	
}