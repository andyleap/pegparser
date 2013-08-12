<?php

namespace andyleap\PEGParser\PatternParser;

use andyleap\PEGParser\Expressions\Expression;
use andyleap\PEGParser\Parser;
use andyleap\PEGParser\PatternParser\PatternParser;
use andyleap\PEGParser\Rule;

class PatternParser extends Parser
{
	/*
	 * grammar: ( :rule / [\r\n]* / )+
	 * rule: :name .':' .] :choice
	 * choice: :tokens ( .> '|' .> :tokens ) *
	 * tokens: :token+
	 * token: .omit:'.' ? ( .tokenname:name ? .named:':' ) ? ( :literal | :name | :set | :regex | :peren | :whitespace ) .> ( zero_or_more:'*' | one_or_more:'+' | one_or_zero:'?' ) ? .>
	 * literal: .q:['"] /(?:(?<!\\\\)(?:\\\\\\\\)*\\\\{:q}|[^{:q}])+/ ."{:q}"
	 * set: .'[' /(?:(?<!\\\\)(?:\\\\\\\\)*\\\\]|[^]])+/ .']'
	 * name: /[-_a-zA-Z0-9]+/
	 * regex: .'/' /(?:(?<!\\\\)(?:\\\\\\\\)*\\\\\/|[^\/])+/ .'/'
	 * peren: '(' .> :choice .> ')'
	 * whitespace: [\\]>]
	 */
	public function __construct()
	{
		$this->rules['grammar'] = new Rule($this, 'grammar');
		$grammar = $this->rules['grammar'];
		$grammar->setNodeType('\\andyleap\\PEGParser\\PatternParser\\GrammarNode');
		$gg1 = $grammar->addGroup('', FALSE, Expression::ONE_OR_MORE);
		$gg1->addSubRule('rule', 'rule');
		$gg1->addRegex('[\r\n]*');
		
		$this->rules['rule'] = new Rule($this, 'rule');
		$rule = $this->rules['rule'];
		$rule->setNodeType('\\andyleap\\PEGParser\\PatternParser\\RuleNode');

		$rule->addSubRule('name', 'name');
		$rule->addLiteral(':', '', FALSE);
		$rule->addRegex('[ \t]+', '', FALSE);
		$rule->addSubRule('choice', 'choice', FALSE, Expression::ONE_OR_MORE);

		$this->rules['choice'] = new Rule($this, 'choice');
		$choice = $this->rules['choice'];
		$choice->setNodeType('\\andyleap\\PEGParser\\PatternParser\\ChoiceNode');

		$choice->addSubRule('tokens', 'tokens');
		$cg1 = $choice->addGroup('', FALSE, Expression::ZERO_OR_MORE);
		$cg1->addRegex('[ \t]*', '', FALSE);
		$cg1->addLiteral('|');
		$cg1->addRegex('[ \t]*', '', FALSE);
		$cg1->addSubRule('tokens', 'tokens');
		
		$this->rules['tokens'] = new Rule($this, 'tokens');
		$tokens = $this->rules['tokens'];
		$tokens->addSubRule('token', 'token', FALSE, Expression::ONE_OR_MORE);

		$token = $this->addRule('token', array());
		$token->setNodeType('\\andyleap\\PEGParser\\PatternParser\\TokenNode');

		$token->addLiteral('.', 'omit', FALSE, Expression::ZERO_OR_ONE);

		$tg1 = $token->addGroup('', FALSE, Expression::ZERO_OR_ONE);
		$tg1->addSubRule('name', 'tokenname', FALSE, Expression::ZERO_OR_ONE);
		$tg1->addLiteral(':', 'named', FALSE);

		$tc1 = $token->addChoice('');
		$tc1->addSubRule('literal', 'literal');
		$tc1->addSubRule('name', 'name');
		$tc1->addSubRule('set', 'set');
		$tc1->addSubRule('regex', 'regex');
		$tc1->addSubRule('peren', 'peren');
		$tc1->addSubRule('whitespace', 'whitespace');

		$token->addRegex('[ \t]*', '', FALSE);

		$tc2 = $token->addChoice('', FALSE, Expression::ZERO_OR_ONE);
		$tc2->addLiteral('*', 'zero_or_more', FALSE);
		$tc2->addLiteral('+', 'one_or_more', FALSE);
		$tc2->addLiteral('?', 'zero_or_one', FALSE);

		$token->addRegex('[ \t]*', '', FALSE);

		$literal = $this->addRule('literal', array());
		$literal->setNodeType('\\andyleap\\PEGParser\\PatternParser\\LiteralNode');

		$literal->addSet('\'"', 'q', TRUE);
		$literal->addRegex('(?:(?<!\\\\)(?:\\\\\\\\)*\\\\{:q}|[^{:q}])+');
		$literal->addLiteral('{:q}', '', TRUE);

		$this->rules['set'] = new Rule($this, 'set');
		$set = $this->rules['set'];
		$set->setNodeType('\\andyleap\\PEGParser\\PatternParser\\SetNode');

		$set->addLiteral('[', '', TRUE);
		$set->addRegex('(?:(?<!\\\\)(?:\\\\\\\\)*\\\\]|[^]])+');
		$set->addLiteral(']', '', TRUE);

		$this->rules['name'] = new Rule($this, 'name');
		$name = $this->rules['name'];

		$name->addRegex('[-_a-zA-Z0-9]+');

		$this->rules['regex'] = new Rule($this, 'regex');
		$regex = $this->rules['regex'];
		$regex->setNodeType('\\andyleap\\PEGParser\\PatternParser\\RegexNode');

		$regex->addLiteral('/', '', TRUE);
		$regex->addRegex('(?:(?<!\\\\)(?:\\\\\\\\)*\\\\\/|[^\/])+');
		$regex->addLiteral('/', '', TRUE);

		$this->rules['peren'] = new Rule($this, 'peren');
		$peren = $this->rules['peren'];
		$regex->setNodeType('\\andyleap\\PEGParser\\PatternParser\\RegexNode');

		$peren->addLiteral('(', '', FALSE);
		$peren->addRegex('[ \t]*', '', FALSE);
		$peren->addSubRule('choice', 'choice');
		$peren->addRegex('[ \t]*', '', FALSE);
		$peren->addLiteral(')', '', FALSE);

		$this->rules['whitespace'] = new Rule($this, 'whitespace');
		$whitespace = $this->rules['whitespace'];
		$whitespace->setNodeType('\\andyleap\\PEGParser\\PatternParser\\WhitespaceNode');

		$whitespace->addSet(']>');
	}
	
	/**
	 *
	 * @var PatternParser
	 */
	private static $patternparser;
	
	public static function GrammarParse(Parser $parser, $grammar)
	{
		if(self::$patternparser == null)	
		{
			self::$patternparser = new PatternParser();
		}
		
		$grammardata = self::$patternparser->match('grammar', $grammar);
		
		return $grammardata->build($parser);
	}
}
