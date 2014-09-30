<?php


namespace ViKon\Parser\markdown\rule\single;

use ViKon\Parser\markdown\MarkdownSet;
use ViKon\Parser\rule\AbstractSingleRule;

class Eol extends AbstractSingleRule
{
    const NAME = 'eol';

    /**
     * Create new EOL rule
     *
     * @param \ViKon\Parser\markdown\MarkdownSet $set rule set instance
     */
    public function __construct(MarkdownSet $set)
    {
        parent::__construct(self::NAME, 370, '(?:^[ \t]*)?\n', $set);
    }
}