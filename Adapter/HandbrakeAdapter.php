<?php

namespace AC\Component\Transcoding\Adapter;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\Adapter;
use AC\Component\Transcoding\File;
use AC\Component\Transcoding\FileHandlerDefinition;

/**
 * A handbrake adapter 
 * Written by Andrew Freix
 */
class HandbrakeAdapter extends Adapter {
	protected $key = "handbrake";
	protected $name = "Handbrake";
	protected $description = "Uses defined handbrake presets to run Handbrake video conversion commands";
	
	private $handbrake_path;
	private $handbrake_conversion;
	
	function __construct($handbrake_path) {
		$this->handbrake_conversion = $this->getConversionArray();
		$this->handbrake_path = $handbrake_path;
	}
	/**
	 * Make sure 
	 */
	/*public function validatePreset(Preset $preset) {
		return true;
	}*/
	
	/*protected function buildInputDefinition() {
		return new FileHandlerDefinition(array(
			'rejectedMimeEncodings' => array('binary'),
		));
	}*/
	
	/**
	 * Run transcode, transforming contents of a text-based file.
	 */
	public function transcodeFile(File $inFile, Preset $preset, $outFilePath) {
	
		$commandString = $this->handbrake_path;
		$commandString .= " -i ".$inFile->getPathname()." -o ".$outFilePath;
		
		foreach ($preset->getOptions() as $key => $value) {
		    if (isset($this->handbrake_conversion[$key])) {
                $commandString .= " ".$this->handbrake_conversion[$key]." ".$value;    
			} else {
				echo($key."<br>");
			    die("THIS SHOULDN'T HAPPEN!");
			}
		}

		//use the Process component to build a process instance with the command string
		$process = new \Symfony\Component\Process\Process($commandString);

		//if this could be a long-running process, be sure to increase the timeout limit accordingly
		$process->setTimeout(3600);
		
		$error_output = "";
		//$i = $inFile->getRelativePath();
		$i = substr($inFile->getPathname(), 6);
		$o = substr($outFilePath, 7);
		$filename = "errors\\".str_replace('.','_',$i)."_to_".str_replace('.','_', $o).".txt";
		if (file_exists($filename)) {
		    file_put_contents($filename, "");
		}
		$result = $process->run(function ($type, $buffer) use($error_output, $filename) {
			if ('err' === $type) {
				//echo 'ERR > '.$buffer.'<br>';
				$error_output = 'ERR > '.$buffer."\n";
			} else {
			    //echo 'OUT > '.$buffer.'<br>';
				$error_output = 'OUT > '.$buffer."\n";
			}
			file_put_contents($filename, $error_output, FILE_APPEND);
		});

		//check for error status return
		if(!$process->isSuccessful()) {
			throw new \RuntimeException($process->getExitCodeText());
		}

		return new File($outFilePath);
	}
	public function getConversionArray(){
		$arr = array(
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
			'flag-srt-as-default-subtitle' => '--srt-default');
		return $arr;
	}
}