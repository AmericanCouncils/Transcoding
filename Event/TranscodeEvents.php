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
    const RESOLVE_OUTPUT_PATH = "transcoder.resolve_output_path";

    const BEFORE = "transcode.before";

    const AFTER = "transcode.after";

    const ERROR = "transcode.error";

    const JOB_BEFORE = "transcode.job.before";

    const JOB_AFTER = "transcode.job.after";

    const JOB_ERROR = "transcode.job.error";

    //TODO: consider these other events
    /*
    const FILE_CREATED = 'transcoder.file.created';
    const FILE_MODIFIED = 'transcoder.file.modified';
    const FILE_REMOVED = 'transcoder.file.removed';
    const DIR_CREATED = 'transcoder.dir.created';
    const DIR_MODIFIED = 'transcoder.dir.modified';
    const DIR_REMOVED = 'transcoder.dir.created';
    */
}
