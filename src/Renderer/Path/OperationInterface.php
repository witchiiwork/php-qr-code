<?php
declare(strict_types = 1);

namespace W2W\QRCode\Renderer\Path;

interface OperationInterface {
	public function translate(float $x, float $y) : self;
}