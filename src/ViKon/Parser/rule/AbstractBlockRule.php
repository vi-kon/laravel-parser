<?php


namespace ViKon\Parser\rule;

use ViKon\Parser\Lexer;
use ViKon\Parser\SyntaxNode;
use ViKon\Parser\SyntaxTree;

abstract class AbstractBlockRule extends AbstractRule
{
    /** @var string|string[] */
    protected $entryPattern;

    /** @var string|string[] */
    protected $exitPattern;

    /**
     * @param string          $name         rule name
     * @param int             $order        order number
     * @param string|string[] $entryPattern entry pattern(s)
     * @param string|string[] $exitPattern  exit pattern(s)
     */
    public function __construct($name, $order, $entryPattern, $exitPattern)
    {
        parent::__construct($name, $order, self::CATEGORY_BLOCK);

        $this->entryPattern = $entryPattern;
        $this->exitPattern  = $exitPattern;
    }

    public function connect($ruleName)
    {
        if (is_array($this->entryPattern))
        {
            foreach ($this->entryPattern as $entryPattern)
            {
                $this->lexer->addEntryPattern($entryPattern, $ruleName, $this->name);
            }

            return;
        }
        $this->lexer->addEntryPattern($this->entryPattern, $ruleName, $this->name);
    }

    public function finish()
    {
        if (is_array($this->exitPattern))
        {
            foreach ($this->exitPattern as $exitPattern)
            {
                $this->lexer->addExitPattern($exitPattern, $this->name);
            }

            return;
        }
        $this->lexer->addExitPattern($this->exitPattern, $this->name);
    }

    public function parseToken($content, $position, $state, SyntaxTree $syntaxTree)
    {
        switch ($state)
        {
            case Lexer::STATE_ENTER:
                $this->resetTokenParser();
                $syntaxTree->openNode($this->name);
                $node = $syntaxTree->addNode($this->name . '_open', $position);
                $this->handleEntryState($node, $content, $position);
                break;

            case Lexer::STATE_UNMATCHED:
                $node = $syntaxTree->addNode($this->name, $position);
                $this->handleUnmatchedState($node, $content, $position);
                break;

            case Lexer::STATE_EXIT:
                $node = $syntaxTree->addNode($this->name . '_close', $position);
                $this->handleExitState($node, $content, $position);
                $syntaxTree->closeNode();
                break;

            default:
                return false;
        }

        return true;
    }

    /**
     * @param SyntaxNode  $node
     * @param string $content
     * @param int    $position
     */
    protected function handleEntryState(SyntaxNode $node, $content, $position)
    {
    }

    /**
     * @param SyntaxNode  $node
     * @param string $content
     * @param int    $position
     */
    protected function handleUnmatchedState(SyntaxNode $node, $content, $position)
    {
        $node->setData('content', $content);
    }

    /**
     * @param SyntaxNode  $node
     * @param string $content
     * @param int    $position
     */
    protected function handleExitState(SyntaxNode $node, $content, $position)
    {
    }
}