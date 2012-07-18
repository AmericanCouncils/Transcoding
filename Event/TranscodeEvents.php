<?php

namespace AC\Component\Transcoding\Event;

/**
 * Documents the events fired by the Transcoder
 *
 * @package Transcode Component
 * @author Evan Villemez
 */
class TranscodeEvents
{
    const BEFORE = "transcode.before";

    const AFTER = "transcode.after";

    const ERROR = "transcode.error";

    const JOB_BEFORE = "job.before";

    const JOB_AFTER = "job.after";

    const JOB_ERROR = "job.error";

    const FILE_CREATED = 'file.created';

    const FILE_MODIFIED = 'file.modified';

    const FILE_REMOVED = 'file.removed';

    const DIR_CREATED = 'dir.created';

    const DIR_MODIFIED = 'dir.modified';

    const DIR_REMOVED = 'dir.created';
}
