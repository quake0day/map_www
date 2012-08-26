<?php
session_start();

include_once( 'config.php' );
include_once( 'saet2.ex.class.php' );

//从POST过来的signed_request中提取oauth2信息
$c = new SaeTClient( WB_AKEY , WB_SKEY ,'e4e3b24123df8f1ac8412ed4e9ee46eb' ,'' );


?>

<?php
$u_id = "1876063045";
$msg  = $c->show_user($u_id); // done
if($msg === false || $msg === null){
	echo "Error occured";
	return false;
}
if(isset($msg['error_code']) && isset($msg['error'])){
	echo (' Error_code:' . $msg['error_code'] .'; Error:' .$msg['error']);
	return false;
}
echo($msg['id'].':'.$msg['name'].' '.$msg['followers_count'].' '.$msg['friends_count']);

$mysql = new SaeMysql();
$sql = "INSERT INTO `app_analysisme`.`data` (`id`, `uid`, `followers`, `friends`, `time`) VALUES (NULL, '".$msg['id']."', '".$msg['followers_count']."', '".$msg['friends_count']."', ".time().")";
$mysql->runSql($sql);
if( $mysql->errno() != 0)
{
	die("Error:".$mysql->errmsg());
}
$mysql->closeDb();	

?>



</body>
</html>
