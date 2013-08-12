<?php

namespace andyleap\PEGParser\PatternParser;

use andyleap\PEGParser\SimpleNode;

class GrammarNode extends SimpleNode
{
	public function build(\andyleap\PEGParser\Parser $parser)
	{
		if(is_array($this['rule']))
		{
			foreach($this['rule'] as $rule)
			{
				$rule->build($parser);
			}
		}
		else
		{
			$this['rule']->build($parser);
		}
	}
}

