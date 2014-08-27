<?php


namespace ViKon\Parser;

class LexerRulePattern
{
    /** @var string */
    protected $name;

    /** @var mixed[] */
    protected $patterns = array();

    /** @var null|string */
    protected $concatenatedPattern = null;

    /**
     * @param string $name rule name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Add pattern to rule pattern
     *
     * @param string      $pattern  regex pattern
     * @param null|string $ruleName regex pattern owner rule name
     */
    public function addPattern($pattern, $ruleName = null)
    {
        $this->patterns[]          = array(
            'pattern'  => $pattern,
            'ruleName' => $ruleName,
            'composed' => false,
        );
        $this->concatenatedPattern = null;
    }

    /**
     * Split subject into pieces
     *
     * @param string $subject  raw data to split
     * @param string $ruleName return matched patterns rule name
     *
     * @return array|bool return array with exploded string chunks
     */
    public function split($subject)
    {
        if (count($this->patterns) === 0 || !preg_match($this->composePatterns(), $subject, $matches))
        {
            return false;
        }

        $match     = array_shift($matches);
        $patternId = count($matches) - 1;
        $ruleName  = $this->patterns[$patternId]['ruleName'];

        list($first, $end) = preg_split($this->patterns[$patternId]['pattern'] . 'msSi', $subject, 2);

        return array($first, $match, $end, $ruleName);
    }

    protected function composePatterns()
    {
        if ($this->concatenatedPattern === null)
        {
            $this->concatenatedPattern = '/';

            foreach ($this->patterns as &$pattern)
            {
                if ($pattern['composed'])
                {
                    $this->concatenatedPattern .= $pattern['pattern'] . '|';
                    continue;
                }

                preg_match_all(
                    '/\\\\.|' . // match \.
                    '\(\?|[()]|' . // match "(" or ")"
                    '\[\^?\]?(?:\\\\.|\[:[^]]*:\]|[^]\\\\])*\]|' . // match combination of "[]"
                    '[^[()\\\\]+/', // match not in list "()\"
                    $pattern['pattern'], $matches);

                $groupDeep          = 0;
                $pattern['pattern'] = '(';

                foreach ($matches[0] as $match)
                {
                    switch ($match)
                    {
                        case '(':
                            $pattern['pattern'] .= '\\(';
                            break;

                        case ')':
                            if ($groupDeep > 0)
                            {
                                $groupDeep--;
                            }
                            else
                            {
                                $pattern['pattern'] .= '\\';
                            }
                            $pattern['pattern'] .= ')';
                            break;

                        case '(?':
                            $groupDeep++;
                            $pattern['pattern'] .= '(?';
                            break;

                        default:
                            $pattern['pattern'] .= substr($match, 0, 1) == '\\'
                                ? $match
                                : str_replace('/', '\\/', $match);
                            break;
                    }
                }

                $pattern['pattern'] .= ')';
                $pattern['composed'] = true;

                $this->concatenatedPattern .= $pattern['pattern'] . '|';
            }

            $this->concatenatedPattern = rtrim($this->concatenatedPattern, '|') . '/msSi';
        }

        return $this->concatenatedPattern;
    }
}