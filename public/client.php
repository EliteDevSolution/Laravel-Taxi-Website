<?php 

error_reporting(E_ALL | E_NOTICE | E_STRICT);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

if(@$_POST['message']){

$host    = "142.93.250.112";
$port    = 4444;
$message = $_POST['message'];
echo "Message To server :".$message.'</br>';
// create socket
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
// connect to server
$result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");  
if ($result === false) {
		echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($this->_hSocket)) . "\n";
	} else {
		echo "OK.\n";
	}
// send string to server
socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
// get server response
$result = socket_read ($socket, 1024) or die("Could not read server response\n");
echo "Reply From Server  :".$result.'</br>';
// close socket
socket_close($socket);
//exit;
//$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
// connect to server
//$result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");  
}

?>

<form method="post">
<input type="text" name="message" />
<button>Send</button>
</form>