<?php
require(__DIR__ . '/../vendor/autoload.php');

use tomcao\tools\util\Logger;
use tomcao\tools\util\FtpClient;

$logger = new Logger('./log', ['file_prefix' => 'ftpUpdate_']);

$config = parse_ini_file(__DIR__ . '/config.ini');
$base_dir = $config['git_base'];

$updatelist_path = $base_dir . './updatelist.txt';
if (!file_exists($updatelist_path)) {
	echo 'no file need to update';
	exit;
}

$file_lines = array_unique(array_filter(array_map(function($file_line){
	return trim($file_line);
}, file($updatelist_path))));

$config = ['timeout' => 3600];
$ftp_client = new FtpClient($config);

foreach ($file_lines as $file_line) {
	$file_line = '/' . $file_line;
	$local_file = $base_dir . $file_line;
	if (!file_exists($local_file)) {
		if ($ftp_client->deletefile($file_lne)) {
			$logger->log($local_file . ' delete success');
		} else {
			$logger->error($local_file . ' delete failed');
		}	
	} else {
		
		if ($ftp_client->uploadfile($file_line, $local_file)) {
			$logger->log($file_line . ' upload success');
		} else {
			$logger->error($file_line . ' upload failed');
		}
	}
}

unlink($updatelist_path);
echo 'success';