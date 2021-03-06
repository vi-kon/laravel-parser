<?php


namespace ViKon\Parser;
use ViKon\Parser\Rule\AbstractBlockRule;

/**
 * Class TokenList
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser
 */
class TokenList implements \Countable {

    /** @var \ViKon\Parser\Token[] */
    protected $tokens = [];

    /** @var string[] */
    protected $openedTokens = [];

    /**
     * Add token to token list
     *
     * @param string $name     token name
     * @param int    $position token found at position
     *
     * @return \ViKon\Parser\Token
     */
    public function addToken($name, $position) {
        $token = new Token($name, $position);
        $this->tokens[] = $token;

        // Autoclose opened tokens
        switch (substr($name, -4)) {
            case AbstractBlockRule::OPEN:
                $this->openedTokens[] = substr($name, 0, -5);
                break;
            case AbstractBlockRule::CLOSE:
                while (count($this->openedTokens) > 0) {
                    $lastOpenToken = array_pop($this->openedTokens);
                    $this->tokens[] = new Token($lastOpenToken . AbstractBlockRule::CLOSE, $position);
                    if ($lastOpenToken == substr($name, 0, -5)) {
                        break;
                    }
                }
                break;
        }

        return $token;
    }

    /**
     * Get token at given position
     *
     * @param int $index token index, if index is negative get token from behind (-1 is last element)
     *
     * @return \ViKon\Parser\Token
     */
    public function getTokenAt($index) {
        // Get token from behind
        if ($index < 0) {
            $index = count($this->tokens) + $index;
        }

        return $this->tokens[$index];
    }

    /**
     * Add token to specific index position
     *
     * @param string $name
     * @param int    $position
     * @param int    $index
     *
     * @return \ViKon\Parser\Token
     */
    public function insertTokenAt($name, $position, $index) {
        array_splice($this->tokens, $index, 0, [$token = new Token($name, $position)]);

        return $token;
    }

    /**
     * Remove token at specific index position
     *
     * @param int $index token index, if index is negative get token from behind (-1 is last element)
     */
    public function removeTokenAt($index) {
        // Remove token from behind
        if ($index < 0) {
            $index = count($this->tokens) + $index;
        }
        array_splice($this->tokens, $index, 1);
    }

    /**
     * Get all tokens
     *
     * @return \ViKon\Parser\Token[]
     */
    public function getTokens() {
        return $this->tokens;
    }

    /**
     * @param callable $callback
     *
     * @return \ViKon\Parser\Token[]
     * @throws \ViKon\Parser\LexerException
     */
    public function getTokensByCallback($callback) {
        if (!is_callable($callback)) {
            throw new LexerException('Not valid callback provided');
        }

        return array_filter($this->tokens, $callback);
    }

    /**
     * Return token count
     *
     * @return int
     */
    public function count() {
        return count($this->tokens);
    }

    /**
     * Get first token
     *
     * @return \ViKon\Parser\Token|null
     */
    public function first() {
        if (count($this->tokens) > 0) {
            return reset($this->tokens);
        }

        return null;
    }

    /**
     * Get last token
     *
     *
     * @return null|\ViKon\Parser\Token
     */
    public function last() {
        if (count($this->tokens) > 0) {
            return end($this->tokens);
        }

        return null;
    }

    /**
     * Get last token that match provided name
     *
     * @param string $name token name
     *
     * @return \ViKon\Parser\Token|null
     */
    public function lastByName($name) {
        for ($i = count($this->tokens) - 1; $i >= 0; $i--) {
            if ($this->tokens[$i]->getName() === $name) {
                return $this->tokens[$i];
            }
        }

        return null;
    }

    /**
     * Get last token index that match provided name
     *
     * @param string $name token name
     *
     * @return int|null
     */
    public function lastIndexByName($name) {
        for ($i = count($this->tokens) - 1; $i >= 0; $i--) {
            if ($this->tokens[$i]->getName() === $name) {
                return $i;
            }
        }

        return null;
    }

    /**
     * Merge two token list
     *
     * @param TokenList $tokenList
     */
    public function merge(TokenList $tokenList) {
        $tokenList->closeOpenTokens();
        $lastTokenPosition = $this->last() === null ? 0 : $this->last()
            ->getPosition();
        foreach ($tokenList->getTokens() as $token) {
            $token->setPosition($token->getPosition() + $lastTokenPosition);
            $this->tokens[] = $token;
        }
    }

    /**
     * Generate HTML list
     *
     * @return string
     */
    public function __toString() {

        $output = '<ul>';

        foreach ($this->tokens as $token) {
            $output .= '<li>' . $token . '</li>';
        }

        $output .= '</ul>';

        return $output;
    }

    /**
     * Automatically close unclosed tags
     */
    public function closeOpenTokens() {
        $lastToken = $this->last();
        while (count($this->openedTokens) > 0) {
            $lastOpenToken = array_pop($this->openedTokens);
            $this->tokens[] = new Token($lastOpenToken . AbstractBlockRule::CLOSE, $lastToken->getPosition());
        }
    }
}