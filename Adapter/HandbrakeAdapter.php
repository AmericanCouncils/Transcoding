<?php

namespace AC\Transcoding\Adapter;

use AC\Transcoding\Preset;
use AC\Transcoding\Adapter;
use AC\Transcoding\File;
use AC\Transcoding\FileHandlerDefinition;
use Symfony\Component\Process\Process;

/**
 * A handbrake adapter, see https://trac.handbrake.fr/wiki/CLIGuide for more information about Handbrake
 *
 * @author Andrew Freix
 */
class HandbrakeAdapter extends AbstractCliAdapter
{
    protected $key = "handbrake";
    protected $name = "Handbrake";
    protected $description = "Uses Handbrake presets to convert video into .mp4 format.";

    /**
     * @var string Path to HandBrakeCLI executable, received in constructor
     */
    private $handbrake_path;

    /**
     * @var array Mappings of human-readable options to handbrake CLI equivalents
     */
    protected $handbrake_conversion = array(
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
        'flag-srt-as-default-subtitle' => '--srt-default'
    );

    /**
     * Handbrake needs a path to the HandBrakeCLI executable
     *
     * @param string $handbrake_path
     * @param int    $timeout        Time in seconds for process timeout, null means no timeout
     */
    public function __construct($handbrake_path = 'HandBrakeCLI', $timeout = null)
    {
        parent::__construct(array(
            'timeout' => $timeout
        ));

        $this->handbrake_path = $handbrake_path;
    }

    /**
     * {@inheritdoc}
     */
    public function verifyEnvironment()
    {
        if (!file_exists($this->handbrake_path)) {
            throw new \RuntimeException(sprintf("Could not find Handbrake executable, given path {%s}", $this->handbrake_path));
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
        foreach ($preset->getOptions() as $key => $value) {
            if (!isset($this->handbrake_conversion[$key])) {
                throw new \InvalidArgumentException(sprintf("Unknown input argument {%s} in adapter {%s}.", $key, $this->getKey()));
            }
        }
    }

    /**
     * Output should also be a binary file
     *
     * {@inheritdoc}
     */
    protected function buildOutputDefinition()
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
        //set basic command with in/out files
        $builder = $this->getProcessBuilder(array(
            $this->handbrake_path,
            '-i',
            $inFile->getPathname(),
            '-o',
            $outFilePath
        ));

        //add options from preset
        foreach ($preset as $key => $val) {
            if (!empty($key)) {
                $builder->add($this->handbrake_conversion[$key]);
            }
            if (!empty($val)) {
                $builder->add($val);
            }
        }

        return $builder;
    }

}
