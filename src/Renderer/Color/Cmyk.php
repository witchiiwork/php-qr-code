<?php
namespace W2W\QRCode\Renderer\Color;

use W2W\QRCode\Exception;

class Cmyk implements ColorInterface {
	protected $cyan;
	
	protected $magenta;
	
	protected $yellow;
	
	protected $black;
	
	public function __construct($cyan, $magenta, $yellow, $black) {
		if($cyan < 0 || $cyan > 100) {
			throw new Exception\InvalidArgumentException("Cyan must be between 0 and 100");
		}
		
		if($magenta < 0 || $magenta > 100) {
			throw new Exception\InvalidArgumentException("Magenta must be between 0 and 100");
		}
		
		if($yellow < 0 || $yellow > 100) {
			throw new Exception\InvalidArgumentException("Yellow must be between 0 and 100");
		}
		
		if($black < 0 || $black > 100) {
			throw new Exception\InvalidArgumentException("Black must be between 0 and 100");
		}
		
		$this->cyan = (int)$cyan;
		$this->magenta = (int)$magenta;
		$this->yellow = (int)$yellow;
		$this->black = (int)$black;
	}
	
	public function getCyan() {
		return $this->cyan;
	}
	
	public function getMagenta() {
		return $this->magenta;
	}
	
	public function getYellow() {
		return $this->yellow;
	}
	
	public function getBlack() {
		return $this->black;
	}
	
	public function toRgb() {
		$k = $this->black / 100;
		$c = (-$k * $this->cyan + $k * 100 + $this->cyan) / 100;
		$m = (-$k * $this->magenta + $k * 100 + $this->magenta) / 100;
		$y = (-$k * $this->yellow + $k * 100 + $this->yellow) / 100;

		return new Rgb(-$c * 255 + 255, -$m * 255 + 255, -$y * 255 + 255);
	}
	
	public function toCmyk() {
		return $this;
	}
	
	public function toGray() {
		return $this->toRgb()->toGray();
	}
}