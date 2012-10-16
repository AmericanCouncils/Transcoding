<?php

namespace AC\Component\Transcoding\Tests;
use AC\Component\Transcoding\Transcoder;
use AC\Component\Transcoding\Tests\Mock\DummySubscriber;

class TranscoderTest extends \PHPUnit_Framework_TestCase
{

    protected function getTranscoder()
    {
        $t = new Transcoder;
        $t->registerAdapter(new \AC\Component\Transcoding\Tests\Mock\PhpTextAdapter);
        $t->registerPreset(new \AC\Component\Transcoding\Tests\Mock\TextToLowerCasePreset);

        return $t;
    }

    public function tearDown()
    {
        @unlink(__DIR__."/test_files/test_file.php");
        @unlink(__DIR__."/test_files/test_file.mp3");
        @unlink(__DIR__."/test_files/transcoded/test_file.php");
        @unlink(__DIR__."/test_files/transcoded/test_file.php");
    }

    public function testInstantiate()
    {
        $t = new Transcoder;
        $this->assertNotNull($t);
        $this->assertInstanceOf('\AC\Component\Transcoding\Transcoder', $t);
    }

    public function testHasRegisterGetAndRemovePreset()
    {
        $t = new Transcoder;
        $this->assertFalse($t->hasPreset('test_preset'));
        $this->assertSame(0, count($t->getPresets()));
        $t->registerPreset(new Mock\DummyPreset);
        $this->assertTrue($t->hasPreset('test_preset'));
        $this->assertSame(1, count($t->getPresets()));
        $p = $t->getPreset('test_preset');
        $this->assertInstanceOf('AC\Component\Transcoding\Tests\Mock\DummyPreset', $p);
        $t->removePreset('test_preset');
        $this->assertFalse($t->hasPreset('test_preset'));
        $this->assertSame(0, count($t->getPresets()));
    }

    public function testHasRegisterGetAndRemoveAdapter()
    {
        $t = new Transcoder;
        $this->assertFalse($t->hasAdapter('test_adapter'));
        $this->assertSame(0, count($t->getAdapters()));
        $t->registerAdapter(new Mock\DummyAdapter);
        $this->assertTrue($t->hasAdapter('test_adapter'));
        $this->assertSame(1, count($t->getAdapters()));
        $p = $t->getAdapter('test_adapter');
        $this->assertInstanceOf('AC\Component\Transcoding\Tests\Mock\DummyAdapter', $p);
        $t->removeAdapter('test_adapter');
        $this->assertFalse($t->hasAdapter('test_adapter'));
        $this->assertSame(0, count($t->getAdapters()));
    }

    public function testGetAndSetFileCreationMode()
    {
        $t = new Transcoder;
        $this->assertSame(0644, $t->getFileCreationMode());
        $t->setFileCreationMode(0777);
        $this->assertSame(0777, $t->getFileCreationMode());
        $t->setFileCreationMode("644");
        $this->assertSame(0644, $t->getFileCreationMode());
    }

    public function testGetAndSetDirectoryCreationMode()
    {
        $t = new Transcoder;
        $this->assertSame(0755, $t->getDirectoryCreationMode());
        $t->setDirectoryCreationMode(0777);
        $this->assertSame(0777, $t->getDirectoryCreationMode());
        $t->setDirectoryCreationMode("644");
        $this->assertSame(0644, $t->getDirectoryCreationMode());
    }

    public function testTranscodeFileWithPreset1()
    {
        $t = $this->getTranscoder();

        $infile = __DIR__."/test_files_2/caps.txt";
        $outfile = __DIR__."/test_files_2/caps_lower.txt";

        $t->transcodeWithPreset($infile, "text_to_lower", $outfile);
        $this->assertTrue(file_exists($outfile));
        $expectedContent = "a test file with caps";
        $this->assertSame($expectedContent, file_get_contents($outfile));

        @unlink($outfile);
    }

    public function testTranscoderEvents1()
    {
        $t = $this->getTranscoder();
        $sub = new DummySubscriber();
        $t->getDispatcher()->addSubscriber($sub);

        $this->assertFalse($sub->onMessage);
        $this->assertFalse($sub->onBefore);
        $this->assertFalse($sub->onAfter);
        $this->assertFalse($sub->onError);

        $infile = __DIR__."/test_files_2/caps.txt";
        $outfile = __DIR__."/test_files_2/caps_lower.txt";

        $t->transcodeWithPreset($infile, "text_to_lower", $outfile);
        $this->assertTrue(file_exists($outfile));
        $expectedContent = "a test file with caps";
        $this->assertSame($expectedContent, file_get_contents($outfile));

        @unlink($outfile);

        $this->assertTrue($sub->onMessage);
        $this->assertTrue($sub->onBefore);
        $this->assertTrue($sub->onAfter);
        $this->assertFalse($sub->onError);

    }

}
