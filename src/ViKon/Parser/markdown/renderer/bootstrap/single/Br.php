<?php


namespace ViKon\Parser\markdown\renderer\bootstrap\single;

use ViKon\Parser\markdown\renderer\bootstrap\AbstractBootstrapRuleRender;
use ViKon\Parser\markdown\rule\single\Hr as HrRule;
use ViKon\Parser\renderer\Renderer;
use ViKon\Parser\Token;

class Br extends AbstractBootstrapRuleRender
{
    public function register(Renderer $renderer)
    {
        $renderer->addTokenRenderer(HrRule::NAME, array($this, 'renderBr'), $this->skin);
    }

    public function renderBr(Token $token)
    {
        return '<br/>';
    }
}