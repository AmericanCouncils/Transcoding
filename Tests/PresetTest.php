<?php

namespace AC\Transcoding\Tests;
use \AC\Transcoding\File;
use \AC\Transcoding\Preset;
use \AC\Transcoding\FileHandlerDefinition;

class PresetTest extends \PHPUnit_Framework_TestCase
{
    public function testInstatiateDynamic1()
    {
        $p = new Preset('name', 'adapter');
        $this->assertNotNull($p);
        $this->assertTrue($p instanceof Preset);
    }

    public function testInstatiateDynamic2()
    {
        $this->setExpectedException('AC\Transcoding\Exception\InvalidPresetException');
        $p = new Preset();
    }

    public function testInstatiateDynamic3()
    {
        $this->setExpectedException('AC\Transcoding\Exception\InvalidPresetException');
        $p = new Preset('foo');
    }

    public function testInstantiateExtended1()
    {
        $this->setExpectedException('AC\Transcoding\Exception\InvalidPresetException');
        $p = new Mock\InvalidDummyPreset;
    }

    public function testInstantiateExtended2()
    {
        $p = new Mock\DummyPreset;
        $this->assertNotNull($p);
        $this->assertTrue($p instanceof Preset);
    }

    public function testSetGetHasRemoveOption()
    {
        $p = new Preset('name', 'adapter');
        $this->assertFalse($p->has('foo'));
        $this->assertSame('bar', $p->get('foo','bar'));
        $p->set('foo', 'baz');
        $this->assertTrue($p->has('foo'));
        $this->assertSame('baz', $p->get('foo', 'bar'));
        $p->remove('foo');
        $this->assertFalse($p->has('foo'));
        $this->assertSame('bar', $p->get('foo','bar'));
    }

    public function testSetGetHasRemoveOptionAsArray()
    {
        $p = new Preset('foo', 'bar');
        $this->assertFalse(isset($p['foo']));
        $this->assertNull($p['foo']);
        $p['foo'] = 'bar';
        $this->assertTrue(isset($p['foo']));
        $this->assertSame('bar', $p['foo']);
        unset($p['foo']);
        $this->assertFalse(isset($p['foo']));
        $this->assertNull($p['foo']);
    }

    public function testSetGetHasRemoveWhenLocked()
    {
        $p = new Preset('name', 'adapter');
        $p->set('foo', 'bar')->lock();
        $this->assertTrue($p->has('foo'));
        $this->assertSame('bar', $p->get('foo', 'baz'));
        $p->remove('foo');
        $this->assertTrue($p->has('foo'));
        $this->assertSame('bar', $p->get('foo', 'baz'));
        $p->set('foo', 'bazzz');
        $this->assertTrue($p->has('foo'));
        $this->assertSame('bar', $p->get('foo', 'baz'));
    }

    public function testSetOptions()
    {
        $p = new Preset('name', 'adapter');
        $p->setOptions(array(
            'foo' => 'bar',
            'baz' => false,
        ));

        $this->assertSame('bar', $p['foo']);
        $this->assertFalse($p->get('baz', true));
    }

    public function testGetNameAdapterAndDescription()
    {
        $p = new Preset('name','adapter');
        $this->assertSame('name', $p->getKey());
        $this->assertSame('adapter', $p->getRequiredAdapter());
        $this->assertSame("No description provided.", $p->getDescription());
    }

    public function testGetInputDefinition()
    {
        $p = new Preset('name', 'adapter');
        $d = $p->getInputDefinition();
        $this->assertNotNull($d);
        $this->assertTrue($d instanceof FileHandlerDefinition);
    }

    public function testGetOutputDefinition()
    {
        $p = new Preset('name', 'adapter');
        $d = $p->getOutputDefinition();
        $this->assertNotNull($d);
        $this->assertTrue($d instanceof FileHandlerDefinition);
    }

    public function testAcceptsInputFile1()
    {
        $p = new Mock\DummyPreset;
        $this->assertTrue($p->acceptsInputFile(new File(__FILE__)));
    }

    public function testAcceptsInputFile2()
    {
        $p = new Mock\DummyPreset;
        $this->assertTrue($p->acceptsInputFile(new File(__DIR__)));
    }

    public function testAcceptsOutputFile1()
    {
        $p = new Mock\DummyPreset;
        $this->assertTrue($p->acceptsOutputFile(new File(__FILE__)));
    }

    public function testAcceptsOutputFile2()
    {
        $p = new Mock\DummyPreset;
        $this->assertTrue($p->acceptsOutputFile(new File(__DIR__)));
    }

