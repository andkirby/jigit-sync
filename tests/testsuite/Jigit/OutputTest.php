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
    public function testDecoratedOutputOneLine()
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
    /**
     * Test decorated output
     */
    public function testDecoratedOutputLongLine()
    {
        $test = new Output();
        $test->enableDecorator();
        $test->add('Some content long text long text long text long text long text long text long text long text');
        $test->disableDecorator();
        //@startSkipCommitHooks
        $expected = <<<EXP
================================================================================
=-- Some content long text long text long text long text long text long text --=
=---------------------------- long text long text -----------------------------=
================================================================================

EXP;
        //@finishSkipCommitHooks
        $test->setOutputDelimiter("\n");
        $this->assertEquals($expected, $test->getOutputString());
    }
}
