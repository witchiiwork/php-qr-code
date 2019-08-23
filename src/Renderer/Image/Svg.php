<?php
namespace W2W\QRCode\Renderer\Image;

use W2W\QRCode\Exception;
use W2W\QRCode\Renderer\Color\ColorInterface;
use SimpleXMLElement;

class Svg extends AbstractRenderer {
	protected $svg;
	
	protected $colors = array();
	
	protected $prototypeIds = array();
	
	public function init() {
		$this->svg = new SimpleXMLElement("<?xml version\"1.0\" encoding=\"utf-8\"?><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"/>");
		$this->svg->addAttribute("version", "1.1");
		$this->svg->addAttribute("width", $this->finalWidth . "px");
		$this->svg->addAttribute("height", $this->finalHeight . "px");
		$this->svg->addAttribute("viewBox", "0 0 " . $this->finalWidth . " " . $this->finalHeight);
		$this->svg->addChild("defs");
	}
	
	public function addColor($id, ColorInterface $color) {
		$this->colors[$id] = (string)$color->toRgb();
	}
	
	public function drawBackground($colorId) {
		$rect = $this->svg->addChild("rect");
		$rect->addAttribute("x", 0);
		$rect->addAttribute("y", 0);
		$rect->addAttribute("width", $this->finalWidth);
		$rect->addAttribute("height", $this->finalHeight);
		$rect->addAttribute("fill", "#" . $this->colors[$colorId]);
	}
	
	public function drawBlock($x, $y, $colorId) {
		$use = $this->svg->addChild("use");
		$use->addAttribute("x", $x);
		$use->addAttribute("y", $y);
		$use->addAttribute("xlink:href", $this->getRectPrototypeId($colorId), "http://www.w3.org/1999/xlink");
	}
	
	public function getByteStream() {
		return $this->svg->asXML();
	}
	
	protected function getRectPrototypeId($colorId) {
		if(!isset($this->prototypeIds[$colorId])) {
			$id = "r" . dechex(count($this->prototypeIds));
			$rect = $this->svg->defs->addChild("rect");
			$rect->addAttribute("id", $id);
			$rect->addAttribute("width", $this->blockSize);
			$rect->addAttribute("height", $this->blockSize);
			$rect->addAttribute("fill", "#" . $this->colors[$colorId]);
			$this->prototypeIds[$colorId] = "#" . $id;
		}
		
		return $this->prototypeIds[$colorId];
	}
}