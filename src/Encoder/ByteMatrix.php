<?php
namespace W2W\QRCode\Encoder;

use SplFixedArray;

class ByteMatrix {
	protected $bytes;
	
	protected $width;
	
	protected $height;
	
	public function __construct($width, $height) {
		$this->height = $height;
		$this->width = $width;
		$this->bytes = new SplFixedArray($height);
		
		for($y = 0; $y < $height; $y++) {
			$this->bytes[$y] = new SplFixedArray($width);
		}
	}
	
	public function getWidth() {
		return $this->width;
	}
	
	public function getHeight() {
		return $this->height;
	}
	
	public function getArray() {
		return $this->bytes;
	}
	
	public function get($x, $y) {
		return $this->bytes[$y][$x];
	}
	
	public function set($x, $y, $value) {
		$this->bytes[$y][$x] = (int)$value;
	}
	
	public function clear($value) {
		for($y = 0; $y < $this->height; $y++) {
			for($x = 0; $x < $this->width; $x++) {
				$this->bytes[$y][$x] = $value;
			}
		}
	}
	
	public function __toString() {
		$result = "";
		
		for($y = 0; $y < $this->height; $y++) {
			for($x = 0; $x < $this->width; $x++) {
				switch($this->bytes[$y][$x]) {
					case 0:
						$result .= " 0";
						
						break;
					case 1:
						$result .= " 1";
						
						break;
					default:
						$result .= "  ";
						
						break;
				}
			}
			
			$result .= "\n";
		}
		
		return $result;
	}
}