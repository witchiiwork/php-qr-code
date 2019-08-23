<?php
namespace W2W\QRCode\Encoder;

use W2W\QRCode\Common\BitUtils;

class MaskUtil {
	const N1 = 3;
	
	const N2 = 3;
	
	const N3 = 40;
	
	const N4 = 10;
	
	public static function applyMaskPenaltyRule1(ByteMatrix $matrix) {
		return (self::applyMaskPenaltyRule1Internal($matrix, true) + self::applyMaskPenaltyRule1Internal($matrix, false));
	}
	
	public static function applyMaskPenaltyRule2(ByteMatrix $matrix) {
		$penalty = 0;
		$array = $matrix->getArray();
		$width = $matrix->getWidth();
		$height = $matrix->getHeight();
		
		for($y = 0; $y < $height - 1; $y++) {
			for($x = 0; $x < $width - 1; $x++) {
				$value = $array[$y][$x];
				
				if($value === $array[$y][$x + 1] && $value === $array[$y + 1][$x] && $value === $array[$y + 1][$x + 1]) {
					$penalty++;
				}
			}
		}
		
		return self::N2 * $penalty;
	}
	
	public static function applyMaskPenaltyRule3(ByteMatrix $matrix) {
		$penalty = 0;
		$array = $matrix->getArray();
		$width = $matrix->getWidth();
		$height = $matrix->getHeight();
		
		for($y = 0; $y < $height; $y++) {
			for($x = 0; $x < $width; $x++) {
				if($x + 6 < $width && $array[$y][$x] === 1 && $array[$y][$x + 1] === 0 && $array[$y][$x + 2] === 1 && $array[$y][$x + 3] === 1 && $array[$y][$x + 4] === 1 && $array[$y][$x + 5] === 0 && $array[$y][$x + 6] === 1 && (($x + 10 < $width && $array[$y][$x + 7] === 0 && $array[$y][$x + 8] === 0 && $array[$y][$x + 9] === 0 && $array[$y][$x + 10] === 0) || ($x - 4 >= 0 && $array[$y][$x - 1] === 0 && $array[$y][$x - 2] === 0 && $array[$y][$x - 3] === 0 && $array[$y][$x - 4] === 0))) {
					$penalty += self::N3;
				}
				
				if($y + 6 < $height && $array[$y][$x] === 1 && $array[$y + 1][$x] === 0 && $array[$y + 2][$x] === 1 && $array[$y + 3][$x] === 1 && $array[$y + 4][$x] === 1 && $array[$y + 5][$x] === 0 && $array[$y + 6][$x] === 1 && (($y + 10 < $height && $array[$y + 7][$x] === 0 && $array[$y + 8][$x] === 0 && $array[$y + 9][$x] === 0 && $array[$y + 10][$x] === 0) || ($y - 4 >= 0 && $array[$y - 1][$x] === 0 && $array[$y - 2][$x] === 0 && $array[$y - 3][$x] === 0 && $array[$y - 4][$x] === 0))) {
					$penalty += self::N3;
				}
			}
		}
		
		return $penalty;
	}
	
	public static function applyMaskPenaltyRule4(ByteMatrix $matrix) {
		$numDarkCells = 0;
		$array = $matrix->getArray();
		$width = $matrix->getWidth();
		$height = $matrix->getHeight();
		
		for($y = 0; $y < $height; $y++) {
			$arrayY = $array[$y];
			
			for($x = 0; $x < $width; $x++) {
				if($arrayY[$x] === 1) {
					$numDarkCells++;
				}
			}
		}
		
		$numTotalCells = $height * $width;
		$darkRatio = $numDarkCells / $numTotalCells;
		$fixedPercentVariances = (int)(abs($darkRatio - 0.5) * 20);
		
		return $fixedPercentVariances * self::N4;
	}
	
	public static function getDataMaskBit($maskPattern, $x, $y) {
		switch($maskPattern) {
			case 0:
				$intermediate = ($y + $x) & 0x1;
				
				break;
			case 1:
				$intermediate = $y & 0x1;
				
				break;
			case 2:
				$intermediate = $x % 3;
				
				break;
			case 3:
				$intermediate = ($y + $x) % 3;
				
				break;
			case 4:
				$intermediate = (BitUtils::unsignedRightShift($y, 1) + ($x / 3)) & 0x1;
				
				break;
			case 5:
				$temp = $y * $x;
				$intermediate = ($temp & 0x1) + ($temp % 3);
				
				break;
			case 6:
				$temp = $y * $x;
				$intermediate = (($temp & 0x1) + ($temp % 3)) & 0x1;
				
				break;
			case 7:
				$temp = $y * $x;
				$intermediate = (($temp % 3) + (($y + $x) & 0x1)) & 0x1;
				
				break;
			default:
				throw new Exception\InvalidArgumentException("Invalid mask pattern: " . $maskPattern);
		}
		
		return $intermediate === 0;
	}
	
	protected static function applyMaskPenaltyRule1Internal(ByteMatrix $matrix, $isHorizontal) {
		$penalty = 0;
		$iLimit = $isHorizontal ? $matrix->getHeight() : $matrix->getWidth();
		$jLimit = $isHorizontal ? $matrix->getWidth() : $matrix->getHeight();
		$array = $matrix->getArray();
		
		for($i = 0; $i < $iLimit; $i++) {
			$numSameBitCells = 0;
			$prevBit = -1;
			
			for($j = 0; $j < $jLimit; $j++) {
				$bit = $isHorizontal ? $array[$i][$j] : $array[$j][$i];

				if($bit === $prevBit) {
					$numSameBitCells++;
				} else {
					if($numSameBitCells >= 5) {
						$penalty += self::N1 + ($numSameBitCells - 5);
					}
					
					$numSameBitCells = 1;
					$prevBit = $bit;
				}
			}
			
			if($numSameBitCells >= 5) {
				$penalty += self::N1 + ($numSameBitCells - 5);
			}
		}
		
		return $penalty;
	}
}