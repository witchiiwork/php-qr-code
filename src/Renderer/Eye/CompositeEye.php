<?php
declare(strict_types = 1);

namespace W2W\QRCode\Renderer\Eye;

use W2W\QRCode\Renderer\Path\Path;

final class CompositeEye implements EyeInterface {
	private $externalEye;
	
	private $internalEye;
	
	public function __construct(EyeInterface $externalEye, EyeInterface $internalEye) {
		$this->externalEye = $externalEye;
		$this->internalEye = $internalEye;
	}
	
	public function getExternalPath() : Path {
		return $this->externalEye->getExternalPath();
	}
	
	public function getInternalPath() : Path {
		return $this->externalEye->getInternalPath();
	}
}