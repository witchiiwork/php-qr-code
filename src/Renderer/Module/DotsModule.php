<?php
declare(strict_types = 1);

namespace W2W\QRCode\Renderer\Module;

use W2W\QRCode\Encoder\ByteMatrix;
use W2W\QRCode\Exception\InvalidArgumentException;
use W2W\QRCode\Renderer\Path\Path;

final class DotsModule implements ModuleInterface {
    public const LARGE = 1;
	
    public const MEDIUM = .8;
	
    public const SMALL = .6;
	
	private $size;
	
    public function __construct(float $size) {
        if($size <= 0 || $size > 1) {
            throw new InvalidArgumentException("Size must between 0 (exclusive) and 1 (inclusive)");
        }
		
        $this->size = $size;
    }
	
    public function createPath(ByteMatrix $matrix) : Path {
        $width = $matrix->getWidth();
        $height = $matrix->getHeight();
        $path = new Path();
        $halfSize = $this->size / 2;
        $margin = (1 - $this->size) / 2;
		
        for($y = 0; $y < $height; ++$y) {
            for($x = 0; $x < $width; ++$x) {
                if(!$matrix->get($x, $y)) {
                    continue;
                }
				
                $pathX = $x + $margin;
                $pathY = $y + $margin;
                $path = $path->move($pathX + $this->size, $pathY + $halfSize)->ellipticArc($halfSize, $halfSize, 0, false, true, $pathX + $halfSize, $pathY + $this->size)->ellipticArc($halfSize, $halfSize, 0, false, true, $pathX, $pathY + $halfSize)->ellipticArc($halfSize, $halfSize, 0, false, true, $pathX + $halfSize, $pathY)->ellipticArc($halfSize, $halfSize, 0, false, true, $pathX + $this->size, $pathY + $halfSize)->close();
            }
        }
		
        return $path;
    }
}