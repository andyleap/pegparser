<?php

namespace andyleap\PEGParser\Expressions;

use \andyleap\PEGParser\Node;
use \andyleap\PEGParser\SimpleNode;

class Literal extends Expression
{
	public $str;
	public $len;
	public $static;
	
	public function __construct($rule, $literal, $name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$this->rule = $rule;
		$this->str = $literal;
		$this->len = strlen($literal);
		$this->static = !preg_match('/(?<!\\\\)\\{:[a-zA-Z0-9]+\\}/', $literal);
		$this->name = $name;
		$this->ignore = $ignore;
		$this->repeat = $repeat;
	}
	
	public function _match($string, Node $curmatch)
	{
		$str = $this->str;
		$len = $this->len;
		
		if(!$this->static)
		{
			$str = preg_replace_callback('/(?<!\\\\)((?:\\\\\\\\)*){:([a-zA-Z0-9]+)\\}/', function($matches) use ($curmatch)
			{
				if(isset($curmatch[$matches[2]]))
				{
					return $matches[1] . $curmatch[$matches[2]]->getText();
				}
				return $matches[1];
			}, $str);
			$len = strlen($str);
		}
		
		if(strncmp($string, $str, $len) == 0)
		{
			$match = new SimpleNode($str);
			if($this->name != '')
			{
				$curmatch[$this->name] = $match;
			}
			return $match;
		}
		return FALSE;
	}
}

