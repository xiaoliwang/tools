<?php
require(__DIR__ . '/../../vendor/autoload.php');

use tomcao\tools\database\MysqlDataBase;
use tomcao\tools\database\RedisDataBase;
use tomcao\tools\database\MemcacheDataBase;
use tomcao\tools\util\Logger;

$mysql = new MysqlDataBase;
$redis = new RedisDataBase;
$memcache = new MemcacheDataBase;
$logger = new Logger('./log', ['file_prefix' => 'homepage_']);

const PREFIX = 'new_home_page';

// 生产banner图
function banner_factory()
{
	global $redis;
	for ($cur = 0; $cur < 5; ++$cur) {
		yield [$redis->hGetAll('link_' . $cur), 
			$redis->hGetAll('AD_' . $cur),
			$redis->hGetAll('new_link_' . $cur)];
	}
}

$links = [];
$ads = [];
$new_links = [];

$banners = banner_factory();
foreach($banners as $banner) {
	list($links[], $ads[], $new_links[]) = $banner;
}

$memcache->set(PREFIX . 'links', $links);
$memcache->set(PREFIX . 'ads', $ads);
$memcache->set(PREFIX . 'newlinks', $new_links);

// 生产分类信息
function map($id, $parent_id)
{
	return [
		'id' => $id,
		'parent_id' => $parent_id
	];
}

function get_leaves($catalogs) {
	print_r($catalogs);
	$leaves = [];
	foreach ($catalogs as $id => $parent_id) {
		if (!array_search($id, $catalogs)) {
			$leaves[] = $id;
		}
	}
	return $leaves;
}

$sql_get_catalogs = 'select id, parent_id from catalog';
$catalogs = array_column(
	$mysql->query($sql_get_catalogs)->fetchAll(PDO::FETCH_FUNC, 'map'),
	'parent_id', 'id');

$leaves = get_leaves($catalogs);
print_r($leaves);

function catalog_recommend_factory()
{

}