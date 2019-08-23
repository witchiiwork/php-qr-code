<?php
namespace W2W\QRCode\Renderer\Image\Decorator;

use W2W\QRCode\Encoder\QrCode;
use W2W\QRCode\Renderer\Image\RendererInterface;

interface DecoratorInterface {
	public function preProcess(QrCode $qrCode, RendererInterface $renderer, $outputWidth, $outputHeight, $leftPadding, $topPadding, $multiple);
	public function postProcess(QrCode $qrCode, RendererInterface $renderer, $outputWidth, $outputHeight, $leftPadding, $topPadding, $multiple);
}