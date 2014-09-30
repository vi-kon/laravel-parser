<?php


namespace ViKon\Parser\markdown\renderer\bootstrap\format;

use ViKon\Parser\markdown\renderer\bootstrap\AbstractBootstrapRuleRender;
use ViKon\Parser\markdown\rule\format\Strong as StrongRule;
use ViKon\Parser\markdown\rule\format\StrongAlt as StrongAltRule;
use ViKon\Parser\renderer\Renderer;
use ViKon\Parser\Token;

class Strong extends AbstractBootstrapRuleRender
{
    public function register(Renderer $renderer)
    {
        $renderer->addTokenRenderer(StrongRule::NAME . '_open', array($this, 'renderStrongOpen'), $this->skin);
        $renderer->addTokenRenderer(StrongRule::NAME, array($this, 'renderStrong'), $this->skin);
        $renderer->addTokenRenderer(StrongRule::NAME . '_close', array($this, 'renderStrongClose'), $this->skin);

        $renderer->addTokenRenderer(StrongAltRule::NAME . '_open', array($this, 'renderStrongOpen'), $this->skin);
        $renderer->addTokenRenderer(StrongAltRule::NAME, array($this, 'renderStrong'), $this->skin);
        $renderer->addTokenRenderer(StrongAltRule::NAME . '_close', array($this, 'renderStrongClose'), $this->skin);
    }

    public function renderStrongOpen(Token $token)
    {
        return '<strong>';
    }

    public function renderStrong(Token $token)
    {
        return $token->get('content', '');
    }

    public function renderStrongClose(Token $token)
    {
        return '</strong>';
    }
}