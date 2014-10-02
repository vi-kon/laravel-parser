<?php


namespace ViKon\Parser\markdown\rule\single;

use ViKon\Parser\markdown\MarkdownSet;
use ViKon\Parser\rule\AbstractSingleRule;
use ViKon\Parser\TokenList;

class Link extends AbstractSingleRule
{
    const NAME = 'link';

    /**
     * Create new Link rule
     *
     * @param \ViKon\Parser\markdown\MarkdownSet $set rule set instance
     */
    public function __construct(MarkdownSet $set)
    {
        parent::__construct(self::NAME, 100, '\\[(?:\\\\.|[^]\\\\])+\\][\\t ]*\\([\\t ]*(?:\\\\.|[^\\)\\\\ ])+[\\t ]*(?:"(?:\\\\.|[^"\\\\])+")?\\)', $set);
    }

    protected function handleSingleState($content, $position, TokenList $tokenList)
    {
        preg_match('/\\[((?:\\\\.|[^]\\\\])+)\\][\\t ]*\\([\\t ]*((?:\\\\.|[^\\)\\\\ ])+)[\\t ]*(?:"((?:\\\\.|[^"\\\\])+)")?\\)/', $content, $matches);

        $tokenList->addToken($this->name, $position)
                  ->set('label', $matches[1])
                  ->set('url', $matches[2])
                  ->set('title', isset($matches[3])
                      ? $matches[3]
                      : null);
    }
}