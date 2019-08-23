<?php
declare(strict_types = 1);

namespace W2W\QRCode\Renderer\RendererStyle;

use W2W\QRCode\Renderer\Eye\EyeInterface;
use W2W\QRCode\Renderer\Eye\ModuleEye;
use W2W\QRCode\Renderer\Module\ModuleInterface;
use W2W\QRCode\Renderer\Module\SquareModule;

final class RendererStyle {
	private $size;
	
	private $margin;
	
	private $module;
	
	private $eye;
	
	private $fill;
	
	public function __construct(int $size, int $margin = 4, ? ModuleInterface $module = null, ? EyeInterface $eye = null, ? Fill $fill = null) {
		$this->margin = $margin;
		$this->size = $size;
		$this->module = $module ? : SquareModule::instance();
		$this->eye = $eye ? : new ModuleEye($this->module);
		$this->fill = $fill ? : Fill::default();
	}
	
	public function withSize(int $size) : self {
		$style = clone $this;
		$style->size = $size;
		
		return $style;
	}
	
	public function withMargin(int $margin) : self {
		$style = clone $this;
		$style->margin = $margin;
		
		return $style;
	}
	
	public function getSize() : int {
		return $this->size;
	}
	
	public function getMargin() : int {
		return $this->margin;
	}
	
	public function getModule() : ModuleInterface {
		return $this->module;
	}
	
	public function getEye() : EyeInterface {
		return $this->eye;
	}
	
	public function getFill() : Fill {
		return $this->fill;
	}
}