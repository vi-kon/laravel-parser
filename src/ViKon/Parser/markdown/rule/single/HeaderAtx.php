<?php


namespace ViKon\Parser\markdown\rule\single;

use ViKon\Parser\AbstractSet;
use ViKon\Parser\rule\AbstractSingleRule;
use ViKon\Parser\TokenList;

class HeaderAtx extends AbstractSingleRule
{
    const NAME = 'header_atx';

    public function __construct(AbstractSet $set)
    {
        parent::__construct(self::NAME, 50, '^[ \t]*#{1,6}[^\n]+(?=\n)', $set);
    }

    protected function handleSingleState($content, $position, TokenList $tokenList)
    {
        $content = trim($content);
        preg_match('/^#{1,6}/', $content, $matches);

        $tokenList->addToken($this->name, $position)
                  ->set('level', abs(strlen($matches[0])))
                  ->set('content', trim($content, "# \t"));
    }
}