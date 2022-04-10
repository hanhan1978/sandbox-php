<?php

$loop = new EvLoop();

$master_socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
//TIME_WAIT時にアドレス・ポートの使い回しが出来るようにセット
socket_set_option($master_socket, SOL_SOCKET, SO_REUSEADDR, 1);

//Socket に SRC Address, Port を bind して listen
if (!socket_bind($master_socket, '127.0.0.1', 8080) || !socket_listen($master_socket, 0)) {
    die("port in use\n");
}
socket_set_nonblock($master_socket);

$loop = EvLoop::defaultLoop();
var_dump($loop);

$evio = new EvIo($master_socket, Ev::READ, 'onRead');

var_dump($evio->getLoop());

$evio->start();

$loop->run(0);

exit;

echo "before run\n";

$loop->run();

echo "after run\n";

//$loop->io($master_socket, Ev::READ, 'accept');
while(true){
    sleep(1);
    $client_sock = socket_accept($master_socket);
    if($client_sock){
        echo("kitemasu\n");
        $loop->io($client_sock, Ev::READ, 'onRead');
        //$loop->io($client_sock, Ev::WRITE, function($w){  echo "unko_w\n";});
    }
    var_dump(Ev::now());
}


function accept()
{
    global $master_socket;
    echo "accept\n";
    $client_sock = socket_accept($master_socket);

}

function onRead(EvIo $ev)
{
    //socket_set_blocking($ev->fd, false);
    echo("unko1\n");
    $buf = fgets($ev->fd, 1024);
    echo("unko2\n");
    fwrite($ev->fd, $buf);
    echo("unko3\n");
    fclose($ev->fd);
    echo("unko4\n");
    exit;
    return $ev;
}

/**
$loop = new EventLoop();

$repeater = new libev\TimerEvent(function()
{
    echo "I repeat every second!\n";
}, 1, 1);

$single = new libev\TimerEvent(function()
{
   echo "I will fire after 5 seconds, without repeat\n";
}, 5);

$loop->add($repeater);
$loop->add($single);

$loop->run();
**/
