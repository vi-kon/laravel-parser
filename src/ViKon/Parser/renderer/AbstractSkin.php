<?php

namespace ViKon\Parser\Renderer;

use ViKon\Parser\Parser;

/**
 * Class AbstractSkin
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser
 */
abstract class AbstractSkin {

    /** @var string */
    protected $name;

    /** @var \ViKon\Parser\Renderer\AbstractRenderer[] */
    protected $ruleRenderers = [];

    /**
     * @param string $skin renderer skin
     */
    public function __construct($skin = 'default') {
        $this->name = $skin;
    }

    /**
     * Set rule renderers to renderer and set renderer to parser
     *
     * @param \ViKon\Parser\Parser            $parser
     * @param \ViKon\Parser\Renderer\Renderer $renderer
     *
     * @return $this
     */
    public function init(Parser $parser, Renderer $renderer) {
        foreach ($this->ruleRenderers as $ruleRenderer) {
            $renderer->registerRenderer($ruleRenderer, $this->name);
        }

        $parser->setRenderer($renderer);

        return $this;
    }

    /**
     * Get renderer set skin
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Add rule renderer
     *
     * @param \ViKon\Parser\Renderer\AbstractRenderer $ruleRenderer
     *
     * @return $this
     */
    protected function addRuleRenderer(AbstractRenderer $ruleRenderer) {
        $ruleRenderer->setRendererSet($this);

        $this->ruleRenderers[] = $ruleRenderer;

        return $this;
    }
}