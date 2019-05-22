<?php

class IpServer
{
    private $data;
    private $size;
    private $server;

    private function loadData($filename)
    {
        $fp = fopen($filename, "r");
        $i = 0;
        while (!feof($fp)) {
            fscanf($fp, "%s\t%s\t%s\t%s\t", $this->data[$i][0], $this->data[$i][1], $this->data[$i][2], $this->data[$i++][3]);
        }

        $this->size = $i;
    }

    public function onStart()
    {
        echo "Start IpServer at ", $this->host . ":", $this->port, "\n";
    }

    public function onRequest($request, $response)
    {
        $response->header("Content-Type", "application/json");
        $response->header("Access-Control-Allow-Origin", "*");

        $ip = isset($request->get["ip"]) ? $request->get["ip"] : $request->server["remote_addr"];

        for ($i = 0; $i < $this->size; $i++) {
            $info = $this->data[$i];
            if ($this->ipcmp($info[0], $ip) <= 0 && $this->ipcmp($info[1], $ip) >= 0) {
                $result = [
                    "ip" => $ip,
                    "area" => $info[2],
                    "isp" => strcmp($info[3],"CZ88.NET") == 0 ? "" : $info[3],
//                    "ip_segment" => ($info[0] == $info[1]) ? $info[1] : [$info[0],$info[1]]
                ];
                $response->end(json_encode($result));
                break;
            }
        }

    }

    private function ipcmp($a, $b)
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

    public function __construct($host, $port)
    {
        $this->loadData("ip.txt");

        $this->host = $host;
        $this->port = $port;
        $this->server = new swoole_http_server($this->host, $this->port);
        $this->server->set(array(
            'worker_num' => 8,   //设置启动的Worker进程数。
        ));
        $this->server->on('start', [$this, 'onStart']);
        $this->server->on('request', [$this, 'onRequest']);
        $this->server->start();

    }
}

(new IpServer("0.0.0.0", 9502));