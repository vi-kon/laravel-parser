<?php


namespace ViKon\Parser\rule;

abstract class AbstractFormatRule extends AbstractBlockRule
{
    /**
     * @param string          $name         rule name
     * @param int             $order        order number
     * @param string|string[] $entryPattern entry pattern(s)
     * @param string|string[] $exitPattern  exit pattern(s)
     */
    public function __construct($name, $order, $entryPattern, $exitPattern)
    {
        parent::__construct($name, $order, $entryPattern, $exitPattern);
        $this->setCategory(self::CATEGORY_FORMAT);
    }

    public function prepare()
    {
        $this->acceptedChildRules = isset(self::$categories[self::CATEGORY_FORMAT])
            ? self::$categories[self::CATEGORY_FORMAT]
            : array();
        array_splice($this->acceptedChildRules, array_search($this->name, $this->acceptedChildRules), 1);
    }
}