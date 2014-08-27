<?php


namespace ViKon\Parser;

use ViKon\Parser\rule\AbstractRule;

class AbstractRuleSet
{
    private $startRuleName;

    /** @var AbstractRule[] */
    private $rules = array();

    /** @var AbstractNodeRenderer[] */
    private $nodeRenderers = array();

    /**
     * @param string $startRuleName
     */
    public function __construct($startRuleName)
    {
        $this->startRuleName = $startRuleName;
    }

    /**
     * @param AbstractRule $rule
     */
    protected function addRule(AbstractRule $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * @param AbstractNodeRenderer $nodeRenderer
     * @param string                $setName
     */
    protected function addTokenRenderer(AbstractNodeRenderer $nodeRenderer, $setName = 'default')
    {
        $this->nodeRenderers[$setName][] = $nodeRenderer;
    }

    /**
     * Add selected TokenRenderer set to Renderer
     *
     * @param Renderer $renderer
     * @param string   $setName
     *
     * @return bool
     */
    public function setupRenderer(Renderer $renderer, $setName = 'default')
    {
        if (!isset($this->nodeRenderers[$setName]))
        {
            return false;
        }

        foreach ($this->nodeRenderers[$setName] as $nodeRenderer)
        {
            $renderer->addTokenRenderer($nodeRenderer);
        }

        return true;
    }

    /**
     * Add rules to ViKon\Parser
     *
     * @param Parser $parser
     *
     * @throws ParserException
     */
    public function setupParser(Parser $parser)
    {
        $parser->getLexer()
               ->setStartRule($this->startRuleName);

        foreach ($this->rules as $rule)
        {
            $parser->addRule($rule);
        }
    }
}