<?php

namespace AC\Transcoding\Tests\Mock;

use AC\Transcoding\Adapter;

class InvalidDummyAdapter extends Adapter
{
    protected $key = "bad_test_adapter";

    protected function verifyEnvironment()
    {
        throw new \Exception("Adapter broken.");
    }

}
