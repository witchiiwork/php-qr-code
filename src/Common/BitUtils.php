<?php
namespace W2W\QRCode\Common;

class BitUtils {
	public static function unsignedRightShift($a, $b) {
		return ($a >= 0 ? $a >> $b : (($a & 0x7fffffff) >> $b) | (0x40000000 >> ($b - 1)));
	}
	
	public static function numberOfTrailingZeros($i) {
		$lastPos = strrpos(str_pad(decbin($i), 32, "0", STR_PAD_LEFT), "1");
		
		return $lastPos === false ? 32 : 31 - $lastPos;
	}
}