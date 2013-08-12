<?php

namespace AC\Transcoding\Adapter;

use AC\Transcoding\Preset;
use AC\Transcoding\Adapter;
use AC\Transcoding\File;
use AC\Transcoding\FileHandlerDefinition;
use Symfony\Component\Process\Process;

/**
 * A ffmpeg adapter, see
 *
 * Written by Andrew Freix
 */
class FFmpegAdapter extends AbstractCliAdapter
{
    protected $key = "ffmpeg";
    protected $name = "FFmpeg";
    protected $description = "Uses ffmpeg presets to convert/edit audio, video, and image files.";

    /**
     * undocumented variable
     *
     * @var string Path to ffmpeg executable, received in constructor
     */
    private $ffmpeg_path;

    /**
     * FFmpeg needs a path to the `ffmpeg` executable
     *
     * @param string $ffmpeg_path
     * @param int    $timeout     Time in seconds for process timeout, null means no timeout
     */
    public function __construct($ffmpeg_path = 'ffmpeg', $timeout = null)
    {
        parent::__construct(array(
            'timeout' => $timeout
        ));

        $this->ffmpeg_path = $ffmpeg_path;
    }

    /**
     * {@inheritdoc}
     */
    public function verifyEnvironment()
    {
        if (!file_exists($this->ffmpeg_path)) {
            throw new \RuntimeException(sprintf("Could not find ffmpeg executable, given path {%s}", $this->ffmpeg_path));
        }

        return true;
    }

    /**
     * Must receive binary files
     *
     * {@inheritdoc}
     */
    protected function buildInputDefinition()
    {
        return new FileHandlerDefinition(array(
            'requiredMimeEncodings' => array('binary'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildProcess(File $inFile, Preset $preset, $outFilePath)
    {
        $options = array($this->ffmpeg_path, '-i', $inFile->getPathname());

        //add preset options
        foreach ($preset->getOptions() as $key => $value) {
            if (!is_null($key)) {
                $options[] = $key;
            }
            if (!is_null($value)) {
                $options[] = $value;
            }
        }
        $options[] = $outFilePath;

        //get builder with required options for in/out file
        $builder = $this->getProcessBuilder($options);

        return $builder;
    }

}
