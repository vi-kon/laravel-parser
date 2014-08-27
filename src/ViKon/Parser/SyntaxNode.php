<?php


namespace ViKon\Parser;

class SyntaxNode
{
    /** @var string */
    protected $name;

    /** @var int */
    protected $position;

    /** @var mixed[] */
    protected $data = array();

    /**
     * @param string $name
     * @param int    $position
     */
    public function __construct($name, $position)
    {
        $this->name     = $name;
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasData($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setData($name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getData($name, $default = null)
    {
        if (isset($this->data[$name]))
        {
            return $this->data[$name];
        }

        return $default;
    }

    /**
     * @return \mixed[]
     */
    public function getAllData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $output = '<h3>' . $this->name . '</h3>';
        if (count($this->data) > 0)
        {
            $output .= '<pre>' . htmlspecialchars(var_export($this->data, true)) . '</pre>';
        }

        return $output;
    }
}