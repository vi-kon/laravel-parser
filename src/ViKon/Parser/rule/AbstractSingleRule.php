<?php


namespace ViKon\Parser\rule;

use ViKon\Parser\Lexer;
use ViKon\Parser\SyntaxNode;
use ViKon\Parser\SyntaxTree;

abstract class AbstractSingleRule extends AbstractRule
{
    /** @var string */
    protected $pattern;

    /**
     * @param string $name    rule name
     * @param string $pattern rule single pattern
     * @param int    $order   rule order number
     */
    public function __construct($name, $order, $pattern)
    {
        parent::__construct($name, $order, self::CATEGORY_SINGLE);
        $this->pattern = $pattern;
    }

    public function connect($ruleName)
    {
        $this->lexer->addSinglePattern($this->pattern, $ruleName, $this->name);
    }

    public function parseToken($content, $position, $state, SyntaxTree $syntaxTree)
    {
        switch ($state)
        {
            case Lexer::STATE_SINGLE:
                $node = $syntaxTree->addNode($this->name, $position);
                $this->handleSingleState($node, $content, $position);
                break;

            default:
                return false;
        }

        return true;
    }

    public function handleSingleState(SyntaxNode $node, $content, $position)
    {
    }
}