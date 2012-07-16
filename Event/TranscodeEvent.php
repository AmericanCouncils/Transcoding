<?php

namespace AC\Component\Transcoding\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 *  Transcode events are fired before and after a transcode process on an individual file, and in the case of exceptions thrown.
 */
class TranscodeEvent extends Event
{
    protected $inFile;
    protected $preset;
    protected $job;
    protected $e;
    protected $outPath;
    protected $outFile;

    /**
     * Constructor.  Created internally in the Transcoder when dispatched.
     *
     * @param File      $inFile
     * @param Preset    $preset
     * @param string    $outFile
     * @param Job       $job
     * @param Exception $e
     * @author Evan Villemez
     */
    public function __construct($inFile, $preset, $outFile = null, Job $job = null, \Exception $e = null)
    {
        $this->inFile = $inFile;
        $this->preset = $preset;
        $this->job = $job;
        $this->exception = $e;

        //check for output file path or file instance
        if ($outFile) {
            if (is_string($outFile)) {
                $this->outPath = $outFile;
            } elseif ($outFile instanceof File) {
                $this->outFile = $outFile;
                $this->outPath = $outFile->getRealPath();
            } else {
                throw new \InvalidArgumentException("[outFile] must be either a string filepath or an instance of AC\Component\Transcoding\File.");
            }
        }
    }

    /**
     * Return the input file instance associated with the event
     *
     * @return File
     */
    public function getInputFile()
    {
        return $this->$inFile;
    }

    /**
     * Return the preset instance associated with the event
     *
     * @return Preset
     */
    public function getPreset()
    {
        return $this->preset;
    }

    /**
     * Return the Job associated with the event (if available)
     *
     * @return Job or null
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Return the exception associated with an error, if available
     *
     * @return Exception or null
     */
    public function getException()
    {
        return $this->e;
    }

    /**
     * Return the string output file path associated with the event, if available
     *
     * @return string or null
     */
    public function getOutputFilepath()
    {
        return $this->outPath;
    }

    /**
     * Get the output file instance associated with this event, if available
     *
     * @return File or null
     */
    public function getOutputFile()
    {
        return $this->outFile;
    }

}
