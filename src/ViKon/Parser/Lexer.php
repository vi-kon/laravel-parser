<?php


namespace ViKon\Parser;

class Lexer
{
    const RULE_EXIT = '::exit';
    const SINGLE_RULE_PREFIX = ':';

    const STATE_ENTER = 0;
    const STATE_EXIT = 1;
    const STATE_MATCHED = 2;
    const STATE_SINGLE = 3;
    const STATE_UNMATCHED = 4;

    /** @var LexerRulePattern[] */
    protected $rulePatterns = array();

    /** @var null|Parser */
    protected $parser = null;

    /** @var string[] */
    protected $stack = array();

    /** @var  string */
    protected $startRule = null;

    /**
     * Add entry pattern to rule. If pattern match switch to next rule
     *
     * @param string $pattern        regexp pattern
     * @param string $parentRuleName parent rule name
     * @param string $ruleName       rule name (next rule)
     *
     * @return $this
     */
    public function addEntryPattern($pattern, $parentRuleName, $ruleName)
    {
        $this->addPattern($pattern, $parentRuleName, $ruleName);

        return $this;
    }

    /**
     * Exit from current rule to his parent
     *
     * @param string $pattern  regexp pattern
     * @param string $ruleName rule name
     *
     * @return $this
     */
    public function addExitPattern($pattern, $ruleName)
    {
        $this->addPattern($pattern, $ruleName, self::RULE_EXIT);

        return $this;
    }

    /**
     * Add single pattern. If pattern match change to next rule and immediately back
     *
     * @param string $pattern        regexp pattern
     * @param string $parentRuleName parent rule name
     * @param string $ruleName       rule name (next rule)
     *
     * @return $this
     */
    public function addSinglePattern($pattern, $parentRuleName, $ruleName)
    {
        $this->addPattern($pattern, $parentRuleName, self::SINGLE_RULE_PREFIX . $ruleName);

        return $this;
    }

    /**
     * Add simple pattern. No rule change.
     *
     * @param $pattern
     * @param $ruleName
     *
     * @return $this
     */
    public function addSimplePattern($pattern, $ruleName)
    {
        $this->addPattern($pattern, $ruleName);

        return $this;
    }

    /**
     * Tokenize raw data
     *
     * @param string $raw
     *
     * @return bool|SyntaxTree
     * @throws ParserException
     */
    public function tokenize($raw)
    {
        if ($this->parser === null)
        {
            throw new ParserException('Parser not set');
        }

        if ($this->startRule === null)
        {
            throw new ParserException('Start rule not set');
        }

        $this->stack = array($this->startRule);

        $syntaxTree = new SyntaxTree($this->startRule);

        $currentPosition = 0;
        $initialLength   = strlen($raw);
        while (is_array($parsed = $this->reduce($raw)))
        {
            list($unmatched, $matched, $ruleName) = $parsed;

            $remainingLength = strlen($raw);
            $matchedPosition = $initialLength - $remainingLength - strlen($matched);

            if (!$this->callParser($unmatched, $currentPosition, self::STATE_UNMATCHED, $syntaxTree))
            {
                return false;
            }

            if ($ruleName === self::RULE_EXIT)
            {
                if (!$this->callParser($matched, $matchedPosition, self::STATE_EXIT, $syntaxTree))
                {
                    return false;
                }
                array_pop($this->stack);

                if (empty($this->stack))
                {
                    return false;
                }
            }
            else if (strpos($ruleName, self::SINGLE_RULE_PREFIX) === 0)
            {
                array_push($this->stack, substr($ruleName, strlen(self::SINGLE_RULE_PREFIX)));

                if (!$this->callParser($matched, $matchedPosition, self::STATE_SINGLE, $syntaxTree))
                {
                    return false;
                }

                array_pop($this->stack);

                if (empty($this->stack))
                {
                    return false;
                }
            }
            else if ($ruleName !== null)
            {
                array_push($this->stack, $ruleName);
                if (!$this->callParser($matched, $matchedPosition, self::STATE_ENTER, $syntaxTree))
                {
                    return false;
                }
            }
            else if (!$this->callParser($matched, $matchedPosition, self::STATE_MATCHED, $syntaxTree))
            {
                return false;
            }

            $currentPosition = $initialLength - $remainingLength;
        }

        return $syntaxTree;
    }

    /**
     * Reset rule patterns
     *
     * @return $this
     */
    public function reset()
    {
        $this->rulePatterns = array();

        return $this;
    }

    /**
     * Set ViKon\Parser
     *
     * @param Parser $parser
     *
     * @return $this
     */
    public function setParser(Parser $parser)
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * Set start rule
     *
     * @param string $ruleName rule name
     */
    public function setStartRule($ruleName)
    {
        $this->startRule = $ruleName;
    }

    /**
     * Add pattern to rule patterns
     *
     * @param string      $pattern       regex pattern
     * @param string      $ruleName      rule name
     * @param string|null $childRuleName child rule name
     *
     * @return $this
     */
    protected function addPattern($pattern, $ruleName, $childRuleName = null)
    {
        if (!isset($this->rulePatterns[$ruleName]))
        {
            $this->rulePatterns[$ruleName] = new LexerRulePattern($ruleName);
        }
        $this->rulePatterns[$ruleName]->addPattern($pattern, $childRuleName);

        return $this;
    }

    /**
     * @param string $raw
     *
     * @return array|bool
     */
    protected function reduce(&$raw)
    {
        if (!isset($this->rulePatterns[end($this->stack)]))
        {
            return false;
        }

        if ($raw !== '' && ($data = $this->rulePatterns[end($this->stack)]->split($raw)) !== false)
        {
            list($unmatched, $matched, $raw, $ruleName) = $data;

            return array($unmatched, $matched, $ruleName);
        }

        return true;
    }

    /**
     * @param string     $content
     * @param int        $position
     * @param string     $state
     * @param SyntaxTree $syntaxTree
     *
     * @throws ParserException
     * @return bool
     */
    protected function callParser($content, $position, $state, SyntaxTree $syntaxTree)
    {
        if ($content === '')
        {
            return true;
        }

        $ruleName = end($this->stack);

        return $this->parser->parseToken($content, $position, $ruleName, $state, $syntaxTree);
    }
}