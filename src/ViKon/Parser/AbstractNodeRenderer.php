<?php


namespace ViKon\Parser;

abstract class AbstractNodeRenderer
{
    /** @var null|AbstractRenderer */
    protected $renderer = null;

    /**
     * Set renderer
     *
     * @param AbstractRenderer $renderer
     */
    public function setRenderer(AbstractRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function preProcessSyntaxTree(SyntaxTree $syntaxTree)
    {
    }

    public function postProcessOutput(array &$outputList)
    {
    }

    public abstract function register();

    public function reset()
    {
    }
}