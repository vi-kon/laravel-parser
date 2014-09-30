<?php


namespace ViKon\Parser\markdown\renderer\bootstrap\block;

use ViKon\Parser\markdown\renderer\bootstrap\AbstractBootstrapRuleRender;
use ViKon\Parser\markdown\rule\block\ListBlock as ListBlockRule;
use ViKon\Parser\renderer\Renderer;
use ViKon\Parser\Token;

class ListBlock extends AbstractBootstrapRuleRender
{
    protected $ordered = array();

    public function register(Renderer $renderer)
    {
        $renderer->addTokenRenderer(ListBlockRule::NAME . '_open', array($this, 'renderListBlockOpen'), $this->skin);
        $renderer->addTokenRenderer(ListBlockRule::NAME . '_level_open', array($this, 'renderListBlockLevelOpen'), $this->skin);
        $renderer->addTokenRenderer(ListBlockRule::NAME . '_item_open', array($this, 'renderListBlockItemOpen'), $this->skin);
        $renderer->addTokenRenderer(ListBlockRule::NAME, array($this, 'renderListBlockItem'), $this->skin);
        $renderer->addTokenRenderer(ListBlockRule::NAME . '_item_close', array($this, 'renderListBlockItemClose'), $this->skin);
        $renderer->addTokenRenderer(ListBlockRule::NAME . '_level_close', array($this, 'renderListBlockLevelClose'), $this->skin);
        $renderer->addTokenRenderer(ListBlockRule::NAME . '_close', array($this, 'renderListBlockClose'), $this->skin);
    }

    public function renderListBlockOpen(Token $token)
    {
        $this->ordered = array();

        return '';
    }

    public function renderListBlockLevelOpen(Token $token)
    {
        $this->ordered[] = $token->get('ordered', false);

        return '<' . ($token->get('ordered', false)
            ? 'ol'
            : 'ul') . '>';
    }

    public function renderListBlockItemOpen(Token $token)
    {
        return '<li>';
    }

    public function renderListBlockItem(Token $token)
    {
        return $token->get('content', '');
    }

    public function renderListBlockItemClose(Token $token)
    {
        return '</li>';
    }

    public function renderListBlockLevelClose(Token $token)
    {
        $ordered = array_pop($this->ordered);

        return '</' . ($ordered
            ? 'ol'
            : 'ul') . '>';
    }

    public function renderListBlockClose(Token $token)
    {
        return '';
    }
}