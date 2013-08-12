<?php

namespace andyleap\PEGParser\Expressions;

use \andyleap\PEGParser\Node;
use \andyleap\PEGParser\SimpleNode;

class Group extends Expression implements \andyleap\PEGParser\ExpressionList
{
	private $expressions = '';
	private $parser;
	
	public function __construct($rule, $parser, $name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$this->rule = $rule;
		$this->parser = $parser;
		$this->name = $name;
		$this->ignore = $ignore;
		$this->repeat = $repeat;
	}
	
	public function addLiteral($string, $name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$this->expressions[] = new Literal($this->rule, $string, $name, $ignore, $repeat);
	}
	
	public function addRegex($regex, $name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$this->expressions[] = new Regex($this->rule, $regex, $name, $ignore, $repeat);
	}
	
	public function addSubRule($rule, $name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$this->expressions[] = new SubRule($this->rule, $this->parser, $rule, $name, $ignore, $repeat);
	}
	
	public function addSet($set, $name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$this->expressions[] = new Set($this->rule, $set, $name, $ignore, $repeat);
	}
	
	public function addGroup($name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$group = new Group($this->rule, $this->parser, $name, $ignore, $repeat);
		$this->expressions[] = $group;
		return $group;
	}
	
	public function addChoice($name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$choice = new Choice($this->rule, $this->parser, $name, $ignore, $repeat);
		$this->expressions[] = $choice;
		return $choice;
	}
	
	protected function _match($string, Node $curmatch)
	{
		$matches = new SimpleNode();
		$curmatch->startTransaction();
		$matchpos = 0;
		foreach($this->expressions as $expr)
		{
			$match = $expr->match(substr($string, $matchpos), $curmatch);
			if($match === FALSE)
			{
				$curmatch->revertTransaction();
				return FALSE;
			}
			
			$matchpos += $match->getLen();
			$matches->addLen($match->getLen());
			if(!$expr->ignore)
			{
				$matches->addText($match->getText());
			}
			
		}
		$curmatch->commitTransaction();
		return $matches;
	}
}