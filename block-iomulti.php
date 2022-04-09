<?php
//TCP Socket を作成
$master_socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
//TIME_WAIT時にアドレス・ポートの使い回しが出来るようにセット
socket_set_option($master_socket, SOL_SOCKET, SO_REUSEADDR, 1);

//Socket に SRC Address, Port を bind して listen
if (!socket_bind($master_socket, '127.0.0.1', 8080) || 
    !socket_listen($master_socket, 0)) {
    die("port in use\n");
}

//全てのソケットを保持する配列
$read = [$master_socket];

while (true) {

    $null = null; //write, except は今回は使わないのでnull
    socket_select($read, $null, $null, $null);

    $new_read = [$master_socket];
    // 変更が検知されたsocketに対してそれぞれ処理を行う。
    foreach ($read as $socket) {
        //master_socketへの要求の場合は通信用の子ソケットを作る
        if($socket === $master_socket){
            $new_read[] = socket_accept($master_socket);
        }else{
            // 子ソケットの場合は、送信されてきた通信内容を読んで処理を行う。
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