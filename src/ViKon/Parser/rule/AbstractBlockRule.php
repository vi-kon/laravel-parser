<?php


namespace ViKon\Parser\rule;

use ViKon\Parser\AbstractSet;
use ViKon\Parser\lexer\Lexer;
use ViKon\Parser\TokenList;

abstract class AbstractBlockRule extends AbstractRule
{
    /** @var string|string[] */
    protected $entryPattern;

    /** @var string|string[] */
    protected $exitPattern;

    /**
     * @param string                    $name         rule name
     * @param int                       $order        order number
     * @param string|string[]           $entryPattern entry pattern(s)
     * @param string|string[]           $exitPattern  exit pattern(s)
     * @param \ViKon\Parser\AbstractSet $set          rule set instance
     */
    public function __construct($name, $order, $entryPattern, $exitPattern, AbstractSet $set)
    {
        parent::__construct($name, $order, $set);

        $this->entryPattern = $entryPattern;
        $this->exitPattern  = $exitPattern;
    }

    /**
     * Embed rule into parent rule
     *
     * @param string                    $parentRuleNameName parent rule name
     * @param \ViKon\Parser\lexer\Lexer $lexer              lexer instance
     *
     * @return $this
     */
    public function embedInto($parentRuleNameName, Lexer $lexer)
    {
        if (is_array($this->entryPattern))
        {
            foreach ($this->entryPattern as $entryPattern)
            {
                $lexer->addEntryPattern($entryPattern, $parentRuleNameName, $this->name);
            }
        }
        else
        {
            $lexer->addEntryPattern($this->entryPattern, $parentRuleNameName, $this->name);
        }

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
        if (is_array($this->exitPattern))
        {
            foreach ($this->exitPattern as $exitPattern)
            {
                $lexer->addExitPattern($exitPattern, $this->name);
            }
        }
        else
        {
            $lexer->addExitPattern($this->exitPattern, $this->name);
        }

        return $this;
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
        switch ($state)
        {
            case Lexer::STATE_ENTER:
                return $this->handleEntryState($content, $position, $tokenList);

            case Lexer::STATE_UNMATCHED:
                return $this->handleUnmatchedState($content, $position, $tokenList);

            case Lexer::STATE_EXIT:
                return $this->handleExitState($content, $position, $tokenList);

            case Lexer::STATE_END:
                return $this->handleEndState($content, $position, $tokenList);
        }

        return false;
    }

    /**
     * @param string                  $content
     * @param int                     $position
     * @param \ViKon\Parser\TokenList $tokenList
     *
     * @return bool
     */
    protected function handleEntryState($content, $position, TokenList $tokenList)
    {
        $tokenList->addToken($this->name . '_open', $position);

        return true;
    }

    /**
     * @param string                  $content
     * @param int                     $position
     * @param \ViKon\Parser\TokenList $tokenList
     *
     * @return bool
     */
    protected function handleUnmatchedState($content, $position, TokenList $tokenList)
    {
        return $this->parseContent($content, $tokenList);
    }

    /**
     * @param string                  $content
     * @param int                     $position
     * @param \ViKon\Parser\TokenList $tokenList
     *
     * @return bool
     */
    protected function handleExitState($content, $position, TokenList $tokenList)
    {
        $tokenList->addToken($this->name . '_close', $position);

        return true;
    }

    /**
     * @param string                  $content
     * @param int                     $position
     * @param \ViKon\Parser\TokenList $tokenList
     *
     * @return bool
     */
    protected function handleEndState($content, $position, TokenList $tokenList)
    {
        $tokenList->addToken($this->name, $position)
                  ->set('content', $content);

        return true;
    }
}