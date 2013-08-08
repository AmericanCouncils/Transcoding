<?php

namespace AC\Transcoding\Tests;
use \AC\Transcoding\FileHandlerDefinition;
use \AC\Transcoding\File;

class FileHandlerDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate()
    {
        $d = new FileHandlerDefinition;
        $this->assertNotNull($d);
        $this->assertTrue($d instanceof FileHandlerDefinition);
    }

    public function testGetDefaults()
    {
        $d = new FileHandlerDefinition;
        $this->assertFalse($d->getAllowedExtensions());
        $this->assertFalse($d->getRejectedExtensions());
        $this->assertTrue($d->getAllowDirectory());

        $this->assertFalse($d->getRequiredType());
        $this->assertTrue($d->acceptsExtension('mp4'));
        $this->assertTrue($d->acceptsMime('text/plain; charset=us-ascii'));
        $this->assertTrue($d->acceptsMimeType('text/plain'));
        $this->assertTrue($d->acceptsMimeEncoding('us-ascii'));
    }

    public function testConstructorSetOptions()
    {
        $d = new FileHandlerDefinition(array(
            'allowedExtensions' => array('html','php','txt'),
            'allowedMimeEncodings' => array('us-ascii'),
            'rejectedMimeTypes' => array('text/x-c++'),
            'inheritInputExtension' => true
        ));

        $this->assertTrue($d->acceptsExtension('html'));
        $this->assertFalse($d->acceptsExtension('pdf'));
        $this->assertTrue($d->acceptsMimeEncoding('us-ascii'));
        $this->assertFalse($d->acceptsMimeEncoding('utf-8'));
        $this->assertTrue($d->acceptsMimeType('text/plain'));
        $this->assertFalse($d->acceptsMimeType('text/x-c++'));
    }

    /**
     * Accept/Reject property tests below
     */

    public function testAcceptsExtension()
    {
        $d = new FileHandlerDefinition;

        $this->assertTrue($d->acceptsExtension('mp3'));
        $this->assertTrue($d->acceptsExtension('mov'));
        $d->setAllowedExtensions(array('mp3','mp4'));
        $this->assertTrue($d->acceptsExtension('mp3'));
        $this->assertFalse($d->acceptsExtension('mov'));

        $d = new FileHandlerDefinition;

        $this->assertTrue($d->acceptsExtension('mp3'));
        $this->assertTrue($d->acceptsExtension('mov'));
        $d->setRejectedExtensions(array('mp3','mp4'));
        $this->assertFalse($d->acceptsExtension('mp3'));
        $this->assertTrue($d->acceptsExtension('mov'));

        $d = new FileHandlerDefinition;
        $d->setRequiredExtension('mp4');
        $this->assertFalse($d->acceptsExtension('mp3'));
        $this->assertTrue($d->acceptsExtension('mp4'));
    }

    public function testAcceptsMimes()
    {
        $d = new FileHandlerDefinition;

        $this->assertTrue($d->acceptsMime('text/plain; charset=us-ascii'));
        $this->assertTrue($d->acceptsMime('text/x-c++; charset=us-ascii'));
        $d->setAllowedMimes(array('text/plain; charset=us-ascii'));
        $this->assertTrue($d->acceptsMime('text/plain; charset=us-ascii'));
        $this->assertFalse($d->acceptsMime('text/x-c++; charset=us-ascii'));

        $d = new FileHandlerDefinition;

        $this->assertTrue($d->acceptsMime('text/plain; charset=us-ascii'));
        $this->assertTrue($d->acceptsMime('text/x-c++; charset=us-ascii'));
        $d->setRejectedMimes(array('text/plain; charset=us-ascii'));
        $this->assertFalse($d->acceptsMime('text/plain; charset=us-ascii'));
        $this->assertTrue($d->acceptsMime('text/x-c++; charset=us-ascii'));
    }

    public function testAcceptsMimeTypes()
    {
        $d = new FileHandlerDefinition;

        $this->assertTrue($d->acceptsMimeType('text/plain'));
        $this->assertTrue($d->acceptsMimeType('text/x-c++'));
        $d->setAllowedMimeTypes(array('text/plain'));
        $this->assertTrue($d->acceptsMimeType('text/plain'));
        $this->assertFalse($d->acceptsMimeType('text/x-c++'));

        $d = new FileHandlerDefinition;

        $this->assertTrue($d->acceptsMimeType('text/plain'));
        $this->assertTrue($d->acceptsMimeType('text/x-c++'));
        $d->setRejectedMimeTypes(array('text/plain'));
        $this->assertFalse($d->acceptsMimeType('text/plain'));
        $this->assertTrue($d->acceptsMimeType('text/x-c++'));
    }

    public function testAcceptsMimeEncodings()
    {
        $d = new FileHandlerDefinition;

        $this->assertTrue($d->acceptsMimeEncoding('us-ascii'));
        $this->assertTrue($d->acceptsMimeEncoding('binary'));
        $d->setAllowedMimeEncodings(array('us-ascii'));
        $this->assertTrue($d->acceptsMimeEncoding('us-ascii'));
        $this->assertFalse($d->acceptsMimeEncoding('binary'));

        $d = new FileHandlerDefinition;

        $this->assertTrue($d->acceptsMimeEncoding('us-ascii'));
        $this->assertTrue($d->acceptsMimeEncoding('binary'));
        $d->setRejectedMimeEncodings(array('us-ascii'));
        $this->assertFalse($d->acceptsMimeEncoding('us-ascii'));
        $this->assertTrue($d->acceptsMimeEncoding('binary'));
    }

    /**
     * Accept/Reject file tests below
     */

     public function testAcceptDirectory1()
     {
         $f = new File(__DIR__);
         $d = new FileHandlerDefinition;
         $this->assertTrue($d->acceptsFile($f));
     }

     public function testAcceptDirectory2()
     {
         $f = new File(__DIR__);
         $d = new FileHandlerDefinition;
         $d->setRequiredType('directory');
         $this->assertTrue($d->acceptsFile($f));
     }

     public function testAcceptDirectory3()
     {
         $f = new File(__DIR__);
         $d = new FileHandlerDefinition;
         $d->setRequiredType('file');
         $this->assertFalse($d->acceptsFile($f));
     }

    public function testAcceptsFile1()
    {
        $f = new File(__FILE__);
        $d = new FileHandlerDefinition;
        $this->assertTrue($d->acceptsFile($f));
    }

    public function testAcceptsFile2()
    {
        $f = new File(__FILE__);
        $d = new FileHandlerDefinition;
        $d->setRequiredType('directory');
        $this->assertFalse($d->acceptsFile($f));
    }

    public function testAcceptsFile3()
    {
        $f = new File(__FILE__);
        $d = new FileHandlerDefinition;
        $d->setAllowedMimeTypes(array('text/x-c++'));
        $this->assertTrue($d->acceptsFile($f));
    }

    public function testAcceptsFile4()
    {
        $f = new File(__FILE__);
        $d = new FileHandlerDefinition;
        $d->setRejectedMimeTypes(array('text/x-c++'));
        $this->assertFalse($d->acceptsFile($f));
    }
}
