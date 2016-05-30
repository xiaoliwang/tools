<?php
require(__DIR__ . '/vendor/autoload.php');

use tomcao\tools\database\MysqlDataBase;
use tomcao\tools\database\RedisDataBase;
use tomcao\tools\util\Logger;
use tomcao\tools\util\Command;
use tomcao\tools\util\Date;

$date = new Date();
$date->test();