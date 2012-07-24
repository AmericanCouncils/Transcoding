<?php

namespace AC\Component\Transcoding\Preset\Handbrake;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\FileHandlerDefinition;

class ClassicPreset extends Preset {
	protected $key = "handbrake.classic";
	protected $name = "Classic Preset";
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
			'video-bitrate' => '1000',
			'select-audio-tracks' => '1',
			'audio-encoder' => 'faac',
			'audio-bitrate' => '160',
			'surround-sound-downmixing' => 'dp12',
			'audio-samplerate' => 'Auto',
			'dynamic-range-compression' => '0.0',
			'format' => 'mp4',
		));
	}
	
}