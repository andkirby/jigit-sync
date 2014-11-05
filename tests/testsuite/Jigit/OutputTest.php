<?php
namespace Jigit;

/**
 * Class OutputTest
 *
 * @package Jigit
 */
class OutputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test decorated output
     */
    public function testDecoratedOutput()
    {
        $test = new Output();
        $test->enableDecorator();
        $test->add('Some content');
        $test->disableDecorator();
        //@startSkipCommitHooks
        $expected = <<<EXP
================================================================================
=-------------------------------- Some content --------------------------------=
================================================================================

EXP;
        //@finishSkipCommitHooks
        $test->setOutputDelimiter("\n");
        $this->assertEquals($expected, $test->getOutputString());
    }
}
