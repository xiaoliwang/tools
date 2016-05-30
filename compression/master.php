<?php
require(__DIR__ . '/../vendor/autoload.php');

use tomcao\tools\database\MysqlDataBase;
use tomcao\tools\database\RedisDataBase;

$mysql = new MysqlDataBase;
$redis = new RedisDataBase;

$last_id_key = 'last_prepare_compression_music_ids';
$compression_ids_key = 'prepare_compression_music_ids';
$slave_key = 'compression_music_slave';

$last_id = $redis->get($last_id_key) ?: 0;

$sql = 'select id from m_sound where soundurl_64 = "" and id > :id';
$stmt = $mysql->prepare($sql);
$stmt->execute([':id' => $last_id]);
$ids = $stmt ->fetchAll(PDO::FETCH_COLUMN);

if ($last_id = end($ids)) {
	$redis->set($last_id_key, $last_id);
}


array_unshift($ids, $compression_ids_key);
call_user_func_array([$redis, 'lPush'], $ids);

$slave_nums = $redis->pubsub(
	'numsub', [$slave_key]
)[$slave_key];

$redis->publish($slave_key, 'start');