<?php
namespace W2W\QRCode\Encoder;

use W2W\QRCode\Common\BitArray;
use W2W\QRCode\Common\ErrorCorrectionLevel;
use W2W\QRCode\Common\Version;
use W2W\QRCode\Exception;

class MatrixUtil {
	protected static $positionDetectionPattern = array(array(1, 1, 1, 1, 1, 1, 1), array(1, 0, 0, 0, 0, 0, 1), array(1, 0, 1, 1, 1, 0, 1), array(1, 0, 1, 1, 1, 0, 1), array(1, 0, 1, 1, 1, 0, 1), array(1, 0, 0, 0, 0, 0, 1), array(1, 1, 1, 1, 1, 1, 1));
	
	protected static $positionAdjustmentPattern = array(array(1, 1, 1, 1, 1), array(1, 0, 0, 0, 1), array(1, 0, 1, 0, 1), array(1, 0, 0, 0, 1), array(1, 1, 1, 1, 1));
	
	protected static $positionAdjustmentPatternCoordinateTable = array(array(null, null, null, null, null, null, null), array(6, 18, null, null, null, null, null), array(6, 22, null, null, null, null, null), array(6, 26, null, null, null, null, null), array(6, 30, null, null, null, null, null), array(6, 34, null, null, null, null, null), array(6, 22, 38, null, null, null, null), array(6, 24, 42, null, null, null, null), array(6, 26, 46, null, null, null, null), array(6, 28, 50, null, null, null, null), array(6, 30, 54, null, null, null, null), array(6, 32, 58, null, null, null, null), array(6, 34, 62, null, null, null, null), array(6, 26, 46, 66, null, null, null), array(6, 26, 48, 70, null, null, null), array(6, 26, 50, 74, null, null, null), array(6, 30, 54, 78, null, null, null), array(6, 30, 56, 82, null, null, null), array(6, 30, 58, 86, null, null, null), array(6, 34, 62, 90, null, null, null), array(6, 28, 50, 72, 94, null, null), array(6, 26, 50, 74, 98, null, null), array(6, 30, 54, 78, 102, null, null), array(6, 28, 54, 80, 106, null, null), array(6, 32, 58, 84, 110, null, null), array(6, 30, 58, 86, 114, null, null), array(6, 34, 62, 90, 118, null, null), array(6, 26, 50, 74, 98, 122, null), array(6, 30, 54, 78, 102, 126, null), array(6, 26, 52, 78, 104, 130, null), array(6, 30, 56, 82, 108, 134, null), array(6, 34, 60, 86, 112, 138, null), array(6, 30, 58, 86, 114, 142, null), array(6, 34, 62, 90, 118, 146, null), array(6, 30, 54, 78, 102, 126, 150), array(6, 24, 50, 76, 102, 128, 154), array(6, 28, 54, 80, 106, 132, 158), array(6, 32, 58, 84, 110, 136, 162), array(6, 26, 54, 82, 110, 138, 166), array(6, 30, 58, 86, 114, 142, 170));
	
	protected static $typeInfoCoordinates = array(array(8, 0), array(8, 1), array(8, 2), array(8, 3), array(8, 4), array(8, 5), array(8, 7), array(8, 8), array(7, 8), array(5, 8), array(4, 8), array(3, 8), array(2, 8), array(1, 8), array(0, 8));
	
	protected static $versionInfoPoly = 0x1f25;
	
	protected static $typeInfoPoly = 0x537;
	
	protected static $typeInfoMaskPattern = 0x5412;
	
	public static function clearMatrix(ByteMatrix $matrix) {
		$matrix->clear(-1);
	}
	
	public static function buildMatrix(BitArray $dataBits, ErrorCorrectionLevel $level, Version $version, $maskPattern, ByteMatrix $matrix) {
		self::clearMatrix($matrix);
		self::embedBasicPatterns($version, $matrix);
		self::embedTypeInfo($level, $maskPattern, $matrix);
		self::maybeEmbedVersionInfo($version, $matrix);
		self::embedDataBits($dataBits, $maskPattern, $matrix);
	}
	
