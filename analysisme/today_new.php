<?php
function getTodayIncrease($uid) {
$uid = $_GET["uid"];
$mysql = new SaeMysql();
$time_dayago = time()- 60*60*24;
echo $time_dayago;
$time_dayago=substr($time_dayago, 0, -5); 
$sql = "select friends from `data` where uid = '".$uid."' and time LIKE '%".$time_dayago."%' order by id desc LIMIT 2";
$increase = $mysql->getData($sql);
if( $mysql->errno() != 0)
{
die("Error:".$mysql->errmsg());
}

$mysql->closeDb();
echo "a:".$increase;
return $increase;	
}
getTodayIncrease('1651712685')

?>
