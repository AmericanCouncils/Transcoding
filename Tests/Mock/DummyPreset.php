<?php

namespace AC\Transcoding\Tests\Mock;

class DummyPreset extends \AC\Transcoding\Preset
{
    protected $key = 'test_preset';
    protected $requiredAdapter = "test_adapter";

    public function getOutputExtension()
    {
        return 'php';
    }
}
