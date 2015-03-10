<?php


namespace ViKon\Parser\Rule;

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
     * @param string          $name         rule name
     * @param int             $order        order number
     * @param string|string[] $entryPattern entry pattern(s)
     * @param string|string[] $exitPattern  exit pattern(s)
     */
    public function __construct($name, $order, $entryPattern, $exitPattern) {
        parent::__construct($name, $order);

        $this->entryPattern = $entryPattern;
        $this->exitPattern = $exitPattern;
    }

    /**
     * Embed rule into parent rule
     *
     * @param string                    $ruleNameName parent rule name
     * @param \ViKon\Parser\Lexer\Lexer $lexer        lexer instance
     *
     * @return $this
     */
    public function embedInto($ruleNameName, Lexer $lexer) {
        if (is_array($this->entryPattern)) {
            foreach ($this->entryPattern as $entryPattern) {
                $lexer->addEntryPattern($entryPattern, $ruleNameName, $this->name);
            }
        } else {
            $lexer->addEntryPattern($this->entryPattern, $ruleNameName, $this->name);
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
        if (empty($content)) {
            return;
        }

        $contentTokenList = $this->parseContent($content);

        $startRule = $this->set->getStartRule();

        for ($i = 0; $i < count($contentTokenList); $i++) {
            if ($contentTokenList->getTokenAt($i)->getName() === $this->name) {
                $token = $contentTokenList->getTokenAt($i);
                $contentTokenList->removeTokenAt($i);
                $contentTokenList->insertTokenAt($startRule->getName(), $token->getPosition(), $i)
                    ->set('content', $token->get('content', ''));
            }
        }

        $tokenList->merge($contentTokenList);

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
        if (!empty($content)) {
            $tokenList->addToken($this->name, $position)
                ->set('content', $content);
        }
    }
}