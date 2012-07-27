<?php

namespace AC\Component\Transcoding\Adapter;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use AC\Component\Transcoding\Adapter;
use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\File;

/**
 * An abstract CLI adapter.  Extending classes just need to return a process builder for the command they want to run, and define custon
 * input/output file handlers and/or validation.
 *
 * @package Transcoding
 * @author Evan Villemez
 */
abstract class AbstractCliAdapter extends Adapter
{
    /**
     * Array of configuration for the process builder
     *
     * @var string
     */
    private $config = array();
    
    /**
     * Construct receives config for the process builder
     *
     * @param string $builderConfig 
     */
    public function __construct($builderConfig = array())
    {
        $this->config = $builderConfig;
    }
    
    /**
     * Return a new instance of a pre-configured process builder.
     *
     * @return ProcessBuilder
     */
    protected function getProcessBuilder(array $config = null)
    {
        $defaults = array(
            'timeout' => null,
        );
        
        $builder = new ProcessBuilder($config);
        
        $builder->setOptions($config['options']);
        $builder->setTimeout($config['timeout']);
        
        return $builder;
    }

    /**
     * Build a process using the ProcessBuilder, given transcoding input.  Return an instance of the configured
     * process builder.
     *
     * @param File $inFile 
     * @param Preset $preset 
     * @param string $outFilePath 
     * @return ProcessBuilder
     */
    public function buildProcess(File $inFile, Preset $preset, $outFilePath)
    {
        throw new \RuntimeException(sprintf("%s must be implemented by an extending class and return an instance of of Symfony\Component\Process\ProcessBuilder.", __METHOD__));
    }

    /**
     * Runs a transcode process by wrapping a command-line process.
     * 
     * {@inheritdoc}
     */
    public function transcodeFile(File $inFile, Preset $preset, $outFilePath)
    {
        //get assembled process instance from extending class
        $builder = $this->buildProcess($inFile, $preset, $outFilePath);
        
        //check the return
        if (!$builder instanceof ProcessBuilder) {
            throw new \InvalidArgumentException(sprintf("%s must return an instance of Symfony\Component\Process\ProcessBuilder", get_class($this)."::buildProcess"));
        }
        
        //get the assembled process
        $process = $builder->getProcess();
        
        //set the dynamic callback, if interactive is enabled
        $adapter = $this;
        $processBufferCallback = (!$this->stream_buffer) ? null : function($type, $buffer) use ($adapter) {
            if($type == 'err') {
                $adapter->warn($buffer);
            } else {
                $adapter->info($buffer);
            }
        };
        
        //run the process, pass feedback as messages to the adapter
        $process->run($processBufferCallback);
        
        //check for error status return
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getExitCodeText());
        }
        
        //return newly created file
        return new File($outFilePath);
    }


}