<?php

namespace AC\Component\Transcoding\Tests\Adapter;

use AC\Component\Transcoding\Transcoder;
use AC\Component\Transcoding\Adapter\HandbrakeAdapter;
use AC\Component\Transcoding\Preset\Handbrake\iPhoneLegacyPreset;

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

    public function testTranscode()
    {
        $t = new Transcoder;
        $t->registerAdapter(new HandbrakeAdapter('/good/path'));
        $t->registerPreset(new iPhoneLegacyPreset);

    }
}
