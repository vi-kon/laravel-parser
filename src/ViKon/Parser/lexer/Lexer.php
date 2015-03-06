<?php

namespace ViKon\Parser\Lexer;

use ViKon\Parser\LexerException;
use ViKon\Parser\Parser;
use ViKon\Parser\TokenList;

/**
 * Class Lexer
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser\Lexer
 */
class Lexer {
    const RULE_EXIT = '::exit';
    const SINGLE_RULE_PREFIX = ':';

    const STATE_ENTER = 0;
    const STATE_EXIT = 1;
    const STATE_SINGLE = 2;
    const STATE_MATCHED = 3;
    const STATE_UNMATCHED = 4;
    const STATE_END = 5;

    /** @var \ViKon\Parser\Lexer\LexerPattern[] */
    private $patterns = [];

    /** @var \ViKon\Parser\Parser|null */
    private $parser = null;

    /** @var \ViKon\Parser\Lexer\LexerStack */
    private $stack = [];

    /**
     * Set parser
     *
     * @param \ViKon\Parser\Parser $parser parser
     *
     * @return $this
     */
    public function setParser(Parser $parser) {
        $this->parser = $parser;

        return $this;
    }

    /**
     * Add entry pattern. If pattern match switch to next rule
     *
     * @param string $pattern       regexp pattern
     * @param string $ruleName      rule name
     * @param string $childRuleName child rule name (next rule)
     *
     * @return \ViKon\Parser\Lexer\Lexer
     */
    public function addEntryPattern($pattern, $ruleName, $childRuleName) {
        return $this->addPattern($pattern, $ruleName, $childRuleName);
    }

    /**
     * Exit from current rule to his parent
     *
     * @param string $pattern  regexp pattern
     * @param string $ruleName rule name
     *
     * @return $this
     */
    public function addExitPattern($pattern, $ruleName) {
        return $this->addPattern($pattern, $ruleName, self::RULE_EXIT);
    }

    /**
     * Add single pattern. If pattern match change to provided rule and immediately back
     *
     * @param string $pattern       regexp pattern
     * @param string $ruleName      rule name
     * @param string $childRuleName child rule name (next rule)
     *
     * @return \ViKon\Parser\Lexer\Lexer
     */
    public function addSinglePattern($pattern, $ruleName, $childRuleName) {
        return $this->addPattern($pattern, $ruleName, self::SINGLE_RULE_PREFIX . $childRuleName);
    }

    /**
     * Add simple pattern. No rule change
     *
     * @param string $pattern  regexp pattern
     * @param string $ruleName rule name
     *
     * @return \ViKon\Parser\Lexer\Lexer
     */
    public function addSimplePattern($pattern, $ruleName) {
        return $this->addPattern($pattern, $ruleName);
    }

    /**
     * @param string                  $text          input text
     * @param string                  $startRuleName start rule name for tokenization
     * @param \ViKon\Parser\TokenList $tokenList     already initialized token list
     *
     * @throws \ViKon\Parser\LexerException
     * @return \ViKon\Parser\TokenList
     */
    public function tokenize($text, $startRuleName, TokenList $tokenList = null) {
        if ($tokenList === null) {
            $tokenList = new TokenList();
        }

        $currentPosition = 0;
        $initialLength = strlen($text);

        $this->stack = new LexerStack($startRuleName);

        while (is_array($parsed = $this->reduce($text))) {
            list($unmatched, $matched, $ruleName) = $parsed;

            $remainingLength = strlen($text);
            $matchedPosition = $initialLength - $remainingLength - strlen($matched);

            $this->callParser($unmatched, $currentPosition, self::STATE_UNMATCHED, $tokenList);

            if ($ruleName === self::RULE_EXIT) {
                $this->callParser($matched, $matchedPosition, self::STATE_EXIT, $tokenList);
                if (!$this->stack->pop()) {
                    throw new LexerException('Stack is empty after pop');
                }
            } else if (strpos($ruleName, self::SINGLE_RULE_PREFIX) === 0) {
                $this->stack->push(substr($ruleName, strlen(self::SINGLE_RULE_PREFIX)));
                $this->callParser($matched, $matchedPosition, self::STATE_SINGLE, $tokenList);

                if (!$this->stack->pop()) {
                    throw new LexerException('Stack is empty after pop');
                }
            } else if ($ruleName !== null) {
                $this->stack->push($ruleName);
                $this->callParser($matched, $matchedPosition, self::STATE_ENTER, $tokenList);
            } else {
                $this->callParser($matched, $matchedPosition, self::STATE_MATCHED, $tokenList);
            }

            $currentPosition = $initialLength - $remainingLength;
        }

        $this->callParser($text, $currentPosition, self::STATE_END, $tokenList);

        return $tokenList;
    }

    /**
     * Add pattern to rule patterns
     *
     * @param string      $pattern       regex pattern
     * @param string      $ruleName      rule name
     * @param string|null $childRuleName child rule name
     *
     * @return $this
     */
    protected function addPattern($pattern, $ruleName, $childRuleName = null) {
        if (!isset($this->patterns[$ruleName])) {
            $this->patterns[$ruleName] = new LexerPattern($ruleName);
        }
        $this->patterns[$ruleName]->addPattern($pattern, $childRuleName);

        return $this;
    }

    /**
     * Reduce input text length by patterns match
     *
     * @param string $text
     *
     * @return array|bool FALSE if no valid pattern found, TRUE if input text is empty or split is not succeed,
     *                    otherwise array with unmatched, matched part of text and used patterns rule name
     */
    protected function reduce(&$text) {
        if (!array_key_exists($this->stack->top(), $this->patterns)) {
            return false;
        }
        if ($text !== '' && ($data = $this->patterns[$this->stack->top()]->split($text)) !== false) {
            list($unmatched, $matched, $text, $childRuleName) = $data;

            return [$unmatched, $matched, $childRuleName];
        }

        return true;
    }

    /**
     * @param string                  $content  matched content
     * @param int                     $position match position
     * @param string                  $state    matched state (entry, exit, single, matched, unmatched)
     * @param \ViKon\Parser\TokenList $tokenList
     */
    protected function callParser($content, $position, $state, TokenList $tokenList) {
        if ($content === '' && ($state === self::STATE_MATCHED || $state === self::STATE_UNMATCHED)) {
            return;
        }

        $ruleName = $this->stack->top();

        $this->parser->parseToken($ruleName, $content, $position, $state, $tokenList);
    }
}