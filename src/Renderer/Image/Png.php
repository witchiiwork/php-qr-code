<?php
namespace W2W\QRCode\Renderer\Image;

use W2W\QRCode\Exception;
use W2W\QRCode\Renderer\Color\ColorInterface;

class Png extends AbstractRenderer {
	protected $image;
	
	protected $colors = array();
	
	public function init() {
		$this->image = imagecreatetruecolor($this->finalWidth, $this->finalHeight);
	}
	
	public function addColor($id, ColorInterface $color) {
		if($this->image === null) {
			throw new Exception\RuntimeException("Colors can only be added after init");
		}
		
		$color = $color->toRgb();
		$this->colors[$id] = imagecolorallocate($this->image, $color->getRed(), $color->getGreen(), $color->getBlue());
	}
	
	public function drawBackground($colorId) {
		imagefill($this->image, 0, 0, $this->colors[$colorId]);
	}
	
	public function drawBlock($x, $y, $colorId) {
		imagefilledrectangle($this->image, $x, $y, $x + $this->blockSize - 1, $y + $this->blockSize - 1, $this->colors[$colorId]);
	}
	
	public function getByteStream() {
		ob_start();
		imagepng($this->image);
		return ob_get_clean();
	}
}