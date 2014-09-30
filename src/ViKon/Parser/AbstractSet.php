<?php


namespace ViKon\Parser;

use ViKon\Parser\lexer\Lexer;
use ViKon\Parser\renderer\AbstractRuleRenderer;
use ViKon\Parser\renderer\Renderer;
use ViKon\Parser\rule\AbstractRule;

abstract class AbstractSet
{
    const CATEGORY_NONE = 0;
    const CATEGORY_BLOCK = 1;
    const CATEGORY_FORMAT = 2;
    const CATEGORY_SINGLE = 3;

    /** @var \ViKon\Parser\rule\AbstractRule|null */
    protected $startRule = null;

    /** @var \ViKon\Parser\rule\AbstractRule[] rules */
    protected $rules = array();

    /** @var \ViKon\Parser\renderer\AbstractRuleRenderer[] */
    protected $ruleRenderers = array();

    /** @var \ViKon\Parser\rule\AbstractRule[][] rules by categories */
    protected $categories = array();

    /**
     * @param \ViKon\Parser\Parser                 $parser
     * @param \ViKon\Parser\lexer\Lexer            $lexer
     * @param \ViKon\Parser\renderer\Renderer|null $renderer
     *
     * @throws \ViKon\Parser\ParserException
     */
    public function init(Parser $parser, Lexer $lexer, Renderer $renderer = null)
    {
        $parser->setLexer($lexer);
        $parser->setStartRule($this->startRule);
        foreach ($this->rules as $rule)
        {
            $parser->addRule($rule);
        }
        if ($renderer !== null)
        {
            $parser->setRenderer($renderer);
            foreach ($this->ruleRenderers as $skin => $ruleRenderers)
            {
                foreach ($ruleRenderers as $ruleRenderer)
                {
                    $renderer->addRuleRenderer($ruleRenderer, $skin);
                }
            }
        }

        $lexer->setParser($parser);
    }

    /**
     * @return \ViKon\Parser\rule\AbstractRule[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Get rules by category
     *
     * @param int $name category identifier
     *
     * @return \ViKon\Parser\rule\AbstractRule[]
     */
    public function getRulesByCategory($name)
    {
        if (array_key_exists($name, $this->categories))
        {
            return $this->categories[$name];
        }

        return array();
    }

    /**
     * Get rule names by category
     *
     * @param int      $name   category identifier
     * @param string[] $except array with rule names (exception list)
     *
     * @return rule\AbstractRule[]
     */
    public function getRuleNamesByCategory($name, array $except = array())
    {
        $rules = $this->getRulesByCategory($name);

        foreach ($rules as $key => &$rule)
        {
            $rule = $rule->getName();

            if (in_array($rule, $except))
            {
                unset($rules[$key]);
            }
        }

        return $rules;
    }

    /**
     * Set start rule
     *
     * @param \ViKon\Parser\rule\AbstractRule $rule     rule
     * @param int                             $category category identifier
     *
     * @return $this
     */
    protected function setStartRule(AbstractRule $rule, $category)
    {
        $this->startRule               = $rule;
        $this->rules[]                 = $rule;
        $this->categories[$category][] = $rule;

        return $this;
    }

    /**
     * Add rule
     *
     * @param \ViKon\Parser\rule\AbstractRule $rule     rule
     * @param int                             $category category identifier
     *
     * @return $this
     */
    protected function addRule(AbstractRule $rule, $category)
    {
        $this->rules[]                 = $rule;
        $this->categories[$category][] = $rule;

        return $this;
    }

    /**
     * @param \ViKon\Parser\renderer\AbstractRuleRenderer $ruleRenderer rule renderer
     */
    protected function addRuleRender(AbstractRuleRenderer $ruleRenderer)
    {
        $this->ruleRenderers[$ruleRenderer->getSkin()][] = $ruleRenderer;
    }
}