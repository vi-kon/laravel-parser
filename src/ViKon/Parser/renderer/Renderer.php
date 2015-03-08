<?php


namespace ViKon\Parser\Renderer;

use ViKon\Parser\ParserException;
use ViKon\Parser\TokenList;

/**
 * Class Renderer
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser\Renderer
 */
class Renderer {
    /** @var \ViKon\Parser\Renderer\AbstractRuleRenderer[] */
    protected $ruleRenderers = [];

    /** @var callback[][] */
    protected $tokenRenderers = [];

    /**
     * Register rule render
     *
     * @param \ViKon\Parser\Renderer\AbstractRuleRenderer $ruleRenderer rule renderer
     */
    public function registerRuleRenderer(AbstractRuleRenderer $ruleRenderer) {
        $this->ruleRenderers[$ruleRenderer->getSkin()][] = $ruleRenderer;
    }

    /**
     * Register token renderer
     *
     * @param string   $tokenName token name
     * @param callable $callback  callback
     * @param string   $skin      renderer skin
     */
    public function registerTokenRenderer($tokenName, $callback, $skin = 'default') {
        $this->tokenRenderers[$skin][$tokenName] = $callback;
    }

    /**
     * Render token list
     *
     * @param \ViKon\Parser\TokenList $tokenList parsed token list
     * @param string                  $skin      renderer skin
     *
     * @return bool|string FALSE on failure otherwise output
     *
     * @throws \ViKon\Parser\ParserException
     */
    public function render(TokenList $tokenList, $skin = 'default') {
        if (!array_key_exists($skin, $this->ruleRenderers)) {
            throw new ParserException('Skin ' . $skin . ' not found');
        }

        if (!array_key_exists($skin, $this->tokenRenderers)) {
            $this->tokenRenderers[$skin] = [];
            foreach ($this->ruleRenderers[$skin] as $ruleRenderer) {
                $ruleRenderer->register($this);
            }
        }

        \Event::fire('vikon.parser.before.render', [$tokenList]);

        $output = '';

        foreach ($tokenList->getTokens() as $token) {
            \Event::fire('vikon.parser.token.render.' . $token->getName(), [$token, $tokenList]);

            if (array_key_exists($token->getName(), $this->tokenRenderers[$skin])) {
                $output .= $this->tokenRenderers[$skin][$token->getName()]($token, $tokenList);
            } else {
                $output .= (string)$token;
            }
        }

        return $output;
    }
}