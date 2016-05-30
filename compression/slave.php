<?php
require(__DIR__ . '/../vendor/autoload.php');

use tomcao\tools\database\MysqlDataBase;
use tomcao\tools\database\RedisDataBase;
use tomcao\tools\util\Logger;

$timeout = 0;

ini_set('default_socket_timeout', -1);
$mysql = new MysqlDataBase;
$redis = new RedisDataBase(['timeout' => $timeout]);
$logger = new Logger('./log', ['file_prefix' => 'compression_music_']);

$compression_ids_key = 'prepare_compression_music_ids';

$redis->subscribe(['compression_music_slave'], function($redis_limit, $chan, $msg)
	use($logger, $compression_ids_key){
	$redis = new RedisDataBase();
	$logger->log($msg);
	while ($id = $redis->rPop($compression_ids_key)) {
	}	
	$logger->log('finish');
});