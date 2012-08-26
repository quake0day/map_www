<?php
$uid = $_GET["uid"];
$mysql = new SaeMysql();

$sql = "select * from `data` where uid = '".$uid."' order by id desc LIMIT 40";
//$sql = "select * from `data` where time = '"."1317612638"."' order by id desc LIMIT 40";

$data = $mysql->getData($sql);
if( $mysql->errno() != 0)
{
	die("Error:".$mysql->errmsg());
}
//$dataset = array_merge($data['time'],$data['followers']);
//print_r($data);
echo "{";
echo "\"label\": \"".$uid."\",";

echo "\"data\": [";
$last_value = end($data);

foreach($data as $items){
	//$a = getdate($items['time']);
	//print_r($a);
	//$time = $a['mday'].".".$a['hours'].$a['minutes'];
	$time = ($items['time'] - 60 * 60 *4) *1000; // Eastern time = UTC-5
	$difference = array_diff($items,$last_value);
	if (count($difference) != 0){
	echo "[".$time.",".$items['followers']."],";
}
	else {
		echo "[".$time.",".$items['followers']."]";
	}
}
echo "]";

echo "}";
$mysql->closeDb();	

?>
