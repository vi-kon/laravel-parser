<?php


namespace ViKon\Parser\markdown\rule\single;

use ViKon\Parser\AbstractSet;
use ViKon\Parser\rule\AbstractSingleRule;

class Hr extends AbstractSingleRule
{
    const NAME = 'hr';

    public function __construct(AbstractSet $set)
    {
        parent::__construct(self::NAME, 10, '\n(?:[\*\_\-] *){3,}', $set);
    }
}