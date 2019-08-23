<?php
namespace W2W\QRCode\Encoder;

use W2W\QRCode\Common\ErrorCorrectionLevel;
use W2W\QRCode\Common\Mode;
use W2W\QRCode\Common\Version;

class QrCode {
	const NUM_MASK_PATTERNS = 8;
	
	protected $mode;
	
	protected $errorCorrectionLevel;
	
	protected $version;
	
	protected $maskPattern = -1;
	
	protected $matrix;
	
	public function getMode() {
		return $this->mode;
	}
	
	public function setMode(Mode $mode) {
		$this->mode = $mode;
	}
	
	public function getErrorCorrectionLevel() {
		return $this->errorCorrectionLevel;
	}
	
	public function setErrorCorrectionLevel(ErrorCorrectionLevel $errorCorrectionLevel) {
		$this->errorCorrectionLevel = $errorCorrectionLevel;
	}
	
	public function getVersion() {
		return $this->version;
	}
	
	public function setVersion(Version $version) {
		$this->version = $version;
	}
	
	public function getMaskPattern() {
		return $this->maskPattern;
	}
	
	public function setMaskPattern($maskPattern) {
		$this->maskPattern = $maskPattern;
	}
	
	public function getMatrix() {
		return $this->matrix;
	}
	
	public function setMatrix(ByteMatrix $matrix) {
		$this->matrix = $matrix;
	}
	
	public static function isValidMaskPattern($maskPattern) {
		return $maskPattern > 0 && $maskPattern < self::NUM_MASK_PATTERNS;
	}
	
	public function __toString() {
		$result = "<<\n mode: " . $this->mode . "\n ecLevel: " . $this->errorCorrectionLevel . "\n version: " . $this->version . "\n maskPattern: " . $this->maskPattern . "\n";
		
		if($this->matrix === null) {
			$result .= " matrix: null\n";
		} else {
			$result .= " matrix:\n";
			$result .= $this->matrix;
		}
		
		$result .= ">>\n";
		
		return $result;
	}
}