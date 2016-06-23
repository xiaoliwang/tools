<?php
require(__DIR__ . '/vendor/autoload.php');

use tomcao\tools\database\MysqlDataBase;
use tomcao\tools\database\RedisDataBase;
use tomcao\tools\util\Logger;

$mysql = new MysqlDataBase;
$redis = new RedisDataBase;
$logger = new Logger('./log', ['file_prefix' => 'person_operation_']);

function getUserId($data_key)
{
	return explode('_', $data_key)[2];
}

function separate($uid, $infos)
{
	$data_arr = explode('_', $infos);
	array_unshift($data_arr, $uid);
	$data_arr = array_pad($data_arr, 5, 0);
	return $data_arr;
}

$logger->log('mission start');
try {
	$begin = null;
	$fields = ['uid', 'operation', 'time', 'equip', 'eid'];
	$data_key_patten = 'Person_Operation_*_sound';
	do {
		$data_scan_keys = $redis->scan($begin, $data_key_patten, 1000);
		foreach ($data_scan_keys as $data_key) {
			$user_id = getUserId($data_key);
			$datas = [];
			while ($data_value = $redis->Rpop($data_key)) {
				$datas[] = separate($user_id, $data_value);
			}
			$mysql->insertMultipleRows('historyoperation', $fields, $datas);
		}
	} while ($begin);
	$logger->log('success');
} catch (\Exception $e) {
	$logger->log('error');
	$logger->error($e->getMessage());
}
$logger->log('finished');