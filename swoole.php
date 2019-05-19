<?php
class Fun
{
	private $data;
	private $templete;
	private $server;

	private function loadData($filename) {
        $fp = fopen($filename,"r");
        $i = 0;
        while (!feof($fp)) {
            fscanf($fp,"%s\t%s\t%s\t%s\t",$this->data[$i][0],$this->data[$i][1],$this->data[$i++][2],$this->data[][3]);
        }
    }

	public function onStart() {
		echo "Start IP Server at ", $this->host.":", $this->port, "\n";
	}

	public function onRequest($request, $response) {
		$response->header("Content-Type", "application/json");
		$response->header("Access-Control-Allow-Origin", "*");
		$ip = $request->get["ip"];

		foreach ($this->data as $info){
            if(ipcmp($info[0],$ip)<=0 && ipcmp($info[1],$ip)>=0){
                response($ip,$info);
                break;
            }
        }


//    	$response->end(json_encode($this->data));
//    	$response->end($this->generatePage($this->data[rand(0, count($this->data)-1)]));
	}

	public function __construct($host, $port) {
		$this->loadData("ip.txt");

		$this->host = $host;
		$this->port = $port;
		$this->server = new swoole_http_server($this->host, $this->port);
		$this->server->set(array(
		    'worker_num' => 8,   //设置启动的Worker进程数。
		));
		$this->server->on('start',[$this,'onStart']);
		$this->server->on('request',[$this,'onRequest']);
		$this->server->start();

	}
}

(new Fun("0.0.0.0", 9502));