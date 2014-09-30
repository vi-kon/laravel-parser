<?php

namespace ViKon\Parser\markdown;

use ViKon\Parser\AbstractSet;
use ViKon\Parser\markdown\renderer\bootstrap\Base as BaseRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\block\CodeBlock as CodeBlockRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\block\ListBlock as ListBlockRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\block\P as PRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\format\Code as CodeRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\format\Italic as ItalicRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\format\Strong as StrongRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\single\Br as BrRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\single\Eol as EolRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\single\Header as HeaderRenderer;
use ViKon\Parser\markdown\renderer\bootstrap\single\Hr as HrRenderer;
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

        $this->addRuleRender(new BaseRenderer($this));
        $this->addRuleRender(new PRenderer($this));

        $this->addRuleRender(new CodeBlockRenderer($this));
        $this->addRuleRender(new ListBlockRenderer($this));

        $this->addRuleRender(new CodeRenderer($this));
        $this->addRuleRender(new ItalicRenderer($this));
        $this->addRuleRender(new StrongRenderer($this));

        $this->addRuleRender(new BrRenderer($this));
        $this->addRuleRender(new EolRenderer($this));
        $this->addRuleRender(new HeaderRenderer($this));
        $this->addRuleRender(new HrRenderer($this));
    }

    public function normalizeLineBreak(&$text)
    {
        $text = str_replace("\r\n", "\n", $text);
    }
}