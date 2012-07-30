<?php

namespace AC\Component\Transcoding;

/**
 * A Preset is basically a collection of options which are used by an Adapter to do some processing.
 * It is a glorified array, but it can also provide custom FileHandlerDefinition instance for validating
 * both input and output files.
 *
 * @package Transcoding
 * @author Evan Villemez
 */
class Preset implements \ArrayAccess, \Serializable, \IteratorAggregate
{
    /**
     * A machine-key string name for the preset.  Should be lower-cased with underscores.  Use "." separators to denote namespaces.
     *
     * @var string
     */
    protected $key = false;

    /**
     * A human-readable name for the preset.
     *
     * @var string
     */
    protected $name = false;

    /**
     * The string key of the required adapter for this preset.
     *
     * @var string
     */
    protected $requiredAdapter = false;

    /**
     * A human-readable description for what the preset does.
     *
     * @var string
     */
    protected $description = "No description provided.";

    /**
     * FileHandlerDefinition instance built during __construct(); that describes input file restrictions.
     *
     * @var \AC\Component\Transcoding\FileHandlerDefinition
     */
    protected $inputDefinition = false;

    /**
     * FileHandlerDefinition instance built during __construct(); that describes output file restrictions.
     *
     * @var \AC\Component\Transcoding\FileHandlerDefinition
     */
    protected $outputDefinition = false;

    /**
     * Boolean for whether or not the preset is locked.  If locked, options cannot be modified, only read
     *
     * @var boolean
     */
    protected $locked = false;

    /**
     * Options are values specific to the adapter required by the prefix.
     *
     * @var array
     */
    protected $options = array();

    /**
     * Constructor - all values are optional so that Presets can be defind on-the-fly, or by extension.
     *
     * @param string $name
     * @param string $adapter
     * @param array  $options
     */
    public function __construct($key = false, $requiredAdapter = false, $options = array())
    {
        //if already set (by extension), don't override
        if (!$this->key) {
            $this->key = $key;
        }

        //if already set (by extension), don't override
        if (!$this->requiredAdapter) {
            $this->requiredAdapter = $requiredAdapter;
        }

        $this->options = $options;

        $this->inputDefinition = $this->buildInputDefinition();
        $this->outputDefinition = $this->buildOutputDefinition();

        $this->configure();

        //make sure we have the requirements
        if (!$this->key) {
            throw new Exception\InvalidPresetException("Presets require a valid key to be specified.");
        }

        if (!$this->requiredAdapter) {
            throw new Exception\InvalidPresetException("Presets must declared their required adapter.");
        }

        if (!$this->inputDefinition) {
            throw new Exception\InvalidPresetException("Missing input FileDefinitionHandler, did you forget to return it from Preset::buildInputDefinition() ?");
        }

        if (!$this->outputDefinition) {
            throw new Exception\InvalidPresetException("Missing output FileDefinitionHandler, did you forget to return it from Preset::buildOutputDefinition() ?");
        }
    }

    /**
     * Return the key for this preset
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Return string name of this preset, the key will be returned if a name is not defined.
     *
     * @return string
     */
    public function getName()
    {
        if (!$this->name) {
            return $this->key;
        }

        return $this->name;
    }

    /**
     * Return string name of required adapter
     *
     * @return string
     */
    public function getRequiredAdapter()
    {
        return $this->requiredAdapter;
    }

    /**
     * Return string human-readable description of what this preset does.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * For overriding classes to extend.  Generally specific preset options should be defined in this method when extending.
     *
     * @return void
     */
    protected function configure() {}

    /**
     * Meant to be overriden in extending preset classes.  The default FileHandlerDefinition will accept files of any format.
     *
     * @return FileHandlerDefinition
     */
    protected function buildInputDefinition()
    {
        return new FileHandlerDefinition();
    }

    /**
     * Meant to be overriden in extending preset classes.  The default FileHandlerDefinition will accept files of any format.
     *
     * @return FileHandlerDefinition
     */
    protected function buildOutputDefinition()
    {
        return new FileHandlerDefinition();
    }

    /**
     * Uses input FileHandlerDefinition to validate given file. Throws exceptions on failure.
     *
     * @param  File $file
     * @return true
     */
    public function validateInputFile(File $file)
    {
        return $this->getInputDefinition()->validateFile($file);
    }

