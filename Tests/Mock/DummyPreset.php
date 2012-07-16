<?php

namespace AC\Component\Transcoding\Tests\Mock;

class DummyPreset extends \AC\Component\Transcoding\Preset
{
    protected $key = 'test_preset';
    protected $requiredAdapter = "test_adapter";

    public function getOutputExtension()
    {
        return 'php';
    }
}
