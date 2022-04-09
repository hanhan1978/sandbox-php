<?php
$sock = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
socket_set_nonblock($sock);

if (!socket_bind($sock, '127.0.0.1', 8080) || !socket_listen($sock, 0)) {
    die("port in use\n");
}

$sockets = [$sock];

while (true) {
    $unset_socket_keys = [];
    foreach($sockets as $i => $socket){

        if($socket === $sock){
            if($client_sock = socket_accept($sock)) {
                //socket_set_nonblock($client_sock);
                $sockets[] = $client_sock;
            }
        }else{
            $buf = socket_read($socket, 1024);
            if($buf && strlen($buf) > 0){
                $res = "HTTP/1.1 200 OK\r\nConnection: close\r\nContent-Length: 15\r\n\r\n{\"status\":\"OK\"}\n";
                socket_write($socket, $res);
                socket_close($socket);
                $unset_socket_keys[] = $i;
            }
        }
    }
    foreach($unset_socket_keys as $key){
        unset($sockets[$key]);
    }
}



