<?php
error_reporting(0);

echo "[Info] ",date("Y-m-d H:i:s")," Starting IP Server\n";

$fc = file("ip.txt");
$lines = count($fc);

echo "[Info] ",date("Y-m-d H:i:s")," Loaded IP Database\n";

$ip = '0.0.0.0';
$port = 8080;

if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) {
    echo "[error] socket_create() 失败:" . socket_strerror($sock) . "\n";
}

if (($ret = socket_bind($sock, $ip, $port)) < 0) {
    echo "[error] socket_bind() 失败:" . socket_strerror($ret) . "\n";
}

if (($ret = socket_listen($sock, 4)) < 0) {
    echo "[error] socket_listen() 失败:" . socket_strerror($ret) . "\n";
}


while (true) {

    if (($msgsock = socket_accept($sock)) < 0) {
        echo "[error] socket_accept() 失败：" . socket_strerror($msgsock) . "\n";
        break;
    } else {

        $buf = socket_read($msgsock,8192);

        $ip = trim(explode("ip=",$buf)[1]);

        if(strlen($ip)>15){
            $ip = trim(explode(" ",$ip)[0]);
        }
        if(strlen($ip)>15){
            continue;
        }

        if (strlen($ip)<7) {
            socket_getpeername($msgsock, $ip);
        }

        var_dump($ip);

        $pid = pcntl_fork();
        if($pid == 0){
            for ($i=0; $i < $lines; $i++) {
                sscanf($fc[$i],"%s\t%s\t%s\t%s\t",$info[0],$info[1],$info[2],$info[3]);
                if(ipcmp($info[0],$ip)<=0 && ipcmp($info[1],$ip)>=0){
                    echo "[OK] Sub-Process Found:",$ip,"\n";
                    //var_dump($ip);
                    response($msgsock,$info);
                }
            }
            exit(0);
        } else {
            //pcntl_wait($id);
            echo "[OK] Father-Process Finished:",$ip,"\n";
        }

    }
}

function response($msgsock,$info)
{
    $result = json_encode([
        "area" => $info[2],
        "isp" => strcmp($info[3], "CZ88.NET") == 0 ? "" : $info[3],
        "ip_segment" => ($info[0] == $info[1]) ? $info[1] : [$info[0], $info[1]]
    ]);

    $msg = "HTTP/1.1 200 OK\r\nAccess-Control-Allow-Origin:*\r\nContent-type: application/json\r\nConnection: keep-alive\r\nContent-Length: ".strlen($result)."\r\nServer: RytiaIPServer/0.1\r\n\r\n".$result;
    socket_write($msgsock, $msg, strlen($msg));
}

function ipcmp($a, $b)
{
    $c = explode('.', $a);
    $d = explode('.', $b);

    for ($i = 0; $i < 4; $i++) {
        if ($c[$i] < $d[$i]) {
            return -1;
        } elseif ($c[$i] > $d[$i]) {
            return 1;
        } elseif ($i == 4) {
            return 0;
        }
    }
}

function getip()
{
    return $_SERVER['REMOTE_ADDR'];
}