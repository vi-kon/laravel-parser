<?php


namespace ViKon\Parser\rule;

use ViKon\Parser\Lexer;
use ViKon\Parser\Parser;
use ViKon\Parser\SyntaxTree;

abstract class AbstractRule
{
    const CATEGORY_NONE = 0;
    const CATEGORY_BLOCK = 1;
    const CATEGORY_FORMAT = 2;
    const CATEGORY_SINGLE = 3;

    /** @var string[][] */
    protected static $categories = array();

    /** @var string */
    protected $name;

    /** @var int */
    protected $order;

    /** @var int */
    private $category;

    /** @var string[] */
    protected $acceptedChildRules = array();

    /** @var null|Parser */
    protected $parser = null;

    /** @var null|Lexer */
    protected $lexer = null;

    /**
     * @param string $name  rule name
     * @param int    $order rule order number
     * @param int    $category
     */
    public function __construct($name, $order, $category)
    {
        $this->name     = $name;
        $this->order    = $order;
        $this->category = $category;

        self::$categories[$category][] = $this->name;
    }

    /**
     * Check if rule accept named child rule
     *
     * @param string $ruleName rule name
     *
     * @return bool
     */
    public function accepts($ruleName)
    {
        return in_array($ruleName, $this->acceptedChildRules);
    }

    public function prepare()
    {
    }

    /**
     * Connect to another rule, that accept this rule
     *
     * @param string $ruleName another rule name that accept this rule
     *
     * @return $this
     */
    public function connect($ruleName)
    {
        return $this;
    }

    /**
     * Finish connecting
     *
     * @return $this
     */
    public function finish()
    {
        return $this;
    }

    /**
     * Handle token found by lexer
     *
     * @param string    $content
     * @param int       $position
     * @param int       $state
     * @param SyntaxTree $syntaxTree
     *
     * @return bool
     */
    public function parseToken($content, $position, $state, SyntaxTree $syntaxTree)
    {
        return true;
    }

    /**
     * Reset rule to defaults
     *
     * @return $this
     */
    public function resetTokenParser()
    {
        return $this;
    }

    /**
     * Get rule name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get rule order
     *
     * Rule with smaller order number dominates if multiple rule have same syntax
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Get rule category
     *
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Lexer $lexer
     *
     * @return $this
     */
    public function setLexer(Lexer $lexer)
    {
        $this->lexer = $lexer;

        return $this;
    }

    /**
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
     * Set new category
     *
     * @param int $category
     */
    protected function setCategory($category)
    {
        array_splice(self::$categories[$this->category], array_search($this->name, self::$categories[$this->category]), 1);
        $this->category                      = $category;
        self::$categories[$this->category][] = $this->name;
    }

    /**
     * @param int $category
     *
     * @return string[]
     */
    public static function getRuleNamesByCategory($category)
    {
        return isset(self::$categories[$category])
            ? self::$categories[$category]
            : array();
    }
}