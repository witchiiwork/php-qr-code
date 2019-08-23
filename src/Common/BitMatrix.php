<?php
namespace W2W\QRCode\Common;

use SplFixedArray;

class BitMatrix {
	protected $width;
	
	protected $height;
	
	protected $rowSize;
	
	protected $bits;
	
	public function __construct($width, $height = null) {
		if($height === null) {
			$height = $width;
		}
		
		if($width < 1 || $height < 1) {
			throw new Exception\InvalidArgumentException("Both dimensions must be greater than zero");
		}
		
		$this->width = $width;
		$this->height = $height;
		$this->rowSize = ($width + 31) >> 5;
		$this->bits = new SplFixedArray($this->rowSize * $height);
	}
	
	public function get($x, $y) {
		$offset = $y * $this->rowSize + ($x >> 5);
		
		return (BitUtils::unsignedRightShift($this->bits[$offset], ($x & 0x1f)) & 1) !== 0;
	}
	
	public function set($x, $y) {
		$offset = $y * $this->rowSize + ($x >> 5);
		$this->bits[$offset] = $this->bits[$offset] | (1 << ($x & 0x1f));
	}
	
	public function flip($x, $y) {
		$offset = $y * $this->rowSize + ($x >> 5);
		$this->bits[$offset] = $this->bits[$offset] ^ (1 << ($x & 0x1f));
	}
	
	public function clear() {
		$max = count($this->bits);
		
		for($i = 0; $i < $max; $i++) {
			$this->bits[$i] = 0;
		}
	}
	
	public function setRegion($left, $top, $width, $height) {
		if($top < 0 || $left < 0) {
			throw new Exception\InvalidArgumentException("Left and top must be non-negative");
		}
		
		if($height < 1 || $width < 1) {
			throw new Exception\InvalidArgumentException("Width and height must be at least 1");
		}
		
		$right = $left + $width;
		$bottom = $top + $height;
		
		if($bottom > $this->height || $right > $this->width) {
			throw new Exception\InvalidArgumentException("The region must fit inside the matrix");
		}
		
		for($y = $top; $y < $bottom; $y++) {
			$offset = $y * $this->rowSize;
			
			for($x = $left; $x < $right; $x++) {
				$index = $offset + ($x >> 5);
				$this->bits[$index] = $this->bits[$index] | (1 << ($x & 0x1f));
			}
		}
	}
	
	public function getRow($y, BitArray $row = null) {
		if($row === null || $row->getSize() < $this->width) {
			$row = new BitArray($this->width);
		}
		
		$offset = $y * $this->rowSize;
		
		for($x = 0; $x < $this->rowSize; $x++) {
			$row->setBulk($x << 5, $this->bits[$offset + $x]);
		}
		
		return $row;
	}
	
	public function setRow($y, BitArray $row) {
		$bits = $row->getBitArray();
		
		for($i = 0; $i < $this->rowSize; $i++) {
			$this->bits[$y * $this->rowSize + $i] = $bits[$i];
		}
	}
	
	public function getEnclosingRectangle() {
		$left = $this->width;
		$top = $this->height;
		$right = -1;
		$bottom = -1;
		
		for($y = 0; $y < $this->height; $y++) {
			for($x32 = 0; $x32 < $this->rowSize; $x32++) {
				$bits = $this->bits[$y * $this->rowSize + $x32];
				
				if($bits !== 0) {
					if($y < $top) {
						$top = $y;
					}
					
					if($y > $bottom) {
						$bottom = $y;
					}
					
					if($x32 * 32 < $left) {
						$bit = 0;
						
						while(($bits << (31 - $bit)) === 0) {
							$bit++;
						}
						
						if(($x32 * 32 + $bit) < $left) {
							$left = $x32 * 32 + $bit;
						}
					}
				}
				
				if($x32 * 32 + 31 > $right) {
					$bit = 31;
					
					while(BitUtils::unsignedRightShift($bits, $bit) === 0) {
						$bit--;
					}
					
					if(($x32 * 32 + $bit) > $right) {
						$right = $x32 * 32 + $bit;
					}
				}
			}
		}

		$width = $right - $left;
		$height = $bottom - $top;

		if($width < 0 || $height < 0) {
			return null;
		}

		return SplFixedArray::fromArray(array($left, $top, $width, $height), false);
	}
	
	public function getTopLeftOnBit() {
		$bitsOffset = 0;
		
		while($bitsOffset < count($this->bits) && $this->bits[$bitsOffset] === 0) {
			$bitsOffset++;
		}
		
		if($bitsOffset === count($this->bits)) {
			return null;
		}
		
		$x = intval($bitsOffset / $this->rowSize);
		$y = ($bitsOffset % $this->rowSize) << 5;
		$bits = $this->bits[$bitsOffset];
		$bit = 0;
		
		while(($bits << (31 - $bit)) === 0) {
			$bit++;
		}
		
		$x += $bit;
		
		return SplFixedArray::fromArray(array($x, $y), false);
	}
	
	public function getBottomRightOnBit() {
		$bitsOffset = count($this->bits) - 1;
		
		while($bitsOffset >= 0 && $this->bits[$bitsOffset] === 0) {
			$bitsOffset--;
		}
		
		if($bitsOffset < 0) {
			return null;
		}
		
		$x = intval($bitsOffset / $this->rowSize);
		$y = ($bitsOffset % $this->rowSize) << 5;
		$bits = $this->bits[$bitsOffset];
		$bit = 0;
		
		while(BitUtils::unsignedRightShift($bits, $bit) === 0) {
			$bit--;
		}
		
		$x += $bit;
		
		return SplFixedArray::fromArray(array($x, $y), false);
	}
	
	public function getWidth() {
		return $this->width;
	}
	
	public function getHeight() {
		return $this->height;
	}
}