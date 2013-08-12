<?php

namespace andyleap\PEGParser;

class SimpleVisitor
{
	
	
	public function visit(SimpleNode $node, $depth)
	{
		$visitname = 'visit' . get_class($node);
		if(method_exists($this, $visitname))
		{
			$this->$visitname($node, $depth);
		}
	}
}
