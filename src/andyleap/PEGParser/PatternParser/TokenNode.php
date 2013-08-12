<?php

namespace andyleap\PEGParser\PatternParser;

use andyleap\PEGParser\ExpressionList;
use andyleap\PEGParser\Expressions\Expression;
use andyleap\PEGParser\SimpleNode;

class TokenNode extends SimpleNode
{
	public function build(ExpressionList $exprlist)
	{
		$repeat = Expression::ONE;
		if(isset($this['zero_or_more']))
		{
			$repeat = Expression::ZERO_OR_MORE;
		}
		elseif(isset($this['one_or_more']))
		{
			$repeat = Expression::ONE_OR_MORE;
		}
		elseif(isset($this['one_or_zero']))
		{
			$repeat = Expression::ONE_OR_ZERO;
		}
		$name = '';
		if(isset($this['tokenname']))
		{
			$name = $this['tokenname']->getText();
		}
		$omit = FALSE;
		if(isset($this['omit']))
		{
			$omit = TRUE;
		}
		
		if(isset($this['literal']))
		{
			$exprlist->addLiteral($this['literal']->getText(), $name, $omit, $repeat);
		}
		
		if(isset($this['name']))
		{
			if($name == '' && isset($this['named']))
			{
				$name = $this['name']->getText();
			}
			$exprlist->addSubRule($this['name']->getText(), $name, $omit, $repeat);
		}
		
		if(isset($this['set']))
		{
			$exprlist->addSet($this['set']->getText(), $name, $omit, $repeat);
		}
		
		if(isset($this['regex']))
		{
			$exprlist->addRegex($this['regex']->getText(), $name, $omit, $repeat);
		}
		
		if(isset($this['peren']))
		{
			$group = $exprlist->addGroup('', FALSE, $repeat);
			$this['peren']['choice']->build($group);
		}
		
		if(isset($this['whitespace']))
		{
			if($this['whitespace']->getText() == '>')
			{
				$exprlist->addRegex('[ \t]*', $name, $omit, $repeat);
			}
			else
			{
				$exprlist->addRegex('[ \t]+', $name, $omit, $repeat);
			}
		}
		
	}
}

