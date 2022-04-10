<?php
$master_socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
socket_set_option($master_socket, SOL_SOCKET, SO_REUSEADDR, 1);
if (!socket_bind($master_socket, '127.0.0.1', 8080) ||
    !socket_listen($master_socket, 0)) {
    die("port in use\n");
}
$read = [$master_socket];
while (true) {
    $null = null; //write, except は今回は使わないのでnull
    socket_select($read, $null, $null, $null);
    $new_read = [$master_socket];
    foreach ($read as $socket) {
        if($socket === $master_socket){
            $new_read[] = socket_accept($master_socket);
        }else{
            $buf = socket_read($socket, 1024);
            if($buf && strlen($buf) > 0) {
                $res =  "HTTP/1.1 200 OK\r\nConnection: close\r\n";
                $res .= "Content-Length: 15\r\n\r\n{\"status\":\"OK\"}\n";
                socket_write($socket, $res);
                socket_close($socket);
            }
        }
    }
    $read = $new_read;
}


