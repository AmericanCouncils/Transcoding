<?php

namespace AC\Component\Transcoding\Event;

use AC\Component\Transcoding\Adapter;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event allows adapters to dispatch messages via the Transcoder.
 *
 * @package Transcoding
 * @author Evan Villemez
 */
class MessageEvent extends Event
{
    
    /**
     * A debug level message
     */
    const DEBUG = 'DEBUG';

    /**
     * An info level message
     */
    const INFO = 'INFO';
    
    /**
     * A warning level message
     */
    const WARN = 'WARN';

    /**
     * An error level message
     */
    const ERROR = 'ERROR';
    
    protected $msg;
    protected $level;
    protected $adapter;

    /**
     * Constructor for a message event.
     *
     * @param string $msg 
     * @param string $level 
     * @param Adapter $adapter 
     * @author Evan Villemez
     */
    public function __construct($msg, $level = self::INFO, Adapter $adapter)
    {
        $this->msg = $msg;
        $this->level = $level;
        $this->adapter = $adapter;
    }
    
    /**
     * Get the message text sent by the adapter
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->msg;
    }

    /**
     * Get the level of the message sent by the adapter
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }
    
    /**
     * Get the adapter instance that sent the message
     *
     * @return Adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
}
