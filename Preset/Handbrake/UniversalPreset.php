<?php

namespace AC\Component\Transcoding\Preset\Handbrake;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\FileHandlerDefinition;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#universal
 */
class UniversalPreset extends BasePreset {
	protected $key = "handbrake.universal";
	protected $name = "Universal Preset";
	protected $description = "HandBrake's universally compatible, full resolution settings for all current Apple devices: iPod (6G and up), iPhone, AppleTV, and Macs ";
	
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
			'max-width' => '720',
			'loose-anamorphic-pixel-aspect-ratio' => '',
			'add-chapter-markers' => '',
			'advanced-encoder-options' =>  'cabac=0:ref=2:me=umh:bframes=0:weightp=0:8x8dct=0:trellis=0:subme=6',
		));
	}
	
}