	protected static function embedTypeInfo(ErrorCorrectionLevel $level, $maskPattern, ByteMatrix $matrix) {
		$typeInfoBits = new BitArray();
		
		self::makeTypeInfoBits($level, $maskPattern, $typeInfoBits);
		
		$typeInfoBitsSize = $typeInfoBits->getSize();
		
		for($i = 0; $i < $typeInfoBitsSize; $i++) {
			$bit = $typeInfoBits->get($typeInfoBitsSize - 1 - $i);
			$x1 = self::$typeInfoCoordinates[$i][0];
			$y1 = self::$typeInfoCoordinates[$i][1];
			$matrix->set($x1, $y1, $bit);
			
			if($i < 8) {
				$x2 = $matrix->getWidth() - $i - 1;
				$y2 = 8;
			} else {
				$x2 = 8;
				$y2 = $matrix->getHeight() - 7 + ($i - 8);
			}
			
			$matrix->set($x2, $y2, $bit);
		}
	}
	
	protected static function makeTypeInfoBits(ErrorCorrectionLevel $level, $maskPattern, BitArray $bits) {
		$typeInfo = ($level->get() << 3) | $maskPattern;
		$bits->appendBits($typeInfo, 5);
		$bchCode = self::calculateBchCode($typeInfo, self::$typeInfoPoly);
		$bits->appendBits($bchCode, 10);
		$maskBits = new BitArray();
		$maskBits->appendBits(self::$typeInfoMaskPattern, 15);
		$bits->xorBits($maskBits);
		
		if($bits->getSize() !== 15) {
			throw new Exception\RuntimeException("Bit array resulted in invalid size: " . $bits->getSize());
		}
	}
	
	protected static function maybeEmbedVersionInfo(Version $version, ByteMatrix $matrix) {
		if($version->getVersionNumber() < 7) {
			return;
		}
		
		$versionInfoBits = new BitArray();
		
		self::makeVersionInfoBits($version, $versionInfoBits);
		
		$bitIndex = 6 * 3 - 1;
		
		for($i = 0; $i < 6; $i++) {
			for($j = 0; $j < 3; $j++) {
				$bit = $versionInfoBits->get($bitIndex);
				$bitIndex--;
				$matrix->set($i, $matrix->getHeight() - 11 + $j, $bit);
				$matrix->set($matrix->getHeight() - 11 + $j, $i, $bit);
			}
		}
	}
	
	protected static function makeVersionInfoBits(Version $version, BitArray $bits) {
		$bits->appendBits($version->getVersionNumber(), 6);
		$bchCode = self::calculateBchCode($version->getVersionNumber(), self::$versionInfoPoly);
		$bits->appendBits($bchCode, 12);
		
		if($bits->getSize() !== 18) {
			throw new Exception\RuntimeException("Bit array resulted in invalid size: " . $bits->getSize());
		}
	}
	
	protected static function calculateBchCode($value, $poly) {
		$msbSetInPoly = self::findMsbSet($poly);
		$value <<= $msbSetInPoly - 1;
		
		while(self::findMsbSet($value) >= $msbSetInPoly) {
			$value ^= $poly << (self::findMsbSet($value) - $msbSetInPoly);
		}
		
		return $value;
	}
	
	protected static function findMsbSet($value) {
		$numDigits = 0;
		
		while($value !== 0) {
			$value >>= 1;
			$numDigits++;
		}
		
		return $numDigits;
	}
	
	protected static function embedBasicPatterns(Version $version, ByteMatrix $matrix) {
		self::embedPositionDetectionPatternsAndSeparators($matrix);
		self::embedDarkDotAtLeftBottomCorner($matrix);
		self::maybeEmbedPositionAdjustmentPatterns($version, $matrix);
		self::embedTimingPatterns($matrix);
	}
	
	protected static function embedPositionDetectionPatternsAndSeparators(ByteMatrix $matrix) {
		$pdpWidth = count(self::$positionDetectionPattern[0]);
		
		self::embedPositionDetectionPattern(0, 0, $matrix);
		self::embedPositionDetectionPattern($matrix->getWidth() - $pdpWidth, 0, $matrix);
		self::embedPositionDetectionPattern(0, $matrix->getWidth() - $pdpWidth, $matrix);
		
		$hspWidth = 8;
		
		self::embedHorizontalSeparationPattern(0, $hspWidth - 1, $matrix);
		self::embedHorizontalSeparationPattern($matrix->getWidth() - $hspWidth, $hspWidth - 1, $matrix);
		self::embedHorizontalSeparationPattern(0, $matrix->getWidth() - $hspWidth, $matrix);
		
		$vspSize = 7;
		
		self::embedVerticalSeparationPattern($vspSize, 0, $matrix);
		self::embedVerticalSeparationPattern($matrix->getHeight() - $vspSize - 1, 0, $matrix);
		self::embedVerticalSeparationPattern($vspSize, $matrix->getHeight() - $vspSize, $matrix);
	}
	
