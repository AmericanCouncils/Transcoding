<?php

namespace AC\Component\Transcoding\Preset\Handbrake;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\FileHandlerDefinition;

/**
 * A very simple preset, for a very simple adapter, to illustrate how writing a preset works.  This will be deleted once we have real Preset\Handbrake that do useful things.
 */
class iPhone4Preset extends Preset {
	protected $key = "handbrake.iPhone_4";
	protected $name = "iPhone 4 Preset";
	protected $requiredAdapter = 'handbrake';
	protected $description = "Contains the array of information for a given handbrake preset";
	
	/**
	 * Restrict input encodings to formats we know PHP won't have a problem with.
	 */
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
			'video-framerate' => '29.97',
			'peak-limited-frame-rate-control' => '',
			'selectaudio-tracks' => '1',
			'audio-encoder' => 'faac',
			'audio-bitrate' => '160',
			'surround-sound-downmixing' => 'dp12',
			'audio-samplerate' => 'Auto',
			'dynamic-range-compression' => '0.0',
			'format' => 'mp4',
			'use-64-bit-mp4-files' => '',
			'max-width' => '960',
			'loose-anamorphic-pixel-aspect-ratio' => '',
			'add-chapter-markers' => '',
		));
	}
	
}