<?php
namespace tomcao\tools\util;

class FtpClient
{
	private $conn_id;

	public function __construct(array $config = [])
	{
		if (!extension_loaded('ftp'))
			throw new \Exception('You should add extension ftp');
		$config = $config + parse_ini_file(__DIR__ .'/../config/main.ini', 
			true)['ftp'];
		$ftp_user_name = $config['username'];
		$ftp_user_pass = $config['password'];
		$host = $config['host'];
		$port = $config['port'] ?? 21;
		$timeout = $config['timeout'] ?? 60;
		$pasv = $config['pasv'] ?? true;
		$this->conn_id = ftp_connect($host, $port, $timeout);
		if (!ftp_login($this->conn_id, $ftp_user_name, $ftp_user_pass)) {
			throw new \Exception("Couldn't connect as $ftp_user\n");
		}
		ftp_pasv($this->conn_id, $pasv);
	}

	public function deletefile($remote_file)
	{
		return ftp_delete($this->conn_id, $remote_file);
	}

	public function uploadfile($remote_file, $local_file, $mode = FTP_BINARY)
	{
		return ftp_put($this->conn_id, $remote_file, $local_file, $mode);
	}

	public function downloadfile($local_file, $remote_file, $mode = FTP_BINARY)
	{
		return ftp_get($this->conn_id, $local_file, $remote_file, $mode);
	}

	public function __destruct()
	{
		ftp_close($this->conn_id);
	}

}