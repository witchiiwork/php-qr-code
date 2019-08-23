<?php
namespace W2W\QRCode\Renderer\Image;

use W2W\QRCode\Renderer\Color\ColorInterface;
use W2W\QRCode\Renderer\RendererInterface as GeneralRendererInterface;

interface RendererInterface extends GeneralRendererInterface {
	public function init();
	
	public function addColor($id, ColorInterface $color);
	
	public function drawBackground($colorId);
	
	public function drawBlock($x, $y, $colorId);
	
	public function getByteStream();
}