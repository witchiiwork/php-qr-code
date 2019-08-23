<?php
namespace W2W\QRCode\Common;

use W2W\QRCode\Exception;
use ReflectionClass;

abstract class AbstractEnum {
	const __default = null;
	
	protected $value;
	
	protected $constants;
	
	protected $strict;
	
	public function __construct($initialValue = null, $strict = false) {
		$this->strict = $strict;
		$this->change($initialValue);
	}
	
	public function change($value) {
		if(!in_array($value, $this->getConstList(), $this->strict)) {
			throw new Exception\UnexpectedValueException("Value not a const in enum " . get_class($this));
		}
		
		$this->value = $value;
	}
	
	public function get() {
		return $this->value;
	}
	
	public function getConstList($includeDefault = true) {
		if($this->constants === null) {
			$reflection = new ReflectionClass($this);
			$this->constants = $reflection->getConstants();
		}
		
		if($includeDefault) {
			return $this->constants;
		}
		
		$constants = $this->constants;
		
		unset($constants["__default"]);
		
		return $constants;
	}
	
	public function __toString() {
		return array_search($this->value, $this->getConstList());
	}
}