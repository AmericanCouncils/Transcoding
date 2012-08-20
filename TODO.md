# TODO #

* Implement an AbstractCliAdapter, have Handbrake/ffmpeg extend that by returning the process to execute
* Unit test TestCliAdapter, HandbrakeAdapter, FFmpeg adapter
* Implement jobs
	* Allow chained presets on one output file
	* Allow creation of multiple output files
	* Questions:
		* Treat this as an extension of a preset?  Probably...
* Create Adapters:
	* mencoder (?)
	* various ImageMagick adapters
	* GD2 adapters?  Maybe make presets for these tools interchangeable?
* Implement common presets for above adapters
* Permissions
    * Implement mode to preserve or change file/dir permissions
