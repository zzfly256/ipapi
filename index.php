<?php
error_reporting(0);
if(!isset($_REQUEST['ip']) || empty($_REQUEST['ip'])){
	$ip = getip();
}else{
	$ip = $_REQUEST['ip'];
}

$fp = fopen("ip.txt","r");

while (!feof($fp)) {
	fscanf($fp,"%s\t%s\t%s\t%s\t",$info[0],$info[1],$info[2],$info[3]);
	if(ipcmp($info[0],$ip)<=0 && ipcmp($info[1],$ip)>=0){
		response($info);
		break;
	}
}

function response($info){
	$result = [
			"area" => $info[2],
			"isp" => strcmp($info[3],"CZ88.NET") == 0 ? "" : $info[3],
			"ip_segment" => ($info[0] == $info[1]) ? $info[1] : [$info[0],$info[1]]
		];
	header('Content-type: application/json');
	echo json_encode($result);
}

function ipcmp($a,$b){
	$c = explode('.',$a);
	$d = explode('.',$b);

	for ($i=0; $i < 4; $i++) { 
		if($c[$i]<$d[$i]){
			return -1;
		}
		elseif($c[$i]>$d[$i]){
			return 1;
		}
		elseif($i==4){
			return 0;
		}
	}
}

function getip() {
    return $_SERVER['REMOTE_ADDR'];
}