<?php

namespace AC\Component\Transcoding\Tests;
use \AC\Component\Transcoding\Adapter;
use \AC\Component\Transcoding\File;
use \AC\Component\Transcoding\Preset;
use \AC\Component\Transcoding\FileHandlerDefinition;

class AdapterTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        @unlink(__DIR__."/test_files/test_file.php");
    }

    public function testInstantiate()
    {
        $a = new Mock\DummyAdapter;
        $this->assertNotNull($a);
        $this->assertTrue($a instanceof Adapter);
    }

    public function testVerify()
    {
        $a = new Mock\DummyAdapter;
        $this->assertTrue($a->verify());
        $this->assertFalse($a->getVerificationError());

        $b = new Mock\InvalidDummyAdapter;
        $this->assertFalse($b->verify());
        $this->assertSame("Adapter broken.", $b->getVerificationError());
    }

    public function testGetKeyNameAndDescription()
    {
        $a = new Mock\DummyAdapter;
        $this->assertSame("test_adapter", $a->getKey());
        $this->assertSame("Test Adapter", $a->getName());
        $this->assertSame("Test description.", $a->getDescription());

        $b = new Mock\InvalidDummyAdapter;
        $this->assertSame("bad_test_adapter", $b->getKey());
        $this->assertSame("bad_test_adapter", $b->getName());
        $this->assertFalse($b->getDescription());
    }

    public function testGetInputDefinition()
    {
        $a = new Mock\DummyAdapter;
        $this->assertTrue($a->getInputDefinition() instanceof FileHandlerDefinition);
    }

    public function testGetOutputDefinition()
    {
        $a = new Mock\DummyAdapter;
        $this->assertTrue($a->getOutputDefinition() instanceof FileHandlerDefinition);
    }

    public function testAcceptsInputFile()
    {
        $a = new Mock\DummyAdapter;
        $this->assertTrue($a->acceptsInputFile(new File(__FILE__)));
    }

    public function testAcceptsOutputFile()
    {
        $a = new Mock\DummyAdapter;
        $this->assertTrue($a->acceptsOutputFile(new File(__FILE__)));
    }

    public function testValidatePreset()
    {
        $a = new Mock\DummyAdapter;
        $this->assertTrue($a->validatePreset(new Preset('foo', 'test_adapter', array())));
    }

    public function testTranscodeFile()
    {
        $expected = new File(__FILE__);
        $a = new Mock\DummyAdapter;
        $outputPath = __DIR__."/test_files/test_file.php";
        $newFile = $a->transcodeFile($expected, new Preset('foo', 'test_adapter', array()), $outputPath);
        $this->assertSame($outputPath, $newFile->getRealPath());
    }
}
