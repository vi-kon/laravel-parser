<?php

namespace ViKon\Parser;

use ViKon\Parser\lexer\Lexer;
use ViKon\Parser\renderer\Renderer;
use ViKon\Parser\rule\AbstractRule;

/**
 * Class Parser
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser
 */
class Parser {
    /** @var \ViKon\Parser\rule\AbstractRule|null */
    private $startRule = null;

    /** @var \ViKon\Parser\rule\AbstractRule[] */
    private $rules = [];

    /** @var \ViKon\Parser\lexer\Lexer|null */
    private $lexer = null;

    /** @var \ViKon\Parser\renderer\Renderer|null */
    private $renderer = null;

    /**
     * @param \ViKon\Parser\lexer\Lexer $lexer
     *
     * @return $this
     */
    public function setLexer(Lexer $lexer) {
        $this->lexer = $lexer;

        return $this;
    }

    /**
     * @param \ViKon\Parser\renderer\Renderer $renderer
     */
    public function setRenderer(Renderer $renderer) {
        $this->renderer = $renderer;
    }

    /**
     * Set start rule for parser
     *
     * @param \ViKon\Parser\rule\AbstractRule $rule rule instance
     *
     * @return $this
     */
    public function setStartRule(AbstractRule $rule) {
        $this->startRule = $rule;

        return $this;
    }

    /**
     * Add rule to parser
     *
     * @param \ViKon\Parser\rule\AbstractRule $rule rule instance
     *
     * @throws \ViKon\Parser\ParserException throws if rule already exists with same name
     *
     * @return $this
     */
    public function addRule(AbstractRule $rule) {
        if (array_key_exists($rule->getName(), $this->rules)) {
            throw new ParserException('Rule with ' . $rule->getName() . ' name already exists');
        }

        $this->rules[$rule->getName()] = $rule;

        return $this;
    }

    /**
     * Parse text by provided rules
     *
     * @param string    $text      raw data
     * @param TokenList $tokenList already initialized token list
     * @param bool      $recursive call is recursive or not
     *
     * @throws LexerException
     * @throws ParserException
     * @return \ViKon\Parser\TokenList
     */
    public function parse($text, TokenList $tokenList = null, $recursive = false) {
        if ($this->startRule === null) {
            throw new ParserException('Start rule not set');
        }

        if ($this->lexer === null) {
            throw new ParserException('Lexer not set');
        }

        uasort($this->rules, [$this, 'sortRulesByOrder']);

        $this->connectRulesToLexer();

        \Event::fire('vikon.parser.before.parse', [&$text]);

        $tokenList = $this->lexer->tokenize($text, $this->startRule->getName(), $tokenList);

        if ($recursive === false) {
            foreach ($this->rules as $rule) {
                $rule->finalize($tokenList);
            }
        }

        return $tokenList;
    }

    /**
     * Parse text by provided rules and try to render token list
     *
     * @param string $text raw data
     * @param string $skin used skin
     *
     * @throws \ViKon\Parser\ParserException
     *
     * @return string
     */
    public function render($text, $skin) {
        if ($this->renderer === null) {
            throw new ParserException('Renderer not set');
        }

        $tokenList = $this->parse($text);

        return $this->renderer->render($tokenList, $skin);
    }

    /**
     * Parse token
     *
     * @param string                  $ruleName  rule name
     * @param string                  $content   token content
     * @param int                     $position  token position in raw data
     * @param int                     $state     token found state
     * @param \ViKon\Parser\TokenList $tokenList token list instance
     *
     * @throws \ViKon\Parser\ParserException
     * @return bool
     */
    public function parseToken($ruleName, $content, $position, $state, TokenList $tokenList) {
        if (!isset($this->rules[$ruleName])) {
            throw new ParserException('Rule with name "' . $ruleName . '" not found');
        }

        $this->rules[$ruleName]->parseToken($content, $position, $state, $tokenList);
    }

    /**
     * Add rule patterns to lexer
     */
    protected function connectRulesToLexer() {
        foreach ($this->rules as $childRule) {
            $childRule->prepare($this->lexer);

            if ($childRule->getName() === $this->startRule->getName()) {
                continue;
            }

            if ($this->startRule->acceptRule($childRule->getName())) {
                $childRule->embedInto($this->startRule->getName(), $this->lexer);
            }

            $childRule->finish($this->lexer);
        }
    }

    /**
     * Sort rules by order ASC
     *
     * @param \ViKon\Parser\rule\AbstractRule $a
     * @param \ViKon\Parser\rule\AbstractRule $b
     *
     * @return int
     */
    protected function sortRulesByOrder(AbstractRule $a, AbstractRule $b) {
        if ($a->getOrder() == $b->getOrder()) {
            return 0;
        }

        return $a->getOrder() < $b->getOrder()
            ? -1
            : 1;
    }
}