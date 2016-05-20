<?php
namespace tomcao\tools\util;

use OSS\OssClient as BaseClient;
use OSS\Core\OssException;

class OssClient
{
	private $ossclient;
	private $bucket;

	public function __construct(array $config = [])
	{
		$config = $config + parse_ini_file(__DIR__ . '/../config/main.ini', true)['oss'];
		$this->ossclient = new BaseClient(
			$config['accessKeyId'],
			$config['accessKeySecret'],
			$config['endpoint']
		);
		$this->bucket = $config['bucket'];
	}

	public function upload($object_path, $remote_path)
	{
		$this->ossclient->uploadFile($this->bucket, $remote_path, $object_path);
	}
}