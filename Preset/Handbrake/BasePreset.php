<?php

namespace AC\Component\Transcoding\Preset\Handbrake;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\FileHandlerDefinition;

abstract class BasePreset extends Preset {
	protected $requiredAdapter = 'handbrake';
	
	protected function buildInputDefinition() {
		return new FileHandlerDefinition(array(
			'requiredType' => 'file',
		));
	}
	
	/**
	 * Output should be a file with and .mp4 extension
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
}