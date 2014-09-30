<?php


namespace ViKon\Parser\markdown\rule\block;

use ViKon\Parser\AbstractSet;
use ViKon\Parser\lexer\Lexer;
use ViKon\Parser\markdown\MarkdownSet;
use ViKon\Parser\markdown\rule\single\Eol;
use ViKon\Parser\rule\AbstractBlockRule;
use ViKon\Parser\TokenList;

class ListBlock extends AbstractBlockRule
{
    const NAME = 'list_block';

    protected $open = array();

    protected $level = -1;

    public function __construct(AbstractSet $set)
    {
        parent::__construct(self::NAME, 30, '^(?: {2}|\t)*(?:[\-\+\*]|\d+\.)(?: |\t)+', '\n', $set);
    }

    public function prepare(Lexer $lexer)
    {
        $this->acceptedRuleNames = array_merge(array(
                                                   CodeBlock::NAME,
                                                   Eol::NAME,
                                               ), $this->set->getRuleNamesByCategory(MarkdownSet::CATEGORY_FORMAT));
    }

    public function embedInto($parentRuleNameName, Lexer $lexer)
    {
        parent::embedInto($parentRuleNameName, $lexer);
        $lexer->addSimplePattern('(?:\n[ \t]*)*\n(?: {2}|\t)*(?:[\-\+\*]|\d+\.)(?: |\t)+', $this->name);
        $lexer->addSimplePattern('(?:\n[ \t]*)*\n[ \t]+', $this->name);
        $lexer->addSimplePattern('\n[^ \t\n][^\n]*', $this->name);
    }

    public function parseToken($content, $position, $state, TokenList $tokenList)
    {
        switch ($state)
        {
            case Lexer::STATE_MATCHED:
                return $this->handleMatchedState($content, $position, $tokenList);
        }

        return parent::parseToken($content, $position, $state, $tokenList);
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
        $this->level = -1;
        $this->open  = array();
        $content     = "\n" . $content;

        $tokenList->addToken($this->name . '_open', $position);

        return $this->handleMatchedState($content, $position, $tokenList);
    }

    /**
     * @param string                  $content
     * @param int                     $position
     * @param \ViKon\Parser\TokenList $tokenList
     *
     * @return bool
     */
    protected function handleMatchedState($content, $position, TokenList $tokenList)
    {
        if (preg_match('/((?:\n[ \t]*)*)\n((?: {2}|\t)*)([\-\+\*]|\d+\.)(?: |\t)+/', $content, $matches))
        {
            $level     = strlen(str_replace('  ', "\t", $matches[2]));
            $ordered   = is_numeric(substr($matches[3], 0, -1));
            $paragraph = strlen($matches[1]) > 0;

            if (!$this->closeLevels($tokenList, $level, $position))
            {
                return false;
            }
            $this->openLevels($tokenList, $level, $ordered, $position);

            if (isset($this->open[$this->level]) && $this->open[$this->level] && !$this->itemClose($tokenList, $position))
            {
                return false;
            }
            $this->open[$this->level] = true;
            $tokenList->addToken($this->name . '_item_open', $position);
        }
        else
        {
            $lastToken = $tokenList->last();
            $content   = explode("\n", $content);
            array_walk($content, function (&$item)
            {
                $item = preg_replace('/^(?: {4}|\t)/', '', $item);
            });
            $lastToken->set('content', $lastToken->get('content', '') . implode("\n", $content));
        }

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
        $lastToken = $tokenList->last();
        if ($tokenList->size() > 0 && $lastToken->getName() === $this->name)
        {
            $lastToken->set('content', $lastToken->get('content', '') . $content);
        }
        else
        {
            $tokenList->addToken($this->name, $position)
                      ->set('content', $content);
        }

        return true;
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
        if (!$this->closeLevels($tokenList, -1, $position))
        {
            return false;
        }
        $tokenList->addToken($this->name . '_close', $position);

        return true;
    }

    /**
     * @param TokenList $tokenList token list
     * @param int       $level     actual level deep
     * @param bool      $ordered   level is ordered or not
     * @param int       $position  match position
     */
    protected function openLevels(TokenList $tokenList, $level, $ordered, $position)
    {
        for ($this->level; $this->level < $level; $this->level++)
        {
            $tokenList->addToken($this->name . '_level_open', $position)
                      ->set('level', $this->level)
                      ->set('ordered', $ordered);
        }
    }

    /**
     * @param TokenList $tokenList token list
     * @param int       $level     actual level deep
     * @param int       $position  match position
     *
     * @return bool
     */
    protected function closeLevels(TokenList $tokenList, $level, $position)
    {
        for ($this->level; $this->level > $level; $this->level--)
        {
            if (isset($this->open[$this->level]) && $this->open[$this->level] && !$this->itemClose($tokenList, $position))
            {
                return false;
            }
            $tokenList->addToken($this->name . '_level_close', $position);
        }

        return true;
    }

    /**
     * Add close element
     *
     * @param TokenList $tokenList
     * @param int       $position
     *
     * @return bool FALSE on failure, otherwise TRUE
     */
    protected function itemClose(TokenList $tokenList, $position)
    {
        $lastToken = $tokenList->last();
        if ($lastToken->getName() === $this->name)
        {
            $content = "\n" . $lastToken->get('content') . "\n";
            $tokenList->removeTokenAt($tokenList->size() - 1);
            if (!$this->parseContent($content, $tokenList))
            {
                return false;
            }
        }
        $this->open[$this->level] = false;
        $tokenList->addToken($this->name . '_item_close', $position);

        return true;
    }
}