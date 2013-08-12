<?php

namespace andyleap\PEGParser\Expressions;

use \andyleap\PEGParser\Node;
use \andyleap\PEGParser\SimpleNode;

class Set extends Expression
{
	private $set = '';
	
	public function __construct($rule, $set, $name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$this->rule = $rule;
		$this->set = $set;
		$this->name = $name;
		$this->ignore = $ignore;
		$this->repeat = $repeat;
	}
	
	public function _match($string, Node $curmatch)
	{
		if(strstr($this->set, substr($string, 0, 1)) !== FALSE)
		{
			$match = new SimpleNode(substr($string, 0, 1));
			if($this->name != '')
			{
				$curmatch[$this->name] = $match;
			}
			return $match;
		}
		return FALSE;
	}
}
