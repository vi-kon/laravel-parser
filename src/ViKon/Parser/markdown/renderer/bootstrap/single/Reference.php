<?php


namespace ViKon\Parser\markdown\renderer\bootstrap\single;

use ViKon\Parser\markdown\renderer\bootstrap\AbstractBootstrapRuleRender;
use ViKon\Parser\markdown\rule\single\Reference as ReferenceRule;
use ViKon\Parser\renderer\Renderer;
use ViKon\Parser\Token;

class Reference extends AbstractBootstrapRuleRender
{

    public function register(Renderer $renderer)
    {
        $renderer->addTokenRenderer(ReferenceRule::NAME, array($this, 'renderReference'), $this->skin);
    }

    public function renderReference(Token $token)
    {
        if ($token->get('used', false))
        {
            return '';
        }

        return $token->get('match');
    }
}