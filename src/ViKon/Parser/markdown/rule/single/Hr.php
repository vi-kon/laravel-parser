<?php


namespace ViKon\Parser\markdown\rule\single;

use ViKon\Parser\markdown\MarkdownSet;
use ViKon\Parser\rule\AbstractSingleRule;

class Hr extends AbstractSingleRule
{
    const NAME = 'hr';

    /**
     * Create new Hr rule
     *
     * @param \ViKon\Parser\markdown\MarkdownSet $set rule set instance
     */
    public function __construct(MarkdownSet $set)
    {
        parent::__construct(self::NAME, 10, '\n(?:[\*\_\-] *){3,}', $set);
    }
}