<?php

namespace AC\Component\Transcoding;

class Job extends Preset
{
    protected $key;
    protected $name;
    protected $description;

    public function getRequiredPresets()
    {
        return array();
    }

    public function generateOutputReferences()
    {
    }

    public function configure($inFile)
    {
        $newFiles = array();

        //chain multiple presets on one file
        $this->addStep()
        $steps = array(
            $this->newStep()->setInput($inFile)->runPreset('video_to_mp4_high')->runPreset('video_to_mp4_low'),
            $this->newStep()->setInput($step1->getOutput())->runPreset();
        );
    }

    public function execute($inFile, $outputFilePath)
    {
    }

    protected function addFile()
    {
    }

}
