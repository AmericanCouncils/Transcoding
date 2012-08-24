<?php

namespace AC\Component\Transcoding\Preset\Handbrake;

use AC\Component\Transcoding\Preset;
use AC\Component\Transcoding\FileHandlerDefinition;
use AC\Component\Transcoding\MimeMap;

abstract class BasePreset extends Preset
{
    protected $requiredAdapter = 'handbrake';

    protected function buildInputDefinition()
    {
		$allowedMimeTypes = array();
		$extensions_to_check = array('mp4','mov','asf','avi','flv','rm','wmv'); //No swf or 3gp
		$mime_map = new MimeMap();
		$mime_map->setExtension('flv','video/x-flv');
		$ext_map = $mime_map->getExtensionToMimeTypes();
		foreach($extensions_to_check as $ext) {
			foreach($ext_map[$ext] as $type) {
				$allowedMimeTypes[] = $type;
			}
		}
		$allowedMimeTypes[]="application/octet-stream";
		//die(var_dump($allowedMimeTypes));
        return new FileHandlerDefinition(array(
			/*'allowedMimeTypes' => array(
				'video/mp4', //mp4
				'video/quicktime', //.mov and .mp4
				'video/x-ms-asf', //asf
				'application/octet-stream', //asf
				'video/x-msvideo', //avi
				//'audio/x-ms-wma',
				'video/x-flv', //flv
				'audio/x-realaudio', //rm
				'application/vnd.rn-realmedia', //rm
				//'application/x-shockwave-flash', //swf
				'video/x-ms-wmv', //wmv
				//None found for .3gp
			),*/
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
            'inheritExtension' => false,
        ));
    }
}
