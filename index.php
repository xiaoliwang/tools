<?php
require(__DIR__ . '/vendor/autoload.php');

use tomcao\tools\database\MysqlDataBase;
use tomcao\tools\database\RedisDataBase;
use tomcao\tools\util\Logger;
use tomcao\tools\util\Command;

$mysql = new MysqlDataBase;
$redis = new RedisDataBase;
$logger = new Logger('./log');

$command = new Command();

/*$data_key_patten = 'Person_Operation_*_sound';
debug_zval_dump($data_key_patten);
function getUserId($data_key) {
	return explode('_', $data_key)[2];
}

$begin = null;

do {
	$data_scan_keys = $redis->scan($begin, $data_key_patten, 1000);
	foreach ($data_scan_keys as $data_key) {
		$user_id = getUserId($data_key);
		echo $user_id . PHP_EOL;
	}
} while ($begin);*/