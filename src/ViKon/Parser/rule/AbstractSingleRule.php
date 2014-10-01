<?php


namespace ViKon\Parser\rule;

use ViKon\Parser\AbstractSet;
use ViKon\Parser\lexer\Lexer;
use ViKon\Parser\TokenList;

abstract class AbstractSingleRule extends AbstractRule
{
    /** @var string */
    protected $pattern;

    /**
     * @param string                    $name    rule name
     * @param int                       $order   rule order no
     * @param string                    $pattern pattern for single rule
     * @param \ViKon\Parser\AbstractSet $set     rule set instance
     */
    public function __construct($name, $order, $pattern, AbstractSet $set)
    {
        parent::__construct($name, $order, $set);

        $this->pattern = $pattern;
    }

    /**
     * Embed rule into parent rule
     *
     * @param string                    $parentParentRuleName parent rule name
     * @param \ViKon\Parser\lexer\Lexer $lexer                lexer instance
     *
     * @return \ViKon\Parser\rule\AbstractSingleRule
     */
    public function embedInto($parentParentRuleName, Lexer $lexer)
    {
        $lexer->addSinglePattern($this->pattern, $parentParentRuleName, $this->name);

        return $this;
    }

    /**
     * @param string                  $content   matched token string
     * @param int                     $position  matched token position
     * @param int                     $state     matched state
     * @param \ViKon\Parser\TokenList $tokenList token list
     *
     * @throws \ViKon\Parser\rule\RuleException
     */
    public function parseToken($content, $position, $state, TokenList $tokenList)
    {
        switch ($state)
        {
            case Lexer::STATE_SINGLE:
                $this->handleSingleState($content, $position, $tokenList);
                break;

            default:
                throw new RuleException($state . ' state not handled or unknown');
        }
    }

    /**
     * @param string                  $content
     * @param int                     $position
     * @param \ViKon\Parser\TokenList $tokenList
     */
    protected function handleSingleState($content, $position, TokenList $tokenList)
    {
        $tokenList->addToken($this->name, $position);
    }
}