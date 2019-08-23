<?php
namespace W2W\QRCode;

use W2W\QRCode\Common\ErrorCorrectionLevel;
use W2W\QRCode\Encoder\Encoder;
use W2W\QRCode\Exception;
use W2W\QRCode\Renderer\RendererInterface;

class Writer {
	protected $renderer;
	
	public function __construct(RendererInterface $renderer) {
		$this->renderer = $renderer;
	}
	
	public function setRenderer(RendererInterface $renderer) {
		$this->renderer = $renderer;
		
		return $this;
	}
	
	public function getRenderer() {
		return $this->renderer;
	}
	
	public function writeString($content, $encoding = Encoder::DEFAULT_BYTE_MODE_ECODING, $ecLevel = ErrorCorrectionLevel::L) {
		if(strlen($content) === 0) {
			throw new Exception\InvalidArgumentException("Found empty contents");
		}
		
		$qrCode = Encoder::encode($content, new ErrorCorrectionLevel($ecLevel), $encoding);
		
		return $this->getRenderer()->render($qrCode);
	}
	
	public function writeFile($content, $filename, $encoding = Encoder::DEFAULT_BYTE_MODE_ECODING, $ecLevel = ErrorCorrectionLevel::L) {
		file_put_contents($filename, $this->writeString($content, $encoding, $ecLevel));
	}
}