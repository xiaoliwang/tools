<?php
require(__DIR__ . '/../vendor/autoload.php');

use tomcao\tools\util\Logger;
use tomcao\tools\database\MysqlDataBase;

$mysql = new MysqlDataBase;
$logger = new Logger('./log', ['file_prefix' => 'test']);

$sql = 'select distinct album_id from test_zz ';
$stmt = $mysql->prepare($sql);
$stmt->execute();
$logger->log('mission start');
while ($id = $stmt ->fetch(PDO::FETCH_COLUMN)){
    $sql = 'select id,sort,sound_id from test_zz where album_id = :albumId';
    $stmt1 = $mysql->prepare($sql);
    $stmt1->execute([':albumId' => $id]);
    $models = $stmt1 ->fetchAll(PDO::FETCH_ASSOC);
    foreach ($models as $model) {
        $modelMap[$model['sort']]['id'] = $model['id'];
        $modelMap[$model['sort']]['sound_id'] = $model['sound_id'];
    }

    $sort = zsort($modelMap,$models);

    if (count($sort) > 1) {
        try {
            $mysql->beginTransaction();
            $sql = 'UPDATE test_zz SET sort=:sort WHERE id = :id';
            $stmt2 = $mysql->prepare($sql);
            update($sort,$stmt2);
            $mysql->commit();
            $logger->log("album $id update success");

        } catch (Exception $e) {
            $mysql->rollBack();
            //$logger->log("album $id update failed");
            $logger->error("album $id".$e->getMessage()."update failed");
        }
    }
    unset($sort,$models);
}
$logger->log('album update finished');
$logger->log('mission end');

function zsort($modelMap,$models){
    $temp = 0;
    $sort = [];
    while (isset($modelMap[$temp])){
        $sort[] = $modelMap[$temp]['id'];
        $temp = $modelMap[$temp]['sound_id'];
    }

    if (count($sort) < count($models)){
        $diff = array_diff(array_column($models,'id'), $sort);
        foreach ($diff as $v){
            $sort[] = $v;
        }
    }
    return $sort;
}

function update($sort,$stmt){
    foreach ($sort as $k => $v) {
        $stmt->execute([':id' => $v,':sort' => $k]);
        if (!$stmt->fetchAll()){
            if($stmt->errorCode() != '000'){
                throw new Exception($k);
            }
        }
    }
}


//建表sql

//CREATE TABLE `app_missevan`.`test_zz` (
//`id` INT(7) UNSIGNED NOT NULL,
//  `sort` INT(7) UNSIGNED NOT NULL,
//  `sound_id` INT
//(7) UNSIGNED NOT NULL,
//  `album_id` INT(7) UNSIGNED NOT NULL,
//  PRIMARY KEY (`id`));
//INSERT INTO `app_missevan`.`test_zz`
//(`id`, `sort`, `sound_id`, `album_id`) VALUES ('345043', '47649', '78896', '1');
//INSERT INTO `app_missevan`.`test_zz` (`id`,
//`sort`, `sound_id`, `album_id`) VALUES ('348809', '107440', '47649', '1');
//INSERT INTO `app_missevan`.`test_zz` (`id`,
//`sort`, `sound_id`, `album_id`) VALUES ('351428', '106734', '107440', '1');
//INSERT INTO `app_missevan`.`test_zz` (`id`,
//`sort`, `sound_id`, `album_id`) VALUES ('351627', '21035', '106734', '1');
//INSERT INTO `app_missevan`.`test_zz` (`id`,
//`sort`, `sound_id`, `album_id`) VALUES ('353898', '59106', '21035', '1');
//INSERT INTO `app_missevan`.`test_zz` (`id`,
//`sort`, `sound_id`, `album_id`) VALUES ('355383', '56307', '59106', '1');
//INSERT INTO `app_missevan`.`test_zz` (`id`,
//`sort`, `sound_id`, `album_id`) VALUES ('355384', '0', '56307', '1');
//INSERT INTO `app_missevan`.`test_zz` (`id`, `sort`,
//`sound_id`, `album_id`) VALUES ('308', '0', '13837', '56');
