<?php
declare(strict_types = 1);

namespace W2W\QRCode\Renderer\Path;

use IteratorAggregate;
use Traversable;

final class Path implements IteratorAggregate {
	private $operations = [];
	
	public function move(float $x, float $y) : self {
		$path = clone $this;
		$path->operations[] = new Move($x, $y);
		
		return $path;
	}
	
	public function line(float $x, float $y) : self {
		$path = clone $this;
		$path->operations[] = new Line($x, $y);
		return $path;
	}
	
	public function ellipticArc(float $xRadius, float $yRadius, float $xAxisRotation, bool $largeArc, bool $sweep, float $x, float $y) : self {
		$path = clone $this;
		$path->operations[] = new EllipticArc($xRadius, $yRadius, $xAxisRotation, $largeArc, $sweep, $x, $y);
		
		return $path;
	}
	
	public function curve(float $x1, float $y1, float $x2, float $y2, float $x3, float $y3) : self {
		$path = clone $this;
		$path->operations[] = new Curve($x1, $y1, $x2, $y2, $x3, $y3);
		
		return $path;
	}
	
	public function close() : self {
		$path = clone $this;
		$path->operations[] = Close::instance();
		
		return $path;
	}
	
	public function append(self $other) : self {
		$path = clone $this;
		$path->operations = array_merge($this->operations, $other->operations);
		
		return $path;
	}

	public function translate(float $x, float $y) : self {
		$path = new self();
		
		foreach($this->operations as $operation) {
			$path->operations[] = $operation->translate($x, $y);
		}
		
		return $path;
	}
	
	public function getIterator() : Traversable {
		foreach($this->operations as $operation) {
			yield $operation;
		}
	}
}