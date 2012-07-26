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
class ffmpegAdapter extends Adapter
{
    protected $key = "ffmpeg";
    protected $name = "ffmpeg Adapter";
    protected $description = "Uses ffmpeg presets to convert/edit audio, video, and image files.";

    /**
     * undocumented variable
     *
     * @var string Path to ffmpeg executable, received in constructor
     */
    private $ffmpeg_path;

    /**
     * @var array Mappings of human-readable options to handbrake CLI equivalents
     */
    protected $ffmpeg_conversion = array(
        'help' => '-h',
        'check-for-updates' => '-u',
        'verbose' => '-v',
        'use-preset' => '-Z',
        'preset-list' => '-z',
        'input-device' => '-i',
        'select-title' => '--title',
        'min-duration' => '--min-duration',
        'scan-titles' => '--scan',
        'detect-select-main-feature-title' => '--main-feature',
        'select-chapters' => '-c',
        'select-dvd-angle' => '--angle',
        'select-num-previews' => '--previews',
        'start-at-preview' => '--start-at-preview',
        'start-encoding-at' => '--start-at',
        'stop-encoding-at' => '--stop-at',
        'output-file' => '-o',
        'format' => '-f',
        'add-chapter-markers' => '-m',
        'use-64-bit-mp4-files' => '-4',
        'optimize-mp4-for-http-streaming' => '-O',
        'mark-mp4-file-for-5.5g-ipods' => '-I',
        'video-library-encoder' => '-e',
        'x264-preset' => '--x264-preset',
        'x264-tune' => '--x264-tune',
        'advanced-encoder-options' => '-x',
        'x264-profile' => '--x264-profile',
        'video-quality' => '-q',
        'video-bitrate' => '-b',
        'use-two-pass-mode' => '--two-pass',
        'use-turbo-options' => '-T',
        'video-framerate' => '-r',
        'variable-frame-control-rate' => '--vfr',
        'constant-frame-control-rate' => '--cfr',
        'peak-limited-frame-control-rate' => '--pfr',
        'select-audio-tracks' => '-a',
        'audio-encoder' => '-E',
        'audio-copy-mask' => '--audio-copy-mask',
        'audio-fallback' => '--audio-fallback',
        'audio-bitrate' => '-B',
        'audio-quality-metric' => '-Q',
        'audio-compression-metric' => '-C',
        'surround-sound-downmixing' => '-6',
        'audio-samplerate' => '-R',
        'dynamic-range-compression' => '-D',
        'amplify-audio-before-encoding' => 'gain',
        'audio-track-names' => '-A',
        'picture-width' => '-w',
        'picture-height' => '-l',
        'cropping-values' => '--crop',
        'loose-crop' => '--loose-crop',
        'max-height' => '-Y',
        'max-width' => '-X',
        'strict-anamorphic-pixel-aspect-ratio' => '--strict-anamorphic',
        'loose-anamorphic-pixel-aspect-ratio' => '--loose-anamorphic',
        'custom-anamorphic-pixel-aspect-ratio' => '--custom-anamorphic',
        'width-to-scale-pixels-to-at-playback-for-custom-anamorphic' => '--display-width',
        'keep-display-aspect-ratio-for-custom-anamorphic' => '--keep-display-aspect',
        'pixel-aspect-for-custom-anamorphic' => '--pixel-aspect',
        'use-wider-itu-pixel-aspect-for-loose-and-custom-anamorphic' => '--itu-par',
        'number-scaled-pixel-dimensions-divide-cleanly-by' => '--modulus',
        'color-space-signaled-by-output' => '-M',
        'deinterlace-video' => '-d',
        'deinterlace-when-detects-combing' => '-5',
        'detelecine-video-with-pullup-filter' => '-9',
        'denoise-video-with-hqdn3d-filter' => '-8',
        'deblock-video-with-pp7-filter' => '-7',
        'flip-image-axes' => '--rotate',
        'grayscale-encoding' => '-g',
        'subtitle-tracks' => '-s',
        'subtitle-forced' => '-F',
        'subtitle-burn' => '--subtitle-burn',
        'subtitle-default' => '--subtitle-default',
        'native-language' => '--native-language',
        'native-dub' => '--native-dub',
        'subrip-srt-filenames' => '--srt-file',
        'codeset-to-encode-srt-files' => '--srt-codeset',
        'offset-for-srt-files' => '--srt-offset',
        'language-for-srt-files' => '--srt-lang',
        'flag-srt-as-default-subtitle' => '--srt-default',
		
		//All presets below are unique to ffmpeg
    );

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
     * Check that given keys have actual handbrake equivalents
     *
     * {@inheritdoc}
     */
    public function validatePreset(Preset $preset)
    {
        /*foreach ($preset->getOptions() as $key => $value) {
            if (!isset($this->ffmpeg_conversion[$key])) {
                throw new \InvalidArgumentException(sprintf("Unknown input argument {%s} in adapter {%s}.", $key, $this->getKey()));
            }
        }*/
		return true; //For now, we have no coversion array for the keys
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
