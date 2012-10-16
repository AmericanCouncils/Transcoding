<?php

namespace AC\Component\Transcoding\Preset\FFmpeg;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\FileHandlerDefinition;

/**
 * For more information on this preset please visit this link: https://trac.handbrake.fr/wiki/BuiltInPresets#classic
 */
class ConvertNonMVideoPreset extends BasePreset
{
    protected $key = "ffmpeg.convert_non_m_video";
    protected $name = "Convert Video Preset";
    protected $description = "Based upon the file extensions of the input and output ffmpeg converts the input type to the output type";
    protected $requiresOutputExtension = true;

    protected function buildOutputDefinition()
    {
        return new FileHandlerDefinition(array(
            'requiredType' => 'file',
            'rejectedExtensions' => array(
                'mp4',
                'mpg',
            ),
            'inheritInputExtension' => false,
        ));
    }

    public function generateOutputPath(File $inFile, $outputPath = false)
    {
        if (!$outputPath) {
            throw new \InvalidArgumentException(sprintf("This preset must have the output extension specified in the outgoing file path."));
        }

        return parent::generateOutputPath($inFile, $outputPath);
    }
}
