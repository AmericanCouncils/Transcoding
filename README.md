# Implementation Details & Example Usage #

The Transcoding component is a library for abstracting file transcoding tool usage, and the presets that configure them.  If you want a quick way to use it directly without implementing it in code elsewhere, check out the [Mutate CLI App](http://github.com/americancouncils/Mutate) or the [Transcoding Symfony2 Bundle](http://github.com/americancouncils/TranscodingBundle).

The Transcoding component consists of several parts.

1. The first is the `Transcoder` class which unifies the transcode process.  It provides the glue through which various adapters, presets and transcoding jobs are registered and can interact in order to transcode files consistently and safely.  It also dispatches pre/post/error events when any file is transcoded.

2. Second, there are `Adapter` classes which are plugins that receive a standard input file, provide some logic to transcode the file, and return a standard output file.  They can put restrictions on the types of input/output files they are allowed to handle.

3. Third are the `Preset` classes, which provide groupings of options for an `Adapter` to use when implementing its transcode logic.  They can also put restrictions on the types of input/output files they are allowed to handle.

4. Fourth are `File` instances.  They are a thin extension of PHP's standard `SplFileObject` class.  These, in conjunction with `Preset` instances, are what `Adapters` take as input.  If the `Adapter` returns a file, it should also be an instance of `AC\Component\Transcoding\File`.

5. Fifth are `FileHandlerDefinition` instances.  These can be specified by Adapters, as well as Presets, and define what types of files are allowable as both input and output.  These instances are used internally by the `Transcoder` to ensure valid input/output and to assist in building a valid output file path if none is specified, or to catch an invalid path if provided, before it gets to the adapter for the transcode process.

6. Last, there are `Job` classes.  (**NOT YET IMPLEMENTED**) Jobs are complex groupings of presets.  For example, if you want to transcode multiple files from one input file, or apply multiple presets to one file, that type of interaction can be specified in a `Job` class, which you can then execute the same as you would any other preset.

Below you will see basic example usage and implementation of each the items mentioned above.

## Transcoder ##

The Transcoder does the work of standardizing the transcoding input and output.  What exactly it does when transcoding a file is determined by the registered presets, adapters and jobs.

### Usage ###

Using the Transcoder by its self is simple, as it has no dependencies.  It can accept presets/adapters/jobs from anywhere, some of which may have their own dependencies if necessary.

	$transcoder = new AC\Component\Transcoding\Transcoder;
	
	// ... register presets, adapters & jobs ... 
	$transcoder->registerAdapter(new MyCustomFFmpegAdapter("/path/to/ffmpeg"));
	$transcoder->registerPreset(new FFmpeg/WebmHDPreset);
	$transcoder->registerJob(new FFmpeg/MyHtml5VideoJob);
	
	//transcode one file using a preset
	$newFile = $transcoder->transcodeWithPreset($inputFilePath, 'webm-hd', $outputFilePath);
	
	//transcode a file with a specific adaptor and options
	$newFile = $transcoder->transcodeWithAdapter($inputFilePath, 'custom-ffmpeg', array(
		/* options */
	));
	
	//transcode one file using a job (could result in many files depending on the job definition), returns an array of files (only one entry if only one file was created)
	//note this is subject to change, jobs have not been implemented
	$files = $transcoder->transcodeWithJob($inputFilePath, 'html5_video');
	
	
## Adapters ##

Adapters are wrappers for a pre-existing toolset which does the real work for any file conversion/manipulation.  Technically these adapters can be anything.  Common examples are `ffmpeg` for audio/video manipulation and ImageMagick for image manipulation in PHP.  By default, the library provides `Adapter` implementations for several commonly used tools, including those just mentioned.

### Registering an adapter ###

Adapters can be fairly simple, or quite complex.  The adapters included in the library do not have external dependencies which aren't provided by the library (aside from requiring certain tools be installed on the system).  However, other adapters may require external PHP dependencies and special set-up.  It is beyond the scope of the library to handle this.
	
	//build your custom adapter
	$adapter = new MyCoolAdapter(/* inject any dependencies */);
	$transcoder->registerAdapter($adapter);

	//register adapters provided with the library
	$transcoder->registerAdapter(new FFmpegAdapter);
	$transcoder->registerAdapter(new ImageFormatConverterAdapter);
	$transcoder->registerAdapter(new ImageEffectsAdapter);
	
### Writing an adapter ###

All adapters receive input in the same way - they simply take an input file object, a string output path, and a `Preset` instance for use during the transcode process.  Generally, adapters aren't used directly, but the `Transcoder` will call passing along registered presets, and testing for valid input/output based on the preset definition.  Below is an example template for a very simple custom adapter.  For more detailed documentation on writing an adapter, see the `README.md` in `adapters/`.

	<?php
	use AC\Component\Transcoding\Adapter;
	use AC\Component\Transcoding\File;
	use AC\Component\Transcoding\Preset;
	
	class FooAdapter extends Adapter {
		protected $name = 'foo';
		protected $description = "A made-up adapter for documentation purposes.";
		
		public function transcodeFile(File $inFile, Preset $preset, $outFilePath) {
			
			//do actual transcode process, however that needs to happen for this adapter
			
			//return a new file instance for the created file
			return new File($outFilePath);
		}
	}
	
### Implementing command-line tools ###

Many file conversion tools are available as command line executables.  Writing code to make executing command line processes safe and consistent accross environments has already been done well with the `Symfony\Process` component, which is provided with this library.  If you want to implement a tool that requires using the command line, we highly recommend using this library rather than writing custom code.  Read more on the `Symfony\Process` component [here](https://github.com/symfony/Process).

For example, the FFmpeg and Handbrake adapters use the `Symfony\Process` component to actually execute its command line process.  The general flow goes something like the following:
	
	//method of an adapter class
	public function transcodeFile(File $inFile, Preset $preset, $outFilePath) {
		// ... parse the $preset object to assemble a command string in $commandString ...
		
		//use the Process component to build a process instance with the command string
		$process = new \Symfony\Component\Process\Process($commandString);
		
		//if this could be a long-running process, be sure to increase the timeout limit accordingly
		$process->setTimeout(3600);

		//pass an anonymous function to the process so the adapter can get output as it occurs
		$result = $process->run(function ($type, $buffer) {
			if($type === 'err') {
				//throw an exception, depending on the error
			} else {
				//do something else with the output, whatever that is, maybe append to a status/log file if available
			}
		});

		//check for error status return
		if(!$process->isSuccessful()) {
			throw new \RuntimeException($process->getExitCodeText());
		}
		
		return new File($outputFilePath);
	}

## Presets ##

Presets help streamline the transcode process by bundling together common options and requirements into one package.  Several presets are provided with the library for common types of file conversions using popular tools.

For more specific documentation and a usable template, see the `README.md` in `presets/`.

### Registering a preset ###

Presets shouldn't have dependencies, since they are really just a mechanism for bundling options which will be passed to an adapter.  You can declare/register presets in two ways:

	//instantiate inline preset
	$transcoder->registerPreset(new \AC\Component\Transcoding\Preset('preset_name', 'required_adapter_name', array(/* preset options */), array(/* FileHandlerDefinition options */)));
	
	//pre-defined preset which extends the Preset class above and defines it's settings internally
	$transcoder->registerPreset(new Mp4_HD_720Preset);

### Writing a preset ###

A preset can be declared in two ways.  You may create one by instantiating the preset class, passing it the required options, or you could extend the base `Preset` class.  The library provides many presets which extend the base `Preset` class, to make them easy to work with.  Presets require two main parts, the first is the actual preset options, which will be passed to the adapter, and the second is a `FileHandlerDefinition` instance, which standardizes what the accepted input/output formats can be.  For example, check out the Handbrake preset for generating 720 mp4 videos:

	TODO: paste example preset when finalized

## FileHandlerDefinition instances ##

Both Adapters and Presets can specify `FileHandlerDefintion` instances to restrict accepted types of input and output files.  The Transcoder uses the `FileHandlerDefinition` instances to handle input and output in a standardized manner.  `FileHandlerDefinition` instances can set restrictions on allowed or rejected input extensions, mime types, mime encodings, and other properties.

The `FileHandlerDefinition` instances are also used by the Transcoder to assemble an output file path, which will be passed to an adapter, if none was provided to the transcoder when running a job.

By default, all `Adapter` and `Preset` classes will return `FileHandlerDefinition` instances for both input and output files which will receive files of any format.

## Jobs ##

A `Job` is a complex grouping of presets which perform multiple transcoding actions in one request.  It requires a little extra setup, but can make repetive tasks much easier to manage.  Jobs can apply multiple presets to one input file, or branch off and create several output files given one input.  For example, when optimizing videos for web delivery, you may need to transcode an uploaded video into several different formats of varying quality, and create several image thumbnails.  By defining a job classes which leverage other presets, you can define and register all of these actions in one location, ensuring each individual action is handled as thoroughly as possible.

### Registering a job ###

	TODO

### Writing a job ###

	TODO
	
### Running a job ###

	TODO
    
    
## Events ##

    TODO
