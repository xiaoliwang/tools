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
		try {
			$this->ossclient = new BaseClient(
				$config['accessKeyId'],
				$config['accessKeySecret'],
				$config['endpoint']
			);
			$this->bucket = $config['bucket'];
		} catch (OssException $e) {
			print $e->getMessage();
			exit;
		}
	}

	public function upload($object_path, $remote_path)
	{
		$this->ossclient->uploadFile($this->bucket, $remote_path, $object_path);
	}

	public function append($object_path, $remote_path)
	{
		if ($this->ossclient->doesObjectExist($this->bucket, $remote_path)) {
			$objectMeta = $this->ossclient->getObjectMeta($this->bucket, $remote_path);
			$position = $objectMeta['content-length'];
		} else {
			$position = 0;
		}
		$position = $this->ossclient->appendFile($this->bucket, $remote_path, $object_path, $position);
        $position = $this->ossclient->appendFile($this->bucket, $remote_path, $object_path, $position);
	}
}