    /**
     * Uses output FileHandlerDefinition to validate given file. Throws exceptions on failure.
     *
     * @param  File $file
     * @return true
     */
    public function validateOutputFile(File $file)
    {
        return $this->getOutputDefinition()->validateFile($file);
    }

    /**
     * Uses input FileHandlerDefinition to accepts given file. Throws exceptions on failure.
     *
     * @param  File $file
     * @return true
     */
    public function acceptsInputFile(File $file)
    {
        return $this->getInputDefinition()->acceptsFile($file);
    }

    /**
     * Uses output FileHandlerDefinition to accepts given file. Throws exceptions on failure.
     *
     * @param  File $file
     * @return true
     */
    public function acceptsOutputFile(File $file)
    {
        return $this->getOutputDefinition()->acceptsFile($file);
    }

    /**
     * Returns suggested string absolute output path, given a user provided path and input file.
     *
     * @param  File   $inFile
     * @param  string $outputPath - optionally provided output path, or default to false
     * @return string
     */
    public function generateOutputPath(File $inFile, $outputPath = false)
    {
        //define path to input directory
        $inputDirectory = rtrim($inFile->isDir() ? $inFile->getRealPath() : dirname($inFile->getRealPath()), DIRECTORY_SEPARATOR);

        //trim output path
        if ($outputPath) {
            $outputPath = rtrim($outputPath, DIRECTORY_SEPARATOR);
        }

        //if a path was provided by the user, make sure it's relatively valid
        if (is_string($outputPath)) {
            //if directory output is required
            if ($this->getOutputDefinition()->getRequiredType() === 'directory') {
                //output path should not contain an extension if the output is supposed to be a directory
                $exp = explode(DIRECTORY_SEPARATOR, $outputPath);
                $name = end($exp);
                $exp = $this->safeExplodeFileName($name);
                if (count($exp) >= 2) {
                    throw new Exception\InvalidInputException(sprintf("Output for this preset is required to be a directory, the output path should not contain a file extension."));
                }

                //otherwise it must be valid, so return
                return $outputPath;
            }

            //if output can be a file, make sure the path acceptable by the output definition
            else {
                //check for a file extension provided
                $exp = explode(DIRECTORY_SEPARATOR, $outputPath);
                $name = end($exp);
                $exp = $this->safeExplodeFileName($name);

                //we assume a valid file extension
                if (count($exp) >= 2) {
                    //if contains an extension, is it valid?
                    $givenExtension = strtolower(array_pop($exp));
                    $inputExtension = strtolower($inFile->getExtension());
                    $baseName = implode(".", $exp);

                    //should it inherit the extension?  if so does it match?
                    if ($this->getOutputDefinition()->getInheritExtension() && ($givenExtension !== $inputExtension)) {
                        throw new Exception\InvalidInputException(sprintf("The output file extension for this preset must match the file extension of the input file."));
                    }

                    //does the output definition generally accept the given extension?
                    if (!$this->getOutputDefinition()->acceptsExtension($givenExtension)) {
                        throw new Exception\InvalidInputException(sprintf("The requested preset cannot output files with extension [%s]", $givenExtension));
                    }

                    //provided path must be valid enough, so return it
                    return $outputPath;
                }

                //otherwise we assume we've been given a directory to put the file in
                else {
                    //generate a default name, append to provided directory, try resolving a valid output extension

                    //get input filename without it's extension
                    $exp = explode(".", $inFile->getFilename());
                    if (count($exp) >= 2) {
                        array_pop($exp);
                    }
                    $baseName = implode(".", $exp);

                    //get proper output extension
                    $outputExtension = $this->resolveOutputExtension($inFile);

                    //default to infixing the preset key of the output file to avoid confusing
                    return $outputPath.DIRECTORY_SEPARATOR.$baseName.".".$this->getKey().".".$outputExtension;
                }
            }
        }

        //otherwise, if no path was provided
        else {
            //check output definition for required types
            if ($this->getOutputDefinition()->getRequiredType() === 'directory') {
                //default to creating directory named by preset
                return $inputDirectory.DIRECTORY_SEPARATOR.$this->getKey();
            } else {
                //otherwise default to creating new file path with format infileName.presetKey.required_or_inheritedExtension
                $outputExtension = $this->resolveOutputExtension($inFile);

                //get input filename without it's extension
                $exp = explode(".", $inFile->getFilename());
                if (count($exp) >= 2) {
                    array_pop($exp);
                }
                $baseName = implode(".", $exp);

                return $inputDirectory.DIRECTORY_SEPARATOR.$baseName.".".$this->getKey().".".$outputExtension;
            }
        }

        return false;
    }

