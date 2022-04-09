<?php
$sock = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
socket_set_nonblock($sock);

if(!socket_bind($sock, '127.0.0.1', 8080) || !socket_listen($sock, 0)){
    die("port in use\n");
}

$pids = [];
foreach(range(0,12) as $i){ //２つの子プロセスを作成する
    $pid = pcntl_fork();
    if($pid === 0){ //子プロセスの場合
        while(true) {
            $client_sock = socket_accept($sock);
            if(!$client_sock) continue;
            socket_set_nonblock($client_sock);
            $res = "HTTP/1.1 200 OK\r\nConnection: close\r\nContent-Length: 15\r\n\r\n{\"status\":\"OK\"}\n";
            socket_write($client_sock, $res);
            socket_close($client_sock);
        }
    }else{
        //親プロセスの場合は、pidを収集
        $pids[] = $pid;
    }
}

foreach($pids as $pid){
    //親側のプロセスは、子プロセスの状態を監視する（ここで処理はBlockする）
    pcntl_waitpid($pid, $status);
}
