<?php

namespace AC\Component\Transcoding\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 *  Transcode events are fired before and after a transcode process on an individual file, and in the case of exceptions thrown.
 */
class TranscodeEvent extends Event
{
    protected $inpath;
    protected $preset;
    protected $outpath;
    protected $job;
    protected $e;

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
    public function __construct($inpath, $preset, $outpath = null, Job $job = null, \Exception $e = null)
    {
        $this->inpath = $inpath;
        $this->preset = $preset;
        $this->outpath = $outpath;
        $this->job = $job;
        $this->exception = $e;
    }

    /**
     * Return the input file instance associated with the event
     *
     * @return File
     */
    public function getInputPath()
    {
        return $this->inpath;
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
    public function getOutputPath()
    {
        return $this->outpath;
    }

}
