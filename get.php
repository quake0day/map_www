<?php
try{
$data= $_POST['data'];

$randomnum = $_POST['rand'];
$path_info = $_POST['path_info'];
//$fq = $_POST['fq'];
//$pol = $_POST['pol'];
//$height = $_POST['height'];
//$heightr = $_POST['height_r'];
//$bandwidth = $_POST['bandwidth'];
}

catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    die("NONOONON");
}
////judege POL
//if( $pol != "Vertical" && $pol != "Horizontal"){
	//$pol = 1;
//}
//elseif($pol == "Vertical"){$pol=1;}
//else{$pol=2;}


//if(!is_numeric($fq)){
 //$fq = preg_replace("/[^0-9]+/", "", $fq);
 //if(empty($fq)) $fq=900; 
//}
//if(!is_numeric($height)){
 //$height = preg_replace("/[^0-9]+/", "", $height);
 //if(empty($height)) $height=50; 
//}
//if(!is_numeric($heightr)){
 //$heightr = preg_replace("/[^0-9]+/", "", $heightr);
 //if(empty($heightr)) $heightr=50; 
//}
//if(!is_numeric($bandwidth)){
 //$bandwidth = preg_replace("/[^0-9]+/", "", $bandwidth);
 //if(empty($bandwidth)) $bandwidth=2; 
//}
// split data into lat lng

//list($none, $lat, $lng,$none) = split('[,()]', $data1);
//list($none, $lat2, $lng2,$none) = split('[,()]', $data2);
// save data into the database
$link = mysql_connect('localhost', 'root', 'chensi');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
// make foo the current db
$db_selected = mysql_select_db('gr', $link);
if (!$db_selected) {
    die ('Can\'t use gr : ' . mysql_error());
}


$query = sprintf("INSERT INTO  `gr`.`main` (
`id` ,
`running_id` ,
`node_attr` ,
`res` ,
`status`,
`path_info`,
`lat2` 
)
VALUES (
NULL ,  '%s',   '0',  '-1',  '0', '%s','%s'
);
",
	$randomnum,
    mysql_real_escape_string($path_info),
    mysql_real_escape_string($data)
);
 
// Perform Query
$result = mysql_query($query) OR die ("Error!! STATUS:CS1");

$query2= " select * from main where running_id = '". $randomnum."' order by id desc";
$query_Et = "select Et from main where running_id = '".$randomnum."' order by id desc";
$i = 0;
do
{
	sleep(1);
	$result = mysql_query($query2) OR die("System ERROR CANNOT CONNECT TO DATABASE");
	$result_et = mysql_query($query_Et) OR die("System ERROR CANNOT CONNECT TO DATABASE");
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$Et = mysql_fetch_array($result_et,MYSQL_ASSOC);
	$i += 1;
	
}while($i < 60 && $row['status'] != '1');
$Et_dataset = explode(',',$Et['Et']);
if($row['status'] == '1' && $i<60 && $row['res'] != '-1'){



	echo "<div class=\"message success close\"> <h2>Congratulations!</h2><p>";

for($k = 0; $k< count($Et_dataset); $k++ ){
echo "Field Strength at Receiver ".$k." :".$Et_dataset[$k]."dB(&mu;hV/m)<br />";
}
echo "GRWAVE Simulation Result:<a href=\"./res/".$row['res']."\" target=\"_blank\">GRWAVE RESULT</a><br />";
echo "Ns2 Simulation Result:<a href=\"./tr/".$row['res']."\" target=\"_blank\">NS2 Trace File</a><br />";
echo "<p><b><a class='iframe' class=\"button\" href=\"./plot_bandwidth.php?data_id=".$randomnum."\">Plot Result</a></p></b> <br />";
$path = "./res/".$row['res'];
echo "</p></div>";

}
elseif($i>2000){
	echo "<div class=\"message warning close\"> <h2>Connect timeout!</h2><p>";
	echo "";
	echo "</p></div>";}
else{echo "<div class=\"message error close\"> <h2>Simulation Error</h2> <p>";
echo "There you found a BUG :) I promise I will kill it. (Error CODE: 0001 -- I can only Calculate New Zealand Terrain District) Please re-run the simulation Thanks!";
echo "</p></div>";}


mysql_close($link);

?>
