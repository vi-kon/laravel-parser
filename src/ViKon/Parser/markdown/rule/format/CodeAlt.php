<?php


namespace ViKon\Parser\markdown\rule\format;

use ViKon\Parser\AbstractSet;
use ViKon\Parser\lexer\Lexer;
use ViKon\Parser\rule\AbstractFormatRule;

class CodeAlt extends AbstractFormatRule
{
    const NAME = 'code_alt';

    public function __construct(AbstractSet $set)
    {
        parent::__construct(self::NAME, 170, '``(?=[^\n]*``)', '``', $set);
    }

    public function prepare(Lexer $lexer)
    {
    }
}