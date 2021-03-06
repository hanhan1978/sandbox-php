<?php
$sock = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
socket_set_nonblock($sock);
if(!socket_bind($sock, '0.0.0.0', 8080) || !socket_listen($sock, 0)){
    die("port in use\n");
}
$pids = [];
foreach(range(0,6) as $i){
    $pid = pcntl_fork();
    if($pid === 0){
        while(true) {
            $client_sock = socket_accept($sock);
            if(!$client_sock) continue;
            socket_set_nonblock($client_sock);
            $res = "HTTP/1.1 200 OK\r\nContent-Length: 15\r\n\r\n{\"status\":\"OK\"}";
            socket_write($client_sock, $res);
            socket_close($client_sock);
        }
    }else{
        $pids[] = $pid;
    }
}
foreach($pids as $pid){
    pcntl_waitpid($pid, $status);
}


