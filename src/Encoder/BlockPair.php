<?php
namespace W2W\QRCode\Encoder;

use SplFixedArray;

class BlockPair {
	protected $dataBytes;
	
	protected $errorCorrectionBytes;
	
	public function __construct(SplFixedArray $data, SplFixedArray $errorCorrection) {
		$this->dataBytes = $data;
		$this->errorCorrectionBytes = $errorCorrection;
	}
	
	public function getDataBytes() {
		return $this->dataBytes;
	}
	
	public function getErrorCorrectionBytes() {
		return $this->errorCorrectionBytes;
	}
}