<?php
declare(strict_types = 1);

namespace W2W\QRCode\Renderer\Path;

final class Line implements OperationInterface {
	private $x;
	
	private $y;
	
	public function __construct(float $x, float $y) {
		$this->x = $x;
		$this->y = $y;
	}
	
	public function getX() : float {
		return $this->x;
	}
	
	public function getY() : float {
		return $this->y;
	}
	
	public function translate(float $x, float $y) : OperationInterface {
		return new self($this->x + $x, $this->y + $y);
	}
}