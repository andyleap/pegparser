<?php

namespace andyleap\PEGParser\PatternParser;

use andyleap\PEGParser\SimpleNode;

class ChoiceNode extends SimpleNode
{
	public function build(\andyleap\PEGParser\ExpressionList $exprlist)
	{
		if(is_array($this['tokens']))
		{
			$choice = $exprlist->addChoice();
			foreach($this['tokens'] as $tokens)
			{
				if(is_array($tokens['token']))
				{
					$group = $choice->addGroup();
					foreach($tokens['token'] as $token)
					{
						$token->build($group);
					}
				}
				else
				{
					$tokens['token']->build($choice);
				}
			}
		}
		else
		{
			if(is_array($this['tokens']['token']))
			{
				foreach($this['tokens']['token'] as $token)
				{
					$token->build($exprlist);
				}
			}
			else
			{
				$this['tokens']['token']->build($exprlist);
			}
		}
	}
}

