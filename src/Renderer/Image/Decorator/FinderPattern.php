<?php
namespace W2W\QRCode\Renderer\Image\Decorator;

use W2W\QRCode\Encoder\QrCode;
use W2W\QRCode\Renderer\Image\RendererInterface;
use W2W\QRCode\Renderer\Color;

class FinderPattern implements DecoratorInterface {
	protected $innerColor;
	
	protected $outerColor;
	
	protected static $outerPositionDetectionPattern = array(array(1, 1, 1, 1, 1, 1, 1), array(1, 0, 0, 0, 0, 0, 1), array(1, 0, 0, 0, 0, 0, 1), array(1, 0, 0, 0, 0, 0, 1), array(1, 0, 0, 0, 0, 0, 1), array(1, 0, 0, 0, 0, 0, 1), array(1, 1, 1, 1, 1, 1, 1));
	
	protected static $innerPositionDetectionPattern = array(array(0, 0, 0, 0, 0, 0, 0), array(0, 0, 0, 0, 0, 0, 0), array(0, 0, 1, 1, 1, 0, 0), array(0, 0, 1, 1, 1, 0, 0), array(0, 0, 1, 1, 1, 0, 0), array(0, 0, 0, 0, 0, 0, 0), array(0, 0, 0, 0, 0, 0, 0));
	
	public function setOuterColor(Color\ColorInterface $color) {
		$this->outerColor = $color;
		
		return $this;
	}
	
	public function getOuterColor() {
		if($this->outerColor === null) {
			$this->outerColor = new Color\Gray(100);
		}
		
		return $this->outerColor;
	}
	
	public function setInnerColor(Color\ColorInterface $color) {
		$this->innerColor = $color;
		return $this;
	}
	
	public function getInnerColor() {
		if($this->innerColor === null) {
			$this->innerColor = new Color\Gray(0);
		}
		
		return $this->innerColor;
	}
	
	public function preProcess(QrCode $qrCode, RendererInterface $renderer, $outputWidth, $outputHeight, $leftPadding, $topPadding, $multiple) {
		$matrix = $qrCode->getMatrix();
		$positions = array(array(0, 0), array($matrix->getWidth() - 7, 0), array(0, $matrix->getHeight() - 7));
		
		foreach(self::$outerPositionDetectionPattern as $y => $row) {
			foreach($row as $x => $isSet) {
				foreach($positions as $position) {
					$matrix->set($x + $position[0], $y + $position[1], 0);
				}
			}
		}
	}
	
	public function postProcess(QrCode $qrCode, RendererInterface $renderer, $outputWidth, $outputHeight, $leftPadding, $topPadding, $multiple) {
		$matrix = $qrCode->getMatrix();
		$positions = array(array(0, 0), array($matrix->getWidth() - 7, 0), array(0, $matrix->getHeight() - 7));
		$renderer->addColor("finder-outer", $this->getOuterColor());
		$renderer->addColor("finder-inner", $this->getInnerColor());
		
		foreach(self::$outerPositionDetectionPattern as $y => $row) {
			foreach($row as $x => $isOuterSet) {
				$isInnerSet = self::$innerPositionDetectionPattern[$y][$x];
				
				if($isOuterSet) {
					foreach($positions as $position) {
						$renderer->drawBlock($leftPadding + $x * $multiple + $position[0] * $multiple, $topPadding + $y * $multiple + $position[1] * $multiple, "finder-outer");
					}
				}
				
				if($isInnerSet) {
					foreach($positions as $position) {
						$renderer->drawBlock($leftPadding + $x * $multiple + $position[0] * $multiple, $topPadding + $y * $multiple + $position[1] * $multiple, "finder-inner");
					}
				}
			}
		}
	}
}