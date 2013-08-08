<?php

namespace AC\Transcoding\Tests\Mock;
use AC\Transcoding\Event\TranscodeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DummySubscriber implements EventSubscriberInterface
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

    public static function getSubscribedEvents()
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
