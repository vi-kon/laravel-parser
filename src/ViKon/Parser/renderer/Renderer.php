<?php


namespace ViKon\Parser\renderer;

use ViKon\Parser\TokenList;

class Renderer
{
    /** @var \ViKon\Parser\renderer\AbstractRuleRenderer[] */
    protected $ruleRenderers = array();

    /** @var callback[][] */
    protected $tokenRenderers = array();

    /**
     * Add rule render
     *
     * @param \ViKon\Parser\renderer\AbstractRuleRenderer $ruleRenderer rule renderer
     */
    public function addRuleRenderer(AbstractRuleRenderer $ruleRenderer)
    {
        $this->ruleRenderers[$ruleRenderer->getSkin()][] = $ruleRenderer;
    }

    /**
     * Add token renderer
     *
     * @param string   $tokenName token name
     * @param callable $callback  callback
     * @param string   $skin      renderer skin
     */
    public function addTokenRenderer($tokenName, $callback, $skin = 'default')
    {
        $this->tokenRenderers[$skin][$tokenName] = $callback;
    }

    /**
     * Render token list
     *
     * @param \ViKon\Parser\TokenList $tokenList parsed token list
     * @param string                  $skin      renderer skin
     *
     * @return string|bool FALSE on failure otherwise output
     */
    public function render(TokenList $tokenList, $skin = 'default')
    {
        if (!array_key_exists($skin, $this->ruleRenderers))
        {
            return false;
        }

        if (!array_key_exists($skin, $this->tokenRenderers))
        {
            $this->tokenRenderers[$skin] = array();
            foreach ($this->ruleRenderers[$skin] as $ruleRenderer)
            {
                $ruleRenderer->register($this);
            }
        }

        \Event::fire('vikon.parser.before.render', array($tokenList));

        $output = '';

        foreach ($tokenList->getTokens() as $token)
        {
            if (array_key_exists($token->getName(), $this->tokenRenderers[$skin]))
            {
                $output .= $this->tokenRenderers[$skin][$token->getName()]($token);
            }
            else
            {
                $output .= (string) $token;
            }
        }

        return $output;
    }
}