<?php

namespace AC\Component\Transcoding\Presets;
use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\FileHandlerDefinition;

/**
 * A very simple preset, for a very simple adapter, to illustrate how writing a preset works.  This will be deleted once we have real presets that do useful things.
 */
class TextToLowerCase extends Preset
{
    protected $key = "text_to_lower";
    protected $name = "Text to lower case";
    protected $requiredAdapter = 'php_text';
    protected $description = "Transforms all text in a file into lower case.  This will be deleted once we have real presets that do useful things.";

    /**
     * Restrict input encodings to formats we know PHP won't have a problem with.
     */
    protected function buildInputDefinition()
    {
        return new FileHandlerDefinition(array(
            'allowedMimeEncodings' => array('us-ascii', 'utf-8'),
            'requiredType' => 'file',
        ));
    }

    /**
     * Output should be a file.
     */
    protected function buildOutputDefinition()
    {
        return new FileHandlerDefinition(array(
            'requiredType' => 'file',
        ));
    }

    /**
     * Specify the function PHP will use to transform text in the file.
     */
    public function configure()
    {
        $this->setOptions(array(
            'func' => 'strtolower'
        ));
    }

}
