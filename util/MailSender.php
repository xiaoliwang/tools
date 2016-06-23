<?php
namespace tomcao\tools\util;

class MailSender
{
	private $mail;
	private $options = [
		'language' => 'zh_cn',
		'charset' => 'utf-8',
	];

	public function __construct(array $config = [], array $options = [])
	{
		if (!class_exists('PHPMailer'))
			throw new \Exception('We need framework PHPMailer');
		$this->options = $options + $this->options;
		$config = $config + parse_ini_file(__DIR__ . '/../config/main.ini', true)['email'];
		$mail = new \PHPMailer;
		$mail->setLanguage($this->options['language'], '/optional/path/to/language/directory/');
		$mail->CharSet = $this->options['charset'];
		$mail->isSMTP();

		$mail->Host = $config['host'];
		$mail->Port = $config['port'];
		$mail->SMTPSecure = $config['smtpsecure'];
		$mail->isSMTP();

		$mail->SMTPAuth = true;
		$mail->Username = $config['username'];
		$mail->Password = $config['password'];

		$mail->setFrom($config['username'], $config['nickname']);
		$mail->addReplyTo($config['username'], $config['nickname']);
		$this->mail = $mail;
	}

	public function __call(string $func, array $args)
	{
		return call_user_func_array([&$this->mail, $func], $args);
	}

	public function __set(string $name, $value)
	{
		$this->mail->$name = $value;
	}

	public function __get(string $name)
	{
		return $this->mail->$name;
	}

	public function __destruct()
	{
		$this->mail = null;
	}
}