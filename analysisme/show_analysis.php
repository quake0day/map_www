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
		$c = new SaeTClient( WB_AKEY , WB_SKEY ,$_SESSION['oauth2']['oauth_token'] ,'' );
} 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>授权后的页面</title>
   <link href="layout.css" rel="stylesheet" type="text/css">
    <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="excanvas.min.js"></script><![endif]-->
    <script language="javascript" type="text/javascript" src="jquery.js"></script>
    <script language="javascript" type="text/javascript" src="jquery.flot.js"></script>
</head>

<body>

<?php
$u_id = "1651712685";
$msg  = $c->show_user($u_id); // done
if($msg === false || $msg === null){
	echo "Error occured";
	return false;
}
if(isset($msg['error_code']) && isset($msg['error'])){
	echo (' Error_code:' . $msg['error_code'] .'; Error:' .$msg['error']);
	return false;
}
//echo($msg['id'].':'.$msg['name'].' '.$msg['followers_count'].' '.$msg['friends_count']);
	
echo "今日新增：";
?>

  <div id="placeholder" style="width:600px;height:300px;"></div>
  
    <p>
      <input class="fetchSeries" type="button" value="First dataset"> -
      <a href="http://analysisme.sinaapp.com/serve_data.php?uid=1651712685">me</a> -
      <span></span>
    </p>
    <p>
      <input class="fetchSeries" type="button" value="First dataset"> -
      <a href="http://analysisme.sinaapp.com/serve_data.php?uid=1876063045">yr</a> -
      <span></span>
    </p>
<script type="text/javascript">
$(function () {
    var options = {
        lines: { show: true },
        points: { show: true },
        xaxis: { tickDecimals: 0, tickSize: 1 }
    };
    var data = [];
    var placeholder = $("#placeholder");
    
    $.plot(placeholder, data, options);

    
    // fetch one series, adding to what we got
    var alreadyFetched = {};
    
    $("input.fetchSeries").click(function () {
        var button = $(this);
        
        // find the URL in the link right next to us 
        var dataurl = button.siblings('a').attr('href');

        // then fetch the data with jQuery
        function onDataReceived(series) {
            // extract the first coordinate pair so you can see that
            // data is now an ordinary Javascript object
            var firstcoordinate = '(' + series.data[0][0] + ', ' + series.data[0][1] + ')';

            button.siblings('span').text('Fetched ' + series.label + ', first point: ' + firstcoordinate);

            // let's add it to our current data
            if (!alreadyFetched[series.label]) {
                alreadyFetched[series.label] = true;
                data.push(series);
            }
            
            // and plot all we got
            $.plot(placeholder, data, options);
         }
        
        $.ajax({
            url: dataurl,
            type: 'GET',
            dataType: 'json',
            success: onDataReceived
        });
    });


    // initiate a recurring data update
    $("input.dataUpdate").click(function () {
        // reset data
        data = [];
        alreadyFetched = {};
        
        $.plot(placeholder, data, options);

        var iteration = 0;
        
        function fetchData() {
            ++iteration;

            function onDataReceived(series) {
                // we get all the data in one go, if we only got partial
                // data, we could merge it with what we already got
                data = [ series ];
                
                $.plot($("#placeholder"), data, options);
            }
        
            $.ajax({
                // usually, we'll just call the same URL, a script
                // connected to a database, but in this case we only
                // have static example files so we need to modify the
                // URL
                url: "data-eu-gdp-growth-" + iteration + ".json",
                type: 'GET',
                dataType: 'json',
                success: onDataReceived
            });
            
            if (iteration < 5)
                setTimeout(fetchData, 1000);
            else {
                data = [];
                alreadyFetched = {};
            }
        }

        setTimeout(fetchData, 1000);
    });
});
</script>

</body>
</html>
