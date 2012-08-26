<?php
session_start();

include_once( 'config.php' );
include_once( 'saet2.ex.class.php' );

//从POST过来的signed_request中提取oauth2信息
if(!empty($_REQUEST["signed_request"])){
	$o = new SaeTOAuth( WB_AKEY , WB_SKEY  );
	$data=$o->parseSignedRequest($_REQUEST["signed_request"]);
	if($data=='-2'){
		 die('签名错误!');
	}else{
		$_SESSION['oauth2']=$data;
	}
}
//判断用户是否授权
if (empty($_SESSION['oauth2']["user_id"])) {
		include "auth.php";
		exit;
} else {
		echo $_SESSION['oauth2']['oauth_token'];
		$c = new SaeTClient( WB_AKEY , WB_SKEY ,$_SESSION['oauth2']['oauth_token'] ,'' );
} 

?>

<?php
$u_id = "夏之幻-你记得喊安可";
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

$sql = "INSERT INTO `app_analysisme`.`data` (`id`, `uid`, `followers`, `friends`, `time`) VALUES (NULL, '".$msg['id']."', '".$msg['followers_count']."', '".$msg['friends_count']."', CURRENT_TIMESTAMP)";
$mysql->runSql($sql);
if( $mysql->errno() != 0)
{
	die("Error:".$mysql->errmsg());
}
$mysql->closeDb();	

?>



</body>
</html>
