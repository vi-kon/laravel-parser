<?php


namespace ViKon\Parser\markdown\renderer\bootstrap\single;

use ViKon\Parser\markdown\renderer\bootstrap\AbstractBootstrapRuleRender;
use ViKon\Parser\markdown\rule\single\Link as LinkRule;
use ViKon\Parser\markdown\rule\single\LinkReference as LinkReferenceRule;
use ViKon\Parser\markdown\rule\single\Reference as ReferenceRule;
use ViKon\Parser\renderer\Renderer;
use ViKon\Parser\Token;
use ViKon\Parser\TokenList;

class Link extends AbstractBootstrapRuleRender
{
    public function register(Renderer $renderer)
    {
        $renderer->addTokenRenderer(LinkRule::NAME, array($this, 'renderLink'), $this->skin);
        $renderer->addTokenRenderer(LinkReferenceRule::NAME, array($this, 'renderLinkReference'), $this->skin);
    }

    public function renderLink(Token $token)
    {
        $title = $token->get('title', null) === null
            ? ''
            : ' title="' . $token->get('title') . '"';

        return '<a href="' . $token->get('url') . '"' . $title . '>' . $token->get('label') . '</a>';
    }

    public function renderLinkReference(Token $token, TokenList $tokenList)
    {
        $reference = $token->get('reference');
        if ($reference instanceof Token)
        {
            $referenceToken = $reference;
        }
        else
        {
            if (trim($reference) === '')
            {
                $reference = $token->get('label');
            }

            $tokens = $tokenList->getTokensByCallback(function (Token $token) use ($reference)
            {
                return $token->getName() === ReferenceRule::NAME && $token->get('reference', null) === $reference;
            });

            if (($referenceToken = reset($tokens)) === false)
            {
                return $token->get('match', '');
            }

            $referenceToken->set('used', true);
        }

        $title = $referenceToken->get('title', null) === null
            ? ''
            : ' title="' . $referenceToken->get('title') . '"';

        return '<a href="' . $referenceToken->get('url') . '"' . $title . '>' . $token->get('label') . '</a>';
    }
}