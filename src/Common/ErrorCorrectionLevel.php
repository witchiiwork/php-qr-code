<?php
namespace W2W\QRCode\Common;

class ErrorCorrectionLevel extends AbstractEnum {
	const L = 0x1;
	
	const M = 0x0;
	
	const Q = 0x3;
	
	const H = 0x2;
	
	public function getOrdinal() {
		switch($this->value) {
			case self::L:
				return 0;
				
				break;
			case self::M:
				return 1;
				
				break;
			case self::Q:
				return 2;
				
				break;
			case self::H:
				return 3;
				break;
		}
	}
}