    public function testGenerateOutputPathFile1()
    {
        $f = new File(__FILE__);
        $p = new Mock\DummyPreset;
        $expectedPath = substr($f->getRealPath(), 0, -4).".".$p->getKey().".php";
        $this->assertSame($expectedPath, $p->generateOutputPath($f));
    }

    public function testGenerateOutputPathFile1_1()
    {
        $f = new File(__FILE__);
        $p = new Mock\DummyPreset;
        $outputPath = dirname($f->getRealPath());
        $expectedPath = substr($f->getRealPath(), 0, -4).".".$p->getKey().".php";
        $this->assertSame($expectedPath, $p->generateOutputPath($f, $outputPath));
    }

    public function testGenerateOutputPathFile2()
    {
        $f = new File(__FILE__);
        $p = new Mock\DummyPreset;
        $expectedPath = '/tmp/test.php';
        $this->assertSame($expectedPath, $p->generateOutputPath($f, $expectedPath));
    }

    public function testGenerateOutputPathFile3()
    {
        $f = new File(__FILE__);
        $p = new Mock\DummyPreset;
        $outputPath = __DIR__;
        $expectedPath = substr($f->getRealPath(), 0, -4).".".$p->getKey().".php";
        $this->assertSame($expectedPath, $p->generateOutputPath($f, $outputPath));
    }

    public function testGenerateOutputPathFile4()
    {
        $f = new File(__FILE__);
        $p = new Mock\DummyPreset;
        $p->getOutputDefinition()->setRequiredExtension('mp3');
        $outputPath = '/foo/stuff.mp4';
        $this->setExpectedException("AC\Transcoding\Exception\InvalidInputException");
        $p->generateOutputPath($f, $outputPath);
    }

    public function testGenerateOutputPathFile5()
    {
        $f = new File(__FILE__);
        $p = new Mock\DummyPreset;
        $p->getOutputDefinition()->setRequiredExtension('mp3');
        $outputPath = '/foo/';
        $expected = '/foo/'.substr($f->getFilename(), 0, -4).".".$p->getKey().".mp3";
        $this->assertSame($expected, $p->generateOutputPath($f, $outputPath));
    }

    public function testGenerateOutputPathFile6()
    {
        $f = new File(__FILE__);
        $p = new Mock\DummyPreset;
        $p->getOutputDefinition()->setRequiredExtension('mp3');
        $outputPath = '/foo';
        $expected = '/foo/'.substr($f->getFilename(), 0, -4).".".$p->getKey().".mp3";
        $this->assertSame($expected, $p->generateOutputPath($f, $outputPath));
    }

    public function testGenerateOutputPathFile7()
    {
        $f = new File(__FILE__);
        $p = new Mock\DummyPreset;
        $p->getOutputDefinition()->setRequiredExtension('mp3');
        $outputPath = '/foo/../';
        $expected = '/foo/../'.substr($f->getFilename(), 0, -4).".".$p->getKey().".mp3";
        $this->assertSame($expected, $p->generateOutputPath($f, $outputPath));
    }

    public function testGenerateOutputPathFile8()
    {
        $f = new File(__FILE__);
        $p = new Mock\DummyPreset2;
        $outputPath = __DIR__;
        $this->setExpectedException("AC\Transcoding\Exception\InvalidPresetException");
        $p->generateOutputPath($f, $outputPath);
    }

    public function testGenerateOutputPathDirectory1()
    {
        $f = new File(__FILE__);
        $p = new Mock\DummyPreset;
        $p->getOutputDefinition()->setRequiredType('directory');
        $expected = dirname($f->getRealPath())."/".$p->getKey();
        $this->assertSame($expected, $p->generateOutputPath($f));
    }

    public function testGenerateOutputPathDirectory2()
    {
        $f = new File(__FILE__);
        $p = new Mock\DummyPreset;
        $p->getOutputDefinition()->setRequiredType('directory');
        $outputPath = '/foo/somedir';
        $expected = $outputPath;
        $this->assertSame($expected, $p->generateOutputPath($f, $outputPath));
    }

    public function testGenerateOutputPathDirectory3()
    {
        $f = new File(__FILE__);
        $p = new Mock\DummyPreset;
        $p->getOutputDefinition()->setRequiredType('directory');

        $outputPath = '/foo.mp3';
        $this->setExpectedException("AC\Transcoding\Exception\InvalidInputException");
        $p->generateOutputPath($f, $outputPath);
    }

    public function testGenerateOutputPathDirectory4()
    {
        $f = new File(__FILE__);
        $p = new Mock\DummyPreset;
        $p->getOutputDefinition()->setRequiredType('directory');

        $outputPath = '../../stuff';
        $expected = $outputPath;
        $this->assertSame($expected, $p->generateOutputPath($f, $outputPath));
    }
}
