<?php


namespace ViKon\Parser\lexer;

/**
 * Class LexerPattern
 *
 * @author  Kovács Vince <vincekovacs@hotmail.com>
 *
 * @package ViKon\Parser\lexer
 */
class LexerPattern {
    /** @var string */
    protected $ruleName;

    /** @var string[] */
    protected $patterns = [];

    /** @var string|null */
    protected $concatenatedPatterns = null;

    /**
     * @param string $ruleName rule name
     */
    public function __construct($ruleName) {
        $this->ruleName = $ruleName;
    }

    /**
     * Get patterns rule name
     *
     * @return string
     */
    public function getRuleName() {
        return $this->ruleName;
    }

    /**
     * Add pattern to rule pattern
     *
     * @param string      $pattern       regex pattern
     * @param string|null $childRuleName regex pattern owner rule name
     */
    public function addPattern($pattern, $childRuleName = null) {
        preg_match_all(
            '/\\\\.|' . // match \.
            '\(\?|[()]|' . // match "(" or ")"
            '\[\^?\]?(?:\\\\.|\[:[^]]*:\]|[^]\\\\])*\]|' . // match combination of "[]"
            '[^[()\\\\]+/', // match not in list "()\"
            $pattern, $matches);

        $groupDeep = 0;
        $pattern = '(';

        foreach ($matches[0] as $match) {
            switch ($match) {
                case '(':
                    $pattern .= '\\(';
                    break;

                case ')':
                    if ($groupDeep > 0) {
                        $groupDeep--;
                    } else {
                        $pattern .= '\\';
                    }
                    $pattern .= ')';
                    break;

                case '(?':
                    $groupDeep++;
                    $pattern .= '(?';
                    break;

                default:
                    $pattern .= substr($match, 0, 1) == '\\'
                        ? $match
                        : str_replace('/', '\\/', $match);
                    break;
            }
        }

        $pattern .= ')';

        $this->patterns[] = [
            'pattern'       => $pattern,
            'childRuleName' => $childRuleName,
        ];
    }

    /**
     * Split subject into pieces
     *
     * @param string $text raw text to split
     *
     * @return array|bool return array with exploded string chunks
     */
    public function split($text) {
        if (count($this->patterns) === 0) {
            return false;
        }
        if ($this->concatenatedPatterns === null) {
            $this->concatenatedPatterns = '/' . implode('|', array_map(function ($pattern) {
                    return $pattern['pattern'];
                }, $this->patterns)) . '/msSi';
        }

        if (!preg_match($this->concatenatedPatterns, $text, $matches)) {
            return false;
        }

        $match = array_shift($matches);
        $patternNo = count($matches) - 1;
        $childRuleName = $this->patterns[$patternNo]['childRuleName'];
        list($first, $end) = preg_split($this->patterns[$patternNo]['pattern'] . 'msSi', $text, 2);

        return [$first, $match, $end, $childRuleName];
    }
}