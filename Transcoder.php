<?php

namespace AC\Component\Transcoding;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use AC\Component\Transcoding\Event\TranscodeEvents;
use AC\Component\Transcoding\Event\TranscodeEvent;
use AC\Component\Transcoding\Event\FileEvent;

/**
 * Main transcoding class.  Standardizes input/output before executing a transcode process via an adapter.
 *
 * @package Transcoding
 * @author Evan Villemez
 */
class Transcoder extends EventDispatcher
{
    /**
     * The general version for the library, stored as a constant here as this object is the main entry point.
     */
    const VERSION = "0.2.0";

    /**
     * If a file already exists, remove the pre-existing file before initiating the transcode
     */
    const ONCONFLICT_DELETE = 1;

    /**
     * If a file already exists, throw an exception.
     */
    const ONCONFLICT_EXCEPTION = 2;

    /**
     * If file already exists, create a derivative file path with numerical increment to avoid conflicts.
     */
    const ONCONFLICT_INCREMENT = 3;

    /**
     * If a transcode process fails, delete any newly created files
     */
    const ONFAIL_DELETE = 1;

    /**
     * If the transcode process fails, keep any created files
     */
    const ONFAIL_PRESERVE = 2;

    /**
     * If the transcode requires creating a directory, create the necessary directories recursively
     */
    const ONDIR_CREATE = 1;

    /**
     * If the transcode requires creating a directory, fail with exception
     */
    const ONDIR_EXCEPTION = 2;

    /**
     * The octal file creation mode to set for any files created during a transcode process
     *
     * @var octal
     */
    protected $fileCreationMode = 0644;

    /**
     * The octal directory creation mode to set for any directories created during the transcode process
     *
     * @var octal
     */
    protected $directoryCreationMode = 0755;

    /**
     * Storage array of registered adapters
     *
     * Format is hash of adapter_name => object
     *
     * @var array
     */
    protected $adapters = array();

    /**
     * Storage array of registered presets
     *
     * Format is hash of preset_name => object
     *
     * @var array
     */
    protected $presets = array();

    /**
     * Storage array of registered jobs.
     *
     * Format is hash of job_name => object
     *
     * @var array
     */
    protected $jobs = array();

    /**
     * The core method of the transcode process.  Takes file input, validates, runs a transcode process, validates return, and returns file output.
     *
     * @param  mixed  $inFile       - if a string filepath is given instead of an instance of \AC\Component\Transcoding\File, then a new File instance will be created automatically
     * @param  mixed  $preset       - if a string is given instead of an instance of \AC\Component\Transcoding\Preset, then it will look for a Preset with a name matching the received string
     * @param  string $outFile      - an optional output file path, even if provided explicity, the Transcoder will validate and process it before starting a transcode process
     * @param  string $conflictMode - flag for what to do if an output file already exists at the given output path
     * @param  string $failMode     - flag for what to do with the output file(s) on a failed transcode
     * @return File   - \AC\Component\Transcoding\File instance for newly created file
     */
    public function transcodeWithPreset($inFile, $preset, $outFile = false, $conflictMode = self::ONCONFLICT_INCREMENT, $dirMode = self::ONDIR_EXCEPTION, $failMode = self::ONFAIL_DELETE)
    {
        //figure out input file path and preset key, without throwing exceptions
        $inputPath = ($inFile instanceof File) ? $inFile->getRealPath() : $inFile;
        $presetKey = ($preset instanceof Preset) ? $preset->getKey() : $preset;
        $outFilePath = $outFile;

        //validate all inputs before attempting to run the transcode process
        try {

            //get file
            if (!$inFile instanceof File && is_string($inFile)) {
                $inFile = new File($inFile);
            }

            //get preset
            if (!$preset instanceof Preset) {
                $preset = $this->getPreset($preset);
            }

            //have preset validate file
            $preset->validateInputFile($inFile);

            //get adapter
            $adapter = $this->getAdapter($preset->getRequiredAdapter());

            //verify if this adapter can work in the current environment (happens only the first time it's loaded)
            if (!$adapter->verify()) {
                throw new \RuntimeException($adapter->getVerificationError());
            }

            //have adapter verify inputs
            $adapter->validateInputFile($inFile);
            $adapter->validatePreset($preset);

            //generate the final output string
            $outFilePath = $preset->generateOutputPath($inFile, $outFile);

            //make sure the output path is valid, create any directories as necessary
            $outFilePath = $this->processOutputFilepath($outFilePath, $conflictMode, $dirMode);

        } catch (\Exception $e) {

            //notify listeners of failure
            $this->dispatch(TranscodeEvents::ERROR, new TranscodeEvent($inputPath, $presetKey, $outFilePath, null, $e));

            //rethrow for containing environment to handle
            throw $e;
        }

        //attempt to run the actual transcode process
        try {

            //notify listeners of transcode start
            $this->dispatch(TranscodeEvents::BEFORE, new TranscodeEvent($inputPath, $presetKey, $outFilePath));

            //run the transcode
            $return = $adapter->transcodeFile($inFile, $preset, $outFilePath);

            //validate return
            if (!$return instanceof File) {
                throw new Exception\InvalidOutputException("Adapters must return an instance of AC\Component\Transcoding\File, or throw an exception upon error.");
            }
            $preset->validateOutputFile($return);
            $adapter->validateOutputFile($return);
            $this->cleanOutputFile($return);

            $returnPath = $return->getRealPath();

            //notify listeners of completion
            $this->dispatch(TranscodeEvents::AFTER, new TranscodeEvent($inputPath, $presetKey, $returnPath));

            //notify of new file
            $this->dispatch(TranscodeEvents::FILE_CREATED, new FileEvent($returnPath));

            //return newly created file
            return $return;

        } catch (\Exception $e) {

            //clean up files after failure
            $this->cleanFailedTranscode($adapter, $outFilePath, $failMode);

            //notify listeners of failure
            $this->dispatch(TranscodeEvents::ERROR, new TranscodeEvent($inputPath, $presetKey, $outFilePath, null, $e));

            //re-throw exception so environment can handle appropriately
            throw $e;
        }

        return false;
    }

