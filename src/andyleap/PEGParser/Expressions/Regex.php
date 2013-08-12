<?php

namespace andyleap\PEGParser\Expressions;

use \andyleap\PEGParser\Node;
use \andyleap\PEGParser\SimpleNode;

class Regex extends Expression
{
	private $regex;
	private $static;
	
	public function __construct($rule, $regex, $name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$this->rule = $rule;
		$this->regex = $regex;
		$this->static = !preg_match('/(?<!\\\\)((?:\\\\\\\\)*){:([a-zA-Z0-9]+)\\}/', $regex);
		$this->name = $name;
		$this->ignore = $ignore;
		$this->repeat = $repeat;
	}
	
	public function _match($string, Node $curmatch)
	{
		$matchdata = array();
		$regex = $this->regex;
		
		if(!$this->static)
		{
			$regex = preg_replace_callback('/(?<!\\\\)((?:\\\\\\\\)*){:([a-zA-Z0-9]+)\\}/', function($matches) use ($curmatch)
			{
				if(isset($curmatch[$matches[2]]))
				{
					return $matches[1] . $curmatch[$matches[2]]->getText();
				}
				return $matches[1];
			}, $regex);
		}
		
		//echo '/^ ' . $regex . ' /x' . PHP_EOL;
		
		$match = preg_match('/^ ' . $regex . ' /x', $string, $matchdata);
		
		if($match)
		{
			$match = new SimpleNode($matchdata[0]);
			if($this->name != '')
			{
				$curmatch[$this->name] = $match;
			}
			return $match;
		}
		return FALSE;
	}
}

