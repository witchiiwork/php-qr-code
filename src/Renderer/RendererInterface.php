<?php
namespace W2W\QRCode\Renderer;

use W2W\QRCode\Encoder\QrCode;

interface RendererInterface {
	public function render(QrCode $qrCode);
}