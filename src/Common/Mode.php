<?php
namespace W2W\QRCode\Common;

class Mode extends AbstractEnum {
	const TERMINATOR = 0x0;
	
	const NUMERIC = 0x1;
	
	const ALPHANUMERIC = 0x2;
	
	const STRUCTURED_APPEND = 0x3;
	
	const BYTE = 0x4;
	
	const ECI = 0x7;
	
	const KANJI = 0x8;
	
	const FNC1_FIRST_POSITION = 0x5;
	
	const FNC1_SECOND_POSITION = 0x9;
	
	const HANZI = 0xd;
	
	protected static $characterCountBitsForVersions = array(self::TERMINATOR => array(0, 0, 0), self::NUMERIC => array(10, 12, 14), self::ALPHANUMERIC => array(9, 11, 13), self::STRUCTURED_APPEND => array(0, 0, 0), self::BYTE => array(8, 16, 16), self::ECI => array(0, 0, 0), self::KANJI => array(8, 10, 12), self::FNC1_FIRST_POSITION => array(0, 0, 0), self::FNC1_SECOND_POSITION => array(0, 0, 0), self::HANZI => array(8, 10, 12));
	
	public function getCharacterCountBits(Version $version) {
		$number = $version->getVersionNumber();
		
		if($number <= 9) {
			$offset = 0;
		} elseif($number <= 26) {
			$offset = 1;
		} else {
			$offset = 2;
		}
		
		return self::$characterCountBitsForVersions[$this->value][$offset];
	}
}