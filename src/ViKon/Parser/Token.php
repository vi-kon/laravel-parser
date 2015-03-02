<?php


namespace ViKon\Parser;

/**
 * Class Token
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser
 */
class Token {
    /** @var string */
    private $name;

    /** @var int */
    private $position;

    /** @var mixed[] store key value pairs */
    private $data = [];

    /**
     * @param string $name     token name
     * @param int    $position token found at position
     */
    public function __construct($name, $position) {
        $this->name = strtolower($name);
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position) {
        $this->position = $position;
    }

    /**
     * Store data value under specific key
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value) {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Get data stored under specific key
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null) {
        if ($this->exists($key)) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * Check if data is stored under specific key
     *
     * @param string $key data key
     *
     * @return bool
     */
    public function exists($key) {
        return array_key_exists($key, $this->data);
    }

    /**
     * Clear (delete) data stored under specific key
     *
     * @param string $key data key
     *
     * @return bool return TRUE if data existed and cleared
     */
    public function clear($key) {
        if ($this->exists($key)) {
            unset($this->data[$key]);

            return true;
        }

        return false;
    }

    /**
     * Generate HTML content
     *
     * @return string
     */
    public function __toString() {
        $output = $output = '<h3>' . $this->name . '</h3>';
        if (count($this->data) > 0) {
            $output .= '<pre>' . htmlspecialchars(var_export($this->data, true)) . '</pre>';
        };

        return $output;
    }
}