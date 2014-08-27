<?php


namespace ViKon\Parser;

class Renderer extends AbstractRenderer
{

    public function render(SyntaxTree $syntaxTree)
    {
        $syntaxTree->flatten();

        foreach ($this->nodeRenderers as $nodeRenderer)
        {
            $nodeRenderer->reset();
            $nodeRenderer->preProcessSyntaxTree($syntaxTree);
        }

        $output          = array();
        $syntaxTreeArray = $syntaxTree->toArray();

        foreach ($syntaxTreeArray as $i => $node)
        {
            if (($output[$i] = $this->renderSyntaxNode($node)) === false)
            {
                $output[$i] = $node;
            }
        }

        foreach ($this->nodeRenderers as $nodeRenderer)
        {
            $nodeRenderer->postProcessOutput($output);
        }

        return implode('', $output);
    }
}