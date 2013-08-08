<?php

namespace AC\Transcoding\Tests\Adapter;

use AC\Transcoding\Transcoder;
use AC\Transcoding\File;
use AC\Transcoding\Adapter\HandbrakeAdapter;
use AC\Transcoding\Preset\Handbrake\iPhoneLegacyPreset;

class HandbrakeTest extends \PHPUnit_Framework_TestCase
{

    public function testRegisterHandbrake()
    {
        $t = new Transcoder;
        $t->registerAdapter(new HandbrakeAdapter("/path/to/handbrake"));
        $this->assertTrue($t->getAdapter('handbrake') instanceof HandbrakeAdapter);
    }

    public function testVerifyEnvironment1()
    {
        $t = new Transcoder;
        $t->registerAdapter(new HandbrakeAdapter("/path/to/handbrake/shouldnt/exist"));

        $this->assertFalse($t->getAdapter('handbrake')->verify());
    }

    public function testIPhoneLegacyPresetCommandString()
    {
        $a = new HandbrakeAdapter('/good/path');

        $inPath = __DIR__."/../test_files/foo.txt";
        $outPath = "/out.mp4";

        $expected = sprintf("'/good/path' '-i' '%s' '-o' '%s' '-e' 'x264' '-b' '960' '-a' '1' '-E' 'faac' '-B' '128' '-6' 'dp12' '-R' 'Auto' '-D' '0.0' '-f' 'mp4' '-I' '-X' '480' '-m' '-x' 'level=30:cabac=0:ref=1:analyse=all:me=umh:no-fast-pskip=1:psy-rd=0,0:bframes=0:weightp=0:subme=6:8x8dct=0:trellis=0'", $inPath, $outPath);

        $this->assertSame($expected, $a->buildProcess(new File($inPath), new iPhoneLegacyPreset(), $outPath)->getProcess()->getCommandLine());
    }
}
