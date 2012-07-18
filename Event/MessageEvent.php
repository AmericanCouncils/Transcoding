<?php

namespace AC\Component\Transcoding\Event;

use Symfony\Component\EventDispatcher\Event;

class MessageEvent extends Event
{
    protected $msg;
    protected $levels = array(

    );

    public function __construct($msg, $level = 0)
    {
        $this->msg = $msg;
    }

    public function getMessage()
    {
        return $this->msg;
    }

    public function getLevel()
    {

    }
}
