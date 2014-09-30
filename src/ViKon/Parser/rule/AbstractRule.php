<?php

namespace ViKon\Parser\rule;

use ViKon\Parser\AbstractSet;
use ViKon\Parser\lexer\Lexer;
use ViKon\Parser\Parser;
use ViKon\Parser\TokenList;

abstract class AbstractRule
{
    /** @var string */
    protected $name;

    /** @var int */
    protected $order;

    /** @var string[] */
    protected $acceptedRuleNames = array();

    /** @var \ViKon\Parser\AbstractSet */
    protected $set;

    /**
     * Create new rule
     *
     * @param string                    $name  rule name
     * @param int                       $order rule order no
     * @param \ViKon\Parser\AbstractSet $set   rule set instance
     */
    public function __construct($name, $order, AbstractSet $set)
    {
        $this->name  = $name;
        $this->order = $order;
        $this->set   = $set;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return \ViKon\Parser\AbstractSet
     */
    public function getSet()
    {
        return $this->set;
    }

    /**
     * Prepare rule before connecting
     *
     * @param \ViKon\Parser\lexer\Lexer $lexer lexer instance
     *
     * @return $this
     */
    public function prepare(Lexer $lexer)
    {
        return $this;
    }

    /**
     * Embed rule into parent rule
     *
     * @param string                    $parentParentRuleName parent rule name
     * @param \ViKon\Parser\lexer\Lexer $lexer                lexer instance
     *
     * @return $this
     */
    public function embedInto($parentParentRuleName, Lexer $lexer)
    {
        return $this;
    }

    /**
     * Finish rule after connecting
     *
     * @param \ViKon\Parser\lexer\Lexer $lexer lexer instance
     *
     * @return $this
     */
    public function finish(Lexer $lexer)
    {
        return $this;
    }

    /**
     * Run after lexer finish tokenization
     *
     * @param TokenList $tokenList tokenization result
     *
     * @return $this
     */
    public function finalize(TokenList $tokenList)
    {
        return $this;
    }

    /**
     * Check if rule accepts named rule as child (sub)
     *
     * @param string $name rule name
     *
     * @return bool TRUE if accepts named rule
     */
    public function acceptRule($name)
    {
        return in_array($name, $this->acceptedRuleNames);
    }

    /**
     * @param string                  $content   matched token string
     * @param int                     $position  matched token position
     * @param int                     $state     matched state
     * @param \ViKon\Parser\TokenList $tokenList token list
     *
     * @return bool
     */
    public function parseToken($content, $position, $state, TokenList $tokenList)
    {
        return true;
    }

    /**
     * Parse token match content
     *
     * @param string    $content
     * @param TokenList $tokenList
     *
     * @return bool FALSE on failure, otherwise TRUE
     * @throws \ViKon\Parser\ParserException
     */
    protected function parseContent($content, TokenList $tokenList)
    {
        $parser = new Parser();
        $lexer  = new Lexer();
        $this->set->init($parser, $lexer);
        $parser->setStartRule($this);

        $childTokenList = $parser->parse($content, true);
        if ($childTokenList === false)
        {
            return false;
        }

        $tokenList->merge($childTokenList);

        return true;
    }
}