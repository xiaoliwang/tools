<?php
namespace tomcao\tools\util;

class PicEdit
{
	private $imagick;

	public function __construct(string $image_path)
	{
		$imagick = new \Imagick($image_path);	
		$this->imagick = $imagick;
	}

	public function __call(string $func, array $args)
	{
		return call_user_func_array([&$this->imagick, $func], $args);
	}

	public function __get(string $name)
	{
		switch ($name) {
			case 'height':
			case 'width':
				$size = $this->getImageGeometry();
				return $size[$name];
			case 'format':
				return $this->getimageformat();
			default:
				throw new \Exception('element does not exist');
		}
	}

	public function mosaicByBlock(int $horizontal_block, int $vertical_block)
	{
		$this->sampleImage($horizontal_block, $vertical_block);
		$this->imagick->sampleImage($this->width, $this->height);
	}

	public function mosaicByWidth(int $horizontal_width, int $vertical_width)
	{
		$horizontal_block = intDiv($this->width, $horizontal_width);
		$vertical_block = intDiv($this->height, $vertical_width);

		$width = $horizontal_width * $horizontal_block;
		$height = $vertical_block * $vertical_width;

		$this->thumbnailImage($width, $height);
		$this->sampleImage($horizontal_block, $vertical_block);
		$this->imagick->sampleImage($width, $height);
	}

	public function writeImage(String $new_path)
	{
		$this->imagick->writeImage($new_path);
	}

	public function __destruct()
	{
		$this->imagick->clear();
		$this->imagick->destroy();
		$this->imagick = null;
	}
}

/*$a = new PicEdit('./1.jpg');
// 去除图片信息
$a->stripImage();

echo $a->mosaicByWidth(50,50);

$a->writeImage('./2.jpg');*/