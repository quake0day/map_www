<?php
$u_id = $_GET["uid"];
mysql_connect("localhost","root","chensi");
mysql_select_db("weibo_add");

$sql = "select * from data where uid='".$u_id."' order by id desc LIMIT 1";

$data = mysql_query($sql);

$data = mysql_fetch_row($data);

print_r($data);
?>
