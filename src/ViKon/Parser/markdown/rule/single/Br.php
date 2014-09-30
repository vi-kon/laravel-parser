<?php


namespace ViKon\Parser\markdown\rule\single;

use ViKon\Parser\markdown\MarkdownSet;
use ViKon\Parser\rule\AbstractSingleRule;

class Br extends AbstractSingleRule
{
    const NAME = 'br';

    public function __construct(MarkdownSet $set)
    {
        parent::__construct(self::NAME, 140, '  \n', $set);
    }
}