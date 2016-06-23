<?php
require(__DIR__ . '/../vendor/autoload.php');

use tomcao\tools\util\Logger;
use tomcao\tools\util\FtpClient;

$logger = new Logger('./log', ['file_prefix' => 'ftpUpdate_']);

$config = parse_ini_file(__DIR__ . '/config.ini');
$base_dir = $config['base_dir'];

$file_lines = array_filter(array_map(function($file_line){
	return trim($file_line);
}, file('./updatelist.txt')));

foreach ($file_lines as $file_line) {
	$local_file = $base_dir . $file_line;
	if (!file_exists($local_file)) {
		$logger->error($local_file . 'not exist');
		continue;
	}
	$config = ['timeout' => 600];
	$ftp_client = new FtpClient($config);
	if ($ftp_client->uploadfile($file_line, $local_file)) {
		$logger->log($file_line . 'upload success');
	} else {
		$logger->error($file_line . 'upload failed');
	}
}