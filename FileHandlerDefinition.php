<?php

namespace AC\Component\Transcoding;

/**
 * FileHandlerDefinition's define valid input/output file type properties for a preset and/or adapter.  The FileHandlerDefinition is checked by the Transcoder before executing.
 *
 * @package Transcoding
 * @author Evan Villemez
 */
class FileHandlerDefinition implements \Serializable
{
    //input type restrictions
    protected $allowedExtensions = false;
    protected $rejectedExtensions = false;
    protected $allowedMimes = false;
    protected $rejectedMimes = false;
    protected $allowedMimeTypes = false;
    protected $rejectedMimeTypes = false;
    protected $allowedMimeEncodings = false;
    protected $rejectedMimeEncodings = false;

    //general i/o type
    protected $allowDirectory = true;
    protected $requiredExtension = false;
    protected $inheritExtension = false;
    protected $requiredType = false;

    /**
     * Optionally set any properties via a hash in the constructor instead of using setter methods
     *
     * @param array $ops - optional key/val hash
     */
    public function __construct($ops = array())
    {
        $this->setOptions($ops);
    }

    /**
     * Return boolean for whether or not the FileHandlerDefinition will accept a given file.
     *
     * @param  File    $file
     * @return boolean
     */
    public function acceptsFile(File $file)
    {
        try {
            return $this->validateFile($file);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Return true if FileHandlerDefinition accepts a given file, otherwise throw an exception on failure.
     *
     * @param  File    $file
     * @return boolean
     */
    public function validateFile(File $file)
    {
        if (!$this->acceptsExtension($file->getExtension())) {
            throw new Exception\InvalidFileException(sprintf("This definition does not accept files with extension %s", $file->getExtension()));
        }

        if (!$this->acceptsMime($file->getMime())) {
            throw new Exception\InvalidFileException(sprintf("This definition does not accept files with mime of %s", $file->getMime()));
        }

        if (!$this->acceptsMimeType($file->getMimeType())) {
            throw new Exception\InvalidFileException(sprintf("This definition does not accept files with mime type of %s", $file->getMimeType()));
        }

        if (!$this->acceptsMimeEncoding($file->getMimeEncoding())) {
            throw new Exception\InvalidFileException(sprintf("This definition does not accept files with mime encoding of %s", $file->getMimeEncoding()));
        }

        if ($this->getRequiredType()) {
            if ($this->getRequiredType() !== $file->getType()) {
                throw new Exception\InvalidFileException(sprintf("This definition only accept files of type %s", $this->getRequiredType()));
            }
        }

        if (!$this->getAllowDirectory() && $file->isDir()) {
            throw new Exception\InvalidFileException("This definition cannot accept a directory as input");
        }

        return true;
    }

    /**
     * Return true/false for whether or not the handler accepts a given extension.
     *
     * @param  string  $ext - string extension, if a preceeding "." is provided, it's automatically stripped
     * @return boolean
     */
    public function acceptsExtension($ext)
    {
        $ext = trim(strtolower($ext), ".");

        if ($this->getRequiredExtension() && $ext !== $this->getRequiredExtension()) {
            return false;
        }

        if ($this->getAllowedExtensions() && !in_array($ext, $this->getAllowedExtensions())) {
            return false;
        }

        if ($this->getRejectedExtensions() && in_array($ext, $this->getRejectedExtensions())) {
            return false;
        }

        return true;
    }

    /**
     * Return true/false for whether or not the handler accepts a given mime.
     *
     * @return boolean
     */
    public function acceptsMime($ext)
    {
        if ($this->getAllowedMimes() && !in_array($ext, $this->getAllowedMimes())) {
            return false;
        }

        if ($this->getRejectedMimes() && in_array($ext, $this->getRejectedMimes())) {
            return false;
        }

        return true;
    }

    /**
     * Return true/false for whether or not the handler accepts a given mime type.
     *
     * @return boolean
     */
    public function acceptsMimeType($ext)
    {
        if ($this->getAllowedMimeTypes() && !in_array($ext, $this->getAllowedMimeTypes())) {
            return false;
        }

        if ($this->getRejectedMimeTypes() && in_array($ext, $this->getRejectedMimeTypes())) {
            return false;
        }

        return true;
    }

    /**
     * Return true/false for whether or not the handler accepts a given mime encoding.
     *
     * @return boolean
     */
    public function acceptsMimeEncoding($ext)
    {
        if ($this->getAllowedMimeEncodings() && !in_array($ext, $this->getAllowedMimeEncodings())) {
            return false;
        }

        if ($this->getRejectedMimeEncodings() && in_array($ext, $this->getRejectedMimeEncodings())) {
            return false;
        }

        return true;
    }

    /**
     * Set a specific required extension
     *
     * @param  string $ext
     * @return self
     */
    public function setRequiredExtension($ext)
    {
        $this->requiredExtension = ltrim(strtolower($ext), ".");

        return $this;
    }

    /**
     * Get required file extension
     *
     * @return string or false
     */
    public function getRequiredExtension()
    {
        return $this->requiredExtension;
    }

    /**
     * Return true/false for whether or not the handler shoud inhert a file extension from another file.  Generally this is only used when checking
     * output file definitions and generating valid output file paths
     *
     * @return boolean
     */
    public function getInheritExtension()
    {
        return $this->inheritExtension;
    }

    /**
     * Set true/false whether or not to inherit an output extension, only used for output definitions to help in generating a valid output path
     *
     * @param  bool $bool
     * @return self
     */
    public function setInheritExtension($bool)
    {
        $this->inheritExtension = (bool) $bool;

        return $this;
    }

    /**
     * Return boolean for whether or not directories count as valid input
     *
     * @return boolean
     */
    public function getAllowDirectory()
    {
        return $this->allowDirectory;
    }

    /**
     * Set true/false whether or not to allow directories as input
     *
     * @param  bool $bool
     * @return self
     */
    public function setAllowDirectory($bool)
    {
        $this->allowDirectory = (bool) $bool;

        return $this;
    }

    /**
     * Get required input file type, can be either 'file' or 'directory'
     *
     * @return string
     */
    public function getRequiredType()
    {
        return $this->requiredType;
    }

    /**
     * Set required file input type, must be either 'file' or 'directory'
     *
     * @param  string $type
     * @return self
     */
    public function setRequiredType($type)
    {
        if (!in_array($type, array('file', 'directory'))) {
            throw new \InvalidArgumentException("Input type must be either 'file' or 'directory'.");
        }

        if ($type === 'directory') {
            $this->setAllowDirectory(true);
        } else {
            $this->setAllowDirectory(false);
        }

        $this->requiredType = $type;

        return $this;
    }

    /**
     *  type restriction getter/setter methods below
     */

    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }

    public function setAllowedExtensions(array $arr)
    {
        $this->allowedExtensions = $arr;

        return $this;
    }

    public function getAllowedMimes()
    {
        return $this->allowedMimes;
    }

    public function setAllowedMimes(array $arr)
    {
        $this->allowedMimes = $arr;

        return $this;
    }

    public function getAllowedMimeTypes()
    {
        return $this->allowedMimeTypes;
    }

    public function setAllowedMimeTypes(array $arr)
    {
        $this->allowedMimeTypes = $arr;

        return $this;
    }

    public function getAllowedMimeEncodings()
    {
        return $this->allowedMimeEncodings;
    }

    public function setAllowedMimeEncodings(array $arr)
    {
        $this->allowedMimeEncodings = $arr;

        return $this;
    }

    public function getRejectedExtensions()
    {
        return $this->rejectedExtensions;
    }

    public function setRejectedExtensions(array $arr)
    {
        $this->rejectedExtensions = $arr;

        return $this;
    }

    public function getRejectedMimes()
    {
        return $this->rejectedMimes;
    }

    public function setRejectedMimes(array $arr)
    {
        $this->rejectedMimes = $arr;

        return $this;
    }

    public function getRejectedMimeTypes()
    {
        return $this->rejectedMimeTypes;
    }

    public function setRejectedMimeTypes(array $arr)
    {
        $this->rejectedMimeTypes = $arr;

        return $this;
    }

    public function getRejectedMimeEncodings()
    {
        return $this->rejectedMimeEncodings;
    }

    public function setRejectedMimeEncodings(array $arr)
    {
        $this->rejectedMimeEncodings = $arr;

        return $this;
    }

    /**
     * Set multiple properties as a key/val hash in one operation.
     *
     * @param  array $ops
     * @return self
     */
    public function setOptions(array $ops)
    {
        foreach ($ops as $key => $val) {
            $this->$key = $val;
        }

        return $this;
    }

    public function serialize()
    {
        $data = array();
        foreach ($this as $key => $val) {
            $data[$key] = $val;
        }

        return serialize($data);
    }

    public function unserialize($string)
    {
        $data = unserialize($string);
        $this->setOptions($data);
    }
}
