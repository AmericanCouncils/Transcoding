<?php

namespace AC\Component\Transcoding\Tests\Mock;
use AC\Component\Transcoding\File;
use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\TranscodeEventListener;

class Listener extends TranscodeEventListener
{
    public $messages = array();

    public function onTranscodeStart(File $inFile, Preset $preset, $outputFilePath)
    {
        $messages[] = __METHOD__;
    }

    public function onTranscodeComplete(File $inFile, Preset $preset, File $outFile)
    {
        $messages[] = __METHOD__;
    }

    public function onTranscodeFailure(File $inFile, Preset $preset, $outputFilePath, \Exception $e)
    {
        $messages[] = __METHOD__;
    }
}
