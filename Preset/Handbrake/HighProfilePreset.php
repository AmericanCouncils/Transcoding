<?php

namespace AC\Component\Transcoding\Preset\Handbrake;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\FileHandlerDefinition;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#highprofile
 */
class HighProfilePreset extends BasePreset {
	protected $key = "handbrake.high_profile";
	protected $name = "High Profile Preset";
	protected $description = "HandBrake's general-purpose preset for High Profile H.264 video, with all the bells and whistles.";
	
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