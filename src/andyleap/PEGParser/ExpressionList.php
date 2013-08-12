<?php

namespace andyleap\PEGParser;

interface ExpressionList
{
	public function addLiteral($string, $name = '', $ignore = FALSE, $repeat = Expression::ONE);
	public function addRegex($regex, $name = '', $ignore = FALSE, $repeat = Expression::ONE);
	public function addSubRule($rule, $name = '', $ignore = FALSE, $repeat = Expression::ONE);
	public function addSet($set, $name = '', $ignore = FALSE, $repeat = Expression::ONE);
	public function addGroup($name = '', $ignore = FALSE, $repeat = Expression::ONE);
	public function addChoice($name = '', $ignore = FALSE, $repeat = Expression::ONE);
}
