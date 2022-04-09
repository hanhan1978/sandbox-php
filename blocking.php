<?php
//TCP Socket を作成
$socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
//TIME_WAIT時にアドレス・ポートの使い回しが出来るようにセット
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

//Socket に SRC Address, Port を bind して listen
if (!socket_bind($socket, '127.0.0.1', 8080) || !socket_listen($socket, 0)) {
    die("port in use\n");
}
while (true) {
    //client からの接続を待つ (block)
    $client_socket = socket_accept($socket);
    //client からの文字入力を待つ (block)
    $buf = socket_read($client_socket, 1024);
    if($buf && strlen($buf) > 0) {

        $res =  "HTTP/1.1 200 OK\r\nConnection: close\r\n";
        $res .= "Content-Length: 15\r\n\r\n{\"status\":\"OK\"}\n";
        socket_write($client_socket, $res);
        socket_close($client_socket);
    }
}