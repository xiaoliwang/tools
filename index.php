<?php
require(__DIR__ . '/vendor/autoload.php');

use tomcao\tools\datebase\MysqlDataBase;
use tomcao\tools\database\RedisDataBase;
use tomcao\tools\util\Logger;

$mysql = new MysqlDataBase;
$redis = new RedisDataBase;
$logger = new Logger('./log');

$data_key_patten = 'Person_Operation_*_sound';

function getUserId($data_key) {
	return explode('_', $data_key)[2];
}

$begin = null;
do {
	$data_scan_keys = $redis->scan($begin, $data_key_patten, 1000);
	foreach ($data_scan_keys as $data_key) {
		$user_id = getUserId($data_key);
	}
} while ($begin);