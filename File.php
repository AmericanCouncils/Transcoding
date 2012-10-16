<?php

namespace AC\Component\Transcoding;

/**
 * An extension of SplFileObject, File instances are used as input/output for the Transcoder.  They mostly extend
 * the base file object with convenience methods for mime checking (which requires the fileinfo PHP extension)
 *
 * @package Transcoding
 * @author Evan Villemez
 */
class File extends \SplFileInfo
{
    private $_realpath = false;
    private $_finfo_mime_type = false;
    private $_finfo_mime_encoding = false;
    private $_finfo_mime = false;

    public function __construct($path)
    {
        parent::__construct($path);
        $this->_realpath = realpath($path);
    }

    public function getType()
    {
        return $this->isDir() ? 'directory' : 'file';
    }

    public function getContents()
    {
        return file_get_contents($this->_realpath);
    }

    public function putContents($content)
    {
        return file_put_contents($this->_realpath, $content);
    }

    /**
     * Returns an array of contained file objects if this file is a directory, otherwise false
     *
     * Note that directory links (`.` and `..`) are always ignored
     *
     * @return array | false
     */
    public function getContainedFiles()
    {
        if ($this->isDir()) {
            $files = array();
            $basePath = rtrim($this->_realpath, DIRECTORY_SEPARATOR);
            foreach (scandir($this->_realpath) as $fileName) {
                if (!in_array($fileName, array('.','..'))) {
                    $files[] = new File($basePath.DIRECTORY_SEPARATOR.$fileName);
                }
            }

            return $files;
        }

        return false;
    }

    public function getExtension()
    {
        return pathinfo($this->getFilename(), PATHINFO_EXTENSION);
    }

    public function getMimeType()
    {
        return $this->getFinfoMimeType()->file($this->_realpath);
    }

    public function getMimeEncoding()
    {
        return $this->getFinfoMimeEncoding()->file($this->_realpath);
    }

    public function getMime()
    {
        return $this->getFinfoMime()->file($this->_realpath);
    }

    private function getFinfoMime()
    {
        if (!$this->_finfo_mime) {
            $this->_finfo_mime = new \finfo(FILEINFO_MIME);
        }

        return $this->_finfo_mime;
    }

    private function getFinfoMimeType()
    {
        if (!$this->_finfo_mime_type) {
            $this->_finfo_mime_type = new \finfo(FILEINFO_MIME_TYPE);
        }

        return $this->_finfo_mime_type;
    }

    private function getFinfoMimeEncoding()
    {
        if (!$this->_finfo_mime_encoding) {
            $this->_finfo_mime_encoding = new \finfo(FILEINFO_MIME_ENCODING);
        }

        return $this->_finfo_mime_encoding;
    }
}
