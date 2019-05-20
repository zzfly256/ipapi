<?php
error_reporting(0);
$ip = "223.5.5.5";

echo "一次性读取开始\n";
$t1 = microtime(true);
$fc = file("ip.txt");
$t2 = microtime(true);
echo "内存加载耗时：",($t2-$t1)," s\n";

$t3 = microtime(true);
$lines = count($fc);
for ($i=0; $i < $lines; $i++) { 
	sscanf($fc[$i],"%s\t%s\t%s\t%s\t",$info[0],$info[1],$info[2],$info[3]);
	if(ipcmp($info[0],$ip)<=0 && ipcmp($info[1],$ip)>=0){
		echo "检索所得结果：",$info[0]," ",$info[1]," ",$info[2]," - ".$info[3],"\n";
		//break;
	}
}
$t4 = microtime(true);
echo "检索内容耗时：",($t4-$t3)," s\n";
echo "整体查询耗时：",($t4-$t1)," s\n";


echo "实时读取开始\n";
$t5 = microtime(true);
$fp = fopen("ip.txt","r");
$t6 = microtime(true);
echo "打开句柄耗时：",($t6-$t5)," s\n";

$t7 = microtime(true);
while (!feof($fp)) {
	fscanf($fp,"%s\t%s\t%s\t%s\t",$info[0],$info[1],$info[2],$info[3]);
	if(ipcmp($info[0],$ip)<=0 && ipcmp($info[1],$ip)>=0){
		echo "检索所得结果：",$info[0]," ",$info[1]," ",$info[2]," - ",$info[3],"\n";
		//break;
	}
}
$t8 = microtime(true);
echo "检索内容耗时：",($t8-$t7)," s\n";
echo "整体查询耗时：",($t8-$t5)," s\n";


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

