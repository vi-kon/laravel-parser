<?php


namespace ViKon\Parser\markdown\rule\single;

use ViKon\Parser\markdown\MarkdownSet;
use ViKon\Parser\rule\AbstractSingleRule;
use ViKon\Parser\Token;
use ViKon\Parser\TokenList;

class LinkReference extends AbstractSingleRule
{
    const NAME = 'link_reference';

    /**
     * Create new Link Reference rule
     *
     * @param \ViKon\Parser\markdown\MarkdownSet $set rule set instance
     */
    public function __construct(MarkdownSet $set)
    {
        parent::__construct(self::NAME, 100, '\\[(?:\\\\.|[^]\\\\])+\\][\\t ]*\\[(?:\\\\.|[^]\\\\])*\\]', $set);
    }

    protected function handleSingleState($content, $position, TokenList $tokenList)
    {
        preg_match('/\\[((?:\\\\.|[^]\\\\])+)\\][\\t ]*\\[((?:\\\\.|[^]\\\\])*)\\]/', $content, $matches);

        $matches[2] = trim($matches[2]);

        $reference = strtolower(($matches[2]) === ''
                                    ? trim($matches[1])
                                    : $matches[2]);

        $referenceTokens = $tokenList->getTokensByCallback(function (Token $token) use ($reference)
        {
            return $token->getName() === Reference::NAME && $token->get('reference', null) === $reference;
        });

        $referenceToken = reset($referenceTokens);

        $tokenList->addToken($this->name, $position)
                  ->set('label', $matches[1])
                  ->set('reference', $referenceToken === false
                      ? $matches[2]
                      : $referenceToken->set('used', true));
    }
}