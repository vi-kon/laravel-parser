<?php


namespace ViKon\Parser\markdown\renderer\bootstrap;

use ViKon\Parser\markdown\rule\Base as BaseRule;
use ViKon\Parser\renderer\Renderer;
use ViKon\Parser\Token;

class Base extends AbstractBootstrapRuleRender
{

    public function register(Renderer $renderer)
    {
        $renderer->addTokenRenderer(BaseRule::NAME, array($this, 'renderBase'), $this->skin);
    }

    public function renderBase(Token $token)
    {
        return $token->get('content', '');
    }
}