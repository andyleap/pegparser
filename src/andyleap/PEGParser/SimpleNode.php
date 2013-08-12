<?php

namespace andyleap\PEGParser;

class SimpleNode implements Node, \Iterator
{
	public $text;
	public $len;
	public $data = array();
	private $transactions = array(array());
	
	public function __construct($text = '', $textlen = -1)
	{
		$this->text = $text;
		$this->len = ($textlen == -1) ? strlen($text) : $textlen;
	}

	public function commitTransaction()
	{
		if(count($this->transactions) > 1)
		{
			$commit = array_shift($this->transactions);
			foreach($commit as $key => $values)
			{
				foreach($values as $value)
				{
					$this[$key] = $value;
				}
			}
		}
	}

	public function revertTransaction()
	{
		if(count($this->transactions) > 1)
		{
			array_shift($this->transactions);
		}
	}

	public function startTransaction()
	{
		array_unshift($this->transactions, array());
	}

	public function offsetExists($offset)
	{
		foreach($this->transactions as $transaction)
		{
			if(array_key_exists($offset, $transaction))
			{
				return TRUE;
			}
		}
		return FALSE;
	}

	public function offsetGet($offset)
	{
		$data = array();
		foreach($this->transactions as $transaction)
		{
			if(array_key_exists($offset, $transaction))
			{
				$data = array_merge($data, $transaction[$offset]);
			}
		}
		if(count($data) == 0)
		{
			return null;
		}
		return count($data) == 1 ? $data[0]: $data;
	}

	public function offsetSet($offset, $value)
	{
		if(!array_key_exists($offset, $this->transactions[0]))
		{
			$this->transactions[0][$offset] = array();
		}
		$this->transactions[0][$offset][] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->transactions[0][$offset]);
	}

	public function addLen($len)
	{
		$this->len += $len;
	}

	public function addText($text)
	{
		$this->text .= $text;
	}

	public function getLen()
	{
		return $this->len;
	}

	public function getText()
	{
		return $this->text;
	}
	
	public function visit(Visitor $visitor, $depth = 0)
	{
		$visitor->visit($this, $depth);
		foreach($this as $key => $values)
		{
			if(is_array($values))
			{
				foreach($this as $key => $value)
				{
					$value->visit($visitor, $depth + 1);
				}
			}
			else
			{
				$values->visit($visitor, $depth + 1);
			}
		}
	}
	
	private $keys = array();
	
	public function current()
	{
		return $this[current($this->keys)];
	}

	public function key()
	{
		return current($this->keys);
	}

	public function next()
	{
		return next($this->keys);
	}

	public function rewind()
	{
		$this->keys = array();
		foreach($this->transactions as $transaction)
		{
			$this->keys = array_merge($this->keys, array_keys($transaction));
		}
		$this->keys = array_unique($this->keys);
		rewind($this->keys);
	}

	public function valid()
	{
		return !empty($this->keys);
	}
}
