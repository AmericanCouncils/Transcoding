<?php

namespace AC\Component\Transcoding\Adapter;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\Adapter;
use AC\Component\Transcoding\File;
use AC\Component\Transcoding\FileHandlerDefinition;
use Symfony\Component\Process\Process;

/**
 * A ffmpeg adapter, see
 *
 * Written by Andrew Freix
 */
class FFmpegAdapter extends Adapter
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
     * Handbrake needs a path to the HandBrakeCLI executable
     *
     * @param string $handbrake_path
     */
    public function __construct($ffmpeg_path)
    {
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
     * Run transcode, transforming contents of a text-based file.
     */
    public function transcodeFile(File $inFile, Preset $preset, $outFilePath)
    {
        $commandString = $this->ffmpeg_path;

        //assemble handbrake arguments from preset
		$preset_options = $preset->getOptions();
        foreach ($preset_options as $key => $value) {
			if ($key == '-i') {
				$commandString .= " ".$key." ".$inFile->getPathname();
			}
			elseif ($key == '-o') {
				$commandString .= " ".$outFilePath	;//.".".$preset_options['-f'];
			}
			else {
				$commandString .= " ".$key." ".$value;
			}
        }
		echo ($commandString);
        //use the Process component to build a process instance with the command string
        $process = new Process($commandString);
        $process->run();

        //check for error status return
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getExitCodeText());
        }

        //send output messages
        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();
        if ($output != null) {
            $this->info($output);
        }

        //error output in handbrake doesn't necessarily mean an error occured, so just send it as a warning
        if ($errorOutput != null) {
            $this->warn($errorOutput);
        }

        //return newly created file
        return new File($outFilePath);
    }

}
