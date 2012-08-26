<?php 
$data_id= $_GET['data_id'];
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
$query= " select * from main where running_id = '". $data_id."' order by id desc";
// Perform Query
$result = mysql_query($query) OR die ("Error!! STATUS:CS1");
$row = mysql_fetch_array($result,MYSQL_ASSOC);

$num = count(split(",",$row['throughput']));
$i = 1;
$string="[";
while ($i < $num){
	$s=$i*10;
	$string = $string.$s.",";
	
	$i++;
}
$string = $string.($i*10)."]";


?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highcharts Example</title>

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'line'
            },
            title: {
                text: 'ns-2 Simulation Average Throughput (Kbps) vs Time (s)'
            },
            subtitle: {
                text: ''
            },
            xAxis: {

               categories: <?php echo $string;?>
            },
            yAxis: {
                title: {
                    text: 'Throughput (Kbps)'
                }
            },
            tooltip: {
                enabled: true,
                formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                        this.x +': '+ this.y +'°C';
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            series: [{
                name: 'Throughput',
                data: <?php echo $row['throughput']; ?>
            }]
        });
    });
    
});
		</script>
				<script type="text/javascript">
$(function () {
    var chart2;
    $(document).ready(function() {
        chart2 = new Highcharts.Chart({
            chart: {
                renderTo: 'container2',
                type: 'line'
            },
            title: {
                text: 'ns-2 Simulation Average Delay (s) vs Time (s)'
            },
            subtitle: {
                text: ''
            },
            xAxis: {

               categories: <?php echo $string;?>
            },
            yAxis: {
                title: {
                    text: 'Delay (s)'
                }
            },
            tooltip: {
                enabled: false,
                formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                        this.x +': '+ this.y +'°C';
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            series: [{
                name: 'Delay',
                data: <?php echo $row['delay']; ?>
            }]
        });
    });
    
});
		</script>
	</head>
	<body>
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>

<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
<div id="container2" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
	</body>
</html>
