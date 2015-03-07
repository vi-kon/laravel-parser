<?php

namespace ViKon\Parser\Rule;

use ViKon\Parser\AbstractSet;
use ViKon\Parser\Lexer\Lexer;
use ViKon\Parser\Parser;
use ViKon\Parser\TokenList;

/**
 * Class AbstractRule
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser\Rule
 */
abstract class AbstractRule {
    /** @var string */
    protected $name;

    /** @var int */
    protected $order;

    /** @var string[] */
    protected $acceptedRuleNames = [];

    /** @var \ViKon\Parser\AbstractSet */
    protected $set;

    /**
     * Create new rule
     *
     * @param string                    $name  rule name
     * @param int                       $order rule order no
     * @param \ViKon\Parser\AbstractSet $set   rule set instance
     */
    public function __construct($name, $order, AbstractSet $set) {
        $this->name = $name;
        $this->order = $order;
        $this->set = $set;
    }

    /**
     * Get rule name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get rule order no
     *
     * @return int
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Get rule set instance
     *
     * @return \ViKon\Parser\AbstractSet
     */
    public function getSet() {
        return $this->set;
    }

    /**
     * Prepare rule before connecting
     *
     * @param \ViKon\Parser\Lexer\Lexer $lexer lexer instance
     *
     * @return $this
     */
    public function prepare(Lexer $lexer) {
        return $this;
    }

    /**
     * Embed rule into parent rule
     *
     * @param string                    $parentParentRuleName parent rule name
     * @param \ViKon\Parser\Lexer\Lexer $lexer                lexer instance
     *
     * @return $this
     */
    public function embedInto($parentParentRuleName, Lexer $lexer) {
        return $this;
    }

    /**
     * Finish rule after connecting
     *
     * @param \ViKon\Parser\Lexer\Lexer $lexer lexer instance
     *
     * @return $this
     */
    public function finish(Lexer $lexer) {
        return $this;
    }

    /**
     * Run after lexer finish tokenization
     *
     * @param TokenList $tokenList tokenization result
     * @param bool      $recursive indicates if parse called inside rule during tokenization
     *
     * @return $this
     */
    public function finalize(TokenList $tokenList, $recursive) {
        return $this;
    }

    /**
     * Check if rule accepts named rule as child (sub)
     *
     * @param string $name rule name
     *
     * @return bool TRUE if accepts named rule
     */
    public function acceptRule($name) {
        return in_array($name, $this->acceptedRuleNames);
    }

    /**
     * Parse token
     *
     * @param string                  $content   matched token string
     * @param int                     $position  matched token position
     * @param int                     $state     matched state
     * @param \ViKon\Parser\TokenList $tokenList token list
     */
    public function parseToken($content, $position, $state, TokenList $tokenList) {
    }

    /**
     * Parse token match content
     *
     * @param string         $content     content to parse
     * @param TokenList|null $tokenList   already initialized token list
     * @param bool           $independent independent parsing (mark as not recursive parsing)
     *
     * @return \ViKon\Parser\TokenList
     * @throws \ViKon\Parser\ParserException
     */
    protected function parseContent($content, TokenList $tokenList = null, $independent = false) {
        $parser = new Parser();
        $lexer = new Lexer();

        $this->set->init($parser, $lexer);

        $parser->setStartRule($this);
        $tokenList = $parser->parse($content, $tokenList, !$independent);

        return $tokenList;
    }
}