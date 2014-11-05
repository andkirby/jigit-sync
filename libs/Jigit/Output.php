<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 12.08.2014
 * Time: 1:47
 */
namespace Jigit;

/**
 * Class Output
 *
 * @package Jigit
 */
class Output
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
        if (is_array($content)) {
            $contents = $content;
        } else {
            $contents = explode($delimiter, $content);
        }
        foreach ($contents as $content) {
            $content = $this->_decorateContent($content);
            $this->_output[] = (string) $content;
        }
        return $this;
    }

    /**
     * Enable decorate mode
     *
     * @param bool $spaced
     * @param bool $skipDelimiter
     * @return $this
     */
    public function enableDecorator($spaced = false, $skipDelimiter = false)
    {
        if ($this->_decoratorOn) {
            return $this;
        }
        if (!$skipDelimiter) {
            $row                    = str_repeat($this->_decoratorSymbol, $this->_decoratorWidth);
            $this->_output[]        = $row;
        }
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

    /**
     * Decorate content
     *
     * @param string $content
     * @return string
     */
    protected function _decorateContent($content)
    {
        $shift  = $this->_decoratorOn ? 4 : 0;
        $length = mb_strlen($content) + $shift;
        if ($length > $this->_decoratorWidth) {
            $content = wordwrap($content, $this->_decoratorWidth - $shift);
            $output  = '';
            foreach (explode(PHP_EOL, $content) as $line) {
                $output .= $this->_decorateRow($line);
            }
        } else {
            $output = $this->_decorateRow($content);
        }
        return $output;
    }

    /**
     * Decorate content string
     *
     * @param string $content
     * @return string
     */
    protected function _decorateRow($content)
    {
        if ($this->_decoratorOn) {
            $length = mb_strlen($content);
            if (!$this->_decoratorSpaced) {
                $internalSymbol = $this->_decoratorDelimiterSymbol;
                $output = $this->_decoratorSymbol
                    . str_repeat($internalSymbol, max(floor(($this->_decoratorWidth - $length) / 2) - 2, 0))
                    . ' ' . $content . ' '
                    . str_repeat($internalSymbol, max(ceil(($this->_decoratorWidth - $length) / 2) - 2, 0))
                    . $this->_decoratorSymbol;
            } else {
                $output = $this->_decoratorSymbol . ' ' . $content . ' '
                    . str_repeat(' ', max(($this->_decoratorWidth - $length) - 5, 0)) . ' '
                    . $this->_decoratorSymbol;
            }
        } else {
            $output = (string)$content;
        }
        return $output;
    }

    /**
     * Get output string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getOutputString();
    }

    /**
     * Get output row delimiter
     *
     * @return string
     */
    public function getOutputDelimiter()
    {
        return $this->_outputDelimiter;
    }

    /**
     * Set output row delimiter
     *
     * @param string $outputDelimiter
     * @return $this
     */
    public function setOutputDelimiter($outputDelimiter)
    {
        $this->_outputDelimiter = $outputDelimiter;
        return $this;
    }
}
