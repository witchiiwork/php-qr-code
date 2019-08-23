<?php
namespace W2W\QRCode\Renderer\Image;

use W2W\QRCode\Renderer\Color\ColorInterface;
use W2W\QRCode\Renderer\Color\Rgb;
use W2W\QRCode\Renderer\Color\Cmyk;
use W2W\QRCode\Renderer\Color\Gray;

class Eps extends AbstractRenderer {
	protected $eps;
	
	protected $colors = array();
	
	protected $currentColor;
	
	public function init() {
		$this->eps = "%!PS-Adobe-3.0 EPSF-3.0\n%%BoundingBox: 0 0 " . $this->finalWidth . " " . $this->finalHeight . "\n/F { rectfill } def\n";
	}
	
	public function addColor($id, ColorInterface $color) {
		if(!$color instanceof Rgb && !$color instanceof Cmyk && !$color instanceof Gray) {
			$color = $color->toCmyk();
		}
		
		$this->colors[$id] = $color;
	}
	
	public function drawBackground($colorId) {
		$this->setColor($colorId);
		$this->eps .= "0 0 " . $this->finalWidth . " " . $this->finalHeight . " F\n";
	}
	
	public function drawBlock($x, $y, $colorId) {
		$this->setColor($colorId);
		$this->eps .= $x . " " . ($this->finalHeight - $y - $this->blockSize) . " " . $this->blockSize . " " . $this->blockSize . " F\n";
	}
	
	public function getByteStream() {
		return $this->eps;
	}
	
	protected function setColor($colorId) {
		if($colorId !== $this->currentColor) {
			$color = $this->colors[$colorId];
			
			if($color instanceof Rgb) {
				$this->eps .= sprintf("%F %F %F setrgbcolor\n", $color->getRed() / 100, $color->getGreen() / 100, $color->getBlue() / 100);
			} elseif($color instanceof Cmyk) {
				$this->eps .= sprintf("%F %F %F %F setcmykcolor\n", $color->getCyan() / 100, $color->getMagenta() / 100, $color->getYellow() / 100, $color->getBlack() / 100);
			} elseif($color instanceof Gray) {
				$this->eps .= sprintf("%F setgray\n", $color->getGray() / 100);
			}
			
			$this->currentColor = $colorId;
		}
	}
}