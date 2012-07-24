<?php

namespace AC\Component\Transcoding\Preset\Handbrake;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\FileHandlerDefinition;

class HighProfilePreset extends Preset {
	protected $key = "handbrake.high_profile";
	protected $name = "High Profile Preset";
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
			'select-audio-tracks' => '1,1',
			'audio-encoder' => 'faac,copy:ac3',
			'audio-bitrate' => '160,160',
			'surround-sound-downmixing' => 'dp12,auto',
			'audio-samplerate' => 'Auto,Auto',
			'dynamic-range-compression' => '0.0,0.0',
			'format' => 'mp4',
			'detelecine-video-with-pullup-filter' => '',
			'deinterlace-when-detects-combing' => '',
			'loose-anamorphic-pixel-aspect-ratio' => '',
			'add-chapter-markers' => '',
			'advanced-encoder-options' => 'b-adapt=2:rc-lookahead=50',
		));
	}
	
}