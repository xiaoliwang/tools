#!/usr/bin/env php
<?php
require(__DIR__ . '/../../vendor/autoload.php');

use tomcao\tools\util\OssClient;

$oss = new OssClient;
$today = date('Y-m-d');
$remote_path = 'log/' . date('Ym') . '/' . date('d') . '.log';
$oss->append('./test.log', $remote_path);

