<?php


namespace ViKon\Parser\renderer;

use ViKon\Parser\AbstractSet;

/**
 * Class AbstractRuleRenderer
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser\renderer
 */
abstract class AbstractRuleRenderer {
    /** @var \ViKon\Parser\AbstractSet */
    protected $set;

    /** @var string */
    protected $skin;

    /**
     * @param string                    $skin
     * @param \ViKon\Parser\AbstractSet $set
     */
    public function __construct(AbstractSet $set, $skin = 'default') {
        $this->set = $set;
        $this->skin = $skin;
    }

    /**
     * Get renderer skin
     *
     * @return string
     */
    public function getSkin() {
        return $this->skin;
    }

    /**
     * Register renderer
     *
     * @param \ViKon\Parser\renderer\Renderer $renderer
     *
     * @return mixed
     */
    abstract public function register(Renderer $renderer);
}