<?php


namespace ViKon\Parser\Lexer;

/**
 * Class LexerStack
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser\Lexer
 */
class LexerStack {
    /** @var mixed[] */
    private $stack = [];

    /**
     * Create new stack with elements provided as list of arguments
     */
    public function __construct() {
        $args = func_get_args();
        $this->stack = func_num_args() === 1 && is_array($args[0])
            ? $args[0]
            : $args;
    }

    /**
     * Pop element off from stack
     *
     * @return bool FALSE if stack is empty otherwise TRUE
     */
    public function pop() {
        array_pop($this->stack);

        return !empty($this->stack);
    }

    /**
     * Push value to stack
     *
     * @param mixed $value
     */
    public function push($value) {
        array_push($this->stack, $value);
    }

    /**
     * Get top element from stack
     *
     * @return mixed
     */
    public function top() {
        return end($this->stack);
    }
}