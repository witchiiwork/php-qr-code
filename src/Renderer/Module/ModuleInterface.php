<?php
declare(strict_types = 1);

namespace W2W\QRCode\Renderer\Module;

use W2W\QRCode\Encoder\ByteMatrix;
use W2W\QRCode\Renderer\Path\Path;

interface ModuleInterface {
	public function createPath(ByteMatrix $matrix) : Path;
}