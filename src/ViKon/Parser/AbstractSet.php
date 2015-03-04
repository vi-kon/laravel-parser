<?php


namespace ViKon\Parser;

use ViKon\Parser\Lexer\Lexer;
use ViKon\Parser\Renderer\AbstractRuleRenderer;
use ViKon\Parser\Renderer\Renderer;
use ViKon\Parser\Rule\AbstractRule;

/**
 * Class AbstractSet
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser
 */
abstract class AbstractSet {
    const CATEGORY_NONE = 0;
    const CATEGORY_BLOCK = 1;
    const CATEGORY_FORMAT = 2;
    const CATEGORY_SINGLE = 3;

    /** @var \ViKon\Parser\Rule\AbstractRule|null */
    protected $startRule = null;

    /** @var \ViKon\Parser\Rule\AbstractRule[] rules */
    protected $rules = [];

    /** @var \ViKon\Parser\Renderer\AbstractRuleRenderer[] */
    protected $ruleRenderers = [];

    /** @var \ViKon\Parser\Rule\AbstractRule[][] rules by categories */
    protected $categories = [];

    /**
     * @param \ViKon\Parser\Parser                 $parser
     * @param \ViKon\Parser\Lexer\Lexer            $lexer
     * @param \ViKon\Parser\Renderer\Renderer|null $renderer
     *
     * @throws \ViKon\Parser\ParserException
     */
    public function init(Parser $parser, Lexer $lexer, Renderer $renderer = null) {
        $parser->setLexer($lexer);
        $parser->setStartRule($this->startRule);
        foreach ($this->rules as $rule) {
            $parser->addRule($rule);
        }
        if ($renderer !== null) {
            $parser->setRenderer($renderer);
            foreach ($this->ruleRenderers as $skin => $ruleRenderers) {
                foreach ($ruleRenderers as $ruleRenderer) {
                    $renderer->registerRuleRenderer($ruleRenderer, $skin);
                }
            }
        }

        $lexer->setParser($parser);
    }

    /**
     * @return \ViKon\Parser\Rule\AbstractRule[]
     */
    public function getRules() {
        return $this->rules;
    }

    /**
     * Get rules by category
     *
     * @param int $name category identifier
     *
     * @return \ViKon\Parser\Rule\AbstractRule[]
     */
    public function getRulesByCategory($name) {
        if (array_key_exists($name, $this->categories)) {
            return $this->categories[$name];
        }

        return [];
    }

    /**
     * Get rule names by category
     *
     * @param int      $name   category identifier
     * @param string[] $except array with rule names (exception list)
     *
     * @return \ViKon\Parser\Rule\AbstractRule[]
     */
    public function getRuleNamesByCategory($name, array $except = []) {
        $rules = $this->getRulesByCategory($name);

        foreach ($rules as $key => &$rule) {
            $rule = $rule->getName();

            if (in_array($rule, $except)) {
                unset($rules[$key]);
            }
        }

        return $rules;
    }

    /**
     * Set start Rule
     *
     * @param \ViKon\Parser\Rule\AbstractRule $rule     Rule
     * @param int                             $category category identifier
     *
     * @return $this
     */
    protected function setStartRule(AbstractRule $rule, $category) {
        $this->startRule = $rule;
        $this->rules[] = $rule;
        $this->categories[$category][] = $rule;

        return $this;
    }

    /**
     * Add Rule
     *
     * @param \ViKon\Parser\Rule\AbstractRule $rule     Rule
     * @param int                             $category category identifier
     *
     * @return $this
     */
    protected function addRule(AbstractRule $rule, $category) {
        $this->rules[] = $rule;
        $this->categories[$category][] = $rule;

        return $this;
    }

    /**
     * @param \ViKon\Parser\Renderer\AbstractRuleRenderer $ruleRenderer Rule renderer
     */
    protected function addRuleRender(AbstractRuleRenderer $ruleRenderer) {
        $this->ruleRenderers[$ruleRenderer->getSkin()][] = $ruleRenderer;
    }
}