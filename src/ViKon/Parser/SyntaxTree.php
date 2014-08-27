<?php


namespace ViKon\Parser;

class SyntaxTree
{
    /** @var string */
    protected $name;

    /** @var SyntaxNode[] */
    protected $nodes = array();

    /** @var SyntaxTree|null */
    protected $child = null;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Create and add node
     *
     * @param string $name node name
     * @param int    $position
     *
     * @return SyntaxNode
     */
    public function addNode($name, $position)
    {
        if ($this->child !== null)
        {
            return $this->child->addNode($name, $position);
        }

        $this->nodes[] = $node = new SyntaxNode($name, $position);

        return $node;
    }

    /**
     * Add node to specific index position
     *
     * @param string $name
     * @param int    $position
     * @param int    $index
     *
     * @return SyntaxNode
     */
    public function addNodeAt($name, $position, $index)
    {
        array_splice($this->nodes, $index, 0, array($node = new SyntaxNode($name, $position)));

        return $node;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get node by index key
     *
     * @param int $index
     *
     * @return null|SyntaxNode
     */
    public function getNodeByIndex($index)
    {
        if (isset($this->nodes[$index]))
        {
            return $this->nodes[$index];
        }

        return null;
    }

    /**
     * Get node array list
     *
     * @return SyntaxNode[]
     */
    public function toArray()
    {
        return $this->nodes;
    }

    public function flatten()
    {
        $this->nodes = $this->flattenSyntaxTree($this);
    }

    /**
     * @return $this
     */
    public function getActualNode()
    {
        if ($this->child === null)
        {
            return $this;
        }

        return $this->child->getActualNode();
    }

    /**
     * Open sub list
     *
     * @param string $name
     */
    public function openNode($name)
    {
        if ($this->child === null)
        {
            $this->child   = new SyntaxTree($name);
            $this->nodes[] = $this->child;
        }
        else
        {
            $this->child->openNode($name);
        }
    }

    /**
     * Close sub list
     *
     * @param bool $delete if TRUE close and delete sub list
     *
     * @return bool
     */
    public function closeNode($delete = false)
    {
        if ($this->child === null)
        {
            return false;
        }
        if ($this->child->closeNode($delete))
        {
            return true;
        }
        $this->child = null;

        if ($delete)
        {
            array_pop($this->nodes);
        }

        return true;
    }

    public function __toString()
    {
        $output = '<ul>';

        foreach ($this->nodes as $node)
        {
            if ($node instanceof SyntaxNode)
            {
                $output .= '<li>' . $node . '</li>';
            }
            elseif ($node instanceof SyntaxTree)
            {
                $output .= '<li><h3>' . $node->getName() . '_node' . $node . '</h3></li>';
            }
        }

        $output .= '</ul>';

        return $output;
    }

    protected function flattenSyntaxTree(SyntaxTree $syntaxTree)
    {
        $nodes = array();

        foreach ($syntaxTree->toArray() as $node)
        {
            if ($node instanceof SyntaxNode)
            {
                $nodes[] = $node;
            }
            elseif ($node instanceof SyntaxTree)
            {
                $nodes = array_merge($nodes, $this->flattenSyntaxTree($node));
            }
        }

        return $nodes;
    }
}