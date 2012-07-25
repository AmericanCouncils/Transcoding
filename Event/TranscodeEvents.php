<?php

namespace AC\Component\Transcoding\Event;

/**
 * Documents the events fired by the Transcoder
 *
 * @package Transcoding
 * @author Evan Villemez
 */
class TranscodeEvents
{
    /**
     * Fired when an adapter sends a message. An adapter can send 4 types of message (Debug, Info, Warn, Error)
     *
     * Listeners receive instance of MessageEvent
     */
    const MESSAGE = "adapter.message";

    /**
     * Fired before single transcode process begins
     *
     * Listeners receive instance of TranscodeEvent
     */
    const BEFORE = "transcode.before";

    /**
     * Fired after single transcode process ends
     *
     * Listeners receive instance of TranscodeEvent
     */
    const AFTER = "transcode.after";

    /**
     * Fired when an exception is thrown during a transcode process
     *
     * Listeners receive instance of TranscodeEvent
     */
    const ERROR = "transcode.error";

    /**
     * Fired before a job process begins
     *
     * Listeners receive instance of TranscodeEvent
     */
    const JOB_BEFORE = "job.before";

    /**
     * Fired after a job process completes
     *
     * Listeners receive instance of TranscodeEvent
     */
    const JOB_AFTER = "job.after";

    /**
     * Fired when an error is encountered during a job
     *
     * Listeners receive instance of TranscodeEvent
     */
    const JOB_ERROR = "job.error";

    /**
     * Fired when a new file has been created by the Transcoder
     *
     * Listeners receive instance of FileEvent
     */
    const FILE_CREATED = 'file.created';

    /**
     * Fired when a file has been modified by the transcoder, this is usually only when it changes permissions on a file for some reason
     *
     * Listeners receive instance of FileEvent
     */
    const FILE_MODIFIED = 'file.modified';

    /**
     * Fired when a file has been removed by the transcoder, this could happen because of a failed process, or because there was a file
     * with the given name that already exists, and is to be replaced with a new file
     *
     * Listeners receive instance of FileEvent
     */
    const FILE_REMOVED = 'file.removed';

    /**
     * Fired when the transcoder creates a new directory for containing files
     *
     * Listeners receive instance of FileEvent
     */
    const DIR_CREATED = 'dir.created';

    /**
     * Fired when a directory is modified, generally would only happen if the Transcoder changes file permissions on it directly
     *
     * Listeners receive instance of FileEvent
     */
    const DIR_MODIFIED = 'dir.modified';

    /**
     * Fired when a directory is removed
     *
     * Listeners receive instance of FileEvent
     */
    const DIR_REMOVED = 'dir.removed';
}
