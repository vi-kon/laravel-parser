<?php

namespace ViKon\Parser\Rule;

use ViKon\Parser\Lexer\Lexer;
use ViKon\Parser\Parser;
use ViKon\Parser\ParserException;

/**
 * Class AbstractRuleSet
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser
 */
abstract class AbstractRuleSet {
    const CATEGORY_NONE = 'NONE';
    const CATEGORY_BLOCK = 'BLOCK';
    const CATEGORY_FORMAT = 'FORMAT';
    const CATEGORY_SINGLE = 'SINGLE';

    /** @var \ViKon\Parser\Rule\AbstractRule|null */
    protected $startRule = null;

    /** @var \ViKon\Parser\Rule\AbstractRule[] */
    protected $rules = [];

    /** @var \ViKon\Parser\Rule\AbstractRule[][] */
    protected $categories = [
        self::CATEGORY_NONE   => [],
        self::CATEGORY_BLOCK  => [],
        self::CATEGORY_FORMAT => [],
        self::CATEGORY_SINGLE => [],
    ];

    /**
     * @return \ViKon\Parser\Rule\AbstractRule[]
     */
    public function getRules() {
        return $this->rules;
    }

    /**
     * Get rule names by category
     *
     * @param string   $name   category name
     * @param string[] $except array with rule names (exception list)
     *
     * @return \ViKon\Parser\Rule\AbstractRule[]
     *
     * @throws \ViKon\Parser\ParserException
     */
    public function getRulesByCategory($name, array $except = []) {
        if (!isset($this->categories[$name])) {
            throw new ParserException('Category with ' . $name . ' not found');
        }

        if (count($except) === 0) {
            return $this->categories[$name];
        }

        $rules = [];
        foreach ($this->categories[$name] as $rule) {
            if (!in_array($rule->getName(), $except)) {
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    /**
     * Get rule names by category
     *
     * @param string $name   category name
     * @param array  $except array with rule names (exception list)
     *
     * @return string[]
     *
     * @throws \ViKon\Parser\ParserException
     */
    public function getRuleNamesByCategory($name, array $except = []) {
        if (!isset($this->categories[$name])) {
            throw new ParserException('Category with ' . $name . ' not found');
        }

        $ruleNames = [];

        foreach ($this->categories[$name] as $rule) {
            if (!in_array($rule->getName(), $except)) {
                $ruleNames[] = $rule->getName();
            }
        }

        return $ruleNames;
    }

    /**
     * @return null|\ViKon\Parser\Rule\AbstractRule
     */
    public function getStartRule() {
        return $this->startRule;
    }

    /**
     * @param \ViKon\Parser\Rule\AbstractRule $startRule
     *
     * @return $this
     */
    protected function setStartRule(AbstractRule $startRule) {
        $this->startRule = $startRule;

        return $this->addRule($startRule);
    }

    /**
     * Set start rule and add set's rules to parser
     *
     * @param \ViKon\Parser\Parser      $parser
     * @param \ViKon\Parser\Lexer\Lexer $lexer
     *
     * @return $this
     * @throws \ViKon\Parser\ParserException
     */
    public function init(Parser $parser, Lexer $lexer) {
        $parser->setStartRule($this->startRule);

        foreach ($this->rules as $rule) {
            $parser->addRule($rule);
        }

        $parser->setLexer($lexer);
        $lexer->setParser($parser);

        return $this;
    }

    /**
     * Add new rule to set
     *
     * @param \ViKon\Parser\Rule\AbstractRule $rule
     * @param string                          $category
     *
     * @return $this
     */
    protected function addRule(AbstractRule $rule, $category = self::CATEGORY_NONE) {
        $rule->setRuleSet($this);

        $this->rules[] = $rule;
        $this->categories[$category][] = $rule;

        return $this;
    }
}