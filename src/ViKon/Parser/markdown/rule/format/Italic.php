<?php


namespace ViKon\Parser\markdown\rule\format;

use ViKon\Parser\markdown\MarkdownSet;
use ViKon\Parser\rule\AbstractFormatRule;

class Italic extends AbstractFormatRule
{
    const NAME = 'italic';

    /**
     * Create new Italic rule
     *
     * @param \ViKon\Parser\markdown\MarkdownSet $set rule set instance
     */
    public function __construct(MarkdownSet $set)
    {
        parent::__construct(self::NAME, 80, '\*(?=[^ ][^\n]*\*)', '\*', $set);
    }
}