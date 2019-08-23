<?php
namespace W2W\QRCode\Renderer\Color;

interface ColorInterface {
	public function toRgb();
	
	public function toCmyk();
	
	public function toGray();
}