<?php
declare(strict_types = 1);

namespace W2W\QRCode\Renderer\Image;

final class TransformationMatrix {
	private $values;
	
	public function __construct() {
		$this->values = [1, 0, 0, 1, 0, 0];
	}
	
	public function multiply(self $other) : self {
		$matrix = new self();
		$matrix->values[0] = $this->values[0] * $other->values[0] + $this->values[2] * $other->values[1];
		$matrix->values[1] = $this->values[1] * $other->values[0] + $this->values[3] * $other->values[1];
		$matrix->values[2] = $this->values[0] * $other->values[2] + $this->values[2] * $other->values[3];
		$matrix->values[3] = $this->values[1] * $other->values[2] + $this->values[3] * $other->values[3];
		$matrix->values[4] = $this->values[0] * $other->values[4] + $this->values[2] * $other->values[5] + $this->values[4];
		$matrix->values[5] = $this->values[1] * $other->values[4] + $this->values[3] * $other->values[5] + $this->values[5];
		
		return $matrix;
	}
	
	public static function scale(float $size) : self {
		$matrix = new self();
		$matrix->values = [$size, 0, 0, $size, 0, 0];
		return $matrix;
	}
	
	public static function translate(float $x, float $y) : self {
		$matrix = new self();
		$matrix->values = [1, 0, 0, 1, $x, $y];
		return $matrix;
	}
	
	public static function rotate(int $degrees) : self {
		$matrix = new self();
		$matrix->values = [cos($degrees), sin($degrees), -sin($degrees), cos($degrees), 0, 0];
		return $matrix;
	}
	
	public function apply(float $x, float $y) : array {
		return [$x * $this->values[0] + $y * $this->values[2] + $this->values[4],$x * $this->values[2] + $x * $this->values[3] + $this->values[5]];
	}
}