<?php

namespace andyleap\PEGParser;

interface Node extends \ArrayAccess
{
	public function __construct($text = '', $len = -1);
	public function addText($text);
	public function getText();
	public function addLen($len);
	public function getLen();
	public function startTransaction();
	public function revertTransaction();
	public function commitTransaction();
}
