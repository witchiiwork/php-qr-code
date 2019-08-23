<?php
namespace W2W\QRCode\Renderer\Color;

use W2W\QRCode\Exception;

class Rgb implements ColorInterface {
	protected $red;
	
	protected $green;
	
	protected $blue;
	
	public function __construct($red, $green, $blue) {
		if($red < 0 || $red > 255) {
			throw new Exception\InvalidArgumentException("Red must be between 0 and 255");
		}
		
		if($green < 0 || $green > 255) {
			throw new Exception\InvalidArgumentException("Green must be between 0 and 255");
		}
		
		if($blue < 0 || $blue > 255) {
			throw new Exception\InvalidArgumentException("Blue must be between 0 and 255");
		}
		
		$this->red = (int)$red;
		$this->green = (int)$green;
		$this->blue = (int)$blue;
	}
	
	public function getRed() {
		return $this->red;
	}
	
	public function getGreen() {
		return $this->green;
	}
	
	public function getBlue() {
		return $this->blue;
	}
	
	public function __toString() {
		return sprintf("%02x%02x%02x", $this->red, $this->green, $this->blue);
	}
	
	public function toRgb() {
		return $this;
	}
	
	public function toCmyk() {
		$c = 1 - ($this->red / 255);
		$m = 1 - ($this->green / 255);
		$y = 1 - ($this->blue / 255);
		$k = min($c, $m, $y);
		
		return new Cmyk(100 * ($c - $k) / (1 - $k), 100 * ($m - $k) / (1 - $k), 100 * ($y - $k) / (1 - $k), 100 * $k);
	}
	
	public function toGray() {
		return new Gray(($this->red * 0.21 + $this->green * 0.71 + $this->blue * 0.07) / 2.55);
	}
}