<?php
declare(strict_types = 1);

namespace W2W\QRCode\Renderer\Path;

final class Close implements OperationInterface {
	private static $instance;

	private function __construct() {
	}
	
	public static function instance() : self {
		return self::$instance ?: self::$instance = new self();
	}
	
	public function translate(float $x, float $y) : OperationInterface {
		return $this;
	}
}