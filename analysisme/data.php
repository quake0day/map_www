<?php
$f = new SaeFetchurl();
$u_id = $_GET["uid"];
$url = "http://www.quake0day.com/data.php?uid=".$u_id;
$content = $f->fetch($url);
//echo $content;
$data = explode(' ',$content);
$id = $data[12];
$followers = $data[18];
$friends = $data[24];
$time = $data[30];
$mysql = new SaeMysql();
$sql = "INSERT INTO `app_analysisme`.`data` (`id`, `uid`, `followers`, `friends`, `time`) VALUES (NULL, '".$id."', '".$followers."', '".$friends."', ".time().")";
if ($u_id != NULL){
$mysql->runSql($sql);
}
if( $mysql->errno() != 0)
{
	die("Error:".$mysql->errmsg());
}
$mysql->closeDb();	

?>
