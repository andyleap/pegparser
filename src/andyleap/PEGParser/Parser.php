<?php

namespace andyleap\PEGParser;

class Parser
{
	public $rules = array();
	
	public function addRule($name, $expressions = array())
	{
		$this->rules[$name] = new Rule($this, $name);
		return $this->rules[$name];
	}
	
	public function parse($grammar)
	{
		return PatternParser\PatternParser::GrammarParse($this, $grammar);
	}
	
	public function match($rule, $string)
	{
		return $this->rules[$rule]->match($string);
	}
}

