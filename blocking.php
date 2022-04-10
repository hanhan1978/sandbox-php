<?php
$socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
if (!socket_bind($socket, '0.0.0.0', 8080) || !socket_listen($socket, 0)) {
    die("port in use\n");
}
while (true) {
    $client_socket = socket_accept($socket);
    $buf = socket_read($client_socket, 1024);
    if($buf && strlen($buf) > 0) {
        $res =  "HTTP/1.1 200 OK\r\nContent-Length: 15\r\n";
        $res .= "Content-Type: text/plain; charset=utf-8\r\n\r\n{\"status\":\"OK\"}";
        socket_write($client_socket, $res);
        socket_close($client_socket);
    }
}


