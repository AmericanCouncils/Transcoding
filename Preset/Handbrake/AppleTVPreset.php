<?php

namespace AC\Component\Transcoding\Preset\Handbrake;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\FileHandlerDefinition;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#atv
 */
class AppleTVPreset extends BasePreset {
	protected $key = "handbrake.apple_tv";
	protected $name = "Apple TV Preset";
	protected $description = "HandBrake's settings for the AppleTV and 2009's iPhone and iPod Touch lineup. Provides a good balance between quality and file size, and pushes the devices to their limits. Includes Dolby Digital 5.1 AC3 sound for the AppleTV.";
	
	/**
	 * Specify the options for this specific preset
	 */
	public function configure() {
		$this->setOptions(array(
			'video-library-encoder' => 'x264',
			'video-quality' => '20',
			'select-audio-tracks' => '1,1',
			'audio-encoder' => 'faac,copy:ac3',
			'audio-bitrate' => '160,160',
			'surround-sound-downmixing' => 'dp12,auto',
			'audio-samplerate' => 'Auto,Auto',
			'dynamic-range-compression' => '0.0,0.0',
			'format' => 'mp4',
			'use-64-bit-mp4-files' => '',
			'max-width' => '960',
			'loose-anamorphic-pixel-aspect-ratio' => '',
			'add-chapter-markers' => '',
			'advanced-encoder-options' =>  'cabac=0:ref=2:me=umh:b-pyramid=none:b-adapt=2:weightb=0:trellis=0:weightp=0:vbv-maxrate=9500:vbv-bufsize=9500',
		));
	}
	
}