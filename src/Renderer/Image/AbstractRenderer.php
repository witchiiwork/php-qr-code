<?php
namespace W2W\QRCode\Renderer\Image;

use W2W\QRCode\Encoder\QrCode;
use W2W\QRCode\Renderer\Color;
use W2W\QRCode\Renderer\Image\Decorator\DecoratorInterface;
use W2W\QRCode\Exception;

abstract class AbstractRenderer implements RendererInterface {
	protected $margin = 4;
	
	protected $width = 0;
	
	protected $height = 0;
	
	protected $roundDimensions = true;
	
	protected $finalWidth;
	
	protected $finalHeight;
	
	protected $blockSize;
	
	protected $backgroundColor;
	
	protected $floorToClosestDimension;
	
	protected $foregroundColor;
	
	protected $decorators = array();
	
	public function setMargin($margin) {
		if($margin < 0) {
			throw new Exception\InvalidArgumentException("Margin must be equal to greater than 0");
		}
		
		$this->margin = (int)$margin;
		
		return $this;
	}
	
	public function getMargin() {
		return $this->margin;
	}
	
	public function setWidth($width) {
		$this->width = (int)$width;
		
		return $this;
	}
	
	public function getWidth() {
		return $this->width;
	}
	
	public function setHeight($height) {
		$this->height = (int)$height;
		
		return $this;
	}
	
	public function getHeight() {
		return $this->height;
	}
	
	public function setRoundDimensions($flag) {
		$this->floorToClosestDimension = $flag;
		
		return $this;
	}
	
	public function shouldRoundDimensions() {
		return $this->floorToClosestDimension;
	}
	
	public function setBackgroundColor(Color\ColorInterface $color) {
		$this->backgroundColor = $color;
		
		return $this;
	}
	
	public function getBackgroundColor() {
		if($this->backgroundColor === null) {
			$this->backgroundColor = new Color\Gray(100);
		}
		
		return $this->backgroundColor;
	}
	
	public function setForegroundColor(Color\ColorInterface $color) {
		$this->foregroundColor = $color;
		
		return $this;
	}
	
	public function getForegroundColor() {
		if($this->foregroundColor === null) {
			$this->foregroundColor = new Color\Gray(0);
		}
		
		return $this->foregroundColor;
	}
	
	public function addDecorator(DecoratorInterface $decorator) {
		$this->decorators[] = $decorator;
		return $this;
	}
	
	public function render(QrCode $qrCode) {
		$input = $qrCode->getMatrix();
		$inputWidth = $input->getWidth();
		$inputHeight = $input->getHeight();
		$qrWidth = $inputWidth + ($this->getMargin() << 1);
		$qrHeight = $inputHeight + ($this->getMargin() << 1);
		$outputWidth = max($this->getWidth(), $qrWidth);
		$outputHeight = max($this->getHeight(), $qrHeight);
		$multiple = (int)min($outputWidth / $qrWidth, $outputHeight / $qrHeight);
		
		if($this->shouldRoundDimensions()) {
			$outputWidth -= $outputWidth % $multiple;
			$outputHeight -= $outputHeight % $multiple;
		}
		
		$leftPadding = (int)(($outputWidth - ($inputWidth * $multiple)) / 2);
		$topPadding = (int)(($outputHeight - ($inputHeight * $multiple)) / 2);
		$this->finalWidth = $outputWidth;
		$this->finalHeight = $outputHeight;
		$this->blockSize = $multiple;
		$this->init();
		$this->addColor("background", $this->getBackgroundColor());
		$this->addColor("foreground", $this->getForegroundColor());
		$this->drawBackground("background");
		
		foreach($this->decorators as $decorator) {
			$decorator->preProcess($qrCode, $this, $outputWidth, $outputHeight, $leftPadding, $topPadding, $multiple);
		}
		
		for($inputY = 0, $outputY = $topPadding; $inputY < $inputHeight; $inputY++, $outputY += $multiple) {
			for($inputX = 0, $outputX = $leftPadding; $inputX < $inputWidth; $inputX++, $outputX += $multiple) {
				if($input->get($inputX, $inputY) === 1) {
					$this->drawBlock($outputX, $outputY, "foreground");
				}
			}
		}
		
		foreach($this->decorators as $decorator) {
			$decorator->postProcess($qrCode, $this, $outputWidth, $outputHeight, $leftPadding, $topPadding, $multiple);
		}
		
		return $this->getByteStream();
	}
}