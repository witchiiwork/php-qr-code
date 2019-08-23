<?php
declare(strict_types = 1);

namespace W2W\QRCode\Renderer\Image;

use W2W\QRCode\Exception\RuntimeException;
use W2W\QRCode\Renderer\Color\ColorInterface;
use W2W\QRCode\Renderer\Path\Path;
use W2W\QRCode\Renderer\RendererStyle\Gradient;

interface ImageBackEndInterface {
	public function new(int $size, ColorInterface $backgroundColor) : void;
	
	public function scale(float $size) : void;
	
	public function translate(float $x, float $y) : void;
	
	public function rotate(int $degrees) : void;
	
	public function push() : void;
	
	public function pop() : void;
	
	public function drawPathWithColor(Path $path, ColorInterface $color) : void;
	
	public function drawPathWithGradient(Path $path, Gradient $gradient, float $x, float $y, float $width, float $height) : void;
	
	public function done() : string;
}