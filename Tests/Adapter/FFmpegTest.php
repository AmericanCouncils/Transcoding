<?php

namespace AC\Transcoding\Tests\Adapter;

use AC\Transcoding\Transcoder;
use AC\Transcoding\File;
use AC\Transcoding\Adapter\FFmpegAdapter;
use AC\Transcoding\Preset\FFmpeg\SoundFromVideoPreset;

class FFmpegTest extends \PHPUnit_Framework_TestCase
{

    public function testRegisterHandbrake()
    {
        $t = new Transcoder;
        $t->registerAdapter(new FFmpegAdapter("/path/to/ffmpeg"));
        $this->assertTrue($t->getAdapter('ffmpeg') instanceof FFmpegAdapter);
    }

    public function testVerifyEnvironment1()
    {
        $t = new Transcoder;
        $t->registerAdapter(new FFmpegAdapter("/path/to/ffmpeg/shouldnt/exist"));

        $this->assertFalse($t->getAdapter('ffmpeg')->verify());
    }

    public function testAudioCompression32kPresetCommandString()
    {
        $a = new FFmpegAdapter('/good/path');

        $inPath = __DIR__."/../test_files/foo.txt";
        $outPath = "/out.mp4";

        $expected = sprintf("'/good/path' '-i' '%s' '-vn' '' '-ar' '44100' '-ac' '2' '-ab' '192' '-f' 'mp3' '%s'", $inPath, $outPath);

        $this->assertSame($expected, $a->buildProcess(new File($inPath), new SoundFromVideoPreset(), $outPath)->getProcess()->getCommandLine());
    }
}
