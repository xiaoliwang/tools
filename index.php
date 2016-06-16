<?php
set_time_limit(0);
require(__DIR__ . '/vendor/autoload.php');

use tomcao\tools\util\Logger;
use tomcao\tools\util\Curl;

$logger = new Logger('./log', ['file_prefix' => 'handlemusic_']);

$handle = fopen('./error_sound.log', 'r');
$contents = fread($handle, filesize('./error_sound.log'));
$log = json_decode($contents);

foreach ($log as $id) {
        $curl = new Curl;
        $curl->setOptions([
                CURLOPT_TIMEOUT => 60,
                CURLOPT_CONNECTTIMEOUT => 30
        ]);
        $url = 'http://218.244.128.228/webserver/handlesound';
        $params = ['id' => $id];
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
        }exit;
}

