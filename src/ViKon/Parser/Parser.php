<?php


namespace ViKon\Parser;

use ViKon\Parser\rule\AbstractRule;

class Parser
{
    /** @var AbstractRule[] */
    protected $rules = array();

    /** @var bool */
    protected $rulesInitialized = false;

    /** @var null|Lexer */
    protected $lexer = null;

    /**
     * Add rule to ViKon\Parser
     *
     * @param AbstractRule $rule
     *
     * @throws ParserException
     * @return $this
     */
    public function addRule(AbstractRule $rule)
    {
        if ($this->lexer === null)
        {
            throw new ParserException('Lexer not set');
        }

        if (isset($this->rules[$rule->getName()]))
        {
            throw new ParserException('Rule with ' . $rule->getName() . 'already exists');
        }

        $rule->setLexer($this->lexer);
        $rule->setParser($this);

        $this->rules[$rule->getName()] = $rule;
        $this->rulesInitialized        = false;

        return $this;
    }

    /**
     * Get rules
     *
     * @return rule\AbstractRule[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Set lexer
     *
     * @param Lexer $lexer
     */
    public function setLexer(Lexer $lexer)
    {
        $this->lexer = $lexer;
        $this->lexer->setParser($this);
    }

    /**
     * Get lexer
     *
     * @return null|Lexer
     */
    public function getLexer()
    {
        return $this->lexer;
    }

    /**
     * Parse raw data
     *
     * @param string $raw data to parse
     *
     * @return bool|\ViKon\Parser\SyntaxTree
     * @throws ParserException
     */
    public function parse($raw)
    {
        if ($this->lexer === null)
        {
            throw new ParserException('Lexer not set');
        }

        $raw = "\n" . str_replace("\r\n", "\n", $raw) . "\n";

        $this->initRules();

        return $this->lexer->tokenize($raw);
    }

    /**
     * Parse token
     *
     * @param string     $content  token content
     * @param int        $position token position in raw data
     * @param string     $ruleName rule name
     * @param int        $state    token found state
     * @param SyntaxTree $syntaxTree
     *
     * @throws ParserException
     * @return bool
     */
    public function parseToken($content, $position, $ruleName, $state, SyntaxTree $syntaxTree)
    {
        if (!isset($this->rules[$ruleName]))
        {
            throw new ParserException('Rule with name ' . $ruleName . ' not found');
        }

        return $this->rules[$ruleName]->parseToken($content, $position, $state, $syntaxTree);
    }

    protected function initRules()
    {
        foreach ($this->rules as $rule)
        {
            $rule->resetTokenParser();
        }

        if ($this->rulesInitialized)
        {
            return;
        }

        $this->lexer->reset();

        uasort($this->rules, array($this, 'sortRulesByOrder'));

        foreach ($this->rules as $childRule)
        {
            $childRule->prepare();

            foreach ($this->rules as $rule)
            {
                if ($rule->accepts($childRule->getName()))
                {
                    $childRule->connect($rule->getName());
                }
            }

            $childRule->finish();
        }

        $this->rulesInitialized = true;
    }

    protected function sortRulesByOrder(AbstractRule $a, AbstractRule $b)
    {
        if ($a->getOrder() == $b->getOrder())
        {
            return 0;
        }

        return $a->getOrder() < $b->getOrder()
            ? -1
            : 1;
    }
}