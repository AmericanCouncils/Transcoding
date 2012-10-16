<?php

namespace AC\Component\Transcoding\Tests\Adapter;

use AC\Component\Transcoding\Transcoder;
use AC\Component\Transcoding\File;
use AC\Component\Transcoding\Adapter\FFmpegAdapter;
use AC\Component\Transcoding\Preset\FFmpeg\AudioCompression32kPreset;

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

        $expected = sprintf("'/good/path' '-i' '%s' '-ab' '32k' '%s'", $inPath, $outPath);

        $this->assertSame($expected, $a->buildProcess(new File($inPath), new AudioCompression32kPreset(), $outPath)->getProcess()->getCommandLine());
    }
}