	protected static function embedPositionDetectionPattern($xStart, $yStart, ByteMatrix $matrix) {
		for($y = 0; $y < 7; $y++) {
			for($x = 0; $x < 7; $x++) {
				$matrix->set($xStart + $x, $yStart + $y, self::$positionDetectionPattern[$y][$x]);
			}
		}
	}
	
	protected static function embedHorizontalSeparationPattern($xStart, $yStart, ByteMatrix $matrix) {
		for($x = 0; $x < 8; $x++) {
			if($matrix->get($xStart + $x, $yStart) !== -1) {
				throw new Exception\RuntimeException("Byte already set");
			}
			
			$matrix->set($xStart + $x, $yStart, 0);
		}
	}
	
	protected static function embedVerticalSeparationPattern($xStart, $yStart, ByteMatrix $matrix) {
		for($y = 0; $y < 7; $y++) {
			if($matrix->get($xStart, $yStart + $y) !== -1) {
				throw new Exception\RuntimeException("Byte already set");
			}
			
			$matrix->set($xStart, $yStart + $y, 0);
		}
	}
	
	protected static function embedDarkDotAtLeftBottomCorner(ByteMatrix $matrix) {
		if($matrix->get(8, $matrix->getHeight() - 8) === 0) {
			throw new Exception\RuntimeException("Byte already set to 0");
		}
		
		$matrix->set(8, $matrix->getHeight() - 8, 1);
	}
	
	protected static function maybeEmbedPositionAdjustmentPatterns(Version $version, ByteMatrix $matrix) {
		if($version->getVersionNumber() < 2) {
			return;
		}
		
		$index = $version->getVersionNumber() - 1;
		$coordinates = self::$positionAdjustmentPatternCoordinateTable[$index];
		$numCoordinates = count($coordinates);
		
		for($i = 0; $i < $numCoordinates; $i++) {
			for($j = 0; $j < $numCoordinates; $j++) {
				$y = $coordinates[$i];
				$x = $coordinates[$j];
				
				if($x === null || $y === null) {
					continue;
				}
				
				if($matrix->get($x, $y) === -1) {
					self::embedPositionAdjustmentPattern($x - 2, $y - 2, $matrix);
				}
			}
		}
	}
	
	protected static function embedPositionAdjustmentPattern($xStart, $yStart, ByteMatrix $matrix) {
		for($y = 0; $y < 5; $y++) {
			for($x = 0; $x < 5; $x++) {
				$matrix->set($xStart + $x, $yStart + $y, self::$positionAdjustmentPattern[$y][$x]);
			}
		}
	}
	
	protected static function embedTimingPatterns(ByteMatrix $matrix) {
		$matrixWidth = $matrix->getWidth();
		
		for($i = 8; $i < $matrixWidth - 8; $i++) {
			$bit = ($i + 1) % 2;
			
			if($matrix->get($i, 6) === -1) {
				$matrix->set($i, 6, $bit);
			}
			
			if($matrix->get(6, $i) === -1) {
				$matrix->set(6, $i, $bit);
			}
		}
	}
	
	protected static function embedDataBits(BitArray $dataBits, $maskPattern, ByteMatrix $matrix) {
		$bitIndex = 0;
		$direction = -1;
		$x = $matrix->getWidth() - 1;
		$y = $matrix->getHeight() - 1;
		
		while($x > 0) {
			if($x === 6) {
				$x--;
			}
			
			while($y >= 0 && $y < $matrix->getHeight()) {
				for($i = 0; $i < 2; $i++) {
					$xx = $x - $i;
					
					if($matrix->get($xx, $y) !== -1) {
						continue;
					}
					
					if($bitIndex < $dataBits->getSize()) {
						$bit = $dataBits->get($bitIndex);
						$bitIndex++;
					} else {
						$bit = false;
					}
					
					if($maskPattern !== -1 && MaskUtil::getDataMaskBit($maskPattern, $xx, $y)) {
						$bit = !$bit;
					}
					
					$matrix->set($xx, $y, $bit);
				}
				
				$y += $direction;
			}
			
			$direction = -$direction;
			$y += $direction;
			$x -= 2;
		}
		
		if($bitIndex !== $dataBits->getSize()) {
			throw new Exception\WriterException("Not all bits consumed (" . $bitIndex . " out of " . $dataBits->getSize() .")");
		}
	}
}