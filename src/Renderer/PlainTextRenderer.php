<?php
declare(strict_types = 1);

namespace W2W\QRCode\Renderer;

use W2W\QRCode\Encoder\QrCode;
use W2W\QRCode\Exception\InvalidArgumentException;

final class PlainTextRenderer implements RendererInterface {
	private const FULL_BLOCK = "\xe2\x96\x88";
	
	private const UPPER_HALF_BLOCK = "\xe2\x96\x80";
	
	private const LOWER_HALF_BLOCK = "\xe2\x96\x84";
	
	private const EMPTY_BLOCK = "\xc2\xa0";
	
	private $margin;
	
	public function __construct(int $margin = 2) {
		$this->margin = $margin;
	}
	
	public function render(QrCode $qrCode) : string {
		$matrix = $qrCode->getMatrix();
		$matrixSize = $matrix->getWidth();
		
		if($matrixSize !== $matrix->getHeight()) {
			throw new InvalidArgumentException("Matrix must have the same width and height");
		}
		
		$rows = $matrix->getArray()->toArray();
		
		if(0 !== $matrixSize % 2) {
			$rows[] = array_fill(0, $matrixSize, 0);
		}
		
		$horizontalMargin = str_repeat(self::EMPTY_BLOCK, $this->margin);
		$result = str_repeat("\n", (int) ceil($this->margin / 2));
		
		for($i = 0; $i < $matrixSize; $i += 2) {
			$result .= $horizontalMargin;
			$upperRow = $rows[$i];
			$lowerRow = $rows[$i + 1];
			
			for($j = 0; $j < $matrixSize; ++$j) {
				$upperBit = $upperRow[$j];
				$lowerBit = $lowerRow[$j];
				
				if($upperBit) {
					$result .= $lowerBit ? self::FULL_BLOCK : self::UPPER_HALF_BLOCK;
				} else {
					$result .= $lowerBit ? self::LOWER_HALF_BLOCK : self::EMPTY_BLOCK;
				}
			}
			
			$result .= $horizontalMargin . "\n";
		}
		
		$result .= str_repeat("\n", (int) ceil($this->margin / 2));
		
		return $result;
	}
}