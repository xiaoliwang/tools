<?php
set_time_limit(0);
require(__DIR__ . '/../../vendor/autoload.php');

use tomcao\tools\util\Curl;
use tomcao\tools\util\Logger;

$logger = new Logger('./log', ['file_prefix' => 'shuapiao_']);

// 获取活动列表
do {
	echo "Please input the event's id:\n";
	$event_id = intval(fgets(STDIN));
} while (!$event_id);

$params = ['mid' => $event_id];
$url = 'http://testslb.missevan.com/mobile/site/event';

$curl = new Curl;
$curl->setParams($params);
if ($curl->get($url)) {
	$json_object = json_decode($curl->response);
	if ($json_object->status === 'success') {
		$tag_id = $json_object->info->event->tag_id;
	} else {
		echo 'fatal error: please find error detail in the shuapiao_error.log';
		$logger->error("活动{$event_id}发生错误，错误如下" . $json_object->info);
		exit;
	}
} else {
	echo 'fatal error, please find error detail in the shuapiao_error.log';
    $logger->error('网络错误，请联系程序开发者');
    exit;
}

// 登录
$config = parse_ini_file(__DIR__ . '/config.ini');

$url = 'http://www.missevan.com/member/login';
$params = [
	'LoginForm[username]' => $config['username'],
	'LoginForm[password]' => $config['password']
];
$options = [
	CURLOPT_HEADER => true, 
	CURLOPT_NOBODY => true
];


$curl = new Curl;
$curl->setParams($params)->setOptions($options);

$curl->post($url);
preg_match('/token=([^;]*);/', $curl->response, $match);
$session_id = $match[1] ?? '';

if (!$session_id) {
	echo 'fatal error: please find error detail in the shuapiao_error.log';
	$logger->error('用户登录失败，请检查您的用户名，密码');
	exit;
}

$p = 1;
do {
	$url = 'http://testslb.missevan.com/mobile/site/soundbytag';
	$params = ['order' => 3, 'tid' => $tag_id, 'p' => $p];
	$curl = new Curl;
	$curl->setParams($params);
	if ($curl->get($url)) {
		$soundsDTO = json_decode($curl->response);
		$pagination = $soundsDTO->info->pagination;
		$maxpage = $pagination->maxpage;
		$sounds = $soundsDTO->info->Datas;
		$sound_ids = array_column($sounds, 'id');
		$url = 'http://www.missevan.com/backend/manage/manualAdding';
		foreach ($sound_ids as $sound_id) {
			$add_num = mt_rand(1, 3);
			$params = [
				'type' => 1,
				'elem_id' => $sound_id,
				'add_num' => $add_num
			];

			$cookies = ['token' => $session_id];

			$curl = new Curl;
			$curl->setCookies($cookies)->setParams($params);

			if ($curl->get($url)) {
				$logger->log("音频{$sound_id}刷票成功，刷票数为$add_num");
			} else {
				$logger->error("音频{$sound_id}刷票失败");
			}
		}
		++$p;
	} else {
	    $logger->error('该活动不存在或者存在不可预知的错误');
	    exit;
	}
} while ($p <= $maxpage);






