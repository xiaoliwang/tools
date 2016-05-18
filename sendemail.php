<?php
require(__DIR__ . '/vendor/autoload.php');
$contents = [
	"手机api及相关接口",
	"自动化脚本实现",
	"日常工作",
	"协助手机端调试",
	"完善文档",
	"音频处理接口",
	"学习大数据相关知识",
	"学习PHP的Command写法",
	"研究shell脚本及linux操作",
	"研究nginx优化",
	"研究mysql5.7",
	"研究mysql优化",
	"测试代码",
	"优化代码",
];

$mail = new PHPMailer;
$mail->setLanguage('zh_cn', '/optional/path/to/language/directory/');
$mail->CharSet = 'utf-8';
$mail->isSMTP();

$mail->Host = 'smtp.exmail.qq.com';
$mail->Port = 465;
$mail->SMTPSecure = 'ssl';

$mail->SMTPAuth = true;
$config = parse_ini_file('./config/email.ini');
$mail->Username = $config['username'];
$mail->Password = $config['password'];

$mail->setFrom('xiaoliwang@missevan.cn', '王小李');
$mail->addReplyTo('xiaoliwang@missevan.cn', '王小李');

$mail->addAddress('mowangsk@missevan.cn', '魔王');
$mail->addAddress('liutian7y@missevan.cn', '七夜');
$mail->addAddress('sa@missevan.cn', '昂基佬');

$date = date('n.j');
$title = "{$date}工作日报";
$mail->Subject = $title;

shuffle($contents);
$max = mt_rand(4, 6);
$content = "";
for ($i = 1; $i < $max; ++$i) {
	$content .= "{$i}. $contents[$i] <br />";
}
$content .= '<br />Regards TomCao';

$mail->Body = $content;
//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';

if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}