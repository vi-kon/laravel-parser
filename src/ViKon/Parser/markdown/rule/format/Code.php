<?php


namespace ViKon\Parser\markdown\rule\format;

use ViKon\Parser\lexer\Lexer;
use ViKon\Parser\markdown\MarkdownSet;
use ViKon\Parser\rule\AbstractFormatRule;

class Code extends AbstractFormatRule
{
    const NAME = 'code';

    public function __construct(MarkdownSet $set)
    {
        parent::__construct(self::NAME, 180, '`(?=[^\n]*`)', '`', $set);
    }

    public function prepare(Lexer $lexer)
    {
        return $this;
    }
}