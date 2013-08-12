<?php

namespace andyleap\PEGParser;

use andyleap\PEGParser\Expressions\Expression;

class Rule implements ExpressionList
{
	public $name;
	private $parser;
	private $expressions = array();
	private $callbacks = array();
	private $nodetype;
	
	public function __construct($parser, $name)
	{
		$this->parser = $parser;
		$this->name = $name;
	}
	
	public function addLiteral($string, $name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$this->expressions[] = new Expressions\Literal($this, $string, $name, $ignore, $repeat);
	}
	
	public function addRegex($regex, $name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$this->expressions[] = new Expressions\Regex($this, $regex, $name, $ignore, $repeat);
	}
	
	public function addSubRule($rule, $name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$this->expressions[] = new Expressions\SubRule($this, $this->parser, $rule, $name, $ignore, $repeat);
	}
	
	public function addSet($set, $name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$this->expressions[] = new Expressions\Set($this, $set, $name, $ignore, $repeat);
	}
	
	public function addGroup($name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$group = new Expressions\Group($this, $this->parser, $name, $ignore, $repeat);
		$this->expressions[] = $group;
		return $group;
	}
	
	public function addChoice($name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$choice = new Expressions\Choice($this, $this->parser, $name, $ignore, $repeat);
		$this->expressions[] = $choice;
		return $choice;
	}
	
	public function setNodeType($type)
	{
		$this->nodetype = $type;
	}
	
	public function store(&$matches, $match, $name)
	{
		if($name != '')
		{
			if(array_key_exists($name, $this->callbacks))
			{
				$this->callbacks[$name]($matches, $match);
			}
			else
			{
//				echo 'store : ' . $name . PHP_EOL;
				$matches->submatches[$name] = $match;
//				print_r($matches);
			}
		}
	}
	
	public function match($string)
	{
		if(!empty($this->nodetype))
		{
			$matches = new $this->nodetype();
		}
		else
		{
			$matches = new SimpleNode();
		}
		
		$matchpos = 0;
		foreach($this->expressions as $expr)
		{
			$match = $expr->match(substr($string, $matchpos), $matches);
			if($match === FALSE)
			{
				return FALSE;
			}
			$matchpos += $match->getLen();
			$matches->addLen($match->getLen());
			if(!$expr->ignore)
			{
				$matches->addText($match->getText());
			}
		}
		return $matches;
	}
}
