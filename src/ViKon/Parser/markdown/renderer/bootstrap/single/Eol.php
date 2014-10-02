<?php


namespace ViKon\Parser\markdown\renderer\bootstrap\single;

use ViKon\Parser\markdown\renderer\bootstrap\AbstractBootstrapRuleRender;
use ViKon\Parser\renderer\Renderer;
use ViKon\Parser\Token;
use ViKon\ParserMarkdown\rule\Eol as EolRule;

class Eol extends AbstractBootstrapRuleRender
{

    public function register(Renderer $renderer)
    {
        $renderer->addTokenRenderer(EolRule::NAME, array($this, 'renderEol'), $this->skin);
    }

    public function renderEol(Token $token)
    {
        return "\n";
    }
}