<?php
require_once 'jira-common.php'; //request settings
require_once 'jira-password.php'; //get jiraPassword

$includePaths = array(
    get_include_path(),
    realpath(__DIR__ . '/../jira-api-restclient/src'),
);
set_include_path(implode(PATH_SEPARATOR, $includePaths));

spl_autoload_register('JigitAutoload::mageAutoload');

/**
 * Class JiraAutoload
 */
class JigitAutoload
{
    /**
     * Autoload
     *
     * @param string $class
     * @return void
     */
    static public function mageAutoload($class)
    {
        $file = str_replace('_', '/', $class) . '.php';
        $file = str_replace('\\', '/', $file);
        $file = str_replace('chobie/', '/', $file); //chobie namespace doesn't exist in the path
        require_once $file;
    }
}

/**
 * Output collector class
 */
class JigitOutput
{
    /**
     * Output rows
     *
     * @var array
     */
    protected $_output = array();

    /**
     * Output line delimiter
     *
     * @var string
     */
    protected $_outputDelimiter = PHP_EOL;

    /**
     * Decorator mode
     *
     * @var bool
     */
    protected $_decoratorOn = false;

    /**
     * Decorator spaced mode
     *
     * @var bool
     */
    protected $_decoratorSpaced = false;

    /**
     * Decorator block with
     *
     * @var int
     */
    protected $_decoratorWidth = 80;

    /**
     * Decorator symbol
     *
     * @var string
     */
    protected $_decoratorSymbol = '=';

    /**
     * Decorator symbol
     *
     * @var string
     */
    protected $_decoratorDelimiterSymbol = '-';

    /**
     * Add output row
     *
     * @param string $content       Content string
     * @param string $delimiter     Delimiter of a multi-row value
     * @return $this
     */
    public function add($content, $delimiter = PHP_EOL)
    {
        $contents = explode($delimiter, $content);
        foreach ($contents as $content) {
            if ($this->_decoratorOn) {
                $length = mb_strlen($content);
                if (!$this->_decoratorSpaced) {
                    $internalSymbol = $this->_decoratorDelimiterSymbol;
                    $this->_output[] =
                        $this->_decoratorSymbol
                        . str_repeat($internalSymbol, max(floor(($this->_decoratorWidth - $length) / 2) - 2, 0))
                        . ' ' . $content . ' '
                        . str_repeat($internalSymbol, max(ceil(($this->_decoratorWidth - $length) / 2) - 2, 0))
                        . $this->_decoratorSymbol;
                } else {
                    $this->_output[] =
                        $this->_decoratorSymbol . ' ' . $content . ' '
                        . str_repeat(' ', max(($this->_decoratorWidth - $length) - 5, 0)) . ' '
                        . $this->_decoratorSymbol;
                }
            } else {
                $this->_output[] = (string) $content;
            }
        }

        return $this;
    }

    /**
     * Enable decorate mode
     *
     * @param bool $spaced
     * @return $this
     */
    public function enableDecorator($spaced = false)
    {
        if ($this->_decoratorOn) {
            return $this;
        }
        $row                    = str_repeat($this->_decoratorSymbol, $this->_decoratorWidth);
        $this->_output[]        = $row;
        $this->_decoratorOn     = true;
        $this->_decoratorSpaced = (bool)$spaced;
        return $this;
    }

    /**
     * Add decorated delimiter
     *
     * @return $this
     */
    public function addDelimiter()
    {
        if ($this->_decoratorOn) {
            $row = $this->_decoratorSymbol
                . str_repeat($this->_decoratorDelimiterSymbol, $this->_decoratorWidth - 2)
                . $this->_decoratorSymbol;
        } else {
            $row = str_repeat($this->_decoratorDelimiterSymbol, $this->_decoratorWidth);
        }
        $this->_output[] = $row;
        return $this;
    }

    /**
     * Disable decorate mode
     *
     * @return $this
     */
    public function disableDecorator()
    {
        if (!$this->_decoratorOn) {
            return $this;
        }
        $row                    = str_repeat($this->_decoratorSymbol, $this->_decoratorWidth);
        $this->_output[]        = $row;
        $this->_decoratorOn     = false;
        $this->_decoratorSpaced = false;
        return $this;
    }

    /**
     * Get output string
     *
     * @return string
     */
    public function getOutputString()
    {
        return implode($this->_outputDelimiter, $this->_output) . $this->_outputDelimiter;
    }
}

/**
 * Class with JQL query types
 */
class JiraJql
{
    /**#@+
     * Query types
     */
    const TYPE_NOT_AFFECTS_CODE                     = 'notAffectsCodeWithFixVersion';
    const TYPE_WITHOUT_FIX_VERSION                  = 'inBranchWithoutFixVersion';
    const TYPE_WITHOUT_AFFECTED_VERSION             = 'inBranchWithoutAffectedVersion';
    const TYPE_WITHOUT_OPEN_FOR_IN_PROGRESS_VERSION = 'inBranchWithoutFixVersionNotDone';
    /**#@-*/
}

/**
 * Class JIRA keys formatter
 */
class JiraKeysFormatter
{
    /**
     * @param string $keys
     * @param int    $count     Number keys in line
     * @param string $delimiter
     * @return mixed
     */
    static public function format($keys, $count = 4, $delimiter = PHP_EOL)
    {
        return preg_replace('/(([A-Za-z-0-9]+,\s*){' . $count . '})/', '$1' . $delimiter, $keys);
    }
}
