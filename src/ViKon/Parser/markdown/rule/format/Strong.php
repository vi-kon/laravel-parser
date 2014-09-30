<?php


namespace ViKon\Parser\markdown\rule\format;

use ViKon\Parser\markdown\MarkdownSet;
use ViKon\Parser\rule\AbstractFormatRule;

class Strong extends AbstractFormatRule
{
    const NAME = 'strong';

    /**
     * Create new Strong rule
     *
     * @param \ViKon\Parser\markdown\MarkdownSet $set rule set instance
     */
    public function __construct(MarkdownSet $set)
    {
        parent::__construct(self::NAME, 70, '\*\*(?=[^ ][^\n]*\*\*[^\*])', '\*\*[^\*]', $set);
    }
}