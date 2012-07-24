<?php

namespace AC\Component\Transcoding\Preset\Handbrake;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\FileHandlerDefinition;

class iPhoneiPodTouchPreset extends Preset {
	protected $key = "handbrake.iPhone_iPod_Touch";
	protected $name = "iPhone and iPod Touch Preset";
	protected $requiredAdapter = 'handbrake';
	protected $description = "Contains the array of information for a given handbrake preset";
	
	protected function buildInputDefinition() {
		return new FileHandlerDefinition(array(
			//'allowedMimeEncodings' => array('us-ascii', 'utf-8'),
			'requiredType' => 'file',
		));
	}
	
	/**
	 * Output should be a file.
	 */
	protected function buildOutputDefinition() {
		return new FileHandlerDefinition(array(
			'requiredType' => 'file',
			'allowedExtensions' => array(
				'mp4',
			),
			'inheritExtension' => false,
		));
	}
	
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