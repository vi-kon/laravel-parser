<?php


namespace ViKon\Parser\Rule;

use ViKon\Parser\Lexer\Lexer;

/**
 * Class AbstractFormatRule
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser\Rule
 */
abstract class AbstractFormatRule extends AbstractBlockRule {
    /**
     * Prepare rule before connecting
     *
     * @param \ViKon\Parser\Lexer\Lexer $lexer lexer instance
     *
     * @return $this
     */
    public function prepare(Lexer $lexer) {
        $this->acceptedRuleNames = $this->set->getRuleNamesByCategory(AbstractRuleSet::CATEGORY_FORMAT);

        return $this;
    }
}