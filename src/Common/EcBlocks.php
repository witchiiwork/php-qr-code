<?php
namespace W2W\QRCode\Common;

use SplFixedArray;

class EcBlocks {
	protected $ecCodewordsPerBlock;
	
	protected $ecBlocks;
	
	public function __construct($ecCodewordsPerBlock, EcBlock $ecb1, EcBlock $ecb2 = null) {
		$this->ecCodewordsPerBlock = $ecCodewordsPerBlock;
		$this->ecBlocks = new SplFixedArray($ecb2 === null ? 1 : 2);
		$this->ecBlocks[0] = $ecb1;
		
		if($ecb2 !== null) {
			$this->ecBlocks[1] = $ecb2;
		}
	}
	
	public function getEcCodewordsPerBlock() {
		return $this->ecCodewordsPerBlock;
	}
	
	public function getNumBlocks() {
		$total = 0;
		
		foreach($this->ecBlocks as $ecBlock) {
			$total += $ecBlock->getCount();
		}
		
		return $total;
	}
	
	public function getTotalEcCodewords() {
		return $this->ecCodewordsPerBlock * $this->getNumBlocks();
	}
	
	public function getEcBlocks() {
		return $this->ecBlocks;
	}
}