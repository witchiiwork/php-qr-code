<?php
declare(strict_types = 1);

namespace W2W\QRCode\Renderer\Color;

use W2W\QRCode\Exception;

final class Alpha implements ColorInterface {
	private $alpha;
	
	private $baseColor;
	
	public function __construct(int $alpha, ColorInterface $baseColor) {
		if($alpha < 0 || $alpha > 100) {
			throw new Exception\InvalidArgumentException("Alpha must be between 0 and 100");
		}
		
		$this->alpha = $alpha;
		$this->baseColor = $baseColor;
	}
	
	public function getAlpha() : int {
		return $this->alpha;
	}
	
	public function getBaseColor() : ColorInterface {
		return $this->baseColor;
	}
	
	public function toRgb() : Rgb {
		return $this->baseColor->toRgb();
	}
	
	public function toCmyk() : Cmyk {
		return $this->baseColor->toCmyk();
	}
	
	public function toGray() : Gray {
		return $this->baseColor->toGray();
	}
}