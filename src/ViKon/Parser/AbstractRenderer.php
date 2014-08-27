<?php


namespace ViKon\Parser;

abstract class AbstractRenderer
{
    /** @var AbstractNodeRenderer[] */
    protected $nodeRenderers = array();

    /** @var callback[] */
    protected $nodeRendererCallbacks = array();

    /**
     * @param AbstractNodeRenderer $nodeRenderer
     */
    public function addTokenRenderer(AbstractNodeRenderer $nodeRenderer)
    {
        $this->nodeRenderers[] = $nodeRenderer;

        $nodeRenderer->setRenderer($this);
        $nodeRenderer->register();
    }

    /**
     * @param string   $nodeName node name
     * @param callback $callback
     */
    public function registerTokenRenderer($nodeName, $callback)
    {
        $this->nodeRendererCallbacks[$nodeName] = $callback;
    }

    /**
     * @param SyntaxTree $syntaxTree
     *
     * @return string
     */
    public abstract function render(SyntaxTree $syntaxTree);

    /**
     * @param SyntaxNode $node
     *
     * @return bool|string return FALSE on failure
     */
    protected function renderSyntaxNode(SyntaxNode $node)
    {
        if (isset($this->nodeRendererCallbacks[$node->getName()]))
        {
            return (string) $this->nodeRendererCallbacks[$node->getName()]($node);
        }

        return false;
    }
}