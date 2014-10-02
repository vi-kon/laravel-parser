<?php

namespace ViKon\Parser\markdown;

use ViKon\Parser\AbstractSet;
use ViKon\Parser\markdown\renderer\bootstrap\Base as BaseBootstrapRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\block\CodeBlock as CodeBlockBootstrapRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\block\ListBlock as ListBlockBootstrapRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\block\P as PBootstrapRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\format\Code as CodeBootstrapRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\format\Italic as ItalicBootstrapRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\format\Strong as StrongBootstrapRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\single\Br as BrBootstrapRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\single\Eol as EolBootstrapRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\single\Header as HeaderBootstrapRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\single\Hr as HrBootstrapRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\single\Link as LinkBootstrapRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\single\Reference as ReferenceBootstrapRenderer;
use ViKon\Parser\markdown\rule\Base;
use ViKon\Parser\markdown\rule\block\CodeBlock;
use ViKon\Parser\markdown\rule\block\ListBlock;
use ViKon\Parser\markdown\rule\block\P;
use ViKon\Parser\markdown\rule\format\Code;
use ViKon\Parser\markdown\rule\format\CodeAlt;
use ViKon\Parser\markdown\rule\format\Italic;
use ViKon\Parser\markdown\rule\format\ItalicAlt;
use ViKon\Parser\markdown\rule\format\Strong;
use ViKon\Parser\markdown\rule\format\StrongAlt;
use ViKon\Parser\markdown\rule\single\Br;
use ViKon\Parser\markdown\rule\single\Eol;
use ViKon\Parser\markdown\rule\single\HeaderAtx;
use ViKon\Parser\markdown\rule\single\HeaderSetext;
use ViKon\Parser\markdown\rule\single\Hr;
use ViKon\Parser\markdown\rule\single\Link;
use ViKon\Parser\markdown\rule\single\LinkReference;
use ViKon\Parser\markdown\rule\single\Reference;

class MarkdownSet extends AbstractSet
{
    public function __construct()
    {
        \Event::listen('vikon.parser.before.parse', array($this, 'normalizeLineBreak'));

        $this->setStartRule(new Base($this), self::CATEGORY_NONE);

        $this->addRule(new CodeBlock($this), self::CATEGORY_BLOCK);
        $this->addRule(new ListBlock($this), self::CATEGORY_BLOCK);
        $this->addRule(new P($this), self::CATEGORY_NONE);

        $this->addRule(new Code($this), self::CATEGORY_FORMAT);
        $this->addRule(new CodeAlt($this), self::CATEGORY_FORMAT);
        $this->addRule(new Strong($this), self::CATEGORY_FORMAT);
        $this->addRule(new StrongAlt($this), self::CATEGORY_FORMAT);
        $this->addRule(new Italic($this), self::CATEGORY_FORMAT);
        $this->addRule(new ItalicAlt($this), self::CATEGORY_FORMAT);

        $this->addRule(new Br($this), self::CATEGORY_SINGLE);
        $this->addRule(new Eol($this), self::CATEGORY_SINGLE);
        $this->addRule(new HeaderAtx($this), self::CATEGORY_SINGLE);
        $this->addRule(new HeaderSetext($this), self::CATEGORY_SINGLE);
        $this->addRule(new Hr($this), self::CATEGORY_SINGLE);
        $this->addRule(new Link($this), self::CATEGORY_SINGLE);
        $this->addRule(new LinkReference($this), self::CATEGORY_SINGLE);
        $this->addRule(new Reference($this), self::CATEGORY_SINGLE);

        $this->addRuleRender(new BaseBootstrapRenderer($this));

        $this->addRuleRender(new CodeBlockBootstrapRenderer($this));
        $this->addRuleRender(new ListBlockBootstrapRenderer($this));
        $this->addRuleRender(new PBootstrapRenderer($this));

        $this->addRuleRender(new CodeBootstrapRenderer($this));
        $this->addRuleRender(new ItalicBootstrapRenderer($this));
        $this->addRuleRender(new StrongBootstrapRenderer($this));

        $this->addRuleRender(new BrBootstrapRenderer($this));
        $this->addRuleRender(new EolBootstrapRenderer($this));
        $this->addRuleRender(new HeaderBootstrapRenderer($this));
        $this->addRuleRender(new HrBootstrapRenderer($this));
        $this->addRuleRender(new LinkBootstrapRenderer($this));
        $this->addRuleRender(new ReferenceBootstrapRenderer($this));
    }

    public function normalizeLineBreak(&$text)
    {
        $text = str_replace("\r\n", "\n", $text);
    }
}