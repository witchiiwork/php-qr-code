<?php
declare(strict_types = 1);

namespace W2W\QRCode\Renderer\Eye;

use W2W\QRCode\Renderer\Path\Path;

interface EyeInterface {
	public function getExternalPath() : Path;
	
	public function getInternalPath() : Path;
}