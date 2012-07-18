<?php

namespace AC\Component\Transcoding\Tests\Mock;
use AC\Component\Transcoding\File;
use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\Adapter;
use AC\Component\Transcoding\Transcoder;
use AC\Component\Transcoding\Event\FileEvent;
use AC\Component\Transcoding\Event\MessageEvent;
use AC\Component\Transcoding\Event\TranscodeEvent;
use AC\Component\Transcoding\Event\TranscodeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TestSubscriber implements EventSubscriberInterface
{   
    public function __construct()
    {
        foreach (self::getSubscribedEvents() as $eventName => $methodName) {
            $this->$methodName = false;
        }
    }
    
    public function __call($methodName, $args)
    {
        $this->$methodName = true;
    }
    
    static public function getSubscribedEvents()
    {
        return array(
            TranscodeEvents::MESSAGE => 'onMessage',
            TranscodeEvents::BEFORE => 'onBefore',
            TranscodeEvents::AFTER => 'onAfter',
            TranscodeEvents::ERROR => 'onError',
            TranscodeEvents::FILE_CREATED => 'onFileCreated',
            TranscodeEvents::FILE_REMOVED => 'onFileRemoved',
            TranscodeEvents::FILE_MODIFIED => 'onFileModified',
            TranscodeEvents::DIR_CREATED => 'onDirCreated',
            TranscodeEvents::DIR_REMOVED => 'onDirRemoved',
            TranscodeEvents::DIR_MODIFIED => 'onDirModified',
        );
    }
}
