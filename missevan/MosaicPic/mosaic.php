<?php
require(__DIR__ . '/../../vendor/autoload.php');

use tomcao\tools\util\OssClient;
use tomcao\tools\util\PicEdit;
use tomcao\tools\util\Logger;
use tomcao\tools\database\MysqlDataBase;

$oss = new OssClient;
$mysql = new MysqlDataBase;
$logger = new Logger('./log', ['file_prefix' => 'mosaic_']);

$base_dir = __DIR__ . '/tmp/mosaic/';
if (!is_dir($base_dir)) {
	mkdir($base_dir, 0777, true);
}

function downloadPic(string $url): string
{
	global $base_dir;
	$base_name = basename($url);
	$img_path = $base_dir . $base_name;
	file_put_contents($img_path, file_get_contents($url));
	return $img_path;
}

function mosaic(string $path): string
{
	global $base_dir;
	$extension = pathinfo($path, PATHINFO_EXTENSION);
	$picEdit = new PicEdit($path);
	$base_name = basename($path, '.' . $extension);

	$height = intval(1280 / $picEdit->width * $picEdit->height);
	$picEdit->thumbnailImage(1280, $height, true);
	$picEdit->cropImage(1280, 400, 0, 0);

	$picEdit->blurImage(150, 50);
	$picEdit->mosaicByWidth(50, 50);
	$mosaic_path = $base_dir . $base_name . '_mosaic.jpg';
	$picEdit->writeImage($mosaic_path);
	return $mosaic_path;
}

function rmrf(string $dir) {
	foreach(glob("{$dir}*") as $file) {
        if(is_dir($file)) { 
            rmrf($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dir);
}


$sql_get_sounds = 'select id, cover_image from m_sound where cover_image != \'\' order by id';	

$sounds = $mysql->query($sql_get_sounds);

$logger->log('mission start');
while ($sound = $sounds->fetch()) {
	try {
		$sid = $sound['id'];
		if ($sound['cover_image']) {
			$front_cover = 'http://static.missevan.com/covers/' . $sound['cover_image'];
			$img_path = downloadPic($front_cover);
			$mosaic_path = mosaic($img_path);
			$remote_path = 'mosaic/' . $sound['cover_image'];
			$oss->upload($mosaic_path, $remote_path);
			$logger->log("sound $sid get mosaic pic success");
		} else {
			$logger->log("sound $sid get mosaic pic skip");
		}
	} catch (\Exception $e) {
		$sid = $sound['id'];
		$logger->log("sound $sid get mosaic pic filed");
		$logger->error("sound $sid get mosaic pic filed");
	}
}
$logger->log('get mosaic pic finished');
rmrf($base_dir);
$logger->log('mission end');




