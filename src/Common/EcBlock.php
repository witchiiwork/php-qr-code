<?php
namespace W2W\QRCode\Common;

class EcBlock {
	protected $count;
	
	protected $dataCodewords;
	
	public function __construct($count, $dataCodewords) {
		$this->count = $count;
		$this->dataCodewords = $dataCodewords;
	}
	
	public function getCount() {
		return $this->count;
	}
	
	public function getDataCodewords() {
		return $this->dataCodewords;
	}
}