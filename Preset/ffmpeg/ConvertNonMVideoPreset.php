<?php

namespace AC\Component\Transcoding\Preset\ffmpeg;

use AC\Component\Transcoding\Preset;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#classic
 */
class ConvertNonMVideoPreset extends BasePreset
{
    protected $key = "ffmpeg.convert_non_m_video";
    protected $name = "Convert Video Preset";
    protected $description = "Based upon the file extensions of the input and output ffmpeg converts the input type to the output type";

    /**
     * Specify the options for this specific preset
     */
    public function configure()
    {
        $this->setOptions(array(
			'-i' => '',
			'-o' => '',
        ));
    }
	protected function buildOutputDefinition()
    {
        return new FileHandlerDefinition(array(
            'requiredType' => 'file',
			'rejectedExtensions' => array(
				'mp4',
				'mpg',
			),
            'inheritExtension' => false,
        ));
    }
}
