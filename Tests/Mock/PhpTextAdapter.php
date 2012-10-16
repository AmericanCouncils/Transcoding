<?php

namespace AC\Component\Transcoding\Tests\Mock;
use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\Adapter;
use AC\Component\Transcoding\File;
use AC\Component\Transcoding\FileHandlerDefinition;

/**
 * A very simple adapter to illustrate how writing an adapter should work.
 */
class PhpTextAdapter extends Adapter
{
    protected $key = "php_text";
    protected $name = "PHP Textifier";
    protected $description = "Uses common php functions to manipulate the contents of a file.";

    /**
     * Array of allowed methods to call on any input.
     *
     * @var string
     */
    protected $allowedFunctions = array(
        'strtolower',
        'strtoupper',
        'ucwords'
    );

    /**
     * Make sure 'func' parameter has been set and is an allowed value.
     */
    public function validatePreset(Preset $preset)
    {
        if (!$preset->get('func', false)) {
            throw new \InvalidArgumentException('"func" is a required preset option.');
        }

        if (!in_array(strtolower($preset['func']), $this->allowedFunctions)) {
            throw new \InvalidArgumentException(sprintf("Specified function can only be one of the following: %s", implode(", ", $this->allowedFunctions)));
        }
    }

    /**
     * Don't let this adapter try encoding binary files
     *
     * {@inheritdoc}
     */
    protected function buildInputDefinition()
    {
        return new FileHandlerDefinition(array(
            'rejectedMimeEncodings' => array('binary'),
        ));
    }

    /**
     * Run transcode, transforming contents of a text-based file.
     */
    public function transcodeFile(File $inFile, Preset $preset, $outFilePath)
    {
        $function = $preset->get('func', false);

        $this->debug(sprintf("Using PHP function [%s] to modify file.", $function));

        if (!file_put_contents($outFilePath, $function(file_get_contents($inFile->getRealPath())))) {
            throw new \RuntimeException(sprintf("Could not put contents into file %s", $outFilePath));
        }

        $this->debug("It worked!");

        return new File($outFilePath);
    }

}
