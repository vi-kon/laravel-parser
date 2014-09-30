<?php


namespace ViKon\Parser\markdown\rule\single;

use ViKon\Parser\AbstractSet;
use ViKon\Parser\rule\AbstractSingleRule;
use ViKon\Parser\TokenList;

class HeaderSetext extends AbstractSingleRule
{
    const NAME = 'header_setext';

    public function __construct(AbstractSet $set)
    {
        parent::__construct(self::NAME, 50, '^[^\n]*\n[=-]{2,}$', $set);
    }

    protected function handleSingleState($content, $position, TokenList $tokenList)
    {
        list($content, $level) = explode("\n", $content);
        $content = trim($content);

        $tokenList->addToken($this->name, $position)
                  ->set('level', $level[0] === '='
                      ? 1
                      : 2)
                  ->set('content', trim($content, "-= \t"));

        return true;
    }
}