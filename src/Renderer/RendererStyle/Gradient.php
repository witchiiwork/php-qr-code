<?php
declare(strict_types = 1);

namespace W2W\QRCode\Renderer\RendererStyle;

use W2W\QRCode\Renderer\Color\ColorInterface;

final class Gradient {
	private $startColor;
	
	private $endColor;
	
	private $type;
	
	public function __construct(ColorInterface $startColor, ColorInterface $endColor, GradientType $type) {
		$this->startColor = $startColor;
		$this->endColor = $endColor;
		$this->type = $type;
	}
	
	public function getStartColor() : ColorInterface {
		return $this->startColor;
	}
	
	public function getEndColor() : ColorInterface {
		return $this->endColor;
	}
	
	public function getType() : GradientType {
		return $this->type;
	}
}