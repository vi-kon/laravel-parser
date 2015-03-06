<?php


namespace ViKon\Parser\Rule;

use ViKon\Parser\AbstractSet;
use ViKon\Parser\Lexer\Lexer;
use ViKon\Parser\TokenList;

/**
 * Class AbstractBlockRule
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser\Rule
 */
abstract class AbstractBlockRule extends AbstractRule {
    const OPEN = '_OPEN';
    const CLOSE = '_CLOSE';

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
    public function __construct($name, $order, $entryPattern, $exitPattern, AbstractSet $set) {
        parent::__construct($name, $order, $set);

        $this->entryPattern = $entryPattern;
        $this->exitPattern = $exitPattern;
    }

    /**
     * Embed rule into parent rule
     *
     * @param string                    $parentRuleNameName parent rule name
     * @param \ViKon\Parser\Lexer\Lexer $lexer              lexer instance
     *
     * @return $this
     */
    public function embedInto($parentRuleNameName, Lexer $lexer) {
        if (is_array($this->entryPattern)) {
            foreach ($this->entryPattern as $entryPattern) {
                $lexer->addEntryPattern($entryPattern, $parentRuleNameName, $this->name);
            }
        } else {
            $lexer->addEntryPattern($this->entryPattern, $parentRuleNameName, $this->name);
        }

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
        if (is_array($this->exitPattern)) {
            foreach ($this->exitPattern as $exitPattern) {
                $lexer->addExitPattern($exitPattern, $this->name);
            }
        } else {
            $lexer->addExitPattern($this->exitPattern, $this->name);
        }

        return $this;
    }

    /**
     * Parse token
     *
     * @param string                  $content   matched token string
     * @param int                     $position  matched token position
     * @param int                     $state     matched state
     * @param \ViKon\Parser\TokenList $tokenList token list
     *
     * @throws \ViKon\Parser\Rule\RuleException
     */
    public function parseToken($content, $position, $state, TokenList $tokenList) {
        switch ($state) {
            case Lexer::STATE_ENTER:
                $this->handleEntryState($content, $position, $tokenList);
                break;

            case Lexer::STATE_UNMATCHED:
                $this->handleUnmatchedState($content, $position, $tokenList);
                break;

            case Lexer::STATE_EXIT:
                $this->handleExitState($content, $position, $tokenList);
                break;

            case Lexer::STATE_END:
                $this->handleEndState($content, $position, $tokenList);
                break;

            default:
                throw new RuleException($state . ' state not handled');
                break;
        }
    }

    /**
     * Handle lexers entry state
     *
     * @param string                  $content
     * @param int                     $position
     * @param \ViKon\Parser\TokenList $tokenList
     */
    protected function handleEntryState($content, $position, TokenList $tokenList) {
        $tokenList->addToken($this->name . self::OPEN, $position);
    }

    /**
     * Handle lexers unmatched state
     *
     * @param string                  $content
     * @param int                     $position
     * @param \ViKon\Parser\TokenList $tokenList
     */
    protected function handleUnmatchedState($content, $position, TokenList $tokenList) {
        $this->parseContent($content, $tokenList);
    }

    /**
     * Handle lexers exit state
     *
     * @param string                  $content
     * @param int                     $position
     * @param \ViKon\Parser\TokenList $tokenList
     */
    protected function handleExitState($content, $position, TokenList $tokenList) {
        $tokenList->addToken($this->name . self::CLOSE, $position);
    }

    /**
     * Handle lexers end state
     *
     * @param string                  $content
     * @param int                     $position
     * @param \ViKon\Parser\TokenList $tokenList
     */
    protected function handleEndState($content, $position, TokenList $tokenList) {
        $tokenList->addToken($this->name, $position)
            ->set('content', $content);
    }
}