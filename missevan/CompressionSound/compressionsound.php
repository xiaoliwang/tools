<?php
set_time_limit(0);
require(__DIR__ . '/../../vendor/autoload.php');

use tomcao\tools\util\Logger;
use tomcao\tools\util\Curl;
use tomcao\tools\database\MysqlDataBase;

$logger = new Logger('./log', ['file_prefix' => 'handlemusic_']);


do {
        echo "input sound id:\n";
        $id = intval(fgets(STDIN));
} while (!$id);

$params = ['id' => $id];
$curl = new Curl;
$curl->setOptions([
        CURLOPT_TIMEOUT => 600,
        CURLOPT_CONNECTTIMEOUT => 600
])->setParams($params);
$url = 'http://218.244.128.228/webserver/handlesound';
if ($curl->get($url)) {
        if ($curl->response === 'not exist') {
                $logger->log('success: ' . $id . ' not exist');
        } elseif ($curl->response === 'success') {
                $logger->log('success: ' . $id);
        } else {
                $logger->error('error: ' . $id);
        }
} else {
        $logger->error('error: ' . $id);
}

