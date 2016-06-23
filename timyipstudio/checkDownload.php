<?php
$dir = 'D:\file\github\php\Ben2';

function check_dir($dir, $conn_id)
{
	$dh = opendir($dir);
	while ($file = readdir($dh)) {
		if ($file != '.' && $file != '..') {
			$fullpath = $dir . '/' . $file;
			if (is_dir($fullpath)) {
				check_dir($fullpath, $conn_id);
			} else {
				if (!filesize($fullpath)) {
					$new_path = str_replace('D:\file\github\php\Ben2', '', $fullpath);
					//ftp_get($conn_id, $fullpath, $new_path, FTP_BINARY);
					echo $new_path . "\n";
				}
			}
		}
	}
	closedir($dh);
}

$ftp_user_name = 'timyipstudio@timyipstudio.net';
$ftp_user_pass = '3i(w6g(TydSS';
$conn_id = ftp_connect('ftp.timyipstudio.net', 21, 6000);
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
ftp_pasv($conn_id, true);

check_dir($dir, $conn_id);

// ftp_get($conn_id, 'C:\Users\tomcao\Desktop\phar\hello.php', '/wp-includes/rest-api/class-wp-rest-response.php', FTP_BINARY);

ftp_close($conn_id);

//