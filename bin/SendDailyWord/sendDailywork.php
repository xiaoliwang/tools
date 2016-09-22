<?php
require(__DIR__ . '/../../vendor/autoload.php');
use tomcao\tools\util\MailSender;
use tomcao\tools\util\Logger;

$contents = [
	"手机api及相关接口",
	"自动化脚本实现",
	"日常工作",
	"协助手机端调试",
	"完善文档",
	"音频处理接口",
	//"学习大数据相关知识",
	//"学习PHP的Command写法",
	//"研究shell脚本及linux操作",
	//"研究nginx优化",
	//"研究mysql5.7",
	//"研究mysql优化",
	//"测试代码",
	"优化代码",
];

$receivers = [
	'测试' => 'xiaoliwang@missevan.cn',
	//'魔王' => 'mowangsk@missevan.cn',
	//'七夜' => 'liutian7y@missevan.cn',
	//'昂基佬' => 'sa@missevan.cn'
];

$emailContent = function () use($contents)
{
	shuffle($contents);
	$max = mt_rand(4, 6);
	$content = "";
	for ($i = 1; $i < $max; ++$i) {
		$content .= "{$i}. $contents[$i] <br />";
	}
	$content .= '<br />Regards TomCao';
	return $content;
};

$mail = new MailSender;
$logger = new Logger('./log', ['file_prefix' => PRE_SEND_EMAIL]);

foreach ($receivers as $name => $email) {
	$mail->addAddress($email, $name);
}

$date = date('n.j');
$title = "{$date}工作日报";
$mail->Subject = $title;
$mail->Body = $emailContent();
//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';

if (!$mail->send()) {
	$logger->log('Mailer Error: ' . $mail->ErrorInfo);
} else {
	$logger->log('Message sent!');
}



