<?php

namespace andyleap\PEGParser\Expressions;

use \andyleap\PEGParser\Node;
use \andyleap\PEGParser\SimpleNode;

abstract class Expression
{
	public $name;
	public $ignore;
	public $repeat;
	/**
	 * @var andyleap\PEGParser\Expressions
	 */
	public $rule;
	
	const ZERO_OR_ONE = 0;
	const ZERO_OR_MORE = 1;
	const ONE_OR_MORE = 2;
	const ONE = 3;
	
	public function match($string, Node $curmatch)
	{
		$matches = new SimpleNode();
		$matchcount = 0;
		$matchpos = 0;
		do
		{
			$match = $this->_match(substr($string, $matchpos), $curmatch);
			if($this->repeat == self::ONE || $this->repeat == self::ZERO_OR_ONE)
			{
				if($match !== FALSE)
				{
					return $match;
				}
				if($this->repeat == self::ZERO_OR_ONE)
				{
					return $matches;
				}
				return FALSE;
			}
			if($match !== FALSE)
			{
				$matchcount++;
				$matchpos += $match->getLen();
				$matches->addLen($match->getLen());
				$matches->addText($match->getText());
			}
		}
		while($match);
		if($matchcount == 0 && $this->repeat == self::ONE_OR_MORE)
		{
			return FALSE;
		}
		return $matches;
	}
	
	protected abstract function _match($string, Node $curmatch);
}
