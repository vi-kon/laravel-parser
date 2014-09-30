<?php


namespace ViKon\Parser\markdown\renderer\bootstrap\block;

use ViKon\Parser\markdown\renderer\bootstrap\AbstractBootstrapRuleRender;
use ViKon\Parser\markdown\rule\block\CodeBlock as CodeBlockRule;
use ViKon\Parser\renderer\Renderer;
use ViKon\Parser\Token;

class CodeBlock extends AbstractBootstrapRuleRender
{
    public function register(Renderer $renderer)
    {
        $renderer->addTokenRenderer(CodeBlockRule::NAME . '_open', array($this, 'renderCodeBlockOpen'), $this->skin);
        $renderer->addTokenRenderer(CodeBlockRule::NAME, array($this, 'renderCodeBlock'), $this->skin);
        $renderer->addTokenRenderer(CodeBlockRule::NAME . '_close', array($this, 'renderCodeBlockClose'), $this->skin);
    }

    public function renderCodeBlockOpen(Token $token)
    {
        return '<pre><code>';
    }

    public function renderCodeBlock(Token $token)
    {
        return htmlspecialchars($token->get('content', ''));
    }

    public function renderCodeBlockClose(Token $token)
    {
        return '</pre></code>';
    }
}