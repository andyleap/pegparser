<?php

namespace andyleap\PEGParser\PatternParser;

use andyleap\PEGParser\SimpleNode;

class RuleNode extends SimpleNode
{
	public function build(\andyleap\PEGParser\Parser $parser)
	{
		$parser->rules[$this['name']->getText()] = new \andyleap\PEGParser\Rule($parser, $this['name']->getText());
		$rule = $parser->rules[$this['name']->getText()];
		$this['choice']->build($rule);
		return $rule;
	}
}

