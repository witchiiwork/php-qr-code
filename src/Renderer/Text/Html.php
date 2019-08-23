<?php
namespace W2W\QRCode\Renderer\Text;

use W2W\QRCode\Encoder\QrCode;

class Html extends Plain {
	protected $class = "";
	
	protected $style = "font-family: monospace; line-height: 0.65em; letter-spacing: -1px";
	
	public function setClass($class) {
		$this->class = $class;
	}
	
	public function getClass() {
		return $this->class;
	}
	
	public function setStyle($style) {
		$this->style = $style;
	}
	
	public function getStyle() {
		return $this->style;
	}
	
	public function render(QrCode $qrCode) {
		$textCode = parent::render($qrCode);
		$result = "<pre style=\"" . htmlspecialchars($this->style, ENT_QUOTES, "utf-8") . "\" class=\"" . htmlspecialchars($this->class, ENT_QUOTES, "utf-8") . "\">" . $textCode . "</pre>";
		
		return $result;
	}
}