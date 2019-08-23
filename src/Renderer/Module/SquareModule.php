<?php
declare(strict_types = 1);

namespace W2W\QRCode\Renderer\Module;

use W2W\QRCode\Encoder\ByteMatrix;
use W2W\QRCode\Renderer\Module\EdgeIterator\EdgeIterator;
use W2W\QRCode\Renderer\Path\Path;

final class SquareModule implements ModuleInterface {
	
	private static $instance;
	
	private function __construct() {
	}
	
	public static function instance() : self {
		return self::$instance ?: self::$instance = new self();
	}
	
	public function createPath(ByteMatrix $matrix) : Path {
		$path = new Path();
		
		foreach(new EdgeIterator($matrix) as $edge) {
			$points = $edge->getSimplifiedPoints();
			$length = count($points);
			$path = $path->move($points[0][0], $points[0][1]);
			
			for($i = 1; $i < $length; ++$i) {
				$path = $path->line($points[$i][0], $points[$i][1]);
			}
			
			$path = $path->close();
		}
		
		return $path;
	}
}