    protected function safeExplodeFileName($fileName)
    {
        if (!in_array($fileName, array('.', '..'))) {
            return explode(".", $fileName);
        }

        return array($fileName);
    }

    /**
     * Resolves the output extension based on the input file.  If it cannot be determined, will call Preset::getOutputExtension, which must
     * be implemented by an extending class, otherwise exceptions are thrown.
     *
     * @param  File   $inFile
     * @return string
     */
    protected function resolveOutputExtension(File $inFile)
    {
        $outDef = $this->getOutputDefinition();
        if ($outDef->getRequiredExtension()) {
            return $outDef->getRequiredExtension();
        }

        if ($outDef->getInheritExtension()) {
            return $inFile->getExtension();
        }

        return $this->getOutputExtension();
    }

    /**
     * Method for determining the output extension based on data from the preset.  This must be implemented
     * by an extending class, and is only called if the output extension can't be determined any other way.
     *
     * @return string
     */
    protected function getOutputExtension()
    {
        throw new Exception\InvalidPresetException(__METHOD__." must be implemented by an extending class to properly determine the required output extension.");
    }

    /**
     * Return input FileHandlerDefinition
     *
     * @return AC\Component\Transcoding\FileHandlerDefinition
     */
    public function getInputDefinition()
    {
        return $this->inputDefinition;
    }

    /**
     * Return output FileHandlerDefinition
     *
     * @return AC\Component\Transcoding\FileHandlerDefinition
     */
    public function getOutputDefinition()
    {
        return $this->outputDefinition;
    }

    /**
     * Set input FileHandlerDefinition (if not locked)
     *
     * @param  FileHandlerDefinition $def
     * @return self
     */
    public function setInputDefinition(FileHandlerDefinition $def)
    {
        if (!$this->locked) {
            $this->inputDefinition = $def;
        }

        return $this;
    }

    /**
     * Set output FileHandlerDefinition (if not locked)
     *
     * @param  FileHandlerDefinition $def
     * @return self
     */
    public function setOutputDefinition(FileHandlerDefinition $def)
    {
        if (!$this->locked) {
            $this->outputDefinition = $def;
        }

        return $this;
    }

    /**
     * Set array of preset options in one operation.
     *
     * @param  array $ops
     * @return self
     */
    public function setOptions(array $ops)
    {
        $this->options = $ops;

        return $this;
    }

    /**
     * Return array of all options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets 'locked' property to true, so that no new options can be set or removed.
     */
    public function lock()
    {
        $this->locked = true;
    }

    /**
     * Retrieve one option by key, returning a default value if not set
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    /**
     * Set a key / value option pair (if not locked)
     *
     * @param  string $key
     * @param  mixed  $val
     * @return self
     */
    public function set($key, $val)
    {
        if (!$this->locked) {
            $this->options[$key] = $val;
        }

        return $this;
    }

    /**
     * Return true/false if option key exists
     *
     * @param  string  $key
     * @return boolean
     */
    public function has($key)
    {
        return isset($this->options[$key]);
    }

    /**
     * Remove an option by key (if not locked)
     *
     * @param  string $key
     * @return self
     */
    public function remove($key)
    {
        if (!$this->locked) {
            if (isset($this->options[$key])) {
                unset($this->options[$key]);
            }
        }

        return $this;
    }

    /**
     * ArrayAccess implementation for Preset::get()
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * ArrayAccess implementation for Preset::set()
     */
    public function offsetSet($key, $val)
    {
        return $this->set($key, $val);
    }

    /**
     * ArrayAccess implementation for Preset::has()
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * ArrayAccess implementation for Preset::remove()
     */
    public function offsetUnset($key)
    {
        return $this->remove($key);
    }

    /**
     * Serializable implementation
     */
    public function serialize()
    {
        $data = array();
        foreach ($this as $key => $val) {
            $data[$key] = $val;
        }

        return serialize($data);
    }

    /**
     * Serializable implementation
     */
    public function unserialize($string)
    {
        $data = unserialize($string);
        foreach ($data as $key => $val) {
            $this->$key = $val;
        }
    }

    /**
     * Implements \IteratorAggregate
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->options);
    }
}
