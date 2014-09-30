<?php


namespace ViKon\Parser\rule;

use ViKon\Parser\AbstractSet;
use ViKon\Parser\lexer\Lexer;

abstract class AbstractFormatRule extends AbstractBlockRule
{
    /**
     * Prepare rule before connecting
     *
     * @param \ViKon\Parser\lexer\Lexer $lexer lexer instance
     *
     * @return $this
     */
    public function prepare(Lexer $lexer)
    {
        $this->acceptedRuleNames = $this->set->getRuleNamesByCategory(AbstractSet::CATEGORY_FORMAT);

        return $this;
    }
}