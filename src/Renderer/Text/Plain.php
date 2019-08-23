<?php
namespace W2W\QRCode\Renderer\Text;

use W2W\QRCode\Exception;
use W2W\QRCode\Encoder\QrCode;
use W2W\QRCode\Renderer\RendererInterface;

class Plain implements RendererInterface {
	protected $margin = 1;
	
	protected $fullBlock = "\xE2\x96\x88";
	
	protected $emptyBlock = " ";
	
	public function setFullBlock($fullBlock) {
		$this->fullBlock = $fullBlock;
	}
	
	public function getFullBlock() {
		return $this->fullBlock;
	}
	
	public function setEmptyBlock($emptyBlock) {
		$this->emptyBlock = $emptyBlock;
	}
	
	public function getEmptyBlock() {
		return $this->emptyBlock;
	}
	
	public function setMargin($margin) {
		if($margin < 0) {
			throw new Exception\InvalidArgumentException("Margin must be equal to greater than 0");
		}
		
		$this->margin = (int)$margin;
		
		return $this;
	}
	
	public function getMargin() {
		return $this->margin;
	}
	
	public function render(QrCode $qrCode) {
		$result = "";
		$matrix = $qrCode->getMatrix();
		$width = $matrix->getWidth();
		
		for($x = 0; $x < $this->margin; $x++) {
			$result .= str_repeat($this->emptyBlock, $width + 2 * $this->margin)."\n";
		}
		
		$array = $matrix->getArray();
		
		foreach($array as $row) {
			$result .= str_repeat($this->emptyBlock, $this->margin);
			
			foreach($row as $byte) {
				$result .= $byte ? $this->fullBlock : $this->emptyBlock;
			}
			
			$result .= str_repeat($this->emptyBlock, $this->margin);
			$result .= "\n";
		}
		
		for($x = 0; $x < $this->margin; $x++) {
			$result .= str_repeat($this->emptyBlock, $width + 2 * $this->margin) . "\n";
		}
		
		return $result;
	}
}