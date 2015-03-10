<?php


namespace ViKon\Parser\Rule;

use ViKon\Parser\Lexer\Lexer;
use ViKon\Parser\TokenList;

/**
 * Class AbstractSingleRule
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser\Rule
 */
abstract class AbstractSingleRule extends AbstractRule {
    /** @var string|string[] */
    protected $pattern;

    /**
     * @param string $name    rule name
     * @param int    $order   rule order no
     * @param string $pattern pattern for single rule
     */
    public function __construct($name, $order, $pattern) {
        parent::__construct($name, $order);

        $this->pattern = $pattern;
    }

    /**
     * Embed rule into parent rule
     *
     * @param string                    $parentRuleName parent rule name
     * @param \ViKon\Parser\Lexer\Lexer $lexer          lexer instance
     *
     * @return \ViKon\Parser\Rule\AbstractSingleRule
     */
    public function embedInto($parentRuleName, Lexer $lexer) {
        if (is_array($this->pattern)) {
            foreach ($this->pattern as $pattern) {
                $lexer->addSinglePattern($pattern, $parentRuleName, $this->name);
            }
        } else {
            $lexer->addSinglePattern($this->pattern, $parentRuleName, $this->name);
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
            case Lexer::STATE_SINGLE:
                $this->handleSingleState($content, $position, $tokenList);
                break;

            default:
                throw new RuleException($state . ' state not handled or unknown');
        }
    }

    /**
     * Handle lexers single state
     *
     * @param string                  $content
     * @param int                     $position
     * @param \ViKon\Parser\TokenList $tokenList
     */
    protected function handleSingleState($content, $position, TokenList $tokenList) {
        $tokenList->addToken($this->name, $position);
    }
}