    /**
     * Transcode a file with a specific adapter directly.  Internally builds a dynamic preset with the specified options.
     *
     * @param  mixed                          $inFile       - either string filepath or instance of \AC\Component\Transcoding\File
     * @param  string                         $adapterName  - string name of adapter to use
     * @param  array                          $options      - key/val option hash to pass to adapter
     * @param  string                         $outFile      - optional output file path, if not provided will be derived automatically by the Transcoder
     * @param  string                         $conflictMode - flag for how to handle output file conflicts
     * @param  string                         $failMode     - flag for how to handle failed transcodes
     * @return \AC\Component\Transcoding\File
     */
    public function transcodeWithAdapter($inFile, $adapterName, $options = array(), $outFile = false, $conflictMode = self::ONCONFLICT_INCREMENT, $dirMode = self::ONDIR_EXCEPTION, $failMode = self::ONFAIL_DELETE)
    {
        //build a preset on the fly based on the options provided
        $preset = new Preset('dynamic', $adapterName, $options);

        return $this->transcodeWithPreset($inFile, $preset, $outFile, $conflictMode, $dirMode, $failMode);
    }

    /**
     * TODO: implement eventually...
     */
    public function transcodeWithJob($inFile, $job, $conflictMode = self::ONCONFLICT_INCREMENT, $dirMode = self::ONDIR_CREATE, $failMode = self::ONFAIL_DELETE)
    {
        if (!$job instanceof Job) {
            $job = $this->getJob($job);
        }

        //TODO: implement once the job-related APIs are defined
        throw new \RuntimeException(__METHOD__." not yet implemented.");

    }

    /**
     * Scan an output path to make sure there are no conflicts.  Handle conflicts according to mode.  Check to make sure final path is actually writable.
     * Returns the final output path, which may have been altered depending on the mode.
     *
     * @param  string $outputPath
     * @param  string $conflictMode
     * @param  string $dirMode
     * @return string
     */
    protected function processOutputFilepath($outputPath, $conflictMode, $dirMode)
    {
        $outputIsDirectory = $this->pathIsDirectory($outputPath);

        //check for pre-existing file and handle based on conflict mode
        if (file_exists($outputPath)) {
            if ($conflictMode === self::ONCONFLICT_EXCEPTION) {
                throw new Exception\FileAlreadyExistsException(sprintf("File %s already exists.", $outputPath));
            }

            if ($conflictMode === self::ONCONFLICT_DELETE) {
                if ($outputIsDirectory) {
                    $this->removeDirectory($outputPath);
                } else {
                    @unlink($outputPath);
                }
            }

            if ($conflictMode === self::ONCONFLICT_INCREMENT) {
                $outputPath = $this->incrementConflictingPath($outputPath);
            }
        }

        //check for necessary containing directory creation, handle based on directory mode
        $outputDirectory = dirname($outputPath);

        if (!file_exists($outputDirectory)) {
            if ($dirMode === self::ONDIR_EXCEPTION) {
                throw new Exception\InvalidModeException("The Transcoder is not permitted to create new directories if needed.");
            }

            //try creating the necessary containing directories recursively
            if (!mkdir($outputDirectory, $this->getDirectoryCreationMode(), true)) {
                throw new Exception\FilePermissionException("The required containing directories could not be created.");
            }

            $this->dispatch(TranscodeEvents::DIR_CREATED, new FileEvent($outputDirectory));
        }

        //check for write permissions
        if (!is_writable($outputDirectory)) {
            throw new Exception\FilePermissionException(sprintf("Cannot transcode because the directory %s is not writable.", $outputDirectory));
        }

        //if the output is a directory, make sure the actual required directory is created
        if ($outputIsDirectory) {
            if (!mkdir($outputPath, $this->getDirectoryCreationMode())) {
                throw new Exception\FilePermissionException(sprintf("Could not properly create the required output directory %s.", $outputPath));
            }

            $this->dispatch(TranscodeEvents::DIR_CREATED, new FileEvent($outputDirectory));
        }

        return $outputPath;
    }

