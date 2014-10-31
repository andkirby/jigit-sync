<?php
namespace Lib;

/**
 * Class Record to work with ini file
 */
class Record
{
    /**
     * Status of behaviour when target file not found
     *
     * If TRUE create file automatically
     * If FALSE throw an exception
     *
     * @var bool
     */
    protected $_createFile = true;

    /**
     * Relative path to directory of work file
     *
     * @var string
     */
    protected $_dir;

    /**
     * Data from file
     *
     * @var array
     */
    protected $_data;

    /**
     * Filename
     *
     * @var string
     */
    protected $_filename;

    /**
     * Set options
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->_dir = isset($options['dir']) ? $options['dir'] : 'var';
        if (isset($options['filename'])) {
            $this->setFilename($options['filename']);
        }
        if (isset($options['createFile'])) {
            $this->_createFile = (bool)$options['createFile'];
        }
    }

    /**
     * Get base dir
     */
    public function getBaseDir()
    {
        return APP_ROOT . DIRECTORY_SEPARATOR . trim($this->_dir, '\\/');
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return $this
     * @throws Exception
     */
    public function setFilename($filename)
    {
        $this->_filename = $filename;
        return $this;
    }

    /**
     * Get path to file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->getBaseDir() . DIRECTORY_SEPARATOR . $this->_filename;
    }

    /**
     * Get data from file
     *
     * @param string|null $section
     * @param string|null $key
     * @return array|string
     */
    public function getData($section = null, $key = null)
    {
        $this->_read();
        if (!$section) {
            return $this->_data;
        } elseif (!$key) {
            return isset($this->_data[$section]) ? $this->_data[$section] : null;
        } else {
            return isset($this->_data[$section][$key]) ? $this->_data[$section][$key] : null;
        }
    }

    /**
     * Get data from file
     *
     * @param string       $section
     * @param string|array $key
     * @param string       $value
     * @param int|bool|null         $index
     * @throws Exception
     * @return array
     */
    public function setData($section, $key = null, $value = null, $index = null)
    {
        $this->_read();
        if (is_array($section)) {
            $this->_data = $section;
        } elseif (is_array($key)) {
            $this->_data[$section] = $key;
        } else {
            if ($index === true) {
                $this->_data[$section][$key][] = $value;
            } elseif ($index) {
                $this->_data[$section][$key][$index] = $value;
            } else {
                $this->_data[$section][$key] = $value;
            }
        }
        $this->_write();
        return $this;
    }

    /**
     * Read file
     *
     * @throws Exception
     * @return $this
     */
    protected function _read()
    {
        if (null === $this->_data) {
            if (!$this->_isFileExists()) {
                $this->_data = array();
            } else {
                $this->_data = parse_ini_file($this->getFile(), true);
            }
        }
        return $this;
    }

    /**
     * Check file exists
     *
     * @return bool
     * @throws Exception
     */
    protected function _isFileExists()
    {
        if (!is_file($this->getFile())) {
            if ($this->_createFile) {
                return false;
            }
            throw new Exception("File '{$this->getFile()}' not found.");
        }
        return true;
    }

    /**
     * Write data to file
     *
     * @param bool $hasSections
     * @return int
     * @throws Exception
     */
    protected function _write($hasSections = true)
    {
        $content = '';
        $data = $this->_data;
        if ($hasSections) {
            foreach ($data as $key => $elem) {
                $content .= '[' . $key . "]\n";
                foreach ($elem as $key2 => $elem2) {
                    if (is_array($elem2)) {
                        for ($i = 0; $i < count($elem2); $i++) {
                            $content .= $key2 . "[] = \"" . addslashes($elem2[$i]) . "\"\n";
                        }
                    } else if ($elem2 == '') {
                        $content .= $key2 . " = \n";
                    } else {
                        $content .= $key2 . " = \"" . addslashes($elem2) . "\"\n";
                    }
                }
            }
        } else {
            foreach ($data as $key => $elem) {
                if (is_array($elem)) {
                    for ($i = 0; $i < count($elem); $i++) {
                        $content .= $key . '[] = "' . $elem[$i] . "\"\n";
                    }
                } else if ($elem == '') {
                    $content .= $key . " = \n";
                } else {
                    $content .= $key . ' = "' . $elem . "\"\n";
                }
            }
        }

        $handle = fopen($this->getFile(), 'w');
        if (!$handle) {
            throw new Exception("Cannot write to file '{$this->getFile()}'.");
        }

        $success = fwrite($handle, $content);
        fclose($handle);

        return $success;
    }
}

