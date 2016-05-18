<?php
namespace tomcao\tools\util;

class Logger
{
  private $options = [
  	'dateFormat' => 'Y-m-d G:i:s',
  	'file_prefix' => 'tomcao_',
  	'extension' => 'log'
  ];

  private $log_handle;
  private $error_handle;
  private $command = true;

  public function __construct(String $DIR_LOG = '', array $options = [])
  {
    $this->options = $options + $this->options;
    if ($DIR_LOG) {
    	if (!is_dir($DIR_LOG) || @mkdir($DIR_LOG, 0777, true))
    		throw new \Exception('Failed to create directory');
    	$log_file_name = $this->options['file_prefix'] . 'log.' . $this->options['extension'];
    	$log_error_name = $this->options['file_prefix'] . 'error.' . $this->options['extension'];
    	$this->log_handle = fopen($DIR_LOG . '/' . $log_file_name, 'a');
    	$this->error_handle = fopen($DIR_LOG . '/' . $log_error_name, 'a');
    	$this->command = false;
    } else {
    	$this->log_handle = fopen('php://stdout', 'w');
    	$this->error_handle = fopen('php://stderr', 'w');
    }
  }

  public function __call(string $func, array $args)
  {
  	$func = strtoupper($func);
  	$this->write($args[0], $func);
  }

  private function write(String $msg, String $type)
  {
    $time = date($this->options['dateFormat']);
    $info = "[$time] [$type] $msg" . PHP_EOL;
    if ($type === 'ERROR') {
     	$handle = &$this->error_handle;
    } else {
    	$handle = &$this->log_handle;
    }
    if ($this->command || flock($handle, LOCK_EX)) {
      fwrite($handle, $info);
      flock($handle, LOCK_UN);
    }
  }

  public function __destruct()
  {
		$this->log_handle && fclose($this->log_handle);
		$this->error_handle && fclose($this->error_handle);
  }
}