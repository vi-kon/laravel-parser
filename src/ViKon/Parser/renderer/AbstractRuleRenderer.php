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
    public function __construct(AbstractSet $set, $skin = 'default')
    {
        $this->set  = $set;
        $this->skin = $skin;
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