    /**
     * If a previous file exists, create a new path, numerically incrementing a number in the string to avoid conflicts.
     *
     * @param  string $path
     * @return string
     */
    protected function incrementConflictingPath($path)
    {
        $isDir = $this->pathIsDirectory($path);
        $expPath = explode(DIRECTORY_SEPARATOR, $path);
        $oldFileName = array_pop($expPath);
        $basePath = implode(DIRECTORY_SEPARATOR, $expPath);
        if ($isDir) {
            //for directories append incremented number after underscore
            $i = 1;
            while (file_exists($newFileName = $basePath.DIRECTORY_SEPARATOR.$oldFileName."_".$i)) {
                $i++;
            }
        } else {
            //for files insert incremented number between filename and extension
            $exp = explode(".", $oldFileName);
            $extension = array_pop($exp);
            $name = implode(".", $exp);
            $i = 1;
            while (file_exists($newFileName = $basePath.DIRECTORY_SEPARATOR.$name.".".$i.".".$extension)) {
                $i++;
            }
        }

        return $newFileName;
    }

    /**
     * Remove a directory and all of its contents
     *
     * @param  string $path
     * @return void
     */
    protected function removeDirectory($path)
    {
        foreach (scandir($path) as $item) {
            if (!in_array($item, array('.','..'))) {
                @unlink($path.DIRECTORY_SEPARATOR.$item);
                $this->dispatch(TranscodeEvents::DIR_REMOVED, new FileEvent($outputPath));
            }
        }

        if (!rmdir($path)) {
            throw new Exception\FilePermissionException(sprintf("Could not remove directory %s", $path));
        }
    }

    /**
     * Return boolean if a given path is likely a directory (this isn't just for pre-existing files)
     *
     * @param  string  $path
     * @return boolean true or false
     */
    protected function pathIsDirectory($path)
    {
        $exp = explode(DIRECTORY_SEPARATOR, $path);
        $name = end($exp);
        $exp = explode(".", $name);

        return !(count($exp) >= 2);
    }

    /**
     * Post process newly created files by setting proper file permissions based on set permission modes
     *
     * @param  File $file
     * @return void
     */
    protected function cleanOutputFile(File $file)
    {
        $path = $file->getRealPath();

        if ($file->isDir()) {
            chmod($path, $this->getDirectoryCreationMode());
            $this->dispatch(TranscodeEvents::DIR_MODIFIED, new FileEvent($path));
        } else {
            chmod($path, $this->getFileCreationMode());
            $this->dispatch(TranscodeEvents::FILE_MODIFIED, new FileEvent($path));
        }
    }

    /**
     * Cleanup after a failed transcode - this may entail deleting newly created files, depending on the mode in which the transcode process executed
     *
     * This will also call the corresponding `Adapter::cleanFailedTranscode()` method for the adapter that was used.
     *
     * @param  AC\Component\Transcoding\Adapter $adapter
     * @param  string                           $outputFilePath
     * @param  string                           $failMode
     * @return void
     */
    protected function cleanFailedTranscode(Adapter $adapter, $outputFilePath, $failMode)
    {
        if (file_exists($outputFilePath)) {
            if ($failMode === self::ONFAIL_DELETE) {
                @unlink($outputFilePath);
                $this->dispatch(TranscodeEvents::FILE_REMOVED, new FileEvent($outputFilePath));
            }
        }

        $adapter->cleanFailedTranscode($outputFilePath);
    }

    /**
     * Dispatch event, but swallow exceptions thrown by listeners.
     *
     * {@inheritdoc}
     */
    public function dispatch($name, Event $e = null)
    {
        try {
            $e = parent::dispatch($name, $e);
        } catch (\Exception $e) {
            //swallow exceptions thrown by listeners, they shouldn't interfere with the process, they are supposed to be passive observers
            return true;
        }

        return $e;
    }

    /**
     * Return an adapter instance by key
     *
     * @param  string                           $key
     * @return AC\Component\Transcoding\Adapter on success, throws exception if not found
     */
    public function getAdapter($key)
    {
        if (!isset($this->adapters[$key])) {
            throw new Exception\AdapterNotFoundException(sprintf("Requested adapter %s was not found in the Transcoder.", $key));
        }

        return $this->adapters[$key];
    }

    /**
     * Return true of Transcoder has an Adapter with the given key
     *
     * @param  string  $key
     * @return boolean
     */
    public function hasAdapter($key)
    {
        return isset($this->adapters[$key]);
    }

    /**
     * Register an adapter instance with the Transcoder
     *
     * @param  Adapter $adapter
     * @return self
     */
    public function registerAdapter(Adapter $adapter)
    {
        $adapter->setTranscoder($this);
        $this->adapters[$adapter->getKey()] = $adapter;

        return $this;
    }

    /**
     * Remove an adapter instance with the given key from the Transcoder
     *
     * @param  string $key
     * @return self
     */
    public function removeAdapter($key)
    {
        if (isset($this->adapters[$key])) {
            $this->adapters[$key]->setTranscoder();
            unset($this->adapters[$key]);
        }

        return $this;
    }

    /**
     * Return array of all adapters registered with the Transcoder
     *
     * @return array
     */
    public function getAdapters()
    {
        return $this->adapters;
    }

    /**
     * Get a preset instance with the given key
     *
     * @param  string                          $key
     * @return AC\Component\Transcoding\Preset
     */
    public function getPreset($key)
    {
        if (!isset($this->presets[$key])) {
            throw new Exception\PresetNotFoundException(sprintf("Requested preset %s was not found in the Transcoder.", $key));
        }

        return $this->presets[$key];
    }

    /**
     * Return true if Preset with the given key is available
     *
     * @param  string  $key
     * @return boolean
     */
    public function hasPreset($key)
    {
        return isset($this->presets[$key]);
    }

    /**
     * Register a new preset instance
     *
     * @param  Preset $preset
     * @return self
     */
    public function registerPreset(Preset $preset)
    {
        $this->presets[$preset->getKey()] = $preset;

        return $this;
    }

    /**
     * Remove a preset with the given key
     *
     * @param  string $key
     * @return self
     */
    public function removePreset($key)
    {
        if (isset($this->presets[$key])) {
            unset($this->presets[$key]);
        }

        return $this;
    }

    /**
     * Get array of all registered Presets
     *
     * @return array
     */
    public function getPresets()
    {
        return $this->presets;
    }

    /**
     * Get a job by the given key
     *
     * @param  string                       $key
     * @return AC\Component\Transcoding\Job
     */
    public function getJob($key)
    {
        if (!isset($this->jobs[$key])) {
            throw new Exception\JobNotFoundException(sprintf("Requested job %s was not found in the Transcoder.", $key));
        }

        return $this->jobs[$key];
    }

    /**
     * Return true/false if Job with given key is registered
     *
     * @param  string  $key
     * @return boolean
     */
    public function hasJob($key)
    {
        return isset($this->jobs[$key]);
    }

    /**
     * Register a job instance
     *
     * @param  Job  $job
     * @return self
     */
    public function registerJob(Job $job)
    {
        $this->jobs[$job->getKey()] = $job;

        return $this;
    }

    /**
     * Remove a job with the given key
     *
     * @param  string $key
     * @return self
     */
    public function removeJob($key)
    {
        if (isset($this->jobs[$key])) {
            unset($this->jobs[$key]);
        }

        return $this;
    }

    /**
     * Get array of all registered Jobs
     *
     * @return array
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * Set the file creation mode used when new files are created during a transcode process
     *
     * @return int (octal)
     */
    public function getFileCreationMode()
    {
        return $this->fileCreationMode;
    }

    /**
     * Set the file creation mode to use when new files are created during a transcode process.
     *
     * Note you can set the property as either a string or octal int, but it will always be converted to the octal format required by `chmod`
     *
     * @param  string|int $mode
     * @return self
     */
    public function setFileCreationMode($mode)
    {
        //force format into octal if a string was received, for example "755" instead of 0755
        if (0 != $mode[0]) {
            $mode = "0".$mode;
        }

        $this->fileCreationMode = intval($mode, 8);

        return $this;
    }

    /**
     * Get the directory creation mode used when creating new directories.
     *
     * @return int (octal)
     */
    public function getDirectoryCreationMode()
    {
        return $this->directoryCreationMode;
    }

    /**
     * Set the file creation mode to use when new directories are created during a transcode process.
     *
     * Note you can set the property as either a string or octal int, but it will always be converted to the octal format required by `chmod`
     *
     * @param  string|int $mode
     * @return self
     */
    public function setDirectoryCreationMode($mode)
    {
        //force format into octal if a string was received, for example "755" instead of 0755
        if (0 != $mode[0]) {
            $mode = "0".$mode;
        }

        $this->directoryCreationMode = intval($mode, 8);

        return $this;
    }

}
