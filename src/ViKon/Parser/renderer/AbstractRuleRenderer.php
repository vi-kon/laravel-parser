<?php


namespace ViKon\Parser\renderer;

use ViKon\Parser\AbstractSet;

abstract class AbstractRuleRenderer
{
    /** @var \ViKon\Parser\AbstractSet */
    protected $set;

    /** @var string */
    protected $skin;

    /**
     * @param string                    $skin
     * @param \ViKon\Parser\AbstractSet $set
     */
    public function __construct($skin = 'default', AbstractSet $set)
    {
        $this->skin = $skin;
        $this->set  = $set;
    }

    /**
     * @return string
     */
    public function getSkin()
    {
        return $this->skin;
    }

    abstract public function register(Renderer $renderer);
}