<?php


namespace ViKon\Parser;

class TokenList
{

    /** @var \ViKon\Parser\Token[] */
    protected $tokens = array();

    /** @var string[] */
    protected $openedTokens = array();

    /**
     * Add token to token list
     *
     * @param string $name     token name
     * @param int    $position token found at position
     *
     * @return \ViKon\Parser\Token
     */
    public function addToken($name, $position)
    {
        $token          = new Token($name, $position);
        $this->tokens[] = $token;

        switch (substr($name, -4))
        {
            case 'open':
                $this->openedTokens[] = substr($name, 0, -5);
                break;
            case 'close':
                while (count($this->openedTokens) > 0)
                {
                    $lastOpenToken  = array_pop($this->openedTokens);
                    $this->tokens[] = new Token($lastOpenToken . '_close', $position);
                    if ($lastOpenToken == substr($name, 0, -5))
                    {
                        break;
                    }
                }
                break;
        }

        return $token;
    }

    /**
     * @param int $index index of token
     *
     * @return \ViKon\Parser\Token
     */
    public function getTokenAt($index)
    {
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
    public function insertTokenAt($name, $position, $index)
    {
        array_splice($this->tokens, $index, 0, array($token = new Token($name, $position)));

        return $token;
    }

    /**
     * Remove token at specific index position
     *
     * @param int $index
     */
    public function removeTokenAt($index)
    {
        array_splice($this->tokens, $index, 1);
    }

    /**
     * Get all tokens
     *
     * @return \ViKon\Parser\Token[]
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * @param callable $callback
     *
     * @return \ViKon\Parser\Token[]
     * @throws \ViKon\Parser\LexerException
     */
    public function getTokensByCallback($callback)
    {
        if (!is_callable($callback))
        {
            throw new LexerException('Not valid callback provided');
        }

        return array_filter($this->tokens, $callback);
    }

    /**
     * Return token count
     *
     * @return int
     */
    public function size()
    {
        return count($this->tokens);
    }

    /**
     * Get last token
     *
     * @return \ViKon\Parser\Token|null
     */
    public function last()
    {
        if (count($this->tokens) > 0)
        {
            return end($this->tokens);
        }

        return null;
    }

    /**
     * Merge two token list
     *
     * @param TokenList $tokenList
     */
    public function merge(TokenList $tokenList)
    {
        $tokenList->closeOpenTokens();
        $lastTokenPosition = $this->last()
                                  ->getPosition();
        foreach ($tokenList->getTokens() as $token)
        {
            $token->setPosition($token->getPosition() + $lastTokenPosition);
            $this->tokens[] = $token;
        }
    }

    public function __toString()
    {

        $output = '<ul>';

        foreach ($this->tokens as $token)
        {
            $output .= '<li>' . $token . '</li>';
        }

        $output .= '</ul>';

        return $output;
    }

    public function closeOpenTokens()
    {
        $lastToken = $this->last();
        while (count($this->openedTokens) > 0)
        {
            $lastOpenToken  = array_pop($this->openedTokens);
            $this->tokens[] = new Token($lastOpenToken . '_close', $lastToken->getPosition());
        }
    }
}