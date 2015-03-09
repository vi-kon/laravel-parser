<?php


namespace ViKon\Parser\Renderer;

/**
 * Class AbstractRenderer
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser\Renderer
 */
abstract class AbstractRenderer {

    /** @var \ViKon\Parser\Renderer\AbstractSkin|null */
    protected $set = null;

    /**
     * @param \ViKon\Parser\Renderer\AbstractSkin $set
     *
     * @return $this
     */
    public function setRendererSet(AbstractSkin $set) {
        $this->set = $set;

        return $this;
    }

    /**
     * @return null|\ViKon\Parser\Renderer\AbstractSkin
     */
    public function getRendererSet() {
        return $this->set;
    }

    /**
     * Register renderer
     *
     * @param \ViKon\Parser\Renderer\Renderer $renderer
     */
    abstract public function register(Renderer $renderer);

    /**
     * Register token renderer
     *
     * @param string                          $tokenName    token name
     * @param string                          $callbackName callback function in class
     * @param \ViKon\Parser\Renderer\Renderer $renderer
     */
    protected function registerTokenRenderer($tokenName, $callbackName, Renderer $renderer) {
        $renderer->registerTokenRenderer($tokenName, [$this, $callbackName], $this->set->getName());
    }
}