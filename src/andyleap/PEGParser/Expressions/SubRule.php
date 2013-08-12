<?php

namespace andyleap\PEGParser\Expressions;

use \andyleap\PEGParser\Node;

class SubRule extends Expression
{
	private $rulename;
	/**
	 *
	 * @var \andyleap\PEGParser\Parser
	 */
	private $parser;
	
	public function __construct($rule, $parser, $rulename, $name = '', $ignore = FALSE, $repeat = Expression::ONE)
	{
		$this->parser = $parser;
		$this->rule = $rule;
		$this->rulename = $rulename;
		$this->name = $name;
		$this->ignore = $ignore;
		$this->repeat = $repeat;
	}
	
	public function _match($string, Node $curmatch)
	{
		$match = $this->parser->match($this->rulename, $string);
		if($match !== FALSE)
		{
			if($this->name != '')
			{
				$curmatch[$this->name] = $match;
			}
		}
		return $match;
	}
}
