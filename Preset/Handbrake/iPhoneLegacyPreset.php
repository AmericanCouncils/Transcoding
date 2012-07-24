<?php

namespace AC\Component\Transcoding\Preset\Handbrake;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\FileHandlerDefinition;

class iPhoneLegacyPreset extends Preset {
	protected $key = "handbrake.iPhone_legacy";
	protected $name = "iPhone Legacy Preset";
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