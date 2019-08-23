<?php
namespace W2W\QRCode\Renderer\Color;

use W2W\QRCode\Exception;

class Gray implements ColorInterface {
	protected $gray;
	
	public function __construct($gray) {
		if($gray < 0 || $gray > 100) {
			throw new Exception\InvalidArgumentException("Gray must be between 0 and 100");
		}
		
		$this->gray = (int)$gray;
	}
	
	public function getGray() {
		return $this->gray;
	}
	
	public function toRgb() {
		return new Rgb($this->gray * 2.55, $this->gray * 2.55, $this->gray * 2.55);
	}
	
	public function toCmyk() {
		return new Cmyk(0, 0, 0, 100 - $this->gray);
	}
	
	public function toGray() {
		return $this;
	}
}