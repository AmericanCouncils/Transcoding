<?php

namespace AC\Transcoding\Preset\Handbrake;

use AC\Transcoding\Preset;
use AC\Transcoding\FileHandlerDefinition;
use AC\Transcoding\MimeMap;

abstract class BasePreset extends Preset
{
    protected $requiredAdapter = 'handbrake';

    protected function buildInputDefinition()
    {
        $allowedMimeTypes = array();
        $extensions_to_check = array('mp4','mov','asf','avi','flv','rm','wmv'); //No swf or 3gp
        $mime_map = new MimeMap();
        $mime_map->addExtensionToMimeType('flv','video/x-flv');
        $ext_map = $mime_map->getExtensionToMimeTypes();
        foreach ($extensions_to_check as $ext) {
            foreach ($ext_map[$ext] as $type) {
                $allowedMimeTypes[] = $type;
            }
        }
        $allowedMimeTypes[]="application/octet-stream";

        return new FileHandlerDefinition(array(
            'allowedMimeTypes' => $allowedMimeTypes,
            'allowedMimeEncodings' => array('binary'),
            'requiredType' => 'file',
        ));
    }

    /**
     * Output should be a file with and .mp4 extension
     */
    protected function buildOutputDefinition()
    {
        return new FileHandlerDefinition(array(
            'requiredType' => 'file',
            'requiredExtension' => 'mp4',
            'inheritInputExtension' => false,
        ));